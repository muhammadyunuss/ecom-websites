@extends('layouts.admin_layout.admin_layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Catalogues</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Coupons</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 10px;">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 10px;">
        {{ Session::get('success_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <form name="couponForm" id="couponForm" @if(empty($coupon['id'])) action="{{ url('admin/add-edit-coupon') }}" @else action="{{ url('admin/add-edit-coupon/'.$coupon['id']) }}" @endif method="post" enctype="multipart/form-data">@csrf
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
              <div class="row">
                  <div class="col-md-6">
                        @if(empty($coupon['coupon_code']))
                            <div class="form-group">
                                <label for="coupon_option">Coupon Option</label><br>
                                <span><input id="AutomaticCoupon" type="radio" name="coupon_option" value="Automatic" checked> Automatic</span><br>
                                <span><input id="ManualCoupon" type="radio" name="coupon_option" value="Manual"> Manual</span>
                            </div>
                            <div class="form-group" style="display: none;" id="couponField">
                                <label for="coupon_code">Coupon Code</label>
                                <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Enter Coupon Code">
                            </div>
                        @else
                            <input type="hidden" name="coupon_option" value="{{ $coupon['coupon_option'] }}">
                            <div class="form-group">
                                <label for="coupon_code">Coupon Code</label>
                                <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Enter Coupon Code" value="{{ $coupon['coupon_code'] }}" readonly="">
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="coupon_option">Coupon Type</label><br>
                            <span><input type="radio" name="coupon_type" value="Single Times" @if(isset($coupon['coupon_type']) && $coupon['coupon_type']=="Single Times") checked="" @elseif(!isset($coupon['coupon_type'])) checked="" @endif> Single Times</span><br>
                            <span><input type="radio" name="coupon_type" value="Multiple Times" @if(isset($coupon['coupon_type']) && $coupon['coupon_type']=="Multiple Times") checked="" @endif> Multiple Times</span>
                        </div>
                        <div class="form-group">
                            <label for="coupon_option">Amount Type</label><br>
                            <span><input type="radio" name="amount_type" value="Percentage" @if(isset($coupon['amount_type']) && $coupon['amount_type']=="Percentage") checked="" @elseif(!isset($coupon['amount_type'])) checked="" @endif> Percentage (in %)</span><br>
                            <span><input type="radio" name="amount_type" value="Fixed" @if(isset($coupon['amount_type']) && $coupon['amount_type']=="Fixed") checked="" @endif> Fixed (in Rupiah)</span>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount" @if(!empty($coupon['amount'])) value="{{ $coupon['amount'] }}"@endif>
                        </div>
                        <div class="form-group">
                            <label>Select Category</label>
                            <select name="categories[]" class="form-control select2" multiple="" style="width: 100%;">
                                <option value="">Select Section</option>
                                @foreach($categories as $section)
                                <optgroup label="{{ $section['name'] }}"></optgroup>
                                @foreach($section['categories'] as $category)
                                <option value="{{$category['id']}}" @if(in_array($category['id'],$selCats)) selected="" @endif>&nbsp;-- {{$category['category_name']}}</option>
                                @foreach($category['subcategories'] as $subcategory)
                                <option value="{{$subcategory['id']}}" @if(in_array($subcategory['id'],$selCats)) selected="" @endif>&nbsp;&nbsp;--> {{$subcategory['category_name']}}</option>
                                @endforeach
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select User</label>
                            <select name="users[]" class="form-control select2" multiple="" style="width: 100%;">
                                <option value="">Select</option>
                                @foreach($users as $user)
                                <option value="{{ $user['email'] }}" @if(in_array($user['email'],$selUsers)) selected="" @endif>{{ $user['email'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date masks:</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                              </div>
                              <input type="text" name="expiry_date" id="expiry_date" placeholder="Enter Expiry Date" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask @if(!empty($coupon['expiry_date'])) value="{{ $coupon['expiry_date'] }}"@endif>
                            </div>
                            <!-- /.input group -->
                          </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

@endsection
