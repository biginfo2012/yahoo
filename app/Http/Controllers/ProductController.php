<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\YahooCategory;
use App\Models\YahooToken;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;
use YConnect\Constant\OIDConnectDisplay;
use YConnect\Constant\OIDConnectPrompt;
use YConnect\Constant\OIDConnectScope;
use YConnect\Constant\ResponseType;
use function Symfony\Component\String\toString;

class ProductController extends Controller
{
    //
    public function storeProduct($id){
        $store_id= $id;
// リクエストとコールバック間の検証用のランダムな文字列を指定してください
        $state = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10);
        // リプレイアタック対策のランダムな文字列を指定してください
        $nonce = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10);
        return view('shop-product', compact('store_id', 'state', 'nonce'));
    }
    public function productList(Request $request){
        $store_id = $request->store_id;
        $page = $request->page;

        $data = ShopProduct::with('product')->where('shop_id', $store_id)->orderBy('created_at', 'asc')
            ->offset(($page - 1) * 50)->limit(50)->get();
        $total = ShopProduct::where('shop_id', $store_id)->pluck('id')->count();
        $page_count = (int)($total/50);
        if($total > $page_count * 50){
            $page_count = $page_count +1;
        }
        return view('product-list', compact('data', 'total', 'page', 'page_count'));
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function yahooAuthCode(){
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        $redirect_uri = env('YAHOO_CALLBACK');
        $state = $this->generateRandomString(35);
        // リプレイアタック対策のランダムな文字列を指定してください
        $nonce = $this->generateRandomString(60);
        $response_type = ResponseType::CODE;
        $scope = array(
            OIDConnectScope::OPENID,
            OIDConnectScope::PROFILE,
            OIDConnectScope::EMAIL,
            OIDConnectScope::ADDRESS
        );
        $display = OIDConnectDisplay::DEFAULT_DISPLAY;
        $prompt = array(
            OIDConnectPrompt::DEFAULT_PROMPT
        );

        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);
        // Authorizationエンドポイントにリクエスト
        $client->requestAuth($redirect_uri, $state, $nonce, $response_type, $scope, $display, $prompt);

        YahooToken::updateOrCreate(['id' => 1], ['state' => $state, 'nonce' => $nonce]);

        return response()->json(['status' => true]);
    }

    public function yahooCallback(Request $request){
        $code = $request->code;
        $state = $request->state;
        Log::info("code: " . $code);
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        $redirect_uri = "http://yahooshop.info/yahoo_callback/";

        $ch = curl_init();
        $grant_type = "authorization_code";
        $cul_url = "https://auth.login.yahoo.co.jp/yconnect/v2/token?grant_type=" . $grant_type . "&client_id=" . $client_id . "$&client_secret=" . $client_secret
            . "&code=" . $code . "&redirect_uri=" . $redirect_uri;
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, $cul_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        Log::info('response: ' . $response);
        $curl_errno = curl_errno($ch);
        Log::info('$curl_errno: ' . $curl_errno);
        $curl_error = curl_error($ch);
        Log::info('$curl_error: ' . $curl_error);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        Log::info('$httpCode: ' . $httpCode);
        curl_close($ch);

        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);

//        $state = YahooToken::find(1)->state;
//        $nonce = YahooToken::find(1)->nonce;
        // 認可コードを取得
        //$code_result = $client->getAuthorizationCode($state);
        //Log::info("code_result: " . $code_result);
        // Tokenエンドポイントにリクエスト
        $client->requestAccessToken(
            $redirect_uri,
            $code
        );
        $access_token = $client->getAccessToken();
        // IDトークンを検証
        YahooToken::updateOrCreate(['id' => 1], ['access_token' => $access_token, 'refresh_token' => $client->getRefreshToken()]);
        return redirect()->back();
    }

    public function yahooRefresh(Request $request){
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);
        // 保存していたリフレッシュトークンを指定してください
        $refresh_token = YahooToken::find(1)->refresh_token;
        // Tokenエンドポイントにリクエストしてアクセストークンを更新
        $client->refreshAccessToken($refresh_token);
        $access_token = $client->getAccessToken();

        // IDトークンを検証
        YahooToken::updateOrCreate(['id' => 1], ['access_token' => $access_token]);
        return redirect()->back();
    }

    public function yahooGetCategory($id){
        $store_id = $id;
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;

        $seller_id = Shop::find($id)->store_account;
//        $cateories = YahooCategory::where('store_id', $id)->where('is_leaf', 0)->whereNull('status')->orderBy('created_at', 'desc')->pluck('category_code')->toArray();
//        foreach ($cateories as $index => $iValue) {
//            $category_code = $iValue;
//            $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getShopCategory?seller_id=". $seller_id . "&category_code=" . $category_code;
//            $org_curl = curl_init();
//            curl_setopt($org_curl, CURLOPT_URL, $url);
//            curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
//            curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
//            curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);
//
//            $response = curl_exec($org_curl);
//            curl_close($org_curl);
//            $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
//        //print_r($response);
//        //print_r($data);
//            $attr = $data['@attributes'];
//            $total = (int)$attr['totalResultsAvailable'];
//            //print_r($total);
////        die();
//            $result = $data['Result'];
//            if($total != 1) {
//                foreach ($result as $item){
//                    $item = (array)$item;
//                    //print_r($item['IsLeaf']);
//                    YahooCategory::updateOrCreate(['store_id' => $id, 'category_code' => (string)$item['CategoryCode']], [
//                        'store_id' => $id,
//                        'category_code' => (string)$item['CategoryCode'],
//                        'category_name' => (string)$item['CategoryName'],
//                        'display' => $item['Display'],
//                        'is_leaf' => $item['IsLeaf'],
//                        'update_date' => date('Y-m-d', strtotime($item['UpdateDate']))
//                    ]);
//                }
//            }
//            else{
//                $item = (array)$result;
//                YahooCategory::updateOrCreate(['store_id' => $id, 'category_code' => (string)$item['CategoryCode']], [
//                    'store_id' => $id,
//                    'category_code' => (string)$item['CategoryCode'],
//                    'category_name' => (string)$item['CategoryName'],
//                    'display' => $item['Display'],
//                    'is_leaf' => $item['IsLeaf'],
//                    'update_date' => date('Y-m-d', strtotime($item['UpdateDate']))
//                ]);
//            }
//            YahooCategory::where('category_code', $iValue)->where('store_id', $store_id)->update(['status' => 1]);
//        }
        //
        //https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/stCategoryList
        //https://shopping.yahooapis.jp/ShoppingWebService/V1/itemSearch?appid=<あなたのアプリケーションID>&query=vaio
        //https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getShopCategory

        //$category = ShopCategory::where('shop_id', $id)->whereNull('status')->orderBy('created_at', 'desc')->first();
        //if(isset($category)){
            //$pagekey = $category->pagekey;
            $org_curl = curl_init();
            $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/stCategoryList?seller_id=" . $seller_id;
            curl_setopt($org_curl, CURLOPT_URL, $url);
            curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
            curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($org_curl);
            print_r($response);
            die();
            $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
            $attr = $data['@attributes'];
            $total = (int)$attr['totalResultsAvailable'];
            $result = $data['Result'];
            if($total > 0){
                if($total != 1) {
                    foreach ($result as $item){
                        $item = (array)$item;
                        //print_r($item['IsLeaf']);
                        Log::info('Shop Category Name: ' . $item['Name']);
                        ShopCategory::updateOrCreate(['shop_id' => $id, 'pagekey' => (string)$item['PageKey']], [
                            'shop_id' => $id,
                            'pagekey' => (string)$item['PageKey'],
                            'name' => (string)$item['Name'],
                            'display' => $item['Display'],
                            'updatetime' => date('Y-m-d H:i:s', strtotime($item['UpdateTime']))
                        ]);
                    }
                }
                else{
                    $item = (array)$result;
                    Log::info('Shop Category Name: ' . $item['Name']);
                    ShopCategory::updateOrCreate(['shop_id' => $id, 'pagekey' => (string)$item['PageKey']], [
                        'shop_id' => $id,
                        'pagekey' => (string)$item['PageKey'],
                        'name' => (string)$item['Name'],
                        'display' => $item['Display'],
                        'updatetime' => date('Y-m-d H:i:s', strtotime($item['UpdateTime']))
                    ]);
                }
            }
            ShopCategory::where('pagekey', $pagekey)->where('shop_id', $store_id)->update(['status' => 1]);
        //}


        return redirect()->back();
    }
    public function yahooSearchProduct1($id){
        $access_token = YahooToken::find(1)->access_token;

        $seller_id = Shop::find($id)->store_account;
        //$authorization = "Authorization: Bearer " . $access_token;
        $total = 100000;
        $start = 1;

        while ($start < $total){
            $org_curl = curl_init();
            $url = "https://shopping.yahooapis.jp/ShoppingWebService/V3/itemSearch?appid=". env('YAHOO_CLIENT_ID') ."&seller_id=" . $seller_id .
                "&genre_category_id=13457&start=" . $start . "&results=100";
            curl_setopt($org_curl, CURLOPT_URL, $url);
            //curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
            curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($org_curl);
            //Log::info("response: " . $response);
            Log::info("start: " . $start);
            print_r('start:' . $start);
            curl_close($org_curl);
            $data = json_decode($response);
            $result = $data->hits;
            $total = (int)$data->totalResultsAvailable;

            foreach ($result as $item)
            {
                $product_id = Product::updateOrCreate(['name' => $item->name], [
                    'name' => $item->name,
                    'price' => $item->price,
                    'headline' => $item->headLine,
                    'explanation' => $item->description,
                ])->id;
                ShopProduct::updateOrCreate(['shop_id' => $id, 'item_code' => $item->code], [
                    'shop_id' => $id,
                    'item_code' => $item->code,
                    'product_id' => $product_id
                ]);
            }
            $start = $start + 100;
        }
        return response()->json(['status' => true]);
        //return redirect()->back();
    }

    public function yahooSearchProduct($id){
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;
        $seller_id = Shop::find($id)->store_account;
        $total = 100000;
        $start = 1;
        $category = ShopCategory::where('shop_id', $id)->where('get_status', '!=', 2)->orderBy('id', 'asc')->first();
        if(isset($category)){
            $pagekey = $category->pagekey;
            if(isset($category->start)){
                $start = $category->start + 100;
            }
            if(isset($category->total)){
                $total = $category->total;
            }

            if($start > $total){
                ShopCategory::where('pagekey', $pagekey)->where('shop_id', $id)->update(['get_status' => 2]);
            }
            else{
                $org_curl = curl_init();
                $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/myItemList?seller_id=" . $seller_id . "&start=" . $start . "&results=100" . "&stcat_key=" . $pagekey;
                curl_setopt($org_curl, CURLOPT_URL, $url);
                curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
                curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($org_curl);
                $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                $attr = $data['@attributes'];
                $total = (int)$attr['totalResultsAvailable'];
                $result = $data['Result'];
                if($total > 0){
                    Log::info("category_code: " . $pagekey);
                    if($total != 1) {
                        foreach ($result as $item){
                            $item = (array)$item;
                            Log::info("item->name: " . $item['Name']);
                            ShopProduct::updateOrCreate(['shop_id' => $id, 'item_code' => $item['ItemCode']], [
                                'shop_id' => $id,
                                'item_code' => $item['ItemCode'],
                            ]);
                        }
                    }
                    else{
                        $item = (array)$result;
                        Log::info("item->name: " . $item['Name']);
                        ShopProduct::updateOrCreate(['shop_id' => $id, 'item_code' => $item['ItemCode']], [
                            'shop_id' => $id,
                            'item_code' => $item['ItemCode'],
                        ]);
                    }
                }
                ShopCategory::where('pagekey', $pagekey)->where('shop_id', $id)->update(['get_status' => 1, 'total' => $total, 'start' => $start]);
            }
        }

        return response()->json(['status' => true]);
        //return redirect()->back();
    }

    public function yahooProductItem($id){
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;
        $seller_id = Shop::find($id)->store_account;
        $products = ShopProduct::where('shop_id', $id)->whereNull('status')->orderBy('id', 'asc')->take(100)->get();
        foreach ($products as $product){

                $item_code = $product->item_code;
                $org_curl = curl_init();
                $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getItem?seller_id=" . $seller_id . "&item_code=" . $item_code;
                curl_setopt($org_curl, CURLOPT_URL, $url);
                curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
                curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($org_curl);
                $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                $attr = $data['@attributes'];
                $total = (int)$attr['totalResultsReturned'];
                $result = $data['Result'];

                if($total == 1) {
                    $item = (array)$result;
                    Log::info("item->name: " . $item['Name']);
                    $release_date = empty($item['ReleaseDate']) ? null : date('Y-m-d', strtotime($item['ReleaseDate']));
                    $sale_period_start= empty($item['SalePeriodStart']) ? null : date('Y-m-d H:i:s', strtotime($item['SalePeriodStart']));
                    $sale_period_end= empty($item['SalePeriodEnd']) ? null : date('Y-m-d H:i:s', strtotime($item['SalePeriodEnd']));
                    $item_image_urls = $item['Image'];
                    $pathlist = (array)$item['PathList'];
                    $path = $pathlist['Path'];
                    for($i = 1; $i <= 20; $i++){
                        $key = 'LibImage' . $i;
                        if(!empty($item[$key])){
                            $item_image_urls = $item_image_urls . ';' . $item[$key];
                        }
                    }
                    $data = [
                        'name' => (string)$item['Name'],
                        'price' => (int)$item['Price'],
                        'path' => (string)$path,
                        'product_category' => (int)$item['ProductCategory'],
                        'item_image_urls' => (string)$item_image_urls,
                        'caption' => (string)$item['Caption'],
                        'abstract' => (string)$item['Abstract'],
                        'original_price' => (int)$item['OriginalPrice'],
                        'sale_price' => (int)$item['SalePrice'],
                        'member_price' => (int)$item['MemberPrice'],
                        'headline' => (string)$item['Headline'],
                        'explanation' => (string)$item['Explanation'],
                        'additional1' => (string)$item['Additional1'],
                        'additional2' => (string)$item['Additional2'],
                        'additional3' => (string)$item['Additional3'],
                        'sp_additional' => (string)$item['SpAdditional'],
                        'cart_related_items' => (string)$item['CartRelatedItems'],
                        'ship_weight' => (int)$item['ShipWeight'],
                        'taxable' => (int)$item['Taxable'],
                        'taxrate_type' => (float)$item['TaxrateType'],
                        'release_date' => $release_date,
                        'sale_period_start' => $sale_period_start,
                        'sale_period_end' => $sale_period_end,
                        'sale_limit' => (int)$item['SaleLimit'],
                        'sp_code' => (int)$item['SpCode'],
                        'point_code' => (string)$item['PointCode'],
                        'meta_desc' => (string)$item['MetaDesc'],
                        'display' => (int)$item['Display'],
                        'brand_code' => (int)$item['BrandCode'],
                        'product_code' => (string)$item['ProductCode'],
                        'jan' => (int)$item['Jan'],
                        'delivery' => (int)$item['Delivery'],
                        'condition' => (int)$item['Condition'],
                        'original_price_evidence' => (string)$item['OriginalPriceEvidence'],
                        'lead_time_instock' => (int)$item['LeadTimeInStock'],
                        'lead_time_outstock' => (int)$item['LeadTimeOutStock'],
                        'keep_stock' => (int)$item['KeepStock'],
                        'postage_set' => (int)$item['PostageSet'],
                        'is_drug' => (int)$item['IsDrug'],
                        'item_tag'=>(string)$item['ItemTag']
                    ];

                    Product::where('id', $product->product_id)->delete();
                    $id = Product::create($data)->id;
                    ShopProduct::where('id', $product->id)->update(['status' => 1, 'product_id' => $id]);
                }

        }


        return response()->json(['status' => true]);
    }
}
