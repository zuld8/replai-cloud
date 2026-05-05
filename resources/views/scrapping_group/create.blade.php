@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('scrappings')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('scraping.group.back_to')}}
    </a>
    <a href="{{route('scrappings')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('scraping.group.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('scrapping_group.store'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-group me-2"></i>
                    {{__('scraping.group.create_title')}}
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
                                {{__('scraping.group.devices')}}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control devices" name="devices[]" multiple required>
                                @foreach ($devices as $device)
                                <option value="<?= $device->id; ?>"><?= $device->name; ?> - <?= $device->phone; ?></option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.group.devices_help')}}
                            </small>
                        </div>

                        <!-- Name -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-purchase-tag text-primary me-1"></i>
                                {{__('scraping.group.name')}}
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
                                       value="<?= old('name'); ?>"
                                       placeholder="{{__('scraping.group.name_placeholder')}}">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.group.name_help')}}
                            </small>
                        </div>

                        <!-- Schedule -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-calendar text-danger me-1"></i>
                                {{__('scraping.group.schedule')}}
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
                                       value="<?= old('schedule'); ?>">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{__('scraping.group.schedule_help')}}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{__('scraping.group.tips_title')}}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{__('scraping.group.tip_device')}}</li>
                            <li>{{__('scraping.group.tip_category')}}</li>
                            <li>{{__('scraping.group.tip_schedule')}}</li>
                            <li>{{__('scraping.group.tip_auto')}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{__('scraping.group.required_field')}}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search-alt me-1"></i>
                    {{__('scraping.group.btn_create')}}
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
        $('.devices').select2({
            placeholder: "{{__('scraping.group.choose_devices')}}"
        });
    });
</script>
@endsection