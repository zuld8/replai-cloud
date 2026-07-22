@extends('layouts.starter')

@section('content')

<style>
.biz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
}
.biz-card {
    background: #fff;
    border: 1px solid #e4eaf2;
    border-radius: 16px;
    padding: 24px 24px 20px;
    position: relative;
    transition: box-shadow .18s, transform .18s;
    display: flex; flex-direction: column;
}
.biz-card:hover  { box-shadow: 0 6px 28px rgba(30,42,74,.12); transform: translateY(-3px); }
.biz-card.biz-danger  { border-color: #fca5a5; }
.biz-card.biz-warning { border-color: #fcd34d; }

.biz-avatar {
    width: 52px; height: 52px; border-radius: 14px;
    font-weight: 700; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.biz-name-wrap { display: flex; align-items: center; gap: 5px; margin-bottom: 3px; flex-wrap: wrap; }
.biz-name { font-size: 16px; font-weight: 700; color: #1e2a4a; line-height: 1.3; }
.biz-sub  { font-size: 12px; color: #64748b; }

/* Pencil icon */
.biz-edit-btn {
    background: none; border: none; padding: 0 2px;
    color: #b0bac9; cursor: pointer; font-size: 13px;
    line-height: 1; transition: color .15s;
    display: inline-flex; align-items: center;
}
.biz-edit-btn:hover { color: #2E8DE1; }

/* Inline input */
.biz-name-input {
    font-size: 16px; font-weight: 700; color: #1e2a4a;
    border: none; border-bottom: 2px solid #2E8DE1;
    outline: none; background: transparent;
    width: 100%; padding: 0; line-height: 1.3;
    max-width: 200px;
}

.biz-badge {
    display: inline-block; font-size: 11px; font-weight: 700;
    padding: 2px 9px; border-radius: 20px;
    vertical-align: middle; line-height: 1.7;
}
.biz-badge-ok      { background:#dcfce7; color:#16a34a; }
.biz-badge-warning { background:#fef9c3; color:#b26b0b; }
.biz-badge-danger  { background:#fee2e2; color:#dc2626; }
.biz-badge-none    { background:#f1f5f9; color:#64748b; }

.biz-progress {
    height: 6px; border-radius: 20px;
    background: #e4eaf2; overflow: hidden; margin: 8px 0 4px;
}
.biz-progress-bar { height: 100%; border-radius: 20px; transition: width .6s ease; }

.biz-actions { display: flex; gap: 10px; margin-top: auto; padding-top: 18px; }
.btn-biz-enter {
    flex: 1; background: #2E8DE1; color: #fff; font-weight: 600;
    border: none; border-radius: 10px; padding: 11px 0;
    text-align: center; font-size: 14px; transition: background .15s;
    text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-biz-enter:hover { background: #1a78c8; color: #fff; }
.btn-biz-renew {
    flex: 1; font-weight: 600; border-radius: 10px; padding: 11px 0;
    text-align: center; font-size: 14px; transition: all .15s;
    text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;
    border: 1px solid #cbd5e1; color: #64748b; background: #fff;
}
.btn-biz-renew:hover          { border-color: #2E8DE1; color: #2E8DE1; background: #eaf3fc; }
.btn-biz-renew.renew-warning  { border-color: #f0c98a; color: #b26b0b; background: #fef8ec; }
.btn-biz-renew.renew-danger   { border-color: #f0a9a9; color: #c0392b; background: #fdf0f0; }
.btn-biz-buy {
    flex: 1; background: #dc2626; color: #fff; font-weight: 600;
    border: none; border-radius: 10px; padding: 11px 0;
    text-align: center; font-size: 14px;
    text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-biz-buy:hover { background: #b91c1c; color: #fff; }

/* Delete link (pindah ke bawah, kecil) */
.biz-del-link {
    font-size: 11px; color: #94a3b8; text-decoration: none;
    display: inline-flex; align-items: center; gap: 3px;
    margin-top: 10px; padding: 3px 0;
    transition: color .15s;
}
.biz-del-link:hover { color: #dc2626; }

.biz-card-new {
    background: #fff; border: 2px dashed #c4cde0; border-radius: 16px;
    min-height: 180px; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 28px; text-decoration: none;
    transition: border-color .15s, background .15s;
}
.biz-card-new:hover { border-color: #2E8DE1; background: #f0f7ff; }
.biz-new-icon {
    width: 46px; height: 46px; border-radius: 50%;
    background: #e7ecfd; color: #2f4bd4;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; font-size: 22px;
}
</style>

<x-validation-component></x-validation-component>

<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e2a4a;font-size:20px;">Bisnis kamu</h5>
    <p class="text-muted mb-0" style="font-size:14px;">Pilih bisnis untuk masuk, atau buat yang baru.</p>
</div>

<div class="biz-grid">
    @foreach ($businesses as $business)
    @php
        $rem        = (int)($business->remaining_day ?? 0);
        $prog       = (int)($business->progress_day  ?? 0);
        $hasPkg     = (bool)$business->package_active;
        $isUnlimited= $hasPkg && ($business->package_active->days_option ?? '') !== 'limited';

        if (!$hasPkg)         $state = 'none';
        elseif ($isUnlimited) $state = 'ok';
        elseif ($rem < 3)     $state = 'danger';
        elseif ($rem < 8)     $state = 'warning';
        else                  $state = 'ok';

        $words    = preg_split('/\s+/', trim($business->name));
        $init     = strtoupper(mb_substr($words[0]??'B',0,1).(isset($words[1])?mb_substr($words[1],0,1):''));
        $barColor = ['ok'=>'#16a34a','warning'=>'#f59e0b','danger'=>'#dc2626','none'=>'#cbd5e1'][$state];
        $avatarBg = ['ok'=>'#dcfce7','warning'=>'#fef9c3','danger'=>'#fee2e2','none'=>'#e7ecfd'][$state];
        $avatarTx = ['ok'=>'#16a34a','warning'=>'#b26b0b','danger'=>'#c0392b','none'=>'#2f4bd4'][$state];
        $pkgName  = $business->package_active->package->name ?? null;
        $pkgPrice = $business->package_active->price ?? 0;
        $expDate  = $business->package_active->expire_date ?? null;
    @endphp

    <div class="biz-card {{ $state==='danger'?'biz-danger':($state==='warning'?'biz-warning':'') }}">

        {{-- Identitas --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="biz-avatar" style="background:{{ $avatarBg }};color:{{ $avatarTx }};">{{ $init }}</div>
            <div style="min-width:0;flex:1;">
                <div class="biz-name-wrap">
                    {{-- Nama (mode tampil) --}}
                    <span class="biz-name" data-biz-id="{{ $business->id }}">{{ $business->name }}</span>
                    {{-- Input (mode edit, hidden) --}}
                    <input type="text" class="biz-name-input"
                           value="{{ $business->name }}"
                           data-biz-id="{{ $business->id }}"
                           data-rename-url="{{ route('starter.business.rename', $business->id) }}"
                           style="display:none;"
                           maxlength="100">
                    {{-- Pencil --}}
                    <button class="biz-edit-btn" data-biz-id="{{ $business->id }}" title="Edit nama">
                        <i class="bx bx-pencil"></i>
                    </button>
                    @if($state==='ok')     <span class="biz-badge biz-badge-ok">Aktif</span>
                    @elseif($state==='warning') <span class="biz-badge biz-badge-warning">Mau habis</span>
                    @elseif($state==='danger')  <span class="biz-badge biz-badge-danger">Segera habis</span>
                    @else                  <span class="biz-badge biz-badge-none">Belum ada paket</span>
                    @endif
                </div>
                <div class="biz-sub">
                    @if($hasPkg && $pkgName)
                        {{ $pkgName }} &middot; Rp{{ number_format($pkgPrice) }}/bln
                    @else
                        Beli paket untuk mulai
                    @endif
                </div>
            </div>
        </div>

        {{-- Masa aktif --}}
        @if($hasPkg && !$isUnlimited)
            <div class="mb-2">
                <div class="d-flex justify-content-between" style="font-size:12px;">
                    <span class="text-muted">Masa aktif</span>
                    <span class="fw-semibold" style="color:{{ $barColor }};">{{ number_format($rem) }} hari lagi</span>
                </div>
                <div class="biz-progress">
                    <div class="biz-progress-bar" style="width:{{ $prog }}%;background:{{ $barColor }};"></div>
                </div>
                <div style="font-size:11px;color:{{ in_array($state,['warning','danger'])?$barColor:'#94a3b8' }};">
                    @if($state==='ok') Berakhir {{ \Carbon\Carbon::parse($expDate)->isoFormat('D MMM YYYY') }}
                    @else Perpanjang biar gak terputus
                    @endif
                </div>
            </div>
        @elseif($hasPkg && $isUnlimited)
            <div class="mb-2" style="font-size:12px;color:#16a34a;">
                <i class="bx bx-infinite me-1"></i><strong>Paket selamanya</strong>
            </div>
        @else
            <div class="mb-2" style="font-size:12px;color:#ef4444;">
                <i class="bx bx-error-circle me-1"></i>Tidak ada paket aktif
            </div>
        @endif

        {{-- Aksi --}}
        <div class="biz-actions">
            @if($hasPkg)
                <a href="{{ route('starter.business.choose', $business->id) }}"
                   class="btn-biz-enter">
                    <i class="bx bx-log-in-circle"></i> Masuk
                </a>
                <a href="{{ route('starter.business.detail', $business->id) }}"
                   class="btn-biz-renew {{ $state==='danger'?'renew-danger':($state==='warning'?'renew-warning':'') }}">
                    <i class="bx bx-refresh"></i> Perpanjang
                </a>
            @else
                <a href="{{ route('starter.business.detail', $business->id) }}" class="btn-biz-buy">
                    <i class="bx bx-cart"></i> Beli paket
                </a>
            @endif
        </div>
</div>
    @endforeach

    {{-- Buat baru --}}
    <a href="{{ route('starter.business.create') }}" class="biz-card-new">
        <div class="biz-new-icon"><i class="bx bx-plus"></i></div>
        <div class="fw-bold" style="color:#1e2a4a;font-size:14px;">Buat bisnis baru</div>
        <div class="text-muted" style="font-size:12px;margin-top:3px;">Kelola beberapa toko dalam 1 akun</div>
    </a>
</div>

@endsection

@section('scripts')
<script>
// ── Inline rename ──────────────────────────────────────────────
$(document).on('click', '.biz-edit-btn', function() {
    const id   = $(this).data('biz-id');
    const wrap = $(this).closest('.biz-name-wrap');
    const span = wrap.find('.biz-name[data-biz-id="' + id + '"]');
    const inp  = wrap.find('.biz-name-input[data-biz-id="' + id + '"]');
    span.hide();
    $(this).hide();
    inp.show().focus().select();
});

$(document).on('blur keydown', '.biz-name-input', function(e) {
    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== 'Escape') return;
    const inp  = $(this);
    const wrap = inp.closest('.biz-name-wrap');
    const id   = inp.data('biz-id');
    const span = wrap.find('.biz-name[data-biz-id="' + id + '"]');
    const btn  = wrap.find('.biz-edit-btn[data-biz-id="' + id + '"]');
    const url  = inp.data('rename-url');
    const name = inp.val().trim();

    // Escape = batalkan
    if (e.type === 'keydown' && e.key === 'Escape') {
        inp.val(span.text().trim()).hide();
        span.show(); btn.show(); return;
    }

    if (!name) { inp.focus(); return; }

    // AJAX rename
    $.ajax({
        url: url,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', name: name },
        success: function(res) {
            if (res.success) {
                span.text(res.name);
                // Update avatar inisial
                const words = res.name.trim().split(/\s+/);
                const init  = (words[0][0] + (words[1] ? words[1][0] : '')).toUpperCase();
                inp.closest('.biz-card').find('.biz-avatar').text(init);
            }
        },
        error: function() {
            inp.val(span.text().trim()); // rollback
        },
        complete: function() {
            inp.hide(); span.show(); btn.show();
        }
    });
});
});

// ── Hapus bisnis ──────────────────────────────────────────────
$(".deletebutton").on("click", function(e) {
    e.preventDefault();
    const href = $(this).attr("href");
    Swal.fire({
        title: "{{ __('starter.delete_business_confirm') }}",
        text: "{{ __('starter.delete_business_warning') }}",
        icon: "warning", showCancelButton: true,
        confirmButtonColor: "#d33", cancelButtonColor: "#3085d6",
        confirmButtonText: "{{ __('starter.yes_delete') }}",
        cancelButtonText: "{{ __('starter.cancel') }}"
    }).then(r => { if (r.value) location.href = href; });
});
</script>
@endsection
