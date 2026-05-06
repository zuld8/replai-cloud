@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <x-validation-component></x-validation-component>

        <form action="{{ route('flow.edit', $flow->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-info-circle me-2"></i>Informasi Dasar</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Flow</label>
                            <input type="text" name="name" class="form-control" value="{{ $flow->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $flow->status === 'active' ? 'selected' : '' }}>✅ Aktif</option>
                                <option value="inactive" {{ $flow->status === 'inactive' ? 'selected' : '' }}>❌ Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Keyword Trigger</label>
                            <input type="text" name="keyword" class="form-control" value="{{ $flow->keyword }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tipe Flow</label>
                            <select name="flow_type" class="form-select">
                                @foreach(['payment'=>'💳 Payment Flow','form'=>'📋 Form Flow','order'=>'🛒 Order Flow','booking'=>'📅 Booking Flow'] as $val=>$label)
                                <option value="{{ $val }}" {{ $flow->flow_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            {{-- WABA Device --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bxl-whatsapp me-2 text-success"></i>Nomor WhatsApp</div>
                </div>
                <div class="card-body">
                    @if(isset($wabaDevices) && $wabaDevices->count() > 0)
                    <div class="row g-2">
                        @foreach($wabaDevices as $device)
                        @php $selected = $flow->select_waba && str_contains($flow->select_waba, $device->id); @endphp
                        <div class="col-md-4">
                            <div class="form-check border rounded p-2 {{ $selected ? 'border-success bg-success bg-opacity-10' : '' }}">
                                <input class="form-check-input" type="checkbox"
                                    name="select_waba[]" value="{{ $device->id }}"
                                    id="dev_{{ $device->id }}" {{ $selected ? 'checked' : '' }}>
                                <label class="form-check-label" for="dev_{{ $device->id }}">
                                    <i class="bx bxl-whatsapp text-success me-1"></i>
                                    {{ $device->phone }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payment Config --}}
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bx bx-credit-card me-2"></i>Konfigurasi Payment</div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabQris">QRIS Statis</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabBank">Rekening Bank</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabQris">
                            @if($flow->qris_image)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">QRIS Saat Ini</label><br>
                                <img src="{{ asset($flow->qris_image) }}" class="img-fluid rounded-3 border" style="max-height:180px">
                            </div>
                            @endif
                            <label class="form-label fw-semibold">Ganti QRIS (opsional)</label>
                            <input type="file" name="qris_image" class="form-control" accept="image/*">
                        </div>
                        <div class="tab-pane fade" id="tabBank">
                            <div id="bankAccounts">
                                @if($flow->payment_accounts)
                                    @foreach($flow->payment_accounts as $acc)
                                    <div class="row g-2 mb-2 bank-row">
                                        <div class="col-md-3"><input type="text" name="bank_name[]" class="form-control" value="{{ $acc['bank'] ?? '' }}" placeholder="Bank"></div>
                                        <div class="col-md-4"><input type="text" name="account_number[]" class="form-control" value="{{ $acc['number'] ?? '' }}" placeholder="Nomor Rekening"></div>
                                        <div class="col-md-4"><input type="text" name="account_owner[]" class="form-control" value="{{ $acc['owner'] ?? '' }}" placeholder="Nama Pemilik"></div>
                                        <div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="removeBank(this)"><i class="bx bx-trash"></i></button></div>
                                    </div>
                                    @endforeach
                                @else
                                <div class="row g-2 mb-2 bank-row">
                                    <div class="col-md-3"><input type="text" name="bank_name[]" class="form-control" placeholder="Bank"></div>
                                    <div class="col-md-4"><input type="text" name="account_number[]" class="form-control" placeholder="Nomor Rekening"></div>
                                    <div class="col-md-4"><input type="text" name="account_owner[]" class="form-control" placeholder="Nama Pemilik"></div>
                                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="removeBank(this)"><i class="bx bx-trash"></i></button></div>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addBank()">
                                <i class="bx bx-plus me-1"></i> Tambah Rekening
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card mb-4">
                <div class="card-header"><div class="card-title">Pesan</div></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pesan Pembuka</label>
                        <textarea name="message_open" class="form-control" rows="3">{{ $flow->message_open }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pesan Penutup</label>
                        <textarea name="message_close" class="form-control" rows="3">{{ $flow->message_close }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('flow') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Flow</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
