<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\YahooCategory;
use App\Models\YahooToken;
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

    public function yahooGetCategory($id){
        $store_id = $id;
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;

        $seller_id = Shop::find($id)->store_account;
        $cateories = YahooCategory::where('store_id', $id)->where('is_leaf', 0)->whereNull('status')->orderBy('created_at', 'desc')->pluck('category_code')->toArray();
        foreach ($cateories as $index => $iValue) {
            $category_code = $iValue;
            $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getShopCategory?seller_id=". $seller_id . "&category_code=" . $category_code;
            $org_curl = curl_init();
            curl_setopt($org_curl, CURLOPT_URL, $url);
            curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
            curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($org_curl);
            curl_close($org_curl);
            $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        //print_r($response);
        //print_r($data);
            $attr = $data['@attributes'];
            $total = (int)$attr['totalResultsAvailable'];
            //print_r($total);
//        die();
            $result = $data['Result'];
            if($total != 1) {
                foreach ($result as $item){
                    $item = (array)$item;
                    //print_r($item['IsLeaf']);
                    YahooCategory::updateOrCreate(['store_id' => $id, 'category_code' => (string)$item['CategoryCode']], [
                        'store_id' => $id,
                        'category_code' => (string)$item['CategoryCode'],
                        'category_name' => (string)$item['CategoryName'],
                        'display' => $item['Display'],
                        'is_leaf' => $item['IsLeaf'],
                        'update_date' => date('Y-m-d', strtotime($item['UpdateDate']))
                    ]);
                }
            }
            else{
                $item = (array)$result;
                YahooCategory::updateOrCreate(['store_id' => $id, 'category_code' => (string)$item['CategoryCode']], [
                    'store_id' => $id,
                    'category_code' => (string)$item['CategoryCode'],
                    'category_name' => (string)$item['CategoryName'],
                    'display' => $item['Display'],
                    'is_leaf' => $item['IsLeaf'],
                    'update_date' => date('Y-m-d', strtotime($item['UpdateDate']))
                ]);
            }
            YahooCategory::where('category_code', $iValue)->where('store_id', $store_id)->update(['status' => 1]);
        }

        //
        //https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/stCategoryList
        //https://shopping.yahooapis.jp/ShoppingWebService/V1/itemSearch?appid=<あなたのアプリケーションID>&query=vaio
        //https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getShopCategory

        return redirect()->back();
    }
    public function yahooSearchProduct($id){
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
}
