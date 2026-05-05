@extends('layouts.app')

@section('content') 

        <div class="row">
            <div class="col-xl-12">
                <form action="<?= route('whatsapp.edit', $whatsapp->id); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
                    @csrf
                    <x-validation-component></x-validation-component>
                    <div class="card-header">
                        <div class="card-title">
                            Edit Whatsapp Account
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan No.Whatsapp</label>
                                        <input class="form-control" name="phone" value="<?= old('phone',$whatsapp->phone); ?>" type="number" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Whatsapp Key</label>
                                        <input class="form-control" name="key" value="<?= old('key',$whatsapp->whatsapp_key); ?>" type="text" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Whatsapp Session</label>
                                        <input class="form-control" name="session" value="<?= old('session',$whatsapp->whatsapp_session); ?>" type="text" required>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Masukkan Limit Harian</label>
                                        <input class="form-control" name="limit" value="<?= old('limit',$whatsapp->limit_per_day); ?>" type="number" required>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Edit Whatsapp Account</button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</div> 
@endsection