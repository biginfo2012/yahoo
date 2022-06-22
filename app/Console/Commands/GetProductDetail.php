<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\YahooToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetProductDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-product-detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Product Detail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = ShopProduct::whereNull('status')->orderBy('id', 'asc')->take(25)->get();
        foreach ($products as $product){
            try {
                $shop_id = $product->shop_id;
                $shop = Shop::with('app')->find($shop_id);
                $seller_id = $shop->store_account;
                $app_id = $shop->app->id;
                $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
                $authorization = "Authorization: Bearer " . $access_token;
                $item_code = $product->item_code;
                $org_curl = curl_init();
                $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getItem?seller_id=" . $seller_id . "&item_code=" . $item_code;
                curl_setopt($org_curl, CURLOPT_URL, $url);
                curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
                curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($org_curl);
                $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                if(array_key_exists('@attributes', $data)){
                    $attr = $data['@attributes'];
                    $total = (int)$attr['totalResultsReturned'];
                    $result = $data['Result'];

                    if($total == 1) {
                        $item = (array)$result;
                        Log::info("item->name: " . $item['Name']);
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
                            'headline' => (string)$item['Headline'],
                            'explanation' => (string)$item['Explanation'],
                            'taxable' => (int)$item['Taxable'],
                            'taxrate_type' => (float)$item['TaxrateType'],
                            'sale_limit' => (int)$item['SaleLimit'],
                            'display' => (int)$item['Display'],
                            'delivery' => (int)$item['Delivery'],
                            'condition' => (int)$item['Condition'],
                            'lead_time_instock' => (int)$item['LeadTimeInStock'],
                            'keep_stock' => (int)$item['KeepStock'],
                            'postage_set' => (int)$item['PostageSet']
                        ];
                        Product::where('id', $product->product_id)->delete();
                        $id = Product::create($data)->id;
                        ShopProduct::where('id', $product->id)->update(['status' => 1, 'product_id' => $id]);
                    }
                }
                else{
                    Log::debug(json_encode($data['Code']));
                    ShopProduct::where('id', $product->id)->delete();
                    Product::where('id', $product->product_id)->delete();
                }
            }
            catch (\ErrorException $e){
               Log::error("Get Product Detail Error: " . $e);
            }
        }
        return 0;
    }
}
