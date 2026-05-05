@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('setting.change'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-cog me-2"></i>
                    {{__('master.configuration.update_configuration')}}
                </div>
            </div>

            <div class="card-body">
                <!-- Section: Konfigurasi Umum -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-globe me-1"></i>
                        Konfigurasi Umum
                    </h6>
                    <div class="row">
                        <!-- Timezone -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-time text-primary me-1"></i>
                                {{__('master.configuration.timezone')}}
                            </label>
                            <select class="form-control timezone" name="timezone">
                                <option value="">{{__('master.configuration.choose_timezone')}}</option>
                                @foreach (timezone() as $t => $timezone)
                                <option value="<?= $timezone; ?>" @if($timezone==$setting->timezone) selected @endif>{{$timezone}}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Zona waktu yang digunakan untuk seluruh sistem
                            </small>
                        </div>

                        <!-- Default Language -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-world text-success me-1"></i>
                                {{__('master.configuration.default_lang')}}
                            </label>
                            <select class="form-control lang" name="default_lang" required>
                                <option value="">{{__('master.configuration.choose_lang')}}</option>
                                <option value="id" @if('id'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.indonesia')}}</option>
                                <option value="en" @if('en'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.english')}}</option>
                                <option value="hi" @if('hi'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.india')}}</option>
                                <option value="pt" @if('pt'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.portugal')}}</option>
                                <option value="es" @if('es'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.spanish')}}</option>
                                <option value="de" @if('de'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.german')}}</option>
                                <option value="ar" @if('ar'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.arab')}}</option>
                                <option value="ja" @if('ja'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.japan')}}</option>
                                <option value="nl" @if('nl'==old('default_lang', $setting->default_lang ?? '')) selected @endif>{{__('sidebar.dutch')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Bahasa default untuk interface sistem
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section: Konfigurasi API -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-key me-1"></i>
                        Konfigurasi API
                    </h6>
                    <div class="row">
                        <!-- Local API Key -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-lock-alt text-warning me-1"></i>
                                {{__('setting.local_api_key')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-key"></i>
                                </span>
                                <input type="text" class="form-control apikeylocal" readonly value="<?= $setting->local_api_key; ?>">
                                <button class="btn btn-primary" type="button" id="generateApiKey">
                                    <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                API key untuk integrasi lokal. Klik refresh untuk generate baru
                            </small>
                        </div>

                        <!-- API Device Usage -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-devices text-info me-1"></i>
                                {{__('setting.device_id_api_usage')}}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" name="api_device_use" required>
                                <option value="">{{__('general.choose')}}</option>
                                <option value="required" @if('required'==$setting->api_device_use) selected @endif>{{__('setting.must_include')}}</option>
                                <option value="optional" @if('optional'==$setting->api_device_use) selected @endif>{{__('general.optional')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Tentukan apakah device ID wajib atau opsional saat API call
                            </small>
                        </div>

                        <!-- Google Maps API Key -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-map-alt text-danger me-1"></i>
                                {{__('master.configuration.google_map_api_key')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bxl-google"></i>
                                </span>
                                <input class="form-control" name="gmap_key" value="<?= $setting->gmap_key; ?>" type="text" placeholder="AIzaSy...">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                API key untuk integrasi Google Maps dan scraping lokasi
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section: Konfigurasi AI Chat -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-brain me-1"></i>
                        Konfigurasi AI Chat
                    </h6>
                    <div class="row">
                        <!-- History Chat Option -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-history text-primary me-1"></i>
                                Opsi Ingatan Histori Chat AI
                            </label>
                            <select class="form-control historychat" name="history_ai_chat_option">
                                <option value="no" @if(old('history_ai_chat_option',$setting->history_ai_chat_option) == 'no') selected @endif>{{__('general.no')}}</option>
                                <option value="yes" @if(old('history_ai_chat_option',$setting->history_ai_chat_option) == 'yes') selected @endif>{{__('general.yes')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Aktifkan agar AI mengingat riwayat percakapan
                            </small>
                        </div>

                        <!-- Reset History Days -->
                        <div class="col-lg-6 mb-4 forgethistory">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-reset text-warning me-1"></i>
                                Reset Ingatan Dalam Berapa Hari
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-calendar"></i>
                                </span>
                                <input class="form-control" name="history_ai_chat" value="<?= $setting->history_ai_chat; ?>" type="number">
                                <span class="input-group-text">hari</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Histori akan direset otomatis setelah periode ini
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section: Konfigurasi Tanda Tangan -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-edit-alt me-1"></i>
                        Konfigurasi Tanda Tangan Pesan
                    </h6>
                    <div class="row">
                        <!-- Signature Option -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-pen text-success me-1"></i>
                                Opsi Tanda Tangan Pesan
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" name="signature_option" required>
                                <option value="none">Jangan Gunakan</option>
                                <option value="by_login" @if('by_login'==$setting->signature_option) selected @endif>Berdasarkan User Login</option>
                                <option value="by_device" @if('by_device'==$setting->signature_option) selected @endif>Berdasarkan Nama Perangkat</option>
                                <option value="custom" @if('custom'==$setting->signature_option) selected @endif>Custom</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Pilih metode tanda tangan otomatis pada pesan
                            </small>
                        </div>

                        <!-- Custom Signature -->
                        <div class="col-12 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-message-square-edit text-info me-1"></i>
                                Custom Tanda Tangan Pesan
                            </label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-2">
                                    <i class="bx bx-edit"></i>
                                </span>
                                <textarea class="form-control" name="signature_text" rows="3" placeholder="Contoh: Terima kasih,&#10;Tim Customer Service">{{$setting->signature_text}}</textarea>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Tanda tangan custom akan digunakan jika opsi "Custom" dipilih
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section: Konfigurasi Scraping -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-search-alt me-1"></i>
                        Konfigurasi Scraping & Pengiriman
                    </h6>
                    <div class="row">
                        <!-- Phone Scrapping Protection -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-shield text-warning me-1"></i>
                                {{__('master.configuration.phone_scrapp')}}
                            </label>
                            <select class="form-control" name="scrapp_phone">
                                <option value="protect_double" @if($setting->scrapp_phone == 'protect_double') selected @endif>{{__('general.no')}}</option>
                                <option value="no_protect" @if($setting->scrapp_phone == 'no_protect') selected @endif>{{__('general.yes')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Proteksi duplikasi nomor saat scraping
                            </small>
                        </div>

                        <!-- Just Scrapp WhatsApp -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bxl-whatsapp text-success me-1"></i>
                                {{__('master.configuration.just_scrapp_whatsapp')}}
                            </label>
                            <select class="form-control" name="scrapp_phone_whatsapp">
                                <option value="must_whatsapp" @if($setting->scrapp_phone == 'must_whatsapp') selected @endif>{{__('master.configuration.yes_scrapp')}}</option>
                                <option value="all" @if($setting->scrapp_phone == 'all') selected @endif>{{__('master.configuration.no_scrapp')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Scraping hanya nomor yang terdaftar WhatsApp
                            </small>
                        </div>

                        <!-- Method Sent -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-shuffle text-primary me-1"></i>
                                {{__('master.configuration.method_sent')}}
                            </label>
                            <select class="form-control" name="whatsapp_sender_notif">
                                <option value="sequence" @if($setting->whatsapp_sender_notif == 'sequence') selected @endif>{{__('master.configuration.sequence')}}</option>
                                <option value="spin" @if($setting->whatsapp_sender_notif == 'spin') selected @endif>Spin</option>
                                <option value="random" @if($setting->whatsapp_sender_notif == 'random') selected @endif>Random</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Metode pemilihan device untuk pengiriman pesan
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section: Konfigurasi Email -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-envelope me-1"></i>
                        Konfigurasi Email SMTP
                    </h6>
                    <div class="row">
                        <!-- Mail Encryption -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-lock text-danger me-1"></i>
                                Mail Encryption
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-key"></i>
                                </span>
                                <input class="form-control" name="mail_encryption" value="<?= $setting->mail_encryption; ?>" type="text" placeholder="tls">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Protokol enkripsi: tls atau ssl
                            </small>
                        </div>

                        <!-- Mail Host -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-server text-primary me-1"></i>
                                Mail Host
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-server"></i>
                                </span>
                                <input class="form-control" name="mail_host" value="<?= $setting->mail_host; ?>" type="text" placeholder="smtp.gmail.com">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Alamat server SMTP
                            </small>
                        </div>

                        <!-- Mail Port -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-network-chart text-success me-1"></i>
                                Mail Port
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-hash"></i>
                                </span>
                                <input class="form-control" name="mail_port" value="<?= $setting->mail_port; ?>" type="text" placeholder="587">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Port SMTP (umumnya 587 atau 465)
                            </small>
                        </div>

                        <!-- Mail Username -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-user text-info me-1"></i>
                                Mail Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-user"></i>
                                </span>
                                <input class="form-control" name="mail_username" value="<?= $setting->mail_username; ?>" type="text" placeholder="your-email@gmail.com">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Username atau email untuk autentikasi SMTP
                            </small>
                        </div>

                        <!-- Mail Password -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-lock-open text-warning me-1"></i>
                                Mail Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-key"></i>
                                </span>
                                <input class="form-control" name="mail_password" value="<?= $setting->mail_password; ?>" type="password" placeholder="••••••••">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Password atau app password untuk SMTP
                            </small>
                        </div>

                        <!-- Mail From Address -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-mail-send text-primary me-1"></i>
                                Mail From Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-at"></i>
                                </span>
                                <input class="form-control" name="mail_from_address" value="<?= $setting->mail_from_address; ?>" type="email" placeholder="noreply@yourdomain.com">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Email pengirim yang akan muncul di inbox
                            </small>
                        </div>

                        <!-- Mail From Name -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-rename text-success me-1"></i>
                                Mail From Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-user-circle"></i>
                                </span>
                                <input class="form-control" name="mail_from_name" value="<?= $setting->mail_from_name; ?>" type="text" placeholder="Your Company Name">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Nama pengirim yang akan ditampilkan
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-warning d-flex align-items-start" role="alert">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <div>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>Pastikan konfigurasi API dan Email sudah benar sebelum disimpan</li>
                            <li>Perubahan timezone akan mempengaruhi semua jadwal di sistem</li>
                            <li>Untuk Gmail, gunakan App Password bukan password biasa</li>
                            <li>Test konfigurasi email sebelum digunakan untuk production</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    Pastikan semua konfigurasi sudah benar sebelum menyimpan
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>
                    {{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.timezone').select2({
            placeholder: '{{__("master.configuration.choose_timezone")}}'
        });

        $('.lang').select2({
            placeholder: '{{__("master.configuration.choose_lang")}}'
        });

        // Toggle history reset days based on history option
        $('.historychat').on('change', function() {
            if ($(this).val() == 'yes') {
                $('.forgethistory').removeClass('d-none');
            } else {
                $('.forgethistory').addClass('d-none');
            }
        });

        // Trigger on page load
        $('.historychat').trigger('change');
    });

    // Generate API Key
    $("#generateApiKey").on("click", function() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "/app/settings/generate-api-local",
            success: function(response) {
                $(".apikeylocal").val(response.message);

                // Show success notification (optional)
                // You can add your notification system here
            },
            error: function(xhr, status, error) {
                console.error('Error generating API key:', error);
            },
        });
    });
</script>
@endsection

@endsection