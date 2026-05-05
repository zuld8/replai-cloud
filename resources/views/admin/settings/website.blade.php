@extends('layouts.admin')

@section('content')
<!-- Start::app-content -->
<div class="row">
    <div class="col-lg-12">
        <x-validation-component></x-validation-component>

        <form action="<?= route('website.settings.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{$page}}
                </div>
            </div>
            <div class="card-body">

                <!-- Template Selection Section -->
                <div class="mb-4">
                    <h5 class="mb-3">{{__('setting.web_template')}}</h5>
                    <div class="row g-3">
                        @foreach($templates as $theme)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="template-selector cursor-pointer">
                                <input type="radio" name="web_template" value="{{$theme->code}}"
                                    class="template-radio"
                                    @if($theme->code == $setting->web_template) checked @endif>
                                <div class="template-card">
                                    <div class="template-thumbnail">
                                        <img src="<?= asset($theme->thumbnail); ?>"
                                            alt="{{$theme->name}}"
                                            class="img-fluid">
                                        <div class="template-overlay">
                                            <i class="ti ti-check fs-24"></i>
                                        </div>
                                    </div>
                                    <div class="template-info">
                                        <h6 class="mb-0">{{$theme->name}}</h6>
                                        <small class="text-muted">{{$theme->code}}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <hr class="my-4">

                <!-- Footer Settings -->
                <h5 class="mb-3">Footer Settings</h5>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">{{__('setting.footer')}}</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <select class="form-control" name="footer_web">
                            <option value="yes" @if($setting->footer_web == 'yes') selected @endif>{{__('general.yes')}}</option>
                            <option value="no" @if($setting->footer_web == 'no') selected @endif>{{__('general.no')}}</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">Footer Text 1</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <input class="form-control" name="footer_1" value="<?= $setting->footer_1; ?>" type="text">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">Footer Text 2</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <input class="form-control" name="footer_2" value="<?= $setting->footer_2; ?>" type="text">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">Footer Text 3</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <input class="form-control" name="footer_3" value="<?= $setting->footer_3; ?>" type="text">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">Copyright</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <input class="form-control" name="copyright" value="<?= $setting->copyright; ?>" type="text">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">Footer Text</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <textarea class="form-control" name="footer" rows="3">{{$setting->footer_description}}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <!-- SEO Settings -->
                <h5 class="mb-3">SEO Settings</h5>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">{{__('blog.meta_keyword')}}</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <input class="form-control" name="keyword" value="<?= $setting->meta_keyword; ?>" type="text" placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 col-sm-12 d-flex align-items-center">
                        <label class="form-label fs-15 mb-0">{{__('blog.meta_description')}}</label>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <textarea class="form-control" name="description" rows="3" placeholder="Description for search engines...">{{$setting->meta_description}}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Website Options -->
                <h5 class="mb-3">Website Options</h5>

                <div class="row g-3">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border text-center h-100">
                            <div class="card-body">
                                <label class="form-label fs-14 fw-semibold mb-2">{{__('setting.web_option')}}</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="frontend" type="checkbox" value="yes" @if($setting->frontend == 'yes') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border text-center h-100">
                            <div class="card-body">
                                <label class="form-label fs-14 fw-semibold mb-2">{{__('setting.register_option')}}</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="register" type="checkbox" value="yes" @if($setting->register == 'yes') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border text-center h-100">
                            <div class="card-body">
                                <label class="form-label fs-14 fw-semibold mb-2">{{__('setting.price_option')}}</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="pricing" type="checkbox" value="yes" @if($setting->pricing == 'yes') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border text-center h-100">
                            <div class="card-body">
                                <label class="form-label fs-14 fw-semibold mb-2">{{__('setting.contact_option')}}</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="contact" type="checkbox" value="yes" @if($setting->contact == 'yes') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border text-center h-100">
                            <div class="card-body">
                                <label class="form-label fs-14 fw-semibold mb-2">{{__('setting.blog_option')}}</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="blog" type="checkbox" value="yes" @if($setting->blog == 'yes') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>
</div>
<!-- End::app-content -->

<style>
    /* Template Selector Styles */
    .template-selector {
        display: block;
        margin: 0;
        cursor: pointer;
    }

    .template-radio {
        display: none;
    }

    .template-card {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #fff;
        height: 100%;
    }

    .template-card:hover {
        border-color: #6c5dd3;
        box-shadow: 0 4px 12px rgba(108, 93, 211, 0.15);
        transform: translateY(-2px);
    }

    .template-thumbnail {
        position: relative;
        padding-top: 75%;
        /* 4:3 Aspect Ratio */
        overflow: hidden;
        background: #f8f9fa;
    }

    .template-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .template-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(108, 93, 211, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .template-overlay i {
        color: white;
        font-size: 48px;
    }

    .template-info {
        padding: 12px;
        text-align: center;
    }

    .template-info h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .template-info small {
        font-size: 12px;
    }

    /* When radio is checked */
    .template-radio:checked+.template-card {
        border-color: #6c5dd3;
        box-shadow: 0 4px 16px rgba(108, 93, 211, 0.25);
    }

    .template-radio:checked+.template-card .template-overlay {
        opacity: 1;
    }

    .template-radio:checked+.template-card .template-info {
        background: #f8f7ff;
    }

    /* Cursor pointer */
    .cursor-pointer {
        cursor: pointer;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .template-info h6 {
            font-size: 13px;
        }

        .template-info small {
            font-size: 11px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add click animation
        const templateCards = document.querySelectorAll('.template-selector');

        templateCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class from all cards
                templateCards.forEach(c => {
                    const cardElement = c.querySelector('.template-card');
                    cardElement.style.transform = 'scale(1)';
                });

                // Add active animation to clicked card
                const clickedCard = this.querySelector('.template-card');
                clickedCard.style.transform = 'scale(0.98)';

                setTimeout(() => {
                    clickedCard.style.transform = 'scale(1)';
                }, 150);
            });
        });
    });
</script>

@endsection