<?php

namespace App\Console\Commands;

use App\Models\YahooToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;

class GetToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get token';

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
        $client_id = env('YAHOO_CLIENT_ID');
        $client_secret = env('YAHOO_CLIENT_SECRET');
        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($client_id, $client_secret);
        // YConnectクライアントインスタンス生成
        $client = new YConnectClient($cred);
        // 保存していたリフレッシュトークンを指定してください
        $refresh_token = YahooToken::find(1)->refresh_token;
        // Tokenエンドポイントにリクエストしてアクセストークンを更新
        $client->refreshAccessToken($refresh_token);
        $access_token = $client->getAccessToken();
        Log::info('get token from refresh token: ' . $access_token);
        // IDトークンを検証
        YahooToken::updateOrCreate(['id' => 1], ['access_token' => $access_token]);
        return 0;
    }
}
