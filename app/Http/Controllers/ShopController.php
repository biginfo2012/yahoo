<?php

namespace App\Http\Controllers;

use App\Models\ProductCopy;
use App\Models\Shop;
use App\Models\YahooApp;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    //
    public function appManage(){
        $data = YahooApp::all();
        return view('app-manage', compact('data'));
    }

    public function appAdd(Request $request){
        YahooApp::create(['app_name' => $request->app_name, 'client_id' => $request->client_id, 'client_secret' => $request->client_secret]);
        return response()->json(['status' => true]);
    }

    public function appDelete(Request $request){
        YahooApp::where('id', $request->app_id)->delete();
        return response()->json(['status' => true]);
    }

    public function storeManage(){
        $data = Shop::with('app')->get()->all();
        $stores = $data;
        $apps = YahooApp::all();
        return view('shop-manage', compact('data', 'stores', 'apps'));
    }

    public function storeAdd(Request $request){
        if(isset($request->shop_id)){
            Shop::where('id', $request->shop_id)->update(['store_name' => $request->store_name, 'store_account' => $request->store_account, 'app_id' => $request->app, 'prefix' => $request->prefix]);
        }
        else{
            Shop::create(['store_name' => $request->store_name, 'store_account' => $request->store_account, 'app_id' => $request->app, 'prefix' => $request->prefix]);
        }
        return response()->json(['status' => true]);
    }

    public function storeDelete(Request $request){
        Shop::where('id', $request->store_id)->delete();
        return response()->json(['status' => true]);
    }
}
