<?php use App\Product; ?>
@extends('layouts.front_layout.front_layout')
@section('content')
<div class="span9">
    <ul class="breadcrumb">
		<li><a href="index.html">Home</a> <span class="divider">/</span></li>
        <li class="active"> THANKS</li>
    </ul>
	<h3>  THANKS </h3>
    <hr class="soft"/>

    <div align="center">
        <h3>YOUR PAYMENT HAS BEEN CONFIRMED</h3>
        <p>Thank for the patment. We will proccess your order very soon</p>
        <p>Your order number is {{ Session::get('order_id') }} and grand total is Rp {{ Session::get('grand_total') }}</p>
    </div>

</div>
@endsection

<?php
Session::forget('grand_total');
Session::forget('order_id');
Session::forget('couponCode');
Session::forget('couponAmount');
?>
