@extends('layouts.admin') 

@section('content')
<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>

    <div class="col-12 mt-4">
        <form action="<?= route('merchants.edit', $merchant->id); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    Edit Bisnis Profil
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-sm-12">
                        <label class="form-label">Nama Bisnis</label>
                        <input class="form-control" name="name" value="<?= $merchant->name; ?>" type="text" required>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="category">
                            @foreach($categories as $category)
                            <option value="{{$category->id}}" @if($merchant->merchant_category_id == $category->id) selected @endif>{{$category->name}} </option>
                            @endforeach 
                        </select>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <label class="form-label">Kode POS</label>
                        <input class="form-control" name="zip_code" value="<?= $merchant->zip_code; ?>" type="text" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" name="address">{{$merchant->address}} </textarea>
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