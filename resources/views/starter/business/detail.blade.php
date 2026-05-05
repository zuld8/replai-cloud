@extends('layouts.starter')

@section('styles')
 <link href="{{asset('assets/css/pages/business_detail.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="package-purchase-container">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="page-header">
                <h2 class="page-title">{{ __('starter.package_purchase') }}</h2>
                <p class="page-subtitle">{{ __('starter.package_purchase_desc') }}</p>
            </div>
            <x-validation-component></x-validation-component>
        </div>

        {{-- Paket Section --}}
        <div class="col-12 col-lg-8">
            <div class="packages-grid">
                @foreach ($packages as $package)
                <div class="package-card-wrapper">
                    <div class="package-card {{ $package->trial_version == 'yes' ? 'free-package' : '' }}" data-package-id="{{ $package->id }}">
                        <div class="package-header">
                            <div class="package-badge-container">
                                <span class="package-badge badge-premium">
                                    {{ $package->name }}
                                </span>
                                @if($package->trial_version == 'yes')
                                <span class="trial-badge">{{ __('starter.free_trial') }}</span>
                                @endif
                            </div>

                            <div class="package-price">
                                <div class="price-container">
                                    <sup class="currency">Rp</sup>
                                    <span class="price">{{ $package->trial_version == 'yes' ? '0' : number_format($package->price ?? 0) }}</span>
                                    <sub class="period">/{{ number_format($package->add_days) }} {{ __('starter.days') }}</sub>
                                </div>
                            </div>
                        </div>

                        <div class="package-body">
                            <div class="package-summary">
                                <p class="package-description">
                                    {{ __('starter.see_all_benefits') }}
                                </p>
                            </div>

                            <!-- Package Details Toggle -->
                            <div class="package-details">
                                <button class="details-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $package->id }}" aria-expanded="false">
                                    <span>{{ __('starter.see_feature_details') }}</span>
                                    <i class="bx bx-chevron-down toggle-icon"></i>
                                </button>

                                <div class="collapse" id="details-{{ $package->id }}">
                                    <div class="features-list">

                                        <div class="feature-item">
                                            <i class="bx {{ $package->storage < 1 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.storage') }} ({{ $package->storage_name }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.users') }} ({{ $package->limit_user_option == 'yes' ? number_format($package->users_limit) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_device == 'yes' && $package->device_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.whatsapp_device') }} ({{ $package->limit_device == 'yes' ? number_format($package->device_limit) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.whatsapp_blast') }} ({{ $package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : __('starter.unlimited') }}{{ $package->whatsapp_priode ? '/' . $package->whatsapp_priode : '' }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_instagram == 'yes' && $package->instagram == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.instagram') }} ({{ $package->limit_instagram == 'yes' ? number_format($package->instagram) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_messanger == 'yes' && $package->messanger == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.messenger') }} ({{ $package->limit_messanger == 'yes' ? number_format($package->messanger) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_telegram == 'yes' && $package->telegram == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.telegram') }} ({{ $package->limit_telegram == 'yes' ? number_format($package->telegram) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.email_blast') }} ({{ $package->limit_email_option == 'yes' ? number_format($package->email_limit) : __('starter.unlimited') }}{{ $package->email_priode ? '/' . $package->email_priode : '' }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.data_scrapping') }} ({{ $package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : __('starter.unlimited') }}{{ $package->scrapping_priode ? '/' . $package->scrapping_priode : '' }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_template == 'yes' && $package->template_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.message_template') }} ({{ $package->limit_template == 'yes' ? number_format($package->template_limit) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.ai_training') }} ({{ $package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.chatbot') }} ({{ $package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.livechat') }} ({{ $package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : __('starter.unlimited') }})</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->cek_ongkir == 'no' ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.cek_ongkir') }}</span>
                                        </div>

                                        <div class="feature-item">
                                            <i class="bx {{ $package->google_sheet == 'no' ? 'bx-x text-danger' : 'bx-check text-success' }}"></i>
                                            <span>{{ __('starter.google_sheet') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="package-footer">
                            <button class="btn-select-package" id="buttonId{{ $package->id }}" onclick="getPackageDetail('{{ $package->id }}')">
                                <span>{{ __('starter.select_package') }}</span>
                                <i class="bx bx-arrow-right"></i>
                            </button>
                        </div>

                        <div class="package-selected-indicator">
                            <i class="bx bx-check-circle"></i>
                            <span>{{ __('starter.selected_package') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Transaksi --}}
        <div class="col-12 col-lg-4">
            <div class="transaction-summary sticky-summary">
                <form action="{{ route('starter.transactions.create', $business->id) }}" method="POST">
                    @csrf
                    <div class="summary-card">
                        <div class="summary-header">
                            <h5 class="summary-title text-white">
                                <i class="bx bx-receipt me-2"></i>
                                {{ __('starter.transaction_summary') }}
                            </h5>
                        </div>

                        <div class="summary-body">
                            <input type="hidden" name="package_id" id="packageid" value="">

                            <div class="summary-item">
                                <span class="item-label">{{ __('starter.package_name') }}</span>
                                <span class="item-value" id="summary-name">-</span>
                            </div>

                            <div class="summary-item">
                                <span class="item-label">{{ __('starter.subtotal') }}</span>
                                <span class="item-value" id="summary-subtotal">0</span>
                            </div>

                            <div class="summary-item">
                                <span class="item-label">{{ __('starter.tax') }}</span>
                                <span class="item-value" id="summary-tax">0</span>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-item total-item">
                                <span class="item-label">{{ __('starter.total') }}</span>
                                <span class="item-value total-value" id="summary-total">0</span>
                            </div>

                            <div class="summary-item">
                                <span class="item-label">{{ __('starter.estimate_expire_date') }}</span>
                                <span class="item-value" id="summary-expire">-</span>
                            </div>
                        </div>

                        <div class="summary-footer">
                            <button class="btn-create-transaction" type="submit">
                                <i class="bx bx-credit-card me-2"></i>
                                {{ __('starter.create_transaction') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activeButton = null;
        let selectedPackageCard = null;

        // Global function for package selection
        window.getPackageDetail = function(packageId) {
            // Show loading state
            const button = document.getElementById('buttonId' + packageId);
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Loading...';
            button.disabled = true;

            $.ajax({
                url: '/app/starter/packages/detail/' + packageId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Reset previous selection
                    if (activeButton && activeButton !== button) {
                        activeButton.disabled = false;
                        activeButton.innerHTML = '<span>{{ __("starter.select_package") }}</span><i class="bx bx-arrow-right"></i>';
                    }

                    if (selectedPackageCard && selectedPackageCard !== button.closest('.package-card')) {
                        selectedPackageCard.classList.remove('selected');
                    }

                    // Set new active selection
                    activeButton = button;
                    selectedPackageCard = button.closest('.package-card');

                    // Update button state
                    button.innerHTML = '<i class="bx bx-check me-2"></i>{{ __("starter.selected_package") }}';
                    button.disabled = true;

                    // Add selected class to card
                    selectedPackageCard.classList.add('selected');

                    // Update form data
                    document.getElementById('packageid').value = packageId;

                    // Update summary with animation
                    updateSummaryWithAnimation({
                        name: response.name,
                        subtotal: response.subtotal,
                        tax: response.tax_total,
                        total: response.price,
                        expire: response.estimate
                    });

                    // Scroll to summary on mobile
                    if (window.innerWidth <= 768) {
                        document.querySelector('.transaction-summary').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Reset button on error
                    button.innerHTML = originalContent;
                    button.disabled = false;

                    // Show error message
                    alert('{{ __("starter.error_loading_package") }}');
                    console.error('Error:', error);
                }
            });
        };

        function updateSummaryWithAnimation(data) {
            const elements = {
                name: document.getElementById('summary-name'),
                subtotal: document.getElementById('summary-subtotal'),
                tax: document.getElementById('summary-tax'),
                total: document.getElementById('summary-total'),
                expire: document.getElementById('summary-expire')
            };

            // Add loading class
            Object.values(elements).forEach(el => {
                if (el) el.classList.add('updating');
            });

            setTimeout(() => {
                if (elements.name) elements.name.textContent = data.name;
                if (elements.subtotal) elements.subtotal.textContent = data.subtotal;
                if (elements.tax) elements.tax.textContent = data.tax;
                if (elements.total) elements.total.textContent = data.total;
                if (elements.expire) elements.expire.textContent = data.expire;

                // Remove loading class
                Object.values(elements).forEach(el => {
                    if (el) el.classList.remove('updating');
                });

                // Add success animation
                document.querySelector('.summary-card').classList.add('updated');
                setTimeout(() => {
                    document.querySelector('.summary-card').classList.remove('updated');
                }, 600);
            }, 300);
        }

        // Handle collapse toggle icons
        document.addEventListener('shown.bs.collapse', function(e) {
            const button = document.querySelector(`[data-bs-target="#${e.target.id}"]`);
            if (button) {
                button.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('hidden.bs.collapse', function(e) {
            const button = document.querySelector(`[data-bs-target="#${e.target.id}"]`);
            if (button) {
                button.setAttribute('aria-expanded', 'false');
            }
        });

        // Add animation delays to cards
        document.querySelectorAll('.package-card').forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });

    // Additional CSS for loading states
    const additionalCSS = `
.updating {
    opacity: 0.5;
    transform: scale(0.95);
    transition: all 0.3s ease;
}

.summary-card.updated {
    transform: scale(1.02);
    transition: transform 0.3s ease;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
}
`;

    // Inject additional CSS
    const style = document.createElement('style');
    style.textContent = additionalCSS;
    document.head.appendChild(style);
</script>
@endsection