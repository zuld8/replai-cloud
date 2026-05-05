@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('blash_email')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('blash.back_to')}}
    </a>
    <a href="{{route('blash_email')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('blash.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row row d-flex justify-content-center">
    <div class="col-xl-8 col-sm-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('blash_email.store'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-envelope me-2"></i>
                    {{__('page.email.add')}}
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Category -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-category text-warning me-1"></i>
                            {{__('sidebar.category')}}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.email.optional') }}</span>
                        </label>
                        <select class="form-control categories" name="category">
                            <option value="">{{__('scrapp.choose_category')}}</option>
                            @foreach ($categories as $category)
                            <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.email.category_help') }}
                        </small>
                    </div>

                    <!-- Template -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-file-blank text-info me-1"></i>
                            {{__('sidebar.message_template')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control templates" name="template" required>
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>"><?= $template->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.email.template_help') }}
                        </small>
                    </div>

                    <!-- Title -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag text-primary me-1"></i>
                            {{__('blash.title')}}
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
                                   placeholder="{{ __('broadcast.email.name_placeholder') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.email.name_help') }}
                        </small>
                    </div>

                    <!-- Schedule -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar text-danger me-1"></i>
                            {{__('scrapp.schedule')}}
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
                            {{ __('broadcast.email.schedule_help') }}
                        </small>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('broadcast.email.email_sending_tips') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('broadcast.email.tip_test_template') }}</li>
                            <li>{{ __('broadcast.email.tip_optimal_time') }}</li>
                            <li>{{ __('broadcast.email.tip_use_category') }}</li>
                            <li>{{ __('broadcast.email.tip_check_spam') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{ __('broadcast.email.required_field') }}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-send me-1"></i>
                    {{__('general.add_data')}}
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
        // Initialize Select2 with placeholders
        $(".categories").select2({
            placeholder: '{{__("scrapp.choose_category")}}',
            allowClear: true
        });
        
        $(".templates").select2({
            placeholder: '{{__("blash.choose_template")}}'
        });
    });
</script>
@endsection