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
{{--                                    <a href="{{route('yahoo-auth-code', $store_id)}}" class="dt-button add-new btn btn-primary"><span>AuthCode取得</span></a>--}}
{{--                                    <a href="{{route('yahoo-refresh')}}" class="dt-button add-new btn btn-primary"><span>Refresh取得</span></a>--}}
{{--                                    <a href="{{route('yahoo-upload-image', $store_id)}}" class="dt-button add-new btn btn-primary"><span>イメージ</span></a>--}}
{{--                                    <a href="{{route('yahoo-product-item', $store_id)}}" class="dt-button add-new btn btn-primary"><span>商品詳細</span></a>--}}
                                    <div class="d-inline-flex">
                                        <a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">
                                            <button class="dt-button buttons-collection btn btn-outline-secondary dropdown-toggle me-2"
                                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="true">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                         stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                                    </svg>一括複製
                                                </span>
                                            </button>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @foreach($stores as $shop)
                                                <a href="javascript:;" class="dropdown-item copy-all" data-id="{{$store_id}}"
                                                   data-shop="{{$shop->id}}">{{$shop->store_name}}
                                                    @if(isset($copy))
                                                        @foreach($copy as $item)
                                                            @if($item->shop_id == $shop->id)
                                                                @if($item->status == 0)
                                                                    <br>(複製中{{$item->start}}%)
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="d-inline-flex">
                                        <a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">
                                            <button class="dt-button buttons-collection btn btn-outline-secondary dropdown-toggle me-2"
                                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="true">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                         stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                                    </svg>選択複製
                                                </span>
                                            </button>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @foreach($stores as $shop)
                                                <a href="javascript:;" class="dropdown-item select-copy"
                                                   data-shop="{{$shop->id}}">{{$shop->store_name}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button data-id="{{$store_id}}" class="dt-button btn btn-primary down-product">
                                        <span>CSVダウンロード</span>
                                    </button>
                                    <button data-id="{{$store_id}}" class="dt-button add-new btn btn-primary search-product">
                                        <span>商品取得</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-7">
                            <form class="needs-validation" novalidate>
                                <div class="row g-1">
                                    <div class="col-md-6 col-12 mb-1 position-relative">
                                        <label class="form-label" for="validationTooltip01">商品コード</label>
                                        <input type="text" class="form-control" id="product-code" placeholder="商品コード" required/>
                                    </div>
                                    <div class="col-md-6 col-12 mb-1 position-relative justify-content-lg-end">
                                        <button class="btn btn-primary" type="submit" id="product-search" style="margin-top: 24px;">検索</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="col-sm-12 col-lg-5">
                            <form>
                                <div class="row g-1">
                                    <div class="col-md-6 col-12 mb-1 position-relative justify-content-lg-end">
                                        <label class="form-label" for="validationTooltip01">商品コード</label>
                                        <input type="hidden" name="csv_file"/>
                                        <button class="dt-button buttons-collection btn btn-outline-secondary dropdown-toggle me-2"
                                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="true">
                                                <span>CSV </span>
                                            </button>
                                    </div>
                                </div>
                            </form>
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
        let product_id = [];
        let product_copy = '{{route('product-copy')}}';
        let copy_all = '{{route('copy-all')}}';
        let search_product = '{{route('yahoo-search-product')}}'
        let product_delete = '{{route('product-delete')}}';
        let csv_down = '{{route('csv-down')}}';

        $(document).ready(function () {
            $('.menu_item').each(function () {
                if($(this).data('id') == $('#store_id').val()){
                    $(this).addClass('active');
                }
                else{
                    $(this).removeClass('active')
                }
            });
            $(document).on('click', '.copy-all', function () {
                let id = $(this).data('id');
                let shop_id = $(this).data('shop');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: copy_all,
                    type:'post',
                    data: {
                        current_id : id,
                        shop_id : shop_id
                    },
                    dataType: "json",
                    responseType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status == true){
                            toastr.success("一括複製を進めます。\n" +
                                "商品が多いので長い時間を要します。");
                        }
                        else{
                            toastr.warning("エラーが発生しました。");
                        }
                    },
                    error: function () {

                    }
                });
            })
            $(document).on('click', '.select-copy', function () {
                if(product_id.length > 0){
                    let shop_id = $(this).data('shop');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    });
                    $.ajax({
                        url: product_copy,
                        type:'post',
                        data: {
                            id : product_id,
                            shop_id : shop_id
                        },
                        responseType: "json",
                        success: function (response) {
                            console.log(response);
                            if(response.status == true){
                                toastr.success(response.success + "個の製品の複製に成功しました。\n" +
                                    response.copied + "個の製品を複製できませんでした。\n" +
                                    "複製できない商品は複製先の店舗にすでに存在する商品です。");
                            }
                            else{
                                toastr.warning("エラーが発生しました。");
                            }
                        },
                        error: function () {

                        }
                    }).done(function( response ) {
                        console.log(response);
                    });
                }
                else{
                    toastr.warning("商品を選択してください。");
                }

            })
            $(document).on('click', '.product-copy', function () {
                let id = $(this).data('id');
                let ids = [];
                ids.push(id);
                let shop_id = $(this).data('shop');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: product_copy,
                    type:'post',
                    data: {
                        id : ids,
                        shop_id : shop_id
                    },
                    dataType: "json",
                    responseType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status == true){
                            toastr.success(response.success + "個の製品の複製に成功しました。\n" +
                                response.copied + "個の製品を複製できませんでした。\n" +
                                "複製できない商品は複製先の店舗にすでに存在する商品です。");
                        }
                        else{
                            toastr.warning("エラーが発生しました。");
                        }
                    },
                    error: function () {

                    }
                });
            })
            $(document).on('click', '.down-product', function () {
                let id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: csv_down,
                    type:'post',
                    data: {
                        shop_id : id,
                    },
                    success: function (response) {
                        console.log(response);
                        if(response.status == true){

                        }
                        else{
                            toastr.warning("失敗しました。");
                        }
                    },
                    error: function () {

                    }
                });
            })
            $(document).on('click', '.search-product', function () {
                let id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: search_product,
                    type:'post',
                    data: {
                        shop_id : id,
                    },
                    success: function (response) {
                        console.log(response);
                        if(response.status == true){
                            toastr.success("商品情報を取得しております。\n" +
                                "ストア登録の商品が多い場合、お時間をいただくことがあります。");
                        }
                        else{
                            toastr.warning("失敗しました。");
                        }
                    },
                    error: function () {

                    }
                });
            })
            $(document).on('click', '.check-item', function () {
                let id = $(this).data('id');
                var index = product_id.indexOf(id);
                if (index !== -1) {
                    product_id.splice(index, 1);
                }
                else{
                    product_id.push(id);
                }
            })
            $(document).on('click', '.check-all', function () {
                $t = $(this);
                product_id = [];
                if($t.is(':checked')){
                    $('.check-item').each(function () {
                        let id = $(this).data('id');
                        product_id.push(id);
                        $(this).prop( "checked", true );
                    })
                }
                else{
                    $('.check-item').each(function () {
                        $(this).prop( "checked", false );
                    })
                }
                console.log(product_id);
            })
            $(document).on('click', '.item-delete', function () {
                let id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: product_delete,
                    type:'post',
                    data: {
                        id : id
                    },
                    success: function (response) {
                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": false,
                            "progressBar": false,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                        if(response.status == true){
                            toastr.success("成功しました。");
                            window.location.reload()
                        }
                        else {
                            toastr.warning("失敗しました。");
                        }
                    },
                    error: function () {

                    }
                });
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
        $(document).on('click', '#product-search', function (e) {
            e.preventDefault();
            if($('#product-code').val()){
                getProduct();
            }
        })
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
            paramObj.append('product_code', $('#product-code').val())
            $.ajax({
                url: product_list,
                type: 'post',
                data: paramObj,
                contentType: false,
                processData: false,
                success: function(response){
                    $('#product-list').html(response);
                    product_id = [];
                },
            });

        }
    </script>
</x-app-layout>
