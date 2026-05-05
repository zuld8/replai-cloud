@extends('layouts.app')

@section('content')
<!-- Start::app-content -->
 


        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <form action="<?= route('whatsapp.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
                    @csrf
                    <div class="card-header">
                        <div class="card-title">
                            Tambah Akun Whatsapp
                        </div>
                        <x-validation-component></x-validation-component>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan No.Whatsapp</label>
                                        <input class="form-control" name="phone" value="<?= old('phone'); ?>" type="number" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Whatsapp Key</label>
                                        <input class="form-control" name="key" value="<?= old('key'); ?>" type="text" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Whatsapp Session</label>
                                        <input class="form-control" name="session" value="<?= old('session'); ?>" type="text" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Limit Harian</label>
                                        <input class="form-control" name="limit" value="<?= old('limit'); ?>" type="number" required>
                                    </div>
                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Tambahkan Akun</button>
                    </div>
                </form>
            </div>
        </div>
        <!--End::row-1 -->

    </div>
</div>
<!-- End::app-content -->

@endsection