<div class="col-12 col-md-2 border-end">
    <div class="card-body">
        <h4 class="subheader">Pengaturan Device</h4>
        <div class="list-group list-group-transparent">
            <a href="{{route('waba.update',$idwaba)}}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('app/waba/update*') && !request()->is('app/waba/update/devices*') && !request()->is('app/waba/update/greeting*') && !request()->is('app/waba/update/broadcast*')  ? 'active' : '' }} border-bottom">Umum</a>
            <a href="{{route('waba.devices',$idwaba)}}" class="list-group-item list-group-item-action d-flex align-items-center border-bottom {{ request()->is('app/waba/update/devices*')  ? 'active' : '' }}" >Nomor Terpasang</a>   
            <a href="{{route('waba.templates',$idwaba)}}" class="list-group-item list-group-item-action d-flex align-items-center border-bottom {{ request()->is('app/waba/templates*')  ? 'active' : '' }}" >Template Pesan</a>
            <a href="{{route('waba.chatbot',$idwaba)}}" class="list-group-item list-group-item-action d-flex align-items-center border-bottom {{ request()->is('app/waba/chatbot*')  ? 'active' : '' }}" >Chatbot Pesan</a>
            <a href="{{route('waba.broadcast',$idwaba)}}" class="list-group-item list-group-item-action d-flex align-items-center border-bottom {{ request()->is('app/waba/broadcast*')  ? 'active' : '' }}" >Broadcast Pesan</a>
        </div>
        <h4 class="subheader mt-4">Tindakan</h4>
        <div class="list-group list-group-transparent">
            <a href="{{route('waba.refresh',$idwaba)}}" class="list-group-item list-group-item-action text-info ">Refresh</a>
            <a href="{{ route('waba.delete',$idwaba) }}" class="list-group-item list-group-item-action text-danger deletebutton">Hapus Integrasi</a>
            
        </div>
    </div>
</div>