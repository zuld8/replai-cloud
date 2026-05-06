@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <x-validation-component></x-validation-component>

        <form action="{{ route('flow.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- SECTION 1: Informasi Dasar --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-info-circle me-2"></i>Informasi Dasar</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Flow <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="cth: Payment QRIS Toko" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">✅ Aktif</option>
                                <option value="inactive">❌ Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Keyword Trigger <span class="text-danger">*</span></label>
                            <input type="text" name="keyword" class="form-control"
                                   placeholder="bayar, transfer, qris (pisah dengan koma)" required>
                            <small class="text-muted">Pisah dengan koma. Customer ketik salah satu → Flow aktif.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Tipe Flow --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-category me-2"></i>Tipe Flow</div>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="flowTypeCards">
                        @foreach([
                            ['payment','💳','Payment Flow','Kirim QRIS & rekening bank otomatis'],
                            ['form','📋','Form Flow','Kumpulkan data customer via chat'],
                            ['order','🛒','Order Flow','Otomasi proses pemesanan'],
                            ['booking','📅','Booking Flow','Jadwalkan janji/reservasi'],
                        ] as [$val,$icon,$label,$desc])
                        <div class="col-md-3">
                            <div class="flow-type-card border rounded-3 p-3 text-center cursor-pointer {{ $loop->first ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                                 data-type="{{ $val }}" style="cursor:pointer">
                                <div class="fs-2 mb-1">{{ $icon }}</div>
                                <div class="fw-semibold">{{ $label }}</div>
                                <small class="text-muted">{{ $desc }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="flow_type" id="selectedFlowType" value="payment">
                </div>
            </div>


            {{-- WABA Device Selector --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bxl-whatsapp me-2 text-success"></i>Pilih Nomor WhatsApp</div>
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Aktifkan Flow untuk nomor WA mana?</label>
                    @if(isset($wabaDevices) && $wabaDevices->count() > 0)
                    <div class="row g-2">
                        @foreach($wabaDevices as $device)
                        <div class="col-md-4">
                            <div class="form-check border rounded p-2">
                                <input class="form-check-input" type="checkbox"
                                    name="select_waba[]" value="{{ $device->id }}"
                                    id="dev_{{ $device->id }}">
                                <label class="form-check-label" for="dev_{{ $device->id }}">
                                    <i class="bx bxl-whatsapp text-success me-1"></i>
                                    {{ $device->phone }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="text-muted mt-1 d-block">Centang nomor yang akan merespons keyword ini</small>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        Belum ada device WABA aktif. Tambahkan device di <a href="#">Pengaturan WABA</a>.
                    </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 3: Konfigurasi Payment (default shown) --}}
            <div class="card custom-card mb-4" id="paymentConfig">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-credit-card me-2"></i>Konfigurasi Payment</div>
                </div>
                <div class="card-body">
                    {{-- Sub-tabs --}}
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabQris">QRIS Statis</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabBank">Rekening Bank</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#">QRIS Dinamis <span class="badge bg-secondary ms-1">Segera</span></a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Tab QRIS --}}
                        <div class="tab-pane fade show active" id="tabQris">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Upload Gambar QRIS</label>
                                    <div class="border rounded-3 p-4 text-center bg-light" id="qrisDropZone"
                                         style="border-style:dashed!important;cursor:pointer"
                                         onclick="document.getElementById('qris_image').click()">
                                        <i class="bx bx-qr-scan fs-1 text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Klik untuk upload gambar QRIS</p>
                                        <small class="text-muted">PNG, JPG (maks 2MB)</small>
                                    </div>
                                    <input type="file" name="qris_image" id="qris_image"
                                           class="d-none" accept="image/*"
                                           onchange="previewQris(this)">
                                </div>
                                <div class="col-md-6">
                                    <div id="qrisPreview" class="d-none">
                                        <label class="form-label fw-semibold">Preview QRIS</label>
                                        <img id="qrisImg" src="" class="img-fluid rounded-3 border" style="max-height:200px">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100"
                                                onclick="clearQris()">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab Rekening Bank --}}
                        <div class="tab-pane fade" id="tabBank">
                            <div id="bankAccounts">
                                <div class="row g-2 mb-2 bank-row">
                                    <div class="col-md-3">
                                        <input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank (BCA, BSI...)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="account_number[]" class="form-control" placeholder="Nomor Rekening">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="account_owner[]" class="form-control" placeholder="Nama Pemilik">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-remove-bank" onclick="removeBank(this)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addBank()">
                                <i class="bx bx-plus me-1"></i> Tambah Rekening
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Pesan --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-message-detail me-2"></i>Pesan</div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pesan Pembuka</label>
                        <textarea name="message_open" class="form-control" rows="3"
                                  placeholder="Halo! Berikut info pembayaran kami:"></textarea>
                        <small class="text-muted">Dikirim sebelum QRIS/rekening</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pesan Penutup</label>
                        <textarea name="message_close" class="form-control" rows="3"
                                  placeholder="Konfirmasi ke admin setelah transfer ya! 😊"></textarea>
                        <small class="text-muted">Dikirim setelah QRIS/rekening</small>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2">
                <a href="{{ route('flow') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Simpan Flow
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Flow type card selection
document.querySelectorAll('.flow-type-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.flow-type-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        });
        this.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        const type = this.dataset.type;
        document.getElementById('selectedFlowType').value = type;
        // Show/hide payment config
        document.getElementById('paymentConfig').style.display = type === 'payment' ? '' : 'none';
    });
});

// QRIS preview
function previewQris(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('qrisImg').src = e.target.result;
            document.getElementById('qrisPreview').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function clearQris() {
    document.getElementById('qris_image').value = '';
    document.getElementById('qrisPreview').classList.add('d-none');
}

// Add bank row
function addBank() {
    const row = document.querySelector('.bank-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    document.getElementById('bankAccounts').appendChild(row);
}
function removeBank(btn) {
    const rows = document.querySelectorAll('.bank-row');
    if (rows.length > 1) btn.closest('.bank-row').remove();
}
</script>
@endsection
