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
            <li class="breadcrumb-item"><a href="#">Categories</a></li>
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
      <form name="CategoryForm" id="CategoryForm" @if(empty($categorydata['id'])) action="{{ url('admin/add-edit-category') }}" @else action="{{ url('admin/add-edit-category/'.$categorydata['id']) }}" @endif method="post" enctype="multipart/form-data">@csrf
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
                  <label for="category_name">Category Name</label>
                  <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter Category Name" @if(!empty($categorydata['category_name'])) value="{{ $categorydata['category_name'] }}" @else value="{{ old('category_name') }}" @endif>
                </div>
                <div id="appendCategoriesLevel">
                  @include('admin.categories.append_categories_level')
                </div>
                @if(!empty($categorydata['category_image']))
                <div style="height: 100px;">
                </div>
                @endif
                <div class="form-group">
                  <label for="category_discount">Category Discount</label>
                  <input type="number" class="form-control" id="category_discount" name="category_discount" placeholder="Enter Category Discount" @if(!empty($categorydata['category_discount'])) value="{{ $categorydata['category_discount'] }}" @else value="{{ old('category_discount') }}" @endif>
                </div>
                <div class="form-group">
                  <label>Category Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter ...">@if(!empty($categorydata['description'])){{ $categorydata['description'] }} @else {{ old('description') }} @endif</textarea>
                </div>
                <div class="form-group">
                  <label>Meta Description</label>
                  <textarea class="form-control" id="meta_description" name="meta_description" rows="3" placeholder="Enter ...">@if(!empty($categorydata['meta_description'])) {{ $categorydata['meta_description'] }} @else {{ old('meta_description') }} @endif</textarea>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Select Section</label>
                  <select name="section_id" id="section_id" class="form-control select2" style="width: 100%;">
                    <option value="">Select Section</option>
                    @foreach($getSections as $section)
                    <option value="{{ $section->id }}" @if(!empty($categorydata['section_id']) && $categorydata['section_id']==$section->id) selected @endif>
                      {{ $section->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">Category Image</label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="category_image" name="category_image">
                      <label class="custom-file-label" for="category_image">Choose file</label>
                    </div>
                    <div class="input-group-append">
                      <span class="input-group-text" id="">Upload</span>
                    </div>
                  </div>
                  @if(!empty($categorydata['category_image']))
                  <div style="height: 100px;">
                    <img style="width:50px; margin-top: 5px;" src="{{ asset('images/category_images/'.$categorydata['category_image']) }}" alt="">
                    &nbsp;
                    <a class="confirmDelete" href="javascript:void(0)" record="category-image" recordid="{{ $categorydata['id'] }}" <?php /*href="{{ url('admin/delete-category-image/'.$categorydata['id']) }}"*/ ?>>Hapus Gambar</a>
                  </div>
                  @endif
                </div>
                <div class="form-group">
                  <label for="url">Category URL</label>
                  <input type="text" class="form-control" id="url" name="url" placeholder="Masukan URL" @if(!empty($categorydata['url'])) value="{{ $categorydata['url'] }}" @else value="{{ old('url') }}" @endif>
                </div>
                <div class="form-group">
                  <label>Meta Title</label>
                  <textarea id="meta_title" name="meta_title" class="form-control" rows="3" placeholder="Enter ...">@if(!empty($categorydata['meta_title'])) {{ $categorydata['meta_title'] }} @else {{ old('meta_title') }} @endif</textarea>
                </div>
                <div class="form-group">
                  <label>Meta Keywords</label>
                  <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="3" placeholder="Enter ...">@if(!empty($categorydata['meta_keywords'])) {{ $categorydata['meta_keywords'] }} @else {{ old('meta_keywords') }} @endif</textarea>
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