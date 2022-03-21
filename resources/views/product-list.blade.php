<table class="product-list-table table">
    <thead class="table-light">
    <tr>
        <th class="px-2" style="padding-top: 18px">選択</th>
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
                <div class="form-check"> <input class="form-check-input" type="checkbox" value="">
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </td>
            <td>{{$item->item_code}}</td>
            <td>{{$item->product->name}}</td>
            <td>{{$item->product->price}}</td>
            <td>{{date('Y-m-d', strtotime($item->created_at))}}</td>
            <td>

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
                    <li class="el-icon more btn-quicknext el-icon-more"></li>
                    <li class="number d-none">{{$total}}</li>
                @endif

                @for($i = ($page > 4 ? $page - 4 : 1); $i <= ($page_count < $page+4 ? $page_count : $page + 4); $i++)
                    <li class="number {{$i == $page ? 'active' : ''}}">{{$i}}</li>
                @endfor
                @if($page_count > 9 && $page < $page_count - 4)
                    <li class="el-icon more btn-quicknext el-icon-more"></li>
                    <li class="number d-none">{{$total}}</li>
                @endif
            </ul>
            <button type="button" {{$page == $page_count ? 'disabled' : ''}} class="btn-next" data-page="{{$page}}"><span>次へ</span></button>
        </div>
    </div>
</span><!---->
