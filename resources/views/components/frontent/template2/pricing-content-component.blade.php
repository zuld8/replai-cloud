@foreach($pricing as $package)
<div class="pricing-card {{ $package->featured == 'yes' ? 'featured' : '' }}">
    @if($package->featured == 'yes')
    <div class="popular-badge">Most Popular</div>
    @endif

    <div class="pricing-header">
        <div class="plan-name">{{ $package->name }}</div>
        <p class="plan-description">{{ $package->description }}</p>

        <div class="price-wrapper">
            <span class="currency">{{ $package->trial_version == 'yes' ? '' : 'Rp' }}</span>
            <span class="price">
                {{ $package->trial_version == 'yes' ? '0' : number_format($package->price, 0, ',', '.') }}
            </span>
        </div>
        <span class="period">
            / {{$package->days_option == 'limited' ? $package->add_days . ' '.__('auth.package_days') : ' '.__('auth.unlimited') }} 
        </span>
    </div>

    <ul class="pricing-features">
        <!-- Storage -->
        <li class="{{ $package->storage < 1 ? 'disabled' : '' }}">
            <i class="fas {{ $package->storage < 1 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            Storage ({{ $package->storage_name }})
        </li>

        <!-- AI Response -->
        <li class="{{ $package->ai_response < 1 ? 'disabled' : '' }}">
            <i class="fas {{ $package->ai_response < 1 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            AI Response ({{ number_format($package->ai_response) }})
        </li>

        <!-- Users -->
        <li class="{{ $package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_user_option == 'yes' ? number_format($package->users_limit) : 'Unlimited' }} Users
        </li>

        <!-- WhatsApp Devices -->
        <li class="{{ $package->limit_device == 'yes' && $package->device_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_device == 'yes' && $package->device_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_device == 'yes' ? number_format($package->device_limit) : 'Unlimited' }} WhatsApp Devices
        </li>

        <!-- WhatsApp Messages -->
        <li class="{{ $package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : 'Unlimited' }} Messages{{ $package->whatsapp_priode ? '/' . $package->whatsapp_priode : '' }}
        </li>

        <!-- Instagram -->
        <li class="{{ $package->limit_instagram == 'yes' && $package->instagram == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_instagram == 'yes' && $package->instagram == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_instagram == 'yes' ? number_format($package->instagram) : 'Unlimited' }} Instagram
        </li>

        <!-- Messenger -->
        <li class="{{ $package->limit_messanger == 'yes' && $package->messanger == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_messanger == 'yes' && $package->messanger == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_messanger == 'yes' ? number_format($package->messanger) : 'Unlimited' }} Messenger
        </li>

        <!-- Telegram -->
        <li class="{{ $package->limit_telegram == 'yes' && $package->telegram == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_telegram == 'yes' && $package->telegram == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_telegram == 'yes' ? number_format($package->telegram) : 'Unlimited' }} Telegram
        </li>

        <!-- Email Blast -->
        <li class="{{ $package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_email_option == 'yes' ? number_format($package->email_limit) : 'Unlimited' }} Email{{ $package->email_priode ? '/' . $package->email_priode : '' }}
        </li>

        <!-- Google Maps Scraping -->
        <li class="{{ $package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : 'Unlimited' }} Scraping{{ $package->scrapping_priode ? '/' . $package->scrapping_priode : '' }}
        </li>

        <!-- Message Templates -->
        <li class="{{ $package->limit_template == 'yes' && $package->template_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_template == 'yes' && $package->template_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_template == 'yes' ? number_format($package->template_limit) : 'Unlimited' }} Templates
        </li>

        <!-- AI Training -->
        <li class="{{ $package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : 'Unlimited' }} AI Trainings
        </li>

        <!-- Chatbot -->
        <li class="{{ $package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : 'Unlimited' }} Chatbots
        </li>

        <!-- Live Chat -->
        <li class="{{ $package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'disabled' : '' }}">
            <i class="fas {{ $package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            {{ $package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : 'Unlimited' }} Live Chat
        </li>

        <!-- Cek Ongkir -->
        <li class="{{ $package->cek_ongkir == 'no' ? 'disabled' : '' }}">
            <i class="fas {{ $package->cek_ongkir == 'no' ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            Shipping Cost Calculator
        </li>

        <!-- Google Sheets -->
        <li class="{{ $package->google_sheet == 'no' ? 'disabled' : '' }}">
            <i class="fas {{ $package->google_sheet == 'no' ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
            Google Sheets Integration
        </li>
    </ul>

    <a href="{{ route('register') }}?package={{$package->id}}" class="pricing-btn">
        {{ $package->trial_version == 'yes' ? 'Start Free Trial' : 'Get Started' }}
    </a>
</div>
@endforeach