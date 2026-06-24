<div class="col-12 col-md-2 border-end">
    <style>
    .waba-settings-nav .list-group-item {
        border: none !important; border-left: 3px solid transparent !important;
        font-size: 13px; color: #1E2A4A; padding: 9px 14px;
        border-radius: 0 8px 8px 0 !important; margin-bottom: 1px;
        transition: all 0.15s ease;
    }
    .waba-settings-nav .list-group-item:hover:not(.active) {
        background: #F5F8FC !important; color: #2E8DE1 !important;
    }
    .waba-settings-nav .list-group-item.active {
        background: #EAF3FC !important; color: #1B5FA6 !important;
        border-left: 3px solid #2E8DE1 !important; font-weight: 600;
    }
    .waba-settings-nav .list-group-item.text-danger,
    .waba-settings-nav .list-group-item.text-danger:hover { color: #DC2626 !important; }
    .waba-settings-nav .list-group-item.text-info,
    .waba-settings-nav .list-group-item.text-info:hover { color: #0EA5E9 !important; }
    .waba-settings-nav .nav-section-title {
        font-size: 10.5px; font-weight: 700; letter-spacing: .6px;
        color: #94A3B8; text-transform: uppercase;
        padding: 14px 14px 4px; display: block;
    }
    </style>
    <div class="py-3 ps-1">
        <div class="waba-settings-nav">
            <span class="nav-section-title">Pengaturan</span>
            <div class="list-group list-group-flush">
                <a href="{{route('waba.update',$idwaba)}}"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->is('app/waba/update*') && !request()->is('app/waba/update/devices*') && !request()->is('app/waba/update/greeting*') && !request()->is('app/waba/update/broadcast*') ? 'active' : '' }}">
                    <i class="ti ti-settings fs-14"></i> Umum
                </a>
                <a href="{{route('waba.devices',$idwaba)}}"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->is('app/waba/update/devices*') ? 'active' : '' }}">
                    <i class="ti ti-device-mobile fs-14"></i> Nomor Terpasang
                </a>
                <a href="{{route('waba.templates',$idwaba)}}"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->is('app/waba/templates*') ? 'active' : '' }}">
                    <i class="ti ti-template fs-14"></i> Template Pesan
                </a>
                <a href="{{route('waba.chatbot',$idwaba)}}"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->is('app/waba/chatbot*') ? 'active' : '' }}">
                    <i class="ti ti-robot fs-14"></i> Chatbot Pesan
                </a>
                <a href="{{route('waba.broadcast',$idwaba)}}"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->is('app/waba/broadcast*') ? 'active' : '' }}">
                    <i class="ti ti-send fs-14"></i> Broadcast Pesan
                </a>
            </div>

            <span class="nav-section-title mt-2">Tindakan</span>
            <div class="list-group list-group-flush">
                <a href="{{route('waba.refresh',$idwaba)}}"
                   class="list-group-item list-group-item-action text-info d-flex align-items-center gap-2">
                    <i class="ti ti-refresh fs-14"></i> Refresh
                </a>
                <a href="{{ route('waba.delete',$idwaba) }}"
                   class="list-group-item list-group-item-action text-danger deletebutton d-flex align-items-center gap-2">
                    <i class="ti ti-trash fs-14"></i> Hapus Integrasi
                </a>
            </div>
        </div>
    </div>
</div>
