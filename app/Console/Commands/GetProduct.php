<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\YahooCategory;
use App\Models\YahooToken;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $categorys = ShopCategory::where('get_status', '!=', 2)->orderBy('updated_at', 'desc')->take(5)->get();
        foreach ($categorys as $category){
            if(isset($category)){
                $total = 100000;
                $start = 1;
                $shop_id = $category->shop_id;
                $shop = Shop::with('app')->find($shop_id);
                $seller_id = $shop->store_account;
                $app_id = $shop->app->id;
                $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
                $authorization = "Authorization: Bearer " . $access_token;
                $pagekey = $category->pagekey;
                if(isset($category->start)){
                    $start = $category->start;
                }
                if(isset($category->total)){
                    $total = $category->total;
                }
                if($start > $total){
                    ShopCategory::where('pagekey', $pagekey)->where('shop_id', $shop_id)->update(['get_status' => 2]);
                }
                else{
                    try {
                        $org_curl = curl_init();
                        $url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/myItemList?seller_id=" . $seller_id . "&start=" . $start . "&results=100"
                            . "&stcat_key=" . $pagekey;
                        curl_setopt($org_curl, CURLOPT_URL, $url);
                        curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
                        curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
                        curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

                        $response = curl_exec($org_curl);
                        $data = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                        if(array_key_exists('@attributes', $data)){
                            $attr = $data['@attributes'];
                            $total = (int)$attr['totalResultsAvailable'];
                            $total_return = (int)$attr['totalResultsReturned'];
                            $result = $data['Result'];
                            if($total_return > 0){
                                if($total_return != 1) {
                                    foreach ($result as $item){
                                        $item = (array)$item;
                                        ShopProduct::updateOrCreate(['shop_id' => $shop_id, 'item_code' => $item['ItemCode']], [
                                            'shop_id' => $shop_id,
                                            'item_code' => $item['ItemCode'],
                                        ]);
                                    }
                                }
                                else{
                                    $item = (array)$result;
                                    ShopProduct::updateOrCreate(['shop_id' => $shop_id, 'item_code' => $item['ItemCode']], [
                                        'shop_id' => $shop_id,
                                        'item_code' => $item['ItemCode'],
                                    ]);
                                }
                            }
                            if($total < $start +100){
                                ShopCategory::where('pagekey', $pagekey)->where('shop_id', $shop_id)->update(['get_status' => 2, 'total' => $total, 'start' => $start + 100]);
                            }
                            else{
                                ShopCategory::where('pagekey', $pagekey)->where('shop_id', $shop_id)->update(['get_status' => 1, 'total' => $total, 'start' => $start + 100]);
                            }
                        }

                    }
                    catch (ErrorException $e){
                        Log::error('Get Product Error' . $e);
                    }
                }
            }
        }
        return 0;
    }
}
