<table class="product-list-table table">
    <thead class="table-light">
    <tr>
        <th class="px-2" style="padding-top: 18px; padding-left: 28px !important;">
            <div class="form-check"> <input class="form-check-input check-all" type="checkbox" value="">
                <label class="form-check-label" for="checkboxSelectAll"></label>
            </div>
        </th>
        <th style="padding-top: 18px">商品コード</th>
        <th style="padding-top: 18px">商品名</th>
        <th style="padding-top: 18px">価格</th>
        <th style="padding-top: 18px">取得日</th>
        <th style="padding-top: 18px"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 15px;" data-col="1" aria-label="">
                <div class="form-check"> <input class="form-check-input check-item" data-id="{{$item->id}}" type="checkbox" value="">
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </td>
            <td>{{$item->item_code}}</td>
            <td>{{isset($item->product) ? $item->product->name : ''}}</td>
            <td>{{isset($item->product) ?$item->product->price : ''}}</td>
            <td>{{date('Y-m-d', strtotime($item->created_at))}}</td>
            <td>
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
                                </svg>複製
                            </span>
                        </button>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        @foreach($stores as $shop)
                            <a href="javascript:;" class="dropdown-item product-copy" data-shop="{{$shop->id}}" data-id="{{$item->id}}">{{$shop->store_name}}</a>
                        @endforeach
                    </div>
                </div>
                <a class="item-delete" data-id="{{$item->id}}">
                    <button class="dt-button buttons-collection btn btn-outline-secondary dropdown-toggle me-2"
                            tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="true">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 font-small-4 me-50">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>削除
                        </span>
                    </button>

                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<span data-v-035982d7="" class="">
    <div data-v-a70b1948="" data-v-035982d7="" class="pagination-container pagination-center text-center m-3"><!---->
        <div data-v-a70b1948="" class="el-pagination is-background">
            <button type="button" {{$page == 1 ? 'disabled' : ''}} class="btn-prev" data-page="{{$page}}"><span>前へ</span></button>
            <ul class="el-pager">
                @if($page_count > 9 && $page> 5)
                    <li class="el-icon more btn-quicknext el-icon-more">...</li>
                    <li class="number d-none">{{$total}}</li>
                @endif

                @for($i = ($page > 4 ? $page - 4 : 1); $i <= ($page_count < $page+4 ? $page_count : $page + 4); $i++)
                    <li class="number {{$i == $page ? 'active' : ''}}">{{$i}}</li>
                @endfor
                @if($page_count > 9 && $page < $page_count - 4)
                    <li class="el-icon more btn-quicknext el-icon-more">...</li>
                    <li class="number d-none">{{$total}}</li>
                @endif
            </ul>
            <button type="button" {{$page == $page_count ? 'disabled' : ''}} class="btn-next" data-page="{{$page}}"><span>次へ</span></button>
        </div>
    </div>
</span><!---->
