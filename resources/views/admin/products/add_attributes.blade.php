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
            <li class="breadcrumb-item"><a href="#">Products Attributes</a></li>
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
      @if(Session::has('error_message'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin-top: 10px;">
        {{ Session::get('error_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <form name="addAttributeForm" id="addAttributeForm" method="post" action="{{url('admin/add-attributes/'.$productdata['id'])}}">@csrf
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
                <div class="form-group">
                  <label for="product_name">Product Name: {{ $productdata['product_name'] }}</label>
                </div>
                <div class="form-group">
                  <label for="product_price">Product Price: {{ $productdata['product_price'] }}</label>
                </div>
                <div class="form-group">
                    <label for="product_code">Product Code: {{ $productdata['product_code'] }}</label>
                  </div>
                  <div class="form-group">
                    <label for="product_color">Product Color: {{ $productdata['product_color'] }}</label>
                  </div>
                <!-- /.form-group -->
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <img style="width:130px; margin-top: 5px;" src="{{ asset('images/product_images/small/'.$productdata['main_image']) }}" alt="">
                </div>
              </div>
              <div class="form-group">
                <div class="field_wrapper">
                    <div>
                        <input id="size" name="size[]" type="text" placeholder="Size" style="width: 120px;"/>
                        <input id="sku" name="sku[]" type="text" placeholder="SKU" style="width: 120px;"/>
                        <input id="price" name="price[]" type="text" placeholder="Price" style="width: 120px;"/>
                        <input id="stock" name="stock[]" type="text" placeholder="Stock" style="width: 120px;"/>
                        <a href="javascript:void(0);" class="add_button" title="Add field">&nbsp<i class="fas fa-plus"></i></a>
                    </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>

      <form name="editAttributeForm" id="editAttributeForm" method="post" action="{{ url('admin/edit-attributes/'.$productdata['id']) }}">@csrf
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Added Product Attributes</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="products" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Size</th>
                  <th>SKU</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($productdata['attributes'] as $attribute)
                <input style="display: none" type="text" name="attrId[]" value="{{ $attribute['id'] }}" required>
                <tr>
                  <td>{{ $attribute['id'] }}</td>
                  <td>{{ $attribute['size'] }}</td>
                  <td>{{ $attribute['sku'] }}</td>
                  <td><input class="form-control form-control-sm" type="number" name="price[]" value="{{ $attribute['price'] }}" required></td>
                  <td><input class="form-control form-control-sm" type="number" name="stock[]" value="{{ $attribute['stock'] }}" required></td>
                  <td>
                    @if($attribute['status']==1)
                    <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)">Active</a>
                    @else
                    <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)">Inactive</a>
                    @endif
                    &nbsp; &nbsp;
                    <a title="Delete Attribute" class="confirmDelete" record="attribute" recordid="{{ $attribute['id'] }}" href="javascript:void(0)"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                @endforeach

              </tbody>

            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Attrtibutes</button>
          </div>
        </div>

    </form>

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

@endsection
