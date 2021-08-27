@extends('layouts.front_layout.front_layout')
@section('content')
<div class="span9">
    <ul class="breadcrumb">
		<li><a href="index.html">Home</a> <span class="divider">/</span></li>
		<li class="active">Order</li>
    </ul>
	<h3> Orders</h3>
	<hr class="soft"/>
	<div class="row">
		<div class="span8">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Order ID</th>
                    <th>Order Products</th>
                    <th>Payment Method</th>
                    <th>Grandt Total</th>
                    <th>Created on</th>
                    <th>Action</th>
                </tr>
                @foreach ($orders as $order)
                <tr>
                    <tr>
                        <td>{{ $order['id'] }}</td>
                        <td>
                            @foreach ($order['orders_products'] as $pro)
                                {{ $pro['product_code'] }}<br>
                            @endforeach
                        </td>
                        <td>{{ $order['payment_method'] }}</td>
                        <td>{{ $order['grand_total'] }}</td>
                        <td>{{ date('d-m-Y',strtotime($order['created_at'])) }}</td>
                        <td>
                            <a style="text-decoration: underline;" href="{{ url('orders/'.$order['id']) }}">View Detail</a>
                        </td>
                    </tr>
                </tr>
                    @endforeach
            </table>
		</div>
	</div>
</div>
@endsection
