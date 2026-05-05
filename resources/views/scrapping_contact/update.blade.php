@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('scrappings')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('scraping.contact.back_to')}}
    </a>
    <a href="{{route('scrappings')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('scraping.contact.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('scrapping_contact.edit', $scrapping->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-edit me-2"></i>
                    {{__('scraping.contact.update_title')}}
                </div>
            </div>
            
            <div class="card-body">
                <!-- Section: Konfigurasi Scraping -->
                <div class="mb-4">
                   
                    <div class="row">
                        <!-- Devices -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-devices text-success me-1"></i>
                                {{__('scraping.contact.devices')}}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control devices" name="devices[]" multiple required>
                                @foreach ($devices as $device)
                                <option value="<?= $device->id; ?>"
                                    {{ in_array($device->id, explode(',',$scrapping->devices)) ? 'selected' : '' }}>
                                    <?= $device->name; ?> - {{$device->phone}}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.contact.devices_help')}}
                            </small>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-category text-warning me-1"></i>
                                {{__('scraping.contact.category')}}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control categories" name="category" required>
                                <option value="">{{__('scraping.contact.choose_category')}}</option>
                                @foreach ($categories as $category)
                                <option value="<?= $category->id; ?>" 
                                        @if($category->id == $scrapping->category_id) selected @endif>
                                    <?= $category->name; ?>
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.contact.category_help')}}
                            </small>
                        </div>

                        <!-- Name -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-purchase-tag text-primary me-1"></i>
                                {{__('scraping.contact.name')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-rename"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="name" 
                                       required 
                                       value="<?= old('name', $scrapping->name); ?>"
                                       placeholder="{{__('scraping.contact.name_placeholder')}}">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.contact.name_help')}}
                            </small>
                        </div>

                        <!-- Schedule -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-calendar text-danger me-1"></i>
                                {{__('scraping.contact.schedule')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-time-five"></i>
                                </span>
                                <input type="datetime-local" 
                                       class="form-control" 
                                       name="schedule" 
                                       required 
                                       value="<?= old('schedule', $scrapping->schedule); ?>">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.contact.schedule_help')}}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{__('scraping.contact.tips_title')}}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{__('scraping.contact.tip_device')}}</li>
                            <li>{{__('scraping.contact.tip_category')}}</li>
                            <li>{{__('scraping.contact.tip_schedule')}}</li>
                            <li>{{__('scraping.contact.tip_auto')}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{__('scraping.contact.required_field')}}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>
                    {{__('scraping.contact.btn_update')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $(".devices").select2({
            placeholder: "{{__('scraping.contact.choose_devices')}}"
        });
        
        $(".categories").select2({
            placeholder: '{{__("scraping.contact.choose_category")}}'
        });
    });
</script>
@endsection