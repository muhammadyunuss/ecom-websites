<div class="tab-pane  active" id="blockView">
    <ul class="thumbnails">
        @foreach ($categoryProducts as $product)
        <li class="span3">
            <div class="thumbnail">
                <a href="{{ url('product/'.$product['id']) }}">
                    @if(isset($product['main_image']))
                    <?php $product_image_path = 'images/product_images/small/'.$product['main_image']; ?>
                    @else
                    <?php $product_image_path = ''; ?>
                    @endif

                    @if(!empty($product['main_image']) && file_exists($product_image_path))
                        <img style="width: 160px" src="{{ asset('images/product_images/small/'.$product['main_image']) }}" alt="">
                    @else
                        <img style="width: 160px" src="{{ asset('images/product_images/small/no-image.png') }}" alt="">
                    @endif
                </a>
                <div class="caption">
                    <h5>{{ $product['product_name'] }}</h5>
                    <p>
                        <textarea name="" id="" cols="10" rows="2" disabled >{{ $product['description'] }}</textarea>
                    </p>
                    <p>
                        {{ $product['fabric'] }}
                    </p>
                    <h4 style="text-align:center"><a class="btn" href="product_details.html"> <i class="icon-zoom-in"></i></a> <a class="btn" href="#">Add to <i class="icon-shopping-cart"></i></a> <a class="btn btn-primary" href="#">Rp. {{$product['product_price']}}</a></h4>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    <hr class="soft"/>
</div>
