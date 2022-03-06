<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\YahooToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;
use YConnect\Constant\OIDConnectDisplay;
use YConnect\Constant\OIDConnectPrompt;
use YConnect\Constant\OIDConnectScope;
use YConnect\Constant\ResponseType;
use function Symfony\Component\String\toString;

class ProductController extends Controller
{
    //
    public function storeProduct($id){
        $store_id= $id;
// リクエストとコールバック間の検証用のランダムな文字列を指定してください
        $state = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10);
        // リプレイアタック対策のランダムな文字列を指定してください
        $nonce = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10);
        return view('shop-product', compact('store_id', 'state', 'nonce'));
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function yahooAuthCode(){
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        $redirect_uri = env('YAHOO_CALLBACK');
        $state = $this->generateRandomString(35);
        // リプレイアタック対策のランダムな文字列を指定してください
        $nonce = $this->generateRandomString(60);
        $response_type = ResponseType::CODE;
        $scope = array(
            OIDConnectScope::OPENID,
            OIDConnectScope::PROFILE,
            OIDConnectScope::EMAIL,
            OIDConnectScope::ADDRESS
        );
        $display = OIDConnectDisplay::DEFAULT_DISPLAY;
        $prompt = array(
            OIDConnectPrompt::DEFAULT_PROMPT
        );

        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);
        // Authorizationエンドポイントにリクエスト
        $client->requestAuth($redirect_uri, $state, $nonce, $response_type, $scope, $display, $prompt);

        YahooToken::updateOrCreate(['id' => 1], ['state' => $state, 'nonce' => $nonce]);

        return response()->json(['status' => true]);
    }

    public function yahooCallback(Request $request){
        $code = $request->code;
        Log::info("code: " . $code);
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        $redirect_uri = env('YAHOO_CALLBACK');

        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);

        $state = YahooToken::find(1)->state;
        $nonce = YahooToken::find(1)->nonce;
        // 認可コードを取得
        $code_result = $client->getAuthorizationCode($state);

        // Tokenエンドポイントにリクエスト
        $client->requestAccessToken(
            $redirect_uri,
            $code_result
        );
        $access_token = $client->getAccessToken();
        // IDトークンを検証
        if($client->verifyIdToken($nonce)){
            YahooToken::where('id', 1)->update(['access_token' => $access_token, 'refresh_token' => $client->getRefreshToken()]);
        }
        else{
            Log::error("Token not valid");
        }
        return response()->json(['status' => true]);
    }

    public function yahooGetCategory($id){
        $store_id = $id;
        $access_token = YahooToken::find(1)->access_token;
        $org_curl = curl_init();
        $seller_id = Shop::find($id)->store_account;
        $authorization = "Authorization: Bearer " . $access_token;
        curl_setopt($org_curl, CURLOPT_URL, "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/stCategoryList?seller_id=" . $seller_id);
        curl_setopt($org_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        curl_setopt($org_curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($org_curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($org_curl);
        $err = curl_error($org_curl);
        $httpcode = curl_getinfo($org_curl, CURLINFO_HTTP_CODE);
        curl_close($org_curl);
        Log::info('Get Category response: ' . $response);
        if($err){
            Log::error('Get Category error: ' . $err);
            Log::info('httpcode: ' . $httpcode);
        }
        else{
            $data = json_decode($response, true);
            Log::info('response data: ' . $data);
        }

        $data1 = (array)simplexml_load_string($response);
        Log::info('data1: ' . $data1);
    }
}
