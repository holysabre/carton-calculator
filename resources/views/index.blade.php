@extends('layouts.app')
@section('title', '首页')

@section('content')
<form action="">

    <div class="container-md">
        <div class="container-md">
            <label for="" class="form-label">请选择产品：</label>
            <div class="input-group">
                @foreach ($products as $product)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="product-id" value="{{ $product->id }}" id="product-id-{{ $product->id }}">
                    <label class="form-check-label" for="product-id-{{ $product->id }}">
                        {{ $product->name }}
                        <!-- @isset ($product->tiered_prices)
                        <dl>
                            @foreach ($product->tiered_prices as $price)
                            <dd>大于 {{$price->gt}},小于等于 {{$price->elt}} ￥{{$price->price}}</dd>
                            @endforeach
                        </dl>
                        @endisset -->
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="container-md">
            <label for="" class="form-label">请选择材质：</label>
            <div class="input-group">
                @foreach ($sku_attribute->attr_value as $attr)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="product-attr" value="{{ $attr }}" id="product-id-{{ $attr}}">
                    <label class="form-check-label" for="product-id-{{ $attr }}">
                        {{ $attr }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label for="" class="form-label">请填写需要的尺寸：</label>
            <div class="input-group mb-3">
                <span class="input-group-text">长</span>
                <input type="text" class="form-control" name="length" aria-label="length" value="0" onfocus="javascript:value=''">
                <span class="input-group-text">宽</span>
                <input type="text" class="form-control" name="width" aria-label="width" value="0" onfocus="javascript:value=''">
                <span class="input-group-text">高</span>
                <input type="text" class="form-control" name="height" aria-label="height" value="0" onfocus="javascript:value=''">
            </div>
            <div class="form-text">单位：厘米</div>
        </div>

        <div class="mb-3">
            <label for="" class="form-label">请填写需要的信息：</label>
            <div class="input-group mb-3">
                <span class="input-group-text">数量</span>
                <input type="text" class="form-control" name="qty" aria-label="qty" value="0" onfocus="javascript:value=''">
                <span class="input-group-text">利润率</span>
                <input type="text" class="form-control" name="profit" aria-label="profit" value="0" onfocus="javascript:value=''">
            </div>
        </div>

        <div class="input-group mb-3">
            <button type="button" class="btn btn-primary" onclick="calc()">开始计算</button>
        </div>

        <hr>

        <div class="input-group mb-3">
            <ul class="list-group">
                <li class="list-group-item">结果</li>
                <li class="list-group-item">纸价：￥ <span id="per_price">0</span></li>
                <li class="list-group-item">方数：<span id="total_square">0</span> 平方米</li>
                <li class="list-group-item">重量：<span id="total_weight">0</span> 千克</li>
            </ul>
            <ul class="list-group">
                <li class="list-group-item">增加利润后的结果</li>
                <li class="list-group-item">纸价：￥ <span id="per_price_by_profit">0</span></li>
            </ul>
        </div>
    </div>

</form>

@stop

<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
@section('javascript')
<script>
    var id = 0,
        attr = '',
        length = 0,
        width = 0,
        height = 0,
        qty = 0,
        profit = 0,
        total_price = 0,
        total_square = 0,
        total_weight = 0;

    $(function() {
        $('input[name="product-id"]').on('change', function() {
            calc()
        })
    })

    function calc() {
        getData()

        if (length == 0 || width == 0 || height == 0 || qty == 0) {
            return
        }

        getPrice()
    }

    function getPrice() {
        $.ajax({
            url: '/index/' + id + '/calc_price',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                attr: attr,
                length: length,
                width: width,
                height: height,
                qty: qty,
                profit: profit,
            },
            success: function(resp) {
                if (resp.status == false) {
                    alert(resp.error)
                }
                $('#total_square').text(resp.total_square)
                $('#total_weight').text(resp.total_weight)
                $('#total_price').text(resp.total_price)
                $('#per_price').text(resp.per_price)
                $('#per_price_by_profit').text(resp.per_price_by_profit)
            },
            error: function(xhr, status, error) {
                console.log(error)
            }
        })

    }

    function getData() {
        var selected_product = $('input[name="product-id"]:checked')
        id = selected_product.val()
        attr = $('input[name="product-attr"]:checked').val()
        length = $('input[name="length"]').val()
        width = $('input[name="width"]').val()
        height = $('input[name="height"]').val()
        qty = $('input[name="qty"]').val()
        profit = $('input[name="profit"]').val()
    }
</script>
@stop