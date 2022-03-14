<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'price', 'path', 'product_category', 'original_price', 'sale_price', 'member_price', 'headline', 'item_image_urls', 'caption',
        'abstract', 'explanation', 'additional1', 'additional2', 'additional3', 'sp_additional', 'relevant_links', 'cart_related_items', 'ship_weight',
        'taxable', 'taxrate_type', 'release_date', 'sale_period_start', 'sale_period_end', 'sale_limit', 'sp_code', 'point_code', 'meta_desc', 'display',
        'hidden_page', 'template', 'brand_code', 'product_code', 'jan', 'delivery', 'condition', 'spec1', 'spec2', 'spec3', 'spec4', 'spec5', 'spec6', 'spec7',
        'spec8', 'spec9', 'spec10', 'options', 'inscriptions', 'subcodes', 'original_price_evidence', 'lead_time_instock', 'lead_time_outstock', 'subcode_param',
        'keep_stock', 'postage_set', 'subcode_images', 'supplier_type', 'y_shopping_display_flag', 'is_drug', 'auc_store_keyword', 'auc_pref_code', 'auc_bcid',
        'auc_category', 'store_in_stock', 'store_in_stock_few', 'store_in_stockupdate', 'pick_and_delivery_code', 'pick_and_delivery_transport_rule_type',
        'yamato_ff_flag', 'item_tag', 'reserve_price', 'reserve_sale_price', 'reserve_member_price', 'reserve_selling_period_start', 'reserve_selling_period_end',
        'linegift_cooperation_flag', 'linegift_item_commission', 'linegift_item_shortname', 'linegift_item_image_url'
    ];
}
