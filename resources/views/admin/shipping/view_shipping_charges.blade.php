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
            <li class="breadcrumb-item active">Shipping Charges</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 10px;">
          {{ Session::get('success_message') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Orders</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="orders" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Country</th>
                  <th>0-500g</th>
                  <th>501-1000g</th>
                  <th>1001-2000g</th>
                  <th>2001-5000g</th>
                  <th>Above 5000g</th>
                  <th>Status</th>
                  <th>Updated At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($shipping_charges as $shipping)
                @if(!isset($order->parentorder->order_name))
                <?php $parent_order = "Root"; ?>
                @else
                <?php $parent_order = $order->parentorder->order_name; ?>
                @endif
                <tr>
                    <td>{{ $shipping['id'] }}</td>
                    <td>{{ $shipping['country'] }}</td>
                    <td>Rp. {{ $shipping['0_500g'] }}</td>
                    <td>Rp. {{ $shipping['501_1000g'] }}</td>
                    <td>Rp. {{ $shipping['1001_2000g'] }}</td>
                    <td>Rp. {{ $shipping['2001_5000g'] }}</td>
                    <td>Rp. {{ $shipping['above_5000g'] }}</td>
                    <td>@if($shipping['status']==1)
                        <a class="updateShippingStatus" id="shipping-{{ $shipping['id'] }}" shipping_id="{{ $shipping['id'] }}" href="javascript:void(0)"><i class="fas fa-toggle-on" aria-hidden="true" style="color:#51cf66;" status="Active"></i></a>
                        @else
                        <a class="updateShippingStatus" id="shipping-{{ $shipping['id'] }}" shipping_id="{{ $shipping['id'] }}" href="javascript:void(0)"><i class="fas fa-toggle-off" aria-hidden="true" style="color:#ff6b6b;" status="Inactive"></i></a>
                        @endif
                      </td>
                      <td>{{ date('d-m-Y',strtotime($shipping['created_at'])) }}</td>
                      <td>
                        <a title="Update Shipping Charges" href="{{ url('admin/edit-shipping-charges/'.$shipping['id']) }}"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;
                      </td>
                </tr>
                @endforeach

              </tbody>

            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>

@endsection
