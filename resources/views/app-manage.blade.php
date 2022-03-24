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
                            <h4 class="card-title mb-0">アプリケーション一覧</h4>
                        </div>
                        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                            <div class="dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap">
                                <div class="dt-buttons">
                                    <button class="dt-button add-new btn btn-primary" tabindex="0"
                                            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modals-slide-in"><span>アプリケーション追加</span>
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
                            <th>アプリケーション名</th>
                            <th>Client ID</th>
                            <th>シークレット</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{$item->app_name}}</td>
                                <td>{{$item->client_id}}</td>
                                <td>{{$item->client_secret}}</td>
                                <td>
                                    <a href="{{route('yahoo-auth-code', $item->id)}}" class="dt-button add-new btn btn-primary"><span>AuthCode取得</span></a>
                                    <a class="item-delete dt-button add-new btn btn-primary" data-id="{{$item->id}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 font-small-4 me-50">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        削除
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
                                <h5 class="modal-title" id="exampleModalLabel">アプリケーション追加</h5>
                            </div>
                            <div class="modal-body flex-grow-1">
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-fullname">アプリケーション名</label>
                                    <input type="text" class="form-control dt-full-name" placeholder="アプリケーション名" name="app_name" required/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-uname">Client ID</label>
                                    <input type="text" class="form-control dt-uname" placeholder="Client ID" name="client_id" required/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-uname">シークレット</label>
                                    <input type="text" class="form-control dt-uname" placeholder="シークレット" name="client_secret" required/>
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
        let app_add = '{{route('app-add')}}';
        let app_delete = '{{route('app-delete')}}';
        $(document).ready(function () {
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                if($('#store_add').valid()){
                    var paramObj = new FormData($('#store_add')[0]);
                    $.ajax({
                        url: app_add,
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
                    url: app_delete,
                    type:'post',
                    data: {
                        app_id : id
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
