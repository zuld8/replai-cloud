<style>
/* ── Pricing Cards Redesign ─────────────────────────────────── */
.pricing-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;align-items:start;max-width:1000px;margin:0 auto}
.pricing-card{position:relative;background:#1a2035;border:1px solid rgba(255,255,255,.1);border-radius:16px;overflow:hidden;transition:transform .2s,box-shadow .2s;color:#e2e8f0}
.pricing-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,0,0,.35)}
.pricing-card.featured{border:2px solid #2E8DE1;box-shadow:0 0 0 1px rgba(46,141,225,.3),0 12px 40px rgba(46,141,225,.2);transform:scale(1.03)}
.pricing-card.featured:hover{transform:scale(1.03) translateY(-4px)}
/* Header */
.pc2-header{padding:22px 24px 16px}
.pc2-tier{font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#64748B;margin-bottom:8px}
.pc2-price-row{display:flex;align-items:flex-end;gap:4px;margin-bottom:2px}
.pc2-currency{font-size:1rem;font-weight:600;color:#94A3B8;margin-bottom:4px}
.pc2-price{font-size:2.6rem;font-weight:800;color:#fff;line-height:1}
.pc2-price-trial{font-size:2.6rem;font-weight:800;color:#4ade80;line-height:1}
.pc2-period{font-size:.82rem;color:#64748B;margin-bottom:2px;align-self:flex-end;padding-bottom:4px}
.pc2-perday{font-size:.75rem;color:#94A3B8;margin-top:2px}
.pc2-perday span{color:#38bdf8;font-weight:600}
.badge-popular{position:absolute;top:-1px;left:50%;transform:translateX(-50%);
  background:linear-gradient(90deg,#2E8DE1,#38bdf8);color:#fff;font-size:10px;
  font-weight:700;padding:4px 16px;border-radius:0 0 10px 10px;letter-spacing:.5px;
  white-space:nowrap}
/* Quota chips */
.pricing-quota{display:flex;gap:6px;padding:0 24px 14px}
.pricing-quota>div{flex:1;text-align:center;background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:8px 4px}
.q-val{display:block;font-weight:700;font-size:13px;color:#fff}
.q-lbl{display:block;font-size:9px;color:#64748B;margin-top:1px;letter-spacing:.3px}
/* Divider */
.pc2-divider{border:none;border-top:1px solid rgba(255,255,255,.08);margin:0}
/* Feature list */
.pc2-body{padding:16px 24px 8px}
.plus-note{font-size:11px;color:#64748B;margin:0 0 8px;font-style:italic}
.pc2-features{list-style:none;padding:0;margin:0 0 8px}
.pc2-features li{display:flex;align-items:flex-start;gap:8px;padding:4px 0;
  font-size:.82rem;color:#cbd5e1;border-bottom:1px solid rgba(255,255,255,.04)}
.pc2-features li:last-child{border-bottom:none}
.pc2-features li .ico-ok{color:#4ade80;flex-shrink:0;margin-top:2px;font-size:.8rem}
.pc2-features li.disabled{display:none}
.upsell{font-size:.75rem;color:#38bdf8;margin:4px 0 8px;padding:6px 10px;
  background:rgba(56,189,248,.08);border-radius:6px;border-left:2px solid #38bdf8}
/* CTA Footer */
.pc2-footer{padding:12px 24px 20px}
.pc2-microcopy{font-size:10px;color:#475569;text-align:center;margin-top:6px;display:flex;align-items:center;justify-content:center;gap:4px}
.pc2-microcopy i{font-size:9px}
/* Buttons */
.pricing-btn{display:block;width:100%;padding:13px;border-radius:50px;
  font-weight:700;font-size:.88rem;text-align:center;transition:all .2s;
  text-decoration:none;cursor:pointer;border:none;letter-spacing:.2px}
.pricing-btn.btn-solid{background:linear-gradient(90deg,#2E8DE1,#38bdf8);color:#fff;
  box-shadow:0 4px 20px rgba(46,141,225,.4)}
.pricing-btn.btn-solid:hover{box-shadow:0 6px 28px rgba(46,141,225,.6);transform:translateY(-1px);color:#fff}
.pricing-btn.btn-outline{background:transparent;color:#94A3B8;
  border:2px solid rgba(255,255,255,.2)}
.pricing-btn.btn-outline:hover{border-color:#2E8DE1;color:#2E8DE1;background:rgba(46,141,225,.06)}
/* Guarantee bar */
.pricing-guarantee{text-align:center;padding:18px 0 0;font-size:.75rem;color:#475569;display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap}
.pricing-guarantee span{display:flex;align-items:center;gap:4px}
.pricing-guarantee i{color:#4ade80;font-size:.7rem}
/* Mobile */
@media(max-width:768px){
  .pricing-card.featured{transform:none}
  .pricing-card.featured:hover{transform:translateY(-4px)}
  .pricing-grid{grid-template-columns:1fr;max-width:400px;margin:0 auto}
}
</style>

@foreach ($pricing as $package)
@php
    $isTrial = $package->trial_version == 'yes' || $package->price == 0;
    $isPopular = $package->featured == 'yes' || $loop->index == 1;
    $perDay = (!$isTrial && $package->add_days > 0)
              ? number_format($package->price / $package->add_days, 0, ',', '.')
              : null;
    $agentVal = $package->limit_user_option == 'yes'
                ? number_format($package->users_limit)
                : '∞';
    $plusNote = match($loop->index) {
        1 => 'Semua di Trial, plus:',
        2 => 'Semua di Basic, plus:',
        default => null
    };
@endphp
<div class="pricing-card {{ $isPopular ? 'featured' : '' }}">
    @if($isPopular)
    <div class="badge-popular">⭐ Paling Populer</div>
    @endif

    {{-- HEADER --}}
    <div class="pc2-header" style="{{ $isPopular ? 'padding-top:32px' : '' }}">
        <div class="pc2-tier">{{ $isTrial ? '✨ 10 HARI GRATIS' : ($loop->index >= 2 ? '🚀 PREMIUM' : '💼 BISNIS') }}</div>
        <div class="pc2-price-row">
            @if(!$isTrial)<span class="pc2-currency">Rp</span>@endif
            <span class="{{ $isTrial ? 'pc2-price-trial' : 'pc2-price' }}">
                {{ $isTrial ? 'Gratis' : number_format($package->price, 0, ',', '.') }}
            </span>
            <span class="pc2-period">
                / {{ $package->days_option == 'limited' ? $package->add_days.' hari' : 'selamanya' }}
            </span>
        </div>
        @if($perDay)
        <div class="pc2-perday">≈ <span>Rp{{ $perDay }}/hari</span> · buat bisnis yang mau tumbuh</div>
        @else
        <div class="pc2-perday">10 hari penuh · tanpa kartu kredit</div>
        @endif
        <div class="pc2-plan-name" style="font-size:.85rem;font-weight:700;color:#94A3B8;margin-top:6px">{{ $package->name }}</div>
    </div>

    {{-- QUOTA CHIPS --}}
    <div class="pricing-quota">
        <div>
            <span class="q-val">{{ $package->storage_name ?? $package->storage.' MB' }}</span>
            <span class="q-lbl">Storage</span>
        </div>
        <div>
            <span class="q-val">{{ number_format($package->ai_response) }}</span>
            <span class="q-lbl">Kredit AI</span>
        </div>
        <div>
            <span class="q-val">{{ $agentVal }}</span>
            <span class="q-lbl">Agen Tim</span>
        </div>
    </div>

    <hr class="pc2-divider">

    {{-- FEATURES --}}
    <div class="pc2-body">
        @if($plusNote)
        <p class="plus-note">{{ $plusNote }}</p>
        @endif

        <ul class="pc2-features">
            {{-- Platform --}}
            @if(!($package->limit_device == 'yes' && $package->device_limit == 0))
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                {{ $package->limit_device == 'yes' ? number_format($package->device_limit) : '∞' }}
                WA Personal + {{ $package->limit_waba == 'yes' ? number_format($package->waba_limit) : '∞' }} WA Business
            </li>
            @endif

            @if($loop->index >= 1 && ($package->instagram > 0 || $package->limit_instagram == 'no'))
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                {{ $package->limit_instagram == 'yes' ? number_format($package->instagram) : '∞' }} Instagram ·
                {{ $package->limit_messanger == 'yes' ? number_format($package->messanger) : '∞' }} Messenger ·
                {{ $package->limit_telegram == 'yes' ? number_format($package->telegram) : '∞' }} Telegram
                @if($package->livechat_limit == 'yes' && $package->limit_livechat > 0)
                · {{ number_format($package->limit_livechat) }} Live Chat
                @endif
            </li>
            @elseif($loop->index == 0)
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                {{ $package->limit_telegram == 'yes' ? number_format($package->telegram) : '∞' }} Telegram ·
                {{ $package->limit_instagram == 'yes' ? number_format($package->instagram) : '∞' }} Instagram ·
                {{ $package->limit_messanger == 'yes' ? number_format($package->messanger) : '∞' }} Messenger
            </li>
            @endif

            {{-- Blast WA --}}
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                @if($package->limit_whatsapp_option == 'no')
                    <strong>Blast WA &amp; Email tanpa batas</strong>
                @else
                    {{ number_format($package->whatsapp_limit) }} Blast WA{{ $package->limit_whatsapp_priode ? '/'.$package->limit_whatsapp_priode : '' }}
                    · {{ $package->limit_email_option == 'no' ? '∞' : number_format($package->email_limit) }} Email
                @endif
            </li>

            {{-- ChatBot & AI --}}
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                @if($package->limit_chatbot == 'no')
                    <strong>ChatBot tanpa batas</strong>
                @else
                    {{ $package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : '∞' }} ChatBot
                @endif
                + {{ $package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : '∞' }} AI Training
            </li>

            {{-- Agent / Template --}}
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                {{ $agentVal }} agen tim ·
                @if($package->limit_template == 'no')
                    Template tanpa batas
                @else
                    {{ $package->limit_template == 'yes' ? number_format($package->template_limit) : '∞' }} template
                @endif
            </li>

            {{-- Kredit Pesan --}}
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                Kredit Pesan tanpa batas
            </li>

            {{-- Google Sheet (positive only) --}}
            @if($package->google_sheet == 'yes')
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                Integrasi Google Sheet ✓
            </li>
            @endif

            {{-- Scrap Data --}}
            @if($package->limit_scrapp_option == 'no' || ($package->limit_scrapp_option == 'yes' && $package->scrapp_limit > 0))
            <li>
                <i class="fas fa-check-circle ico-ok"></i>
                {{ $package->limit_scrapp_option == 'no' ? '∞' : number_format($package->scrapp_limit) }}
                Scrap Data{{ $package->limit_scrapp_priode ? '/'.$package->limit_scrapp_priode : '' }}
            </li>
            @endif
        </ul>

        {{-- Upsell untuk Trial: gantikan ❌ --}}
        @if($isTrial)
        <div class="upsell">
            <i class="fas fa-arrow-up"></i>
            Cek Ongkir &amp; Google Sheet tersedia di Basic ke atas ↑
        </div>
        @endif

        @if($loop->index >= 2)
        <div class="upsell" style="background:rgba(91,63,176,.1);border-color:#5B3FB0;color:#a78bfa">
            <i class="fas fa-star"></i>
            Prioritas dukungan · SLA terjamin
        </div>
        @endif
    </div>

    {{-- CTA --}}
    <div class="pc2-footer">
        <a href="{{ route('register') }}?package={{$package->id}}"
           class="pricing-btn {{ $isPopular ? 'btn-solid' : 'btn-outline' }}">
            {{ $isTrial ? 'Coba 10 hari gratis' : 'Pilih '.explode(' ', $package->name)[1].' sekarang' }}
        </a>
        <div class="pc2-microcopy">
            <i class="fas fa-shield-alt"></i>
            {{ $isTrial ? 'Gak perlu kartu kredit' : 'Batal kapan saja' }}
        </div>
    </div>
</div>
@endforeach

{{-- Guarantee bar --}}
<div class="pricing-guarantee" style="grid-column:1/-1">
    <span><i class="fas fa-check-circle"></i> Tanpa kontrak</span>
    <span><i class="fas fa-check-circle"></i> Tanpa biaya tersembunyi</span>
    <span><i class="fas fa-check-circle"></i> Data aman</span>
    <span><i class="fas fa-check-circle"></i> Batal kapan saja</span>
</div>
