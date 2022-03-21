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
{{--                                    <a href="{{route('yahoo-auth-code')}}" class="dt-button add-new btn btn-primary"><span>AuthCode取得</span></a>--}}
{{--                                    <a href="{{route('yahoo-refresh')}}" class="dt-button add-new btn btn-primary"><span>Refresh取得</span></a>--}}
                                    <a href="{{route('yahoo-get-category', $store_id)}}" class="dt-button add-new btn btn-primary"><span>カテゴリ取得</span></a>
                                    <a href="{{route('yahoo-search-product', $store_id)}}" class="dt-button add-new btn btn-primary"><span>商品取得</span></a>
                                    <a href="{{route('yahoo-product-item', $store_id)}}" class="dt-button add-new btn btn-primary"><span>商品詳細</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="search_form">
                    @csrf
                    <input type="hidden" id="store_id" name="store_id" value="{{$store_id}}">
                    <input type="hidden" name="page" value="1" id="page">
                </form>

                <div class="card-body card-datatable table-responsive" id="product-list">

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
    <link href="{{asset('')}}css/app.c3330493.css" rel="stylesheet">
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

        let product_list = '{{route('product-list')}}';
        $(document).ready(function () {
            getProduct()
        });
        $(document).on('click', '.number', function () {
            $('#page').val($(this).text());
            getProduct()
        })
        $(document).on('click', '.btn-prev', function () {
            let page = parseInt($(this).data('page')) - 1;
            $('#page').val(page);
            getProduct()
        })
        $(document).on('click', '.btn-next', function () {
            let page = parseInt($(this).data('page')) + 1;
            $('#page').val(page);
            getProduct()
        });
        function getProduct() {
            let sort = '';
            var paramObj = new FormData($('#search_form')[0]);
            paramObj.append('sort', sort);
            $.ajax({
                url: product_list,
                type: 'post',
                data: paramObj,
                contentType: false,
                processData: false,
                success: function(response){
                    $('#product-list').html(response);
                },
            });

        }
    </script>
</x-app-layout>
