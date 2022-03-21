<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\YahooCategory;
use App\Models\YahooToken;
use Illuminate\Console\Command;

class GetCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get category';

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
        $store_id = 2;
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;

        $seller_id = Shop::find(2)->store_account;
        $cateories = YahooCategory::where('store_id', 2)->where('is_leaf', 0)->whereNull('status')->orderBy('created_at', 'desc')->pluck('category_code')->toArray();
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
                    YahooCategory::updateOrCreate(['store_id' => 2, 'category_code' => (string)$item['CategoryCode']], [
                        'store_id' => 2,
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
                YahooCategory::updateOrCreate(['store_id' => 2, 'category_code' => (string)$item['CategoryCode']], [
                    'store_id' => 2,
                    'category_code' => (string)$item['CategoryCode'],
                    'category_name' => (string)$item['CategoryName'],
                    'display' => $item['Display'],
                    'is_leaf' => $item['IsLeaf'],
                    'update_date' => date('Y-m-d', strtotime($item['UpdateDate']))
                ]);
            }
            YahooCategory::where('category_code', $iValue)->where('store_id', $store_id)->update(['status' => 1]);
        }
    }
}
