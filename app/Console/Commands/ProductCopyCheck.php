<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCopy;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\YahooToken;
use CURLFile;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductCopyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-copy-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Copy Check';

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
            $error = 0;
            $now = date('s');
            while($now < 52){
                $ids = ShopProduct::where('shop_id', $copy_id)->orderBy('id', 'desc')->skip($start)->take(1)->get();
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
                    Log::info('Get Product Copy Check copy_code '.$copy_code);
                    if(!empty($is_ex)){
                        $copied++;
                        $start++;
                        //$this->yahooUploadImage($copy_code, $shop_id);
                    }
                    else{
                        $product = Product::find($product_id);
                        $shop = Shop::with('app')->find($shop_id);
                        $seller_id = $shop->store_account;
                        $app_id = $shop->app->id;
                        $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
                        $authorization = "Authorization: Bearer " . $access_token;
                        usleep(500000);
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
                            $res = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            if($httpCode == 200){
                                ShopProduct::create(['shop_id' => $shop_id, 'product_id' => $product_id, 'item_code' => $copy_code, 'status' => 1]);
                                $this->yahooUploadImage($copy_code, $shop_id);
                                $success++;
                                $start++;
                            }
                            else{
                                $error++;
                                Log::info("Copy Item Res: " . $res);
                            }
                        }
                        catch (ErrorException $e){
                            Log::error("Copy Item Error: " . $e);
                            $error++;
                        }
                    }
                }
                $now = date('s');

            }

            if(count($ids) == 0){
                ProductCopy::where('id', $item->id)->update(['start' => $start, 'status' => 1]);
            }
            else{
                ProductCopy::where('id', $item->id)->update(['start' => $start]);
            }
            Log::info('Product Copy Check: copied: ' . $copied . ' success : ' .$success . ' error : ' . $error);
        }
        return 0;
    }

    public function yahooUploadImage($copy_code, $shop_id){
        $shop = Shop::with('app')->find($shop_id);
        $app_id = $shop->app->id;
        $access_token = YahooToken::where('app_id', $app_id)->first()->access_token;
        $seller_id = $shop->store_account;

        $shop_product = ShopProduct::where('item_code', $copy_code)->get()->first();
        $product = Product::find($shop_product->product_id);
        $images = explode(';', $product->item_image_urls);

        foreach ($images as $index => $image){
            if($index < 6){
                $names = explode('/', $image);
                $origin_name = $names[count($names)-1] . ".jpg";
                $contents = file_get_contents(str_replace('/b/', '/n/', $image));
                Storage::disk('local')->put($origin_name, $contents);
                $path = storage_path('app') . '/' . $origin_name;
                $mime = mime_content_type($path);
                $header = [
                    'Content-Type: multipart/form-data',
                    'POST /ShoppingWebService/V1/uploadItemImage?seller_id=' . $seller_id .' HTTP/1.1',
                    'Host: circus.shopping.yahooapis.jp',
                    'Authorization: Bearer ' . $access_token,
                ];
                if($index == 0){
                    $file_name = $copy_code . ".jpg";
                }
                else{
                    $file_name = $copy_code . "_" . $index . ".jpg";
                }
                //Log::info("Item Image Copy: " . $file_name);
                $url   = 'https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/uploadItemImage?seller_id=' .$seller_id;
                $param = array('file' => new CURLFile($path, $mime, $file_name));

                // 必要に応じてオプションを追加してください。
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
                curl_setopt($ch, CURLOPT_URL,            $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST,           true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,     $param);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($httpCode != 200){
                    Log::info('Item Image Upload Error: ' . $response);
                }
                //sleep(1);
                //
            }
        }
    }
}
