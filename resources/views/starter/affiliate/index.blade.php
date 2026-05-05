@extends('layouts.starter')

@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
* { font-family: 'Inter', sans-serif; }

/* ── Subtle Hero ──────────────────────────── */
.aff-hero {
    background: linear-gradient(120deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);
    border: 1px solid #d1fae5;
    border-radius: 16px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
}
.aff-hero::before {
    content:'';position:absolute;top:-40px;right:-40px;
    width:160px;height:160px;border-radius:50%;
    background:radial-gradient(circle,rgba(16,185,129,.08) 0%,transparent 70%);
}
.aff-commission-pill {
    display:inline-flex;align-items:center;gap:6px;
    background:#dcfce7;border:1px solid #bbf7d0;
    border-radius:100px;padding:3px 12px;
    font-size:.72rem;font-weight:700;color:#065f46;
    margin-bottom:.6rem;
}
.aff-commission-pill .dot {
    width:6px;height:6px;background:#10b981;border-radius:50%;
}
.aff-hero-title {
    font-size:1.35rem;font-weight:800;color:#111827;
    margin-bottom:.3rem;line-height:1.3;
}
.aff-hero-sub { font-size:.82rem;color:#6b7280;margin-bottom:1.1rem;line-height:1.6; }

/* ── Ref Link ──────────────────────────────── */
.aff-refbox {
    background:white;border:1.5px solid #d1fae5;
    border-radius:12px;padding:.85rem 1.1rem;
    display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap;
}
.aff-refbox-url { font-size:.83rem;font-weight:600;color:#059669;word-break:break-all; }
.aff-refbox-meta { font-size:.7rem;color:#9ca3af;margin-top:2px; }
.aff-btn {
    display:inline-flex;align-items:center;gap:6px;
    background:#f0fdf4;border:1.5px solid #bbf7d0;
    border-radius:8px;padding:.4rem .9rem;
    font-size:.75rem;font-weight:700;color:#059669;
    cursor:pointer;transition:all .18s;white-space:nowrap;
}
.aff-btn:hover { background:#dcfce7;border-color:#6ee7b7; }
.aff-btn-primary {
    background:#10b981;border-color:#10b981;color:white;
}
.aff-btn-primary:hover { background:#059669;border-color:#059669; }

/* ── Stat Cards ─────────────────────────────── */
.aff-stat {
    background:white;border:1px solid #f3f4f6;
    border-radius:14px;padding:1.1rem 1.25rem;
    box-shadow:0 1px 8px rgba(0,0,0,.04);
    transition:box-shadow .2s,transform .2s;
}
.aff-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,.08);transform:translateY(-2px); }
.aff-stat-icon {
    width:38px;height:38px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.1rem;margin-bottom:.6rem;
}
.aff-stat-label { font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-bottom:.15rem; }
.aff-stat-value { font-size:1.35rem;font-weight:800;line-height:1; }
.aff-stat-sub { font-size:.68rem;color:#9ca3af;margin-top:.25rem; }

/* ── Section Cards ──────────────────────────── */
.aff-card {
    background:white;border:1px solid #f3f4f6;
    border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.04);
    overflow:hidden;
}
.aff-card-header {
    padding:.9rem 1.2rem;border-bottom:1px solid #f9fafb;
    display:flex;align-items:center;justify-content:space-between;
}
.aff-card-title { font-size:.84rem;font-weight:700;color:#111827; }
.aff-card-body { padding:1.1rem 1.2rem; }

/* ── How It Works ───────────────────────────── */
.aff-step {
    display:flex;align-items:flex-start;gap:.85rem;
    padding:.8rem 0;border-bottom:1px solid #f9fafb;
}
.aff-step:last-child { border-bottom:none;padding-bottom:0; }
.aff-step-num {
    width:30px;height:30px;border-radius:50%;flex-shrink:0;
    background:linear-gradient(135deg,#10b981,#059669);
    color:white;font-size:.75rem;font-weight:700;
    display:flex;align-items:center;justify-content:center;
}
.aff-step-title { font-size:.82rem;font-weight:700;color:#111827;margin-bottom:.15rem; }
.aff-step-desc { font-size:.74rem;color:#6b7280;line-height:1.5; }

/* ── Commission Info ────────────────────────── */
.aff-comm-card {
    background:#fafafa;border:1px solid #f3f4f6;
    border-radius:14px;padding:1.1rem 1.2rem;
}
.aff-comm-row {
    display:flex;justify-content:space-between;align-items:center;
    padding:.6rem 0;border-bottom:1px solid #f3f4f6;font-size:.8rem;
}
.aff-comm-row:last-child { border-bottom:none;padding-bottom:0; }
.aff-comm-badge {
    background:#dcfce7;color:#065f46;
    border-radius:100px;padding:2px 10px;
    font-size:.72rem;font-weight:700;
}

/* ── Calculator ─────────────────────────────── */
.aff-calc { background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:1rem; }
.aff-calc label { font-size:.7rem;font-weight:600;color:#64748b;margin-bottom:3px;display:block; }
.aff-calc .form-control,.aff-calc .form-select {
    font-size:.79rem;border-radius:8px;border:1px solid #e2e8f0;
    padding:.4rem .65rem;
}
.aff-calc-result {
    background:white;border:1px solid #d1fae5;border-radius:10px;
    padding:.65rem .85rem;margin-top:.75rem;
}

/* ── WD Form ────────────────────────────────── */
.aff-wd-label { font-size:.72rem;font-weight:600;color:#374151;margin-bottom:3px;display:block; }
.form-control-aff,.form-select-aff {
    font-size:.8rem;border-radius:9px;border:1.5px solid #e5e7eb;
    padding:.45rem .7rem;width:100%;
    transition:border-color .18s;
}
.form-control-aff:focus,.form-select-aff:focus {
    outline:none;border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,.1);
}
.aff-submit-btn {
    width:100%;background:linear-gradient(135deg,#10b981,#059669);
    color:white;border:none;border-radius:9px;
    padding:.6rem;font-size:.82rem;font-weight:700;
    cursor:pointer;transition:all .18s;
}
.aff-submit-btn:hover { transform:translateY(-1px);box-shadow:0 4px 14px rgba(16,185,129,.3); }
.aff-submit-btn:disabled { opacity:.5;cursor:not-allowed;transform:none; }

/* ── Table ──────────────────────────────────── */
.aff-table { width:100%;border-collapse:collapse; }
.aff-table th { padding:.55rem 1rem;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;background:#f9fafb;text-align:left;white-space:nowrap; }
.aff-table td { padding:.65rem 1rem;font-size:.77rem;border-top:1px solid #f3f4f6; }
.aff-table tr:hover td { background:#fafafa; }

/* ── Pills ──────────────────────────────────── */
.pill { border-radius:100px;padding:2px 9px;font-size:.67rem;font-weight:700; }
.pill-pending  { background:#fef3c7;color:#92400e; }
.pill-available{ background:#d1fae5;color:#065f46; }
.pill-withdrawn{ background:#e0e7ff;color:#3730a3; }
.pill-reversed { background:#fee2e2;color:#991b1b; }

/* ── Empty ──────────────────────────────────── */
.aff-empty { text-align:center;padding:2.5rem 1rem; }
.aff-empty-icon { font-size:2.5rem;margin-bottom:.6rem; }
.aff-empty-text { font-size:.79rem;color:#9ca3af;line-height:1.6; }
</style>
@endsection

@section('content')

{{-- ═══ HERO ═══════════════════════════════════ --}}
<div class="aff-hero">
    <div style="position:relative;z-index:1;">
        <div class="aff-commission-pill">
            <span class="dot"></span>
            Program Affiliate Aktif &nbsp;·&nbsp; Komisi <strong>15%</strong> flat per transaksi
        </div>
        <div class="aff-hero-title">Hasilkan Uang dari Referral Kamu 💸</div>
        <div class="aff-hero-sub">
            Ajak teman pakai Replai → mereka bayar → kamu dapat komisi <strong>15%</strong> otomatis.<br>
            Berlaku hingga bulan ke-6, min. WD Rp 100.000.
        </div>

        <div class="aff-refbox">
            <div>
                <div class="aff-refbox-url">🔗 {{ $ref_url }}</div>
                <div class="aff-refbox-meta">
                    Kode: <strong>{{ $affiliate->code }}</strong>
                    &nbsp;·&nbsp; {{ number_format($affiliate->total_click) }} klik
                    &nbsp;·&nbsp; {{ number_format($affiliate->total_referral) }} referral
                </div>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <button class="aff-btn aff-btn-primary" id="copyLinkBtn"
                    onclick="navigator.clipboard.writeText('{{ $ref_url }}').then(()=>{
                        const b=document.getElementById('copyLinkBtn');
                        b.innerHTML='✅ Copied!';
                        setTimeout(()=>{b.innerHTML='📋 Copy Link';},2000);
                    })">📋 Copy Link</button>
                <button class="aff-btn"
                    onclick="navigator.clipboard.writeText('{{ $affiliate->code }}').then(()=>{
                        this.innerHTML='✅ Done!';
                        setTimeout(()=>{this.innerHTML='🏷️ Kode';},2000);
                    })">🏷️ Kode</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ STATS ══════════════════════════════════ --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-sm-6">
        <div class="aff-stat">
            <div class="aff-stat-icon" style="background:#f0fdf4;">💰</div>
            <div class="aff-stat-label">Saldo Tersedia</div>
            <div class="aff-stat-value" style="color:#059669;">Rp {{ number_format($available,0,',','.') }}</div>
            <div class="aff-stat-sub">Bisa ditarik sekarang</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="aff-stat">
            <div class="aff-stat-icon" style="background:#fffbeb;">⏳</div>
            <div class="aff-stat-label">Komisi Pending</div>
            <div class="aff-stat-value" style="color:#d97706;">Rp {{ number_format($pending,0,',','.') }}</div>
            <div class="aff-stat-sub">Cair otomatis 30 hari</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="aff-stat">
            <div class="aff-stat-icon" style="background:#eff6ff;">📈</div>
            <div class="aff-stat-label">Total Earned</div>
            <div class="aff-stat-value" style="color:#2563eb;">Rp {{ number_format($total_earned,0,',','.') }}</div>
            <div class="aff-stat-sub">Sepanjang waktu</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="aff-stat">
            <div class="aff-stat-icon" style="background:#f5f3ff;">🏦</div>
            <div class="aff-stat-label">Total Dicairkan</div>
            <div class="aff-stat-value" style="color:#7c3aed;">Rp {{ number_format($total_withdrawn,0,',','.') }}</div>
            <div class="aff-stat-sub">Sudah ditransfer</div>
        </div>
    </div>
</div>

{{-- ═══ INFO ROW ════════════════════════════════ --}}
<div class="row g-3 mb-3">
    {{-- Cara Kerja --}}
    <div class="col-lg-5">
        <div class="aff-card h-100">
            <div class="aff-card-header">
                <div class="aff-card-title">📖 Cara Kerja Affiliate</div>
                <span style="font-size:.69rem;color:#9ca3af;">4 langkah mudah</span>
            </div>
            <div class="aff-card-body">
                <div class="aff-step">
                    <div class="aff-step-num">1</div>
                    <div>
                        <div class="aff-step-title">🔗 Bagikan Link Referral</div>
                        <div class="aff-step-desc">Share link atau kode unikmu ke WhatsApp, media sosial, atau langsung ke calon pelanggan.</div>
                    </div>
                </div>
                <div class="aff-step">
                    <div class="aff-step-num">2</div>
                    <div>
                        <div class="aff-step-title">👤 Teman Daftar &amp; Beli Paket</div>
                        <div class="aff-step-desc">Temanmu klik link, buat akun baru, lalu subscribe salah satu paket Replai.</div>
                    </div>
                </div>
                <div class="aff-step">
                    <div class="aff-step-num">3</div>
                    <div>
                        <div class="aff-step-title">💰 Komisi 15% Masuk Otomatis</div>
                        <div class="aff-step-desc">Setiap pembayaran berhasil, kamu dapat <strong>15% flat</strong>. Berlaku sampai bulan ke-6 per referral.</div>
                    </div>
                </div>
                <div class="aff-step">
                    <div class="aff-step-num">4</div>
                    <div>
                        <div class="aff-step-title">🏦 Cairkan ke Rekening</div>
                        <div class="aff-step-desc">Setelah 30 hari holding, cairkan min. Rp 100.000 ke rekening atau e-wallet manapun.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Komisi + Kalkulator --}}
    <div class="col-lg-7">
        <div class="aff-card h-100">
            <div class="aff-card-header">
                <div class="aff-card-title">💡 Struktur Komisi &amp; Kalkulator</div>
            </div>
            <div class="aff-card-body" style="display:flex;flex-direction:column;gap:.85rem;">
                {{-- Commission rows --}}
                <div class="aff-comm-card">
                    <div class="aff-comm-row">
                        <div>
                            <div style="font-size:.8rem;font-weight:600;color:#111827;">Bulan ke-1 s/d Bulan ke-6</div>
                            <div style="font-size:.7rem;color:#9ca3af;">Setiap pembayaran perpanjangan berhasil</div>
                        </div>
                        <span class="aff-comm-badge">15% / transaksi</span>
                    </div>
                    <div class="aff-comm-row">
                        <span style="font-size:.78rem;color:#374151;">⏰ Holding Period</span>
                        <span style="font-size:.78rem;font-weight:600;color:#374151;">30 hari</span>
                    </div>
                    <div class="aff-comm-row">
                        <span style="font-size:.78rem;color:#374151;">💸 Minimum Withdrawal</span>
                        <span style="font-size:.78rem;font-weight:600;color:#374151;">Rp 100.000</span>
                    </div>
                    <div class="aff-comm-row">
                        <span style="font-size:.78rem;color:#374151;">📅 Maks. Durasi per Referral</span>
                        <span style="font-size:.78rem;font-weight:600;color:#374151;">6 bulan</span>
                    </div>
                </div>

                {{-- Calculator --}}
                <div class="aff-calc">
                    <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.65rem;">🧮 Estimasi Penghasilan Bulanan</div>
                    <div class="row g-2">
                        <div class="col-5">
                            <label>Jumlah Referral</label>
                            <input type="number" id="calcRef" class="form-control" value="5" min="1" style="font-size:.79rem;border-radius:8px;" onchange="calcComm()" oninput="calcComm()">
                        </div>
                        <div class="col-7">
                            <label>Paket (dari Subscription Plan)</label>
                            <select id="calcPkg" class="form-select" style="font-size:.79rem;border-radius:8px;" onchange="calcComm()">
                                @foreach($packages as $pkg)
                                @if($pkg->price > 0 && $pkg->price < 9999999999)
                                <option value="{{ $pkg->price }}">{{ $pkg->name }} — Rp {{ number_format($pkg->price,0,',','.') }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="aff-calc-result">
                        <div style="font-size:.68rem;color:#9ca3af;">Estimasi per bulan (referral × harga paket × 15%)</div>
                        <div style="font-size:1.15rem;font-weight:800;color:#059669;margin:.1rem 0;" id="calcResult">—</div>
                        <div style="font-size:.68rem;color:#9ca3af;" id="calcDetail">—</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ BOTTOM ROW: WD + KOMISI HISTORY ═══════ --}}
<div class="row g-3">
    {{-- WD Form --}}
    <div class="col-lg-4">
        <div class="aff-card">
            <div class="aff-card-header">
                <div class="aff-card-title">💸 Tarik Dana</div>
                <span style="font-size:.69rem;color:#9ca3af;">1–3 hari kerja</span>
            </div>
            <div class="aff-card-body">
                @if(session('flash'))
                <div class="alert alert-success py-2 mb-2" style="font-size:.78rem;border-radius:9px;">✅ {{ session('flash') }}</div>
                @endif
                @if(session('gagal'))
                <div class="alert alert-danger py-2 mb-2" style="font-size:.78rem;border-radius:9px;">❌ {{ session('gagal') }}</div>
                @endif

                @if($available < 100000)
                <div class="aff-empty">
                    <div class="aff-empty-icon">😔</div>
                    <div style="font-size:.83rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Saldo belum mencukupi minimum WD</div>
                    <div class="aff-empty-text">
                        Min. WD: <strong>Rp 100.000</strong><br>
                        Tersedia: <strong style="color:#059669;">Rp {{ number_format($available,0,',','.') }}</strong>
                    </div>
                    @if($pending > 0)
                    <div style="margin-top:.75rem;background:#fffbeb;border:1px solid #fed7aa;border-radius:9px;padding:.6rem;font-size:.73rem;color:#92400e;">
                        ⏳ Rp {{ number_format($pending,0,',','.') }} sedang pending — cair dalam 30 hari
                    </div>
                    @endif
                </div>
                @else
                <form method="POST" action="{{ route('starter.affiliate.withdraw') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="aff-wd-label">Bank / E-Wallet</label>
                        <select name="bank_name" class="form-select-aff" required>
                            <option value="">— Pilih —</option>
                            @foreach(['BCA','BRI','BNI','Mandiri','BSI','CIMB Niaga','Danamon','Permata','BTN','Jenius','Jago','OVO','GoPay','DANA','ShopeePay'] as $bk)
                            <option value="{{ $bk }}">{{ $bk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="aff-wd-label">No. Rekening / Akun</label>
                        <input type="text" name="bank_account" class="form-control-aff" required placeholder="0891234567">
                    </div>
                    <div class="mb-2">
                        <label class="aff-wd-label">Atas Nama</label>
                        <input type="text" name="bank_holder" class="form-control-aff" required placeholder="Nama sesuai rekening">
                    </div>
                    <div class="mb-3">
                        <label class="aff-wd-label">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control-aff" required min="100000" max="{{ $available }}" placeholder="100000">
                        <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">Tersedia: Rp {{ number_format($available,0,',','.') }}</div>
                    </div>
                    <button type="submit" class="aff-submit-btn">💸 Ajukan Penarikan</button>
                </form>
                @endif

                @if($withdrawals->count() > 0)
                <div style="margin-top:1rem;padding-top:.9rem;border-top:1px solid #f3f4f6;">
                    <div style="font-size:.73rem;font-weight:700;color:#374151;margin-bottom:.5rem;">Riwayat Penarikan</div>
                    @foreach($withdrawals->take(5) as $wd)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem 0;border-bottom:1px solid #f9fafb;">
                        <div>
                            <div style="font-size:.75rem;font-weight:600;">{{ $wd->bank_name }}</div>
                            <div style="font-size:.67rem;color:#9ca3af;">{{ $wd->created_at->format('d M Y') }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:.78rem;font-weight:700;">Rp {{ number_format($wd->amount,0,',','.') }}</div>
                            <span class="pill pill-{{ $wd->status }}">{{ ucfirst($wd->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Commission History --}}
    <div class="col-lg-8">
        <div class="aff-card">
            <div class="aff-card-header">
                <div class="aff-card-title">📊 Riwayat Komisi</div>
                @if($commissions->count() > 0)
                <span style="font-size:.69rem;color:#9ca3af;">{{ $commissions->count() }} transaksi</span>
                @endif
            </div>
            @if($commissions->isEmpty())
            <div class="aff-empty">
                <div class="aff-empty-icon">📭</div>
                <div style="font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Belum ada komisi</div>
                <div class="aff-empty-text">Mulai bagikan link referralmu dan komisi akan muncul di sini setelah teman kamu melakukan pembayaran.</div>
            </div>
            @else
            <div style="overflow-x:auto;">
                <table class="aff-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Paket</th>
                            <th style="text-align:center;">Bln</th>
                            <th style="text-align:right;">Nilai Trx</th>
                            <th style="text-align:right;">Komisi (15%)</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $comm)
                        <tr>
                            <td style="white-space:nowrap;color:#6b7280;">{{ \Carbon\Carbon::parse($comm->created_at)->format('d M Y') }}</td>
                            <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $comm->package_name ?: '—' }}</td>
                            <td style="text-align:center;">
                                <span style="background:#e0e7ff;color:#3730a3;border-radius:100px;padding:2px 8px;font-size:.67rem;font-weight:700;">{{ $comm->commission_month }}</span>
                            </td>
                            <td style="text-align:right;color:#6b7280;">Rp {{ number_format($comm->transaction_amount,0,',','.') }}</td>
                            <td style="text-align:right;font-weight:700;color:#059669;">+Rp {{ number_format($comm->amount,0,',','.') }}</td>
                            <td style="text-align:center;">
                                @if($comm->status=='pending')
                                    <span class="pill pill-pending">⏳ Pending</span>
                                    @if($comm->available_at)<div style="font-size:.62rem;color:#9ca3af;margin-top:2px;">{{ \Carbon\Carbon::parse($comm->available_at)->diffForHumans() }}</div>@endif
                                @elseif($comm->status=='available')
                                    <span class="pill pill-available">✅ Cair</span>
                                @elseif($comm->status=='withdrawn')
                                    <span class="pill pill-withdrawn">💸 WD</span>
                                @else
                                    <span class="pill pill-reversed">❌ Batal</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function calcComm(){
    const ref=parseInt(document.getElementById('calcRef').value)||1;
    const pkg=parseFloat(document.getElementById('calcPkg').value)||0;
    if(!pkg){return;}
    const comm=ref*pkg*0.15;
    const fmt=v=>new Intl.NumberFormat('id-ID').format(Math.round(v));
    document.getElementById('calcResult').textContent='Rp '+fmt(comm);
    document.getElementById('calcDetail').textContent=ref+' referral × Rp '+fmt(pkg)+' × 15% = Rp '+fmt(comm)+'/bulan';
}
document.addEventListener('DOMContentLoaded', calcComm);
</script>

@endsection
