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
                            <h4 class="card-title mb-0">店舗一覧</h4>
                        </div>
                        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                            <div class="dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap">
                                <div class="dt-buttons">
                                    <button class="dt-button add-new btn btn-primary" tabindex="0"
                                            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modals-slide-in"><span>ストア追加</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body card-datatable table-responsive">
                    <table class="user-list-table table">
                        <thead class="table-light">
                        <tr>
                            <th>ストア名</th>
                            <th>ストアアカウント</th>
                            <th>登録日</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{$item->store_name}}</td>
                                <td>{{$item->store_account}}</td>
                                <td>{{date('Y-m-d', strtotime($item->created_at))}}</td>
                                <td>
{{--                                    <a href="" class="item-edit">--}}
{{--                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"--}}
{{--                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit font-small-4">--}}
{{--                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>--}}
{{--                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>--}}
{{--                                        </svg>--}}
{{--                                    </a>--}}
                                    <a class="item-delete" data-id="{{$item->id}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 font-small-4 me-50">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- Modal to add new user starts-->
                <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                    <div class="modal-dialog">
                        <form class="add-new-user modal-content pt-0" id="store_add">
                            @csrf
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×
                            </button>
                            <div class="modal-header mb-1">
                                <h5 class="modal-title" id="exampleModalLabel">ストア追加</h5>
                            </div>
                            <div class="modal-body flex-grow-1">
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-fullname">ストア名</label>
                                    <input type="text" class="form-control dt-full-name" placeholder="ストア名" name="store_name" required/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-uname">ストアアカウント</label>
                                    <input type="text" class="form-control dt-uname" placeholder="ストアアカウント" name="store_account" required/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="role">アプリケーション</label>
                                    <select class="form-select" id="app" name="app">
                                        @foreach($apps as $app)
                                            <option value="{{$app->id}}">{{$app->app_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary me-1 data-submit" id="saveBtn">追加</button>
                                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    キャンセル
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Modal to add new user Ends-->
            </div>
            <!-- list and filter end -->
        </section>
        <!-- users list ends -->
    </div>
    <script>
        let store_add = '{{route('store-add')}}';
        let store_delete = '{{route('store-delete')}}';
        $(document).ready(function () {
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                if($('#store_add').valid()){
                    var paramObj = new FormData($('#store_add')[0]);
                    $.ajax({
                        url: store_add,
                        type: 'post',
                        data: paramObj,
                        contentType: false,
                        processData: false,
                        success: function(response){
                            $('#modals-slide-in').modal('hide');
                            window.location.reload();
                        },
                    });
                }
            })
            $('.item-delete').click(function (e) {
                let id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                $.ajax({
                    url: store_delete,
                    type:'post',
                    data: {
                        store_id : id
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
        });
    </script>
    <!-- BEGIN: Page JS-->
{{--    <script src="{{asset('js/app-user-list.js')}}"></script>--}}
    <!-- END: Page JS-->
</x-app-layout>
