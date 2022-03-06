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
                                            data-bs-target="#modals-slide-in"><span>ストア追加</span></button>
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
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{$item->store_name}}</td>
                                <td>{{$item->store_account}}</td>
                                <td>{{date('Y-m-d', strtotime($item->created_at))}}</td>
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
    </script>
    <!-- BEGIN: Page JS-->
{{--    <script src="{{asset('js/app-user-list.js')}}"></script>--}}
    <!-- END: Page JS-->
</x-app-layout>
