<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\YahooApp;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    //
    public function storeManage(){
        $data = Shop::all();
        $stores = $data;
        $apps = YahooApp::all();
        return view('shop-manage', compact('data', 'stores', 'apps'));
    }

    public function storeAdd(Request $request){
        Shop::create(['store_name' => $request->store_name, 'store_account' => $request->store_account]);
        return response()->json(['status' => true]);
    }
}
