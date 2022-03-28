<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\YahooToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetShopCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-shop-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Shop Category';

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
        $categorys = ShopCategory::whereNull('status')->orderBy('updated_at', 'desc')->take(10)->get();
        foreach ($categorys as $category){
            if(isset($category)){
                $shop_id = $category->shop_id;
                $shop = Shop::with('app')->find($shop_id);
                $seller_id = $shop->store_account;
                $app_id = $shop->app->id;
                $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
                $authorization = "Authorization: Bearer " . $access_token;
                $pagekey = $category->pagekey;
                try {
                    $org_curl = curl_init();
                    $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/stCategoryList?seller_id=" . $seller_id . "&page_key=" . $pagekey;
                    curl_setopt($org_curl, CURLOPT_URL, $url);
                    curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
                    curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($org_curl);
                    $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                    $attr = $data['@attributes'];
                    $total = (int)$attr['totalResultsAvailable'];
                    $total_return = (int)$attr['totalResultsReturned'];
                    $result = $data['Result'];
                    if($total_return > 0){
                        if($total_return != 1) {
                            foreach ($result as $item){
                                $item = (array)$item;
                                //print_r($item['IsLeaf']);
                                Log::info('Shop Category Name: ' . $item['Name']);
                                ShopCategory::updateOrCreate(['shop_id' => $shop_id, 'pagekey' => (string)$item['PageKey']], [
                                    'shop_id' => $shop_id,
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
                            ShopCategory::updateOrCreate(['shop_id' => $shop_id, 'pagekey' => (string)$item['PageKey']], [
                                'shop_id' => $shop_id,
                                'pagekey' => (string)$item['PageKey'],
                                'name' => (string)$item['Name'],
                                'display' => $item['Display'],
                                'updatetime' => date('Y-m-d H:i:s', strtotime($item['UpdateTime']))
                            ]);
                        }
                    }
                    ShopCategory::where('pagekey', $pagekey)->where('shop_id', $shop_id)->update(['status' => 1]);
                }
                catch (\ErrorException $e){
                    Log::error('Get Shop Category Error');
                }
            }
        }

        return 0;
    }
}
