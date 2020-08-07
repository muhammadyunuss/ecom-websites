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
            <li class="breadcrumb-item"><a href="#">Products Images</a></li>
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
      <form name="addImageForm" id="addImageForm" method="post" action="{{url('admin/add-images/'.$productdata['id'])}}" enctype="multipart/form-data">@csrf
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
              <div class="col-md-6">
              <div class="form-group">
                <div class="field_wrapper">
                    <div>
                        <input  multiple="" id="image" name="image[]" type="file" placeholder="Image" required/>
                    </div>
                </div>
              </div>
            </div>
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Add Image</button>
          </div>
        </div>
      </form>

      <form name="editImageForm" id="editImageForm" method="post" action="{{ url('admin/edit-images/'.$productdata['id']) }}">@csrf
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Added Product Images</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="products" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($productdata['images'] as $image)
                <input style="display: none" type="text" name="attrId[]" value="{{ $image['id'] }}" required>
                <tr>
                  <td>{{ $image['id'] }}</td>
                  <td><img style="width:130px; margin-top: 5px;" src="{{ asset('images/product_images/small/'.$image['image']) }}" alt="">
                  </td>
                  <td>
                    @if($image['status']==1)
                    <a class="updateImageStatus" id="image-{{ $image['id'] }}" image_id="{{ $image['id'] }}" href="javascript:void(0)">Active</a>
                    @else
                    <a class="updateImageStatus" id="image-{{ $image['id'] }}" image_id="{{ $image['id'] }}" href="javascript:void(0)">Inactive</a>
                    @endif
                    &nbsp; &nbsp;
                    <a title="Delete Images" class="confirmDelete" record="image" recordid="{{ $image['id'] }}" href="javascript:void(0)"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                @endforeach

              </tbody>

            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Images</button>
          </div>
        </div>

    </form>

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

@endsection
