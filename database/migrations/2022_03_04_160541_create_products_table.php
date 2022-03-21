<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->integer('price')->nullable();
            $table->mediumText('path')->nullable();
            $table->integer('product_category')->nullable();
            $table->integer('original_price')->nullable();
            $table->integer('sale_price')->nullable();
            $table->integer('member_price')->nullable();
            $table->mediumText('headline')->nullable();
            $table->longText('item_image_urls')->nullable();
            $table->mediumText('caption')->nullable();
            $table->mediumText('abstract')->nullable();
            $table->longText('explanation')->nullable();
            $table->mediumText('additional1')->nullable();
            $table->mediumText('additional2')->nullable();
            $table->mediumText('additional3')->nullable();
            $table->mediumText('sp_additional')->nullable();
            $table->mediumText('relevant_links')->nullable();
            $table->mediumText('cart_related_items')->nullable();
            $table->integer('ship_weight')->nullable();
            $table->integer('taxable')->nullable();
            $table->float('taxrate_type')->nullable();
            $table->date('release_date')->nullable();
            $table->dateTime('sale_period_start')->nullable();
            $table->dateTime('sale_period_end')->nullable();
            $table->integer('sale_limit')->nullable();
            $table->integer('sp_code')->nullable();
            $table->string('point_code')->nullable();
            $table->mediumText('meta_desc')->nullable();
            $table->integer('display')->nullable();
            $table->string('hidden_page')->nullable();
            $table->string('template')->nullable();
            $table->integer('brand_code')->nullable();
            $table->string('product_code')->nullable();
            $table->integer('jan')->nullable();
            $table->integer('delivery')->nullable();
            $table->integer('condition')->nullable();
            $table->mediumText('spec1')->nullable();
            $table->mediumText('spec2')->nullable();
            $table->mediumText('spec3')->nullable();
            $table->mediumText('spec4')->nullable();
            $table->mediumText('spec5')->nullable();
            $table->mediumText('spec6')->nullable();
            $table->mediumText('spec7')->nullable();
            $table->mediumText('spec8')->nullable();
            $table->mediumText('spec9')->nullable();
            $table->mediumText('spec10')->nullable();
            $table->mediumText('options')->nullable();
            $table->mediumText('inscriptions')->nullable();
            $table->mediumText('subcodes')->nullable();
            $table->mediumText('original_price_evidence')->nullable();
            $table->integer('lead_time_instock')->nullable();
            $table->integer('lead_time_outstock')->nullable();
            $table->mediumText('subcode_param')->nullable();
            $table->integer('keep_stock')->nullable();
            $table->integer('postage_set')->nullable();
            $table->mediumText('subcode_images')->nullable();
            $table->integer('supplier_type')->nullable();
            $table->integer('y_shopping_display_flag')->nullable();
            $table->integer('is_drug')->nullable();
            $table->string('auc_store_keyword')->nullable();
            $table->string('auc_pref_code')->nullable();
            $table->mediumText('auc_bcid')->nullable();
            $table->string('auc_category')->nullable();
            $table->longText('store_in_stock')->nullable();
            $table->longText('store_in_stock_few')->nullable();
            $table->longText('store_in_stockupdate')->nullable();
            $table->mediumText('pick_and_delivery_code')->nullable();
            $table->integer('pick_and_delivery_transport_rule_type')->nullable();
            $table->integer('yamato_ff_flag')->nullable();
            $table->mediumText('item_tag')->nullable();
            $table->integer('reserve_price')->nullable();
            $table->integer('reserve_sale_price')->nullable();
            $table->integer('reserve_member_price')->nullable();
            $table->dateTime('reserve_selling_period_start')->nullable();
            $table->dateTime('reserve_selling_period_end')->nullable();
            $table->integer('linegift_cooperation_flag')->nullable();
            $table->integer('linegift_item_commission')->nullable();
            $table->string('linegift_item_shortname')->nullable();
            $table->mediumText('linegift_item_image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
