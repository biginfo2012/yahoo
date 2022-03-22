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
        $id= 2;
        $access_token = YahooToken::find(1)->access_token;
        $authorization = "Authorization: Bearer " . $access_token;
        $seller_id = Shop::find($id)->store_account;
        $products = ShopProduct::where('shop_id', $id)->whereNull('status')->orderBy('id', 'asc')->take(100)->get();
        foreach ($products as $product){
            try {
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
            catch (\ErrorException $e){
               Log::error('Get Product Detail Error');
            }
        }
        return 0;
    }
}
