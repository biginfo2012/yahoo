<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCopy;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\YahooToken;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetProductCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-product-copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Product Copy';

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
        $items = ProductCopy::where('status', 0)->orderBy('id', 'asc')->get();
        foreach ($items as $item){
            $copy_id = $item->copy_id;
            $shop_id = $item->shop_id;
            $start = $item->start;
            $copied = 0;
            $success = 0;
            $ids = ShopProduct::where('shop_id', $copy_id)->orderBy('id', 'asc')->offset(($start - 1) * 10)->limit(10)->get();
            foreach ($ids as $id) {
                $shop_product = ShopProduct::find($id->id);
                $item_code = $shop_product->item_code;
                $product_id = $shop_product->product_id;
                $codes = explode('-', $item_code);
                if(count($codes) == 2){
                    $code = $codes[1];
                }
                else{
                    $code = $codes[0];
                }
                $prefix = Shop::find($shop_id)->prefix;
                $copy_code = $prefix . '-' . $code;
                $is_ex = ShopProduct::where('item_code', $copy_code)->first();
                Log::info('Get Product Copy copy_code '.$copy_code);
                if(isset($is_ex)){
                    $copied++;
                }
                else{
                    $product = Product::find($product_id);
                    $shop = Shop::with('app')->find($shop_id);
                    $seller_id = $shop->store_account;
                    $app_id = $shop->app->id;
                    $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
                    $authorization = "Authorization: Bearer " . $access_token;

                    try {
                        $url = "seller_id=" . $seller_id . "&path=" . $product->path . "&item_code=" . $copy_code
                            . "&name=" . $product->name . "&price=" . $product->price . "&product_category=" . $product->product_category . "&headline=" . $product->headline
                            . "&item_image_urls=" .$product->item_image_urls . "&caption=" . $product->caption . "&explanation=" . $product->explanation . "&taxable=" . $product->taxable
                            . "&taxrate_type=" . $product->taxrate_type . "&display=" . $product->display . "&delivery=" . $product->delivery
                            . "&lead_time_instock=" . $product->lead_time_instock . "&keep_stock=" .$product->keep_stock . "&postage_set=" . $product->postage_set;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,"https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/editItem");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', $authorization));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if($httpCode == 200){
                            ShopProduct::create(['shop_id' => $shop_id, 'product_id' => $product_id, 'item_code' => $copy_code, 'status' => 1]);
                            $success++;
                        }
                        else{
                            $copied++;
                        }
                    }
                    catch (ErrorException $e){
                        Log::error("Copy Item Error: " . $e);
                        $copied++;
                    }
                }
            }
            $count = ShopProduct::where('shop_id', $copy_id)->pluck('id')->count();
            Log::info('Get Product Copy count '.$count);
            if($count < $start * 10){
                $start = $start + 1;
                ProductCopy::where('id', $item->id)->update(['start' => $start, 'status' => 1]);
            }
            else{
                $start = $start + 1;
                ProductCopy::where('id', $item->id)->update(['start' => $start]);
            }
            Log::info('Product Copy: copied: ' . $copied . ' success : ' .$success);
        }
        return 0;
    }
}
