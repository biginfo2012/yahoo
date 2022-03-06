<x-app-layout>
    <div class="content-header row">
    </div>
    <div class="content-body"><!-- users list start -->
        <section class="app-user-list">
            <!-- list and filter start -->
            <div class="card">
                <div class="card-body border-bottom">
                    <div class="d-flex justify-content-between align-items-center header-actions mx-2 row mt-75">
                        <div class="col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                            <h4 class="card-title mb-0">商品一覧</h4>
                        </div>
                        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                            <div class="dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap">
                                <div class="dt-buttons position-relative">
                                    <input id="state" type="hidden" value="{{$state}}">
                                    <input id="nonce" type="hidden" value="{{$nonce}}">
{{--                                    <span class="yconnectLogin position-absolute" style="left: -155px;"></span>--}}
                                    <button class="dt-button add-new btn btn-primary"><span>AuthCode取得</span></button>
                                    <button class="dt-button add-new btn btn-primary"><span>トークン取得</span></button>
                                    <button class="dt-button add-new btn btn-primary"><span>カテゴリ取得</span></button>
                                    <button class="dt-button add-new btn btn-primary"><span>商品取得</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="store_id" value="{{$store_id}}">
                <div class="card-body card-datatable table-responsive">
                    <table class="user-list-table table">
                        <thead class="table-light">
                        <tr>
                            <th>選択</th>
                            <th>商品コード</th>
                            <th>商品名</th>
                            <th>カテゴリ</th>
                            <th>ステータス</th>
                            <th>更新日</th>
                        </tr>
                        </thead>
                        <tbody>
{{--                        @foreach($data as $item)--}}
                            <tr>
                                <td class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 15px;" data-col="1" aria-label="">
                                    <div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                        <label class="form-check-label" for="checkboxSelectAll"></label>
                                    </div>
                                </td>
                                <td>商品コード</td>
                                <td>商品名</td>
                                <td>カテゴリ</td>
                                <td>ステータス</td>
                                <td>更新日</td>
                            </tr>
{{--                        @endforeach--}}
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- list and filter end -->
        </section>
        <!-- users list ends -->
    </div>
    <script>
        $(document).ready(function () {
            $('.menu_item').each(function () {
                if($(this).data('id') == $('#store_id').val()){
                    $(this).addClass('active');
                }
                else{
                    $(this).removeClass('active')
                }
            })
        })
    </script>

    <script type="text/javascript">
        let client_id = "{{env('YAHOO_CLIENT_ID')}}";
        let redirectUrl = "{{env('YAHOO_CALLBACK')}}"
        window.yconnectInit = function() {
            YAHOO.JP.yconnect.Authorization.init({
                button: {    // ボタンに関しては下記URLを参考に設定してください
                    // https://developer.yahoo.co.jp/yconnect/loginbuttons.html
                    format: "image",
                    type: "a",
                    textType: "a",
                    width: 150,
                    height: 38,
                    className: "yconnectLogin"
                },
                authorization: {
                    clientId: client_id,    // 登録したClient IDを入力してください
                    redirectUri: redirectUrl, // 本スクリプトを埋め込むページのURLを入力してください
                    scope: "openid email",
                    // Authorization Codeフローの場合はresponseType, state, nonceパラメーターは必須です,
                    responseType: "code",
                    state: $('#state').val(),
                    nonce: $('#nonce').val(),
                    windowWidth: "500",
                    windowHeight: "400"
                },
                onError: function(res) {
                    // エラー発生時のコールバック関数
                },
                onCancel: function(res) {
                    // 同意キャンセルされた時のコールバック関数
                }
            });
        };
        (function(){
            var fs = document.getElementsByTagName("script")[0], s = document.createElement("script");
            s.setAttribute("src", "https://s.yimg.jp/images/login/yconnect/auth/2.0.4/auth-min.js");
            fs.parentNode.insertBefore(s, fs);
        })();
    </script>
</x-app-layout>
