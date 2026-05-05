@extends('layouts.starter')
@section('content')
<!-- Plans Comparison -->
<div class="row mb-5">
    <div class="col-xl-12">
        <div class="tab-content" id="myTabContent3">
            <div class="tab-pane show active p-0 border-0" id="pricing-monthly3-pane"
                role="tabpanel" aria-labelledby="pricing-monthly3" tabindex="0">
                <div class="row justify-content-center">
                    <div class="col-xxl-9 col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        <div class="container-lg">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center">
                                                        <p class="fs-18 fw-semibold mb-3 h5">{{ __('starter.feature_list') }}</p>
                                                    </th>
                                                    @foreach ($packages as $package)
                                                    <th scope="col" class="text-center c">
                                                        <div class="avatar avatar-md rounded-circle bg-light mb-2">
                                                            <i class="bx bx-bolt-circle fs-24 text-primary"></i>
                                                        </div>
                                                        <p class="fs-18 mb-0 fw-semibold h5">{{$package->name}}</p>
                                                        <span class="text-lg fw-semibold text-dark">
                                                            @if($package->trial_version == 'yes')
                                                            {{ __('starter.package_free') }}
                                                            @else
                                                            {{platform_currency()->currency_position == 'start' ? platform_currency()->currency : '' }} {{number_format($package->price)}} {{platform_currency()->currency_position == 'end' ? platform_currency()->currency : '' }}
                                                            @endif
                                                        </span>
                                                    </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="text-center bg-primary-transparent">
                                                    <td colspan="{{(count($packages) + 1)}}">
                                                        <span class="fs-14 fw-semibold">{{ __('starter.main_features') }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold fs-14">
                                                            {{ __('starter.active_period') }}
                                                        </span>
                                                    </td>
                                                    @foreach ($packages as $package)
                                                    <td>
                                                        <span>
                                                            {{$package->add_days > 0 ? number_format($package->add_days) .' '. __('starter.days') : __('starter.forever')}}
                                                        </span>
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold fs-14">
                                                            {{ __('starter.ai_response_credit') }}
                                                        </span>
                                                    </td>
                                                    @foreach ($packages as $package)
                                                    <td>
                                                        <span>
                                                            {{number_format($package->ai_response)}} {{ __('starter.ai_response') }}
                                                        </span>
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr class="text-center bg-primary-transparent">
                                                    <td colspan="{{(count($packages) + 1)}}">
                                                        <span class="fs-14 fw-semibold">{{ __('starter.complete_features') }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.storage') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->storage < 1)
                                                            <i class="bx bx-x-circle icon text-red"></i>
                                                            @else
                                                            <div class="d-flex justify-content-left"> 
                                                                <span> {{$package->storage_name }} </span>
                                                            </div>
                                                            @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.human_agent') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_user_option == 'yes' && $package->users_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_user_option == 'yes' ? strval(number_format($package->users_limit)) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.whatsapp_number') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_device == 'yes' && $package->users_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_device == 'yes' ? number_format($package->device_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>

                                                <tr>
                                                    <td>{{ __('starter.instagram') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_instagram == 'yes' && $package->users_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_instagram == 'yes' ? number_format($package->instagram) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>

                                                <tr>
                                                    <td>{{ __('starter.facebook_messenger') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_messanger == 'yes' && $package->users_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_messanger == 'yes' ? number_format($package->messanger) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>

                                                <tr>
                                                    <td>{{ __('starter.telegram') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_telegram == 'yes' && $package->users_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_telegram == 'yes' ? number_format($package->telegram) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>

                                                <tr>
                                                    <td>{{ __('starter.broadcast_whatsapp') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.broadcast_email') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_email_option == 'yes' && $package->email_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_email_option == 'yes' ? number_format($package->email_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.scraping_contacts') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.whatsapp_template') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_template == 'yes' && $package->template_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_template == 'yes' ? number_format($package->template_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.email_template') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_template == 'yes' && $package->template_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_template == 'yes' ? number_format($package->template_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.data_training_ai') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_ai_training == 'yes' && $package->ai_training_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.ai_agent') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->limit_chatbot == 'yes' && $package->chatbot_limit == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.widget_livechat') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->livechat_limit == 'yes' && $package->limit_livechat == 0)
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                            <span>( {{$package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : __('starter.unlimited') }} )</span>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.cek_ongkir_integration') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->cek_ongkir == 'no' )
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>{{ __('starter.google_sheet_integration') }}</td>
                                                    @foreach ($packages as $package)
                                                    <td class="text-center">
                                                        @if($package->google_sheet == 'no' )
                                                        <i class="bx bx-x-circle icon text-red"></i>
                                                        @else
                                                        <div class="d-flex justify-content-left">
                                                            <i class="bx bx-check-circle icon text-green" style="margin-right: 5px;"></i>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection