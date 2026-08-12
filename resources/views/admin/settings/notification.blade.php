@extends('layouts.admin')

@section('styles')
<style>
/* ── Notification Settings — Table Layout ──────────── */
.notif-header-card {
    background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 24px;
    color: #fff;
}
.notif-header-card h4 { font-size: 1.25rem; font-weight: 700; margin: 0 0 4px; }
.notif-header-card p  { opacity: .8; margin: 0; font-size: .9rem; }

.sender-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
}
.sender-card .label-sm { font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
.brevo-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #dcfce7; color: #15803d; border-radius: 20px;
    padding: 4px 12px; font-size: .78rem; font-weight: 600;
}
.brevo-badge::before { content: '✓'; font-weight: 700; }

/* Table */
.notif-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
.notif-table thead th {
    background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
    color: #fff; padding: 14px 18px; font-size: .8rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .05em; border: none;
}
.notif-table thead th:first-child { width: 34%; }
.notif-table thead th:nth-child(2),
.notif-table thead th:nth-child(3) { width: 33%; }
.notif-table tbody tr:nth-child(even) { background: #f8faff; }
.notif-table tbody tr:hover { background: #f0f4ff; }
.notif-table td { padding: 14px 18px; vertical-align: top; border-bottom: 1px solid #e8edf5; }
.notif-table tbody tr:last-child td { border-bottom: none; }

/* Event cell */
.event-icon { font-size: 1.3rem; margin-right: 8px; }
.event-title { font-weight: 600; font-size: .93rem; color: #1e293b; }
.event-desc  { font-size: .78rem; color: #64748b; margin-top: 2px; }
.badge-new   { background: #ede9fe; color: #7c3aed; font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 6px; }
.badge-rec   { background: #fef9c3; color: #854d0e; font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 6px; }

/* Toggle cell */
.channel-cell { }
.toggle-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.toggle-label { font-size: .82rem; font-weight: 500; color: #334155; }

/* Custom toggle switch */
.notif-switch { position: relative; display: inline-block; width: 42px; height: 24px; }
.notif-switch input { opacity: 0; width: 0; height: 0; }
.notif-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #cbd5e1; transition: .3s; border-radius: 24px;
}
.notif-slider:before {
    position: absolute; content: ""; height: 18px; width: 18px;
    left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: .3s;
}
input:checked + .notif-slider { background: linear-gradient(135deg, #2563EB, #7C3AED); }
input:checked + .notif-slider:before { transform: translateX(18px); }

.tpl-select {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 6px 10px; font-size: .82rem; color: #334155;
    background: #fff; transition: border .2s;
}
.tpl-select:focus { border-color: #2563EB; outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.tpl-select:disabled { background: #f1f5f9; color: #94a3b8; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('notification.settings.store'); ?>" enctype="multipart/form-data" method="POST">
            @csrf

            {{-- ── Header ── --}}
            <div class="notif-header-card">
                <h4>🔔 Pengaturan Notifikasi</h4>
                <p>Kelola notifikasi otomatis via WhatsApp &amp; Email untuk setiap kejadian penting.</p>
            </div>

            {{-- ── Pengaturan Pengirim ── --}}
            <div class="sender-card">
                <div class="row g-3 align-items-end">
                    {{-- Nomor WA pengirim (gateway) --}}
                    <div class="col-lg-4 col-sm-12">
                        <div class="label-sm">📱 Nomor WhatsApp Pengirim</div>
                        <select class="form-control" name="device">
                            <option value="">— Pakai nomor CRM (default) —</option>
                            @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @if(old('device', $setting->device_notification) == $device->id) selected @endif>
                                {{ $device->name }} — {{ $device->phone }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Untuk notifikasi WA gateway (unofficial)</small>
                    </div>

                    {{-- WABA Device --}}
                    <div class="col-lg-4 col-sm-12">
                        <div class="label-sm">
                            <i class="bx bxl-whatsapp" style="color:#25d366;"></i>
                            WhatsApp Business API (WABA)
                            <span class="badge bg-primary ms-1" style="font-size:.7rem;">Recommended</span>
                        </div>
                        <select class="form-control" name="waba_device">
                            <option value="">— Tidak menggunakan WABA —</option>
                            @foreach ($wabaDevices as $wdev)
                            @php
                                $wMeta   = json_decode($wdev->meta_data, true);
                                $wName   = $wMeta['whatsapp']['verified_name'] ?? $wdev->phone;
                                $wStatus = $wMeta['whatsapp']['status'] ?? 'UNKNOWN';
                            @endphp
                            <option value="{{ $wdev->id }}" @if(old('waba_device', $setting->waba_device_notification) == $wdev->id) selected @endif>
                                {{ $wName }} — {{ $wdev->phone }}
                                @if($wStatus === 'CONNECTED') ✅ @else ({{ $wStatus }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Notifikasi via API resmi Meta (tidak masuk CRM)</small>
                    </div>

                    {{-- WA penerima notif admin --}}
                    <div class="col-lg-4 col-sm-12">
                        <div class="label-sm">📞 No. WA Penerima Notifikasi Admin</div>
                        <input class="form-control" name="received_notification"
                               value="{{ old('received_notification', $setting->received_notification) }}"
                               type="number" placeholder="628xxxxxxxxxx">
                    </div>

                    {{-- Email pengirim (Brevo read-only) --}}
                    <div class="col-lg-4 col-sm-12">
                        <div class="label-sm">✉️ Email Pengirim</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="fw-semibold text-dark">noreply@replai.id</span>
                            <span class="brevo-badge">Brevo terhubung</span>
                        </div>
                        <small class="text-muted">Konfigurasi SMTP di Pengaturan → SMTP</small>
                    </div>

                    {{-- Email penerima notifikasi admin --}}
                    <div class="col-lg-4 col-sm-12">
                        <div class="label-sm">📧 Email Penerima Notifikasi Admin</div>
                        <input class="form-control" name="received_email_notification"
                               value="{{ old('received_email_notification', $setting->received_email_notification) }}"
                               type="email" placeholder="admin@replai.id">
                    </div>
                </div>
            </div>

            {{-- ── Tabel Notifikasi ── --}}
            <div class="card custom-card mb-0">
                <div class="card-body p-0">
                    <table class="notif-table">
                        <thead>
                            <tr>
                                <th>Kejadian</th>
                                <th>🟢 WhatsApp</th>
                                <th>✉️ Email</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- ─── ROW 1: Registrasi pengguna ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">👤</span>
                                    <span class="event-title">Registrasi pengguna</span>
                                    <span class="badge-rec">disarankan aktif</span>
                                    <div class="event-desc ms-4">ke pengguna, saat daftar</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waReg = old('whatsapp_register', $setting->whatsapp_register) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_register" value="no">
                                            <input type="checkbox" name="whatsapp_register" value="yes" @if($waReg) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waReg ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_register_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_register_template', $setting->whatsapp_register_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emReg = old('email_register', $setting->email_register) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_register" value="no">
                                            <input type="checkbox" name="email_register" value="yes" @if($emReg) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emReg ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_register_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_register_template', $setting->email_register_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 2: Pembelian paket ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">🛒</span>
                                    <span class="event-title">Pembelian paket</span>
                                    <div class="event-desc ms-4">ke pengguna, saat beli</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waBuy = old('whatsapp_buy_package', $setting->whatsapp_buy_package) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_buy_package" value="no">
                                            <input type="checkbox" name="whatsapp_buy_package" value="yes" @if($waBuy) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waBuy ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_buy_package_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_buy_package_template', $setting->whatsapp_buy_package_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emBuy = old('email_buy_package', $setting->email_buy_package) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_buy_package" value="no">
                                            <input type="checkbox" name="email_buy_package" value="yes" @if($emBuy) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emBuy ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_buy_package_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_buy_package_template', $setting->email_buy_package_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 3: Pembayaran diterima ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">💳</span>
                                    <span class="event-title">Pembayaran diterima</span>
                                    <div class="event-desc ms-4">ke pengguna, saat upload bukti</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waPay = old('whatsapp_package_payment', $setting->whatsapp_package_payment) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_package_payment" value="no">
                                            <input type="checkbox" name="whatsapp_package_payment" value="yes" @if($waPay) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waPay ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_package_payment_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_package_payment_template', $setting->whatsapp_package_payment_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emPay = old('email_package_payment', $setting->email_package_payment) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_package_payment" value="no">
                                            <input type="checkbox" name="email_package_payment" value="yes" @if($emPay) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emPay ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_package_payment_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_package_payment_template', $setting->email_package_payment_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 4: Pembayaran disetujui admin ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">✅</span>
                                    <span class="event-title">Pembayaran disetujui</span>
                                    <div class="event-desc ms-4">ke pengguna, saat admin approve</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waAppr = old('whatsapp_approval_payment', $setting->whatsapp_approval_payment) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_approval_payment" value="no">
                                            <input type="checkbox" name="whatsapp_approval_payment" value="yes" @if($waAppr) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waAppr ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_approval_payment_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_approval_payment_template', $setting->whatsapp_approval_payment_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emAppr = old('email_approval_payment', $setting->email_approval_payment) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_approval_payment" value="no">
                                            <input type="checkbox" name="email_approval_payment" value="yes" @if($emAppr) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emAppr ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_approval_payment_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_approval_payment_template', $setting->email_approval_payment_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 5: Notifikasi ke admin ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">🔔</span>
                                    <span class="event-title">Notifikasi ke admin</span>
                                    <div class="event-desc ms-4">ke admin, ada transaksi masuk</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waUser = old('whatsapp_package_user', $setting->whatsapp_package_user) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_package_user" value="no">
                                            <input type="checkbox" name="whatsapp_package_user" value="yes" @if($waUser) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waUser ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_package_user_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_package_user_template', $setting->whatsapp_package_user_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emUser = old('email_package_user', $setting->email_package_user) === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_package_user" value="no">
                                            <input type="checkbox" name="email_package_user" value="yes" @if($emUser) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emUser ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_package_user_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_package_user_template', $setting->email_package_user_template) == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 6: Paket akan berakhir (BARU) ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">⏰</span>
                                    <span class="event-title">Paket akan berakhir</span>
                                    <span class="badge-new">Baru</span>
                                    <div class="event-desc ms-4">otomatis H-7, H-3, H-1 sebelum expire</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waExpiry = old('whatsapp_expiry_reminder', $setting->whatsapp_expiry_reminder ?? 'no') === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_expiry_reminder" value="no">
                                            <input type="checkbox" name="whatsapp_expiry_reminder" value="yes" @if($waExpiry) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waExpiry ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_expiry_reminder_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_expiry_reminder_template', $setting->whatsapp_expiry_reminder_template ?? '') == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emExpiry = old('email_expiry_reminder', $setting->email_expiry_reminder ?? 'no') === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_expiry_reminder" value="no">
                                            <input type="checkbox" name="email_expiry_reminder" value="yes" @if($emExpiry) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emExpiry ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_expiry_reminder_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_expiry_reminder_template', $setting->email_expiry_reminder_template ?? '') == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- ─── ROW 7: Paket sudah berakhir (BARU) ─── --}}
                            <tr>
                                <td>
                                    <span class="event-icon">⚠️</span>
                                    <span class="event-title">Paket sudah berakhir</span>
                                    <span class="badge-new">Baru</span>
                                    <div class="event-desc ms-4">ke pengguna, H+1 setelah expire</div>
                                </td>
                                <td class="channel-cell">
                                    @php $waExpired = old('whatsapp_expired_reminder', $setting->whatsapp_expired_reminder ?? 'no') === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="whatsapp_expired_reminder" value="no">
                                            <input type="checkbox" name="whatsapp_expired_reminder" value="yes" @if($waExpired) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $waExpired ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="whatsapp_expired_reminder_template">
                                        <option value="">— Pilih template WA —</option>
                                        @foreach ($watemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('whatsapp_expired_reminder_template', $setting->whatsapp_expired_reminder_template ?? '') == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="channel-cell">
                                    @php $emExpired = old('email_expired_reminder', $setting->email_expired_reminder ?? 'no') === 'yes'; @endphp
                                    <div class="toggle-row">
                                        <label class="notif-switch">
                                            <input type="hidden" name="email_expired_reminder" value="no">
                                            <input type="checkbox" name="email_expired_reminder" value="yes" @if($emExpired) checked @endif
                                                   onchange="this.previousElementSibling.value = this.checked ? 'yes' : 'no'">
                                            <span class="notif-slider"></span>
                                        </label>
                                        <span class="toggle-label">{{ $emExpired ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                    <select class="tpl-select" name="email_expired_reminder_template">
                                        <option value="">— Pilih template email —</option>
                                        @foreach ($mailtemplates as $t)
                                        <option value="{{ $t->id }}" @if(old('email_expired_reminder_template', $setting->email_expired_reminder_template ?? '') == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-end py-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy fs-16 me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Update label teks Aktif/Nonaktif saat toggle berubah
document.querySelectorAll('.notif-switch input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var label = this.closest('.toggle-row').querySelector('.toggle-label');
        if (label) label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
});
</script>
@endsection
