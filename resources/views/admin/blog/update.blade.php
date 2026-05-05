@extends('layouts.admin')

@section('styles')
<link href="{{asset('assets/libs/summernote/summernote.min.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('blogs')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('blog.back_to_list')}}
    </a>
    <a href="{{route('blogs')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('blog.back_to_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('blogs.edit',$blog->id); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{$page}}</div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('blog.title')}}</label>
                        <input class="form-control" name="subject" value="<?= old('name',$blog->name); ?>" type="text" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('sidebar.category')}}</label>
                        <select class="form-control" name="category">
                            <option value="">{{__('scrapp.choose_category')}}</option>
                            @foreach ($categories as $category)
                            <option value="{{$category->id}}" @if(old('category',$blog->category_id)==$category->id) selected @endif >{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('blog.meta_keyword')}}</label>
                        <input class="form-control" name="keyword" value="<?= old('keyword',$blog->keyword); ?>" type="text" >
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('blog.meta_description')}}</label>
                        <textarea class="form-control" style="height: 100px;" name="description" >{{old('description',$blog->meta_description)}}</textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('blog.content')}}</label>
                        <textarea class="form-control" id="texteditor" style="height: 300px;" name="content" required>{{old('content',$blog->description)}}</textarea>
                    </div>
                    <div class="col-12 mt-3 uploadimage">
                        <label class="form-label">{{__('general.upload_image')}} ( {{__('general.optional')}} ) </label>
                        <input class="form-control" name="thumbnail" type="file">
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/summernote/summernote.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#texteditor').summernote();
    });
</script>
@endsection