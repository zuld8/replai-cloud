@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">

    {{-- Download CSV --}}
    <a href="{{ route('group.export', $group->id) }}"
       class="btn btn-success d-none d-sm-inline-block">
        <i class="bx bx-download me-1"></i> Download CSV
    </a>
    <a href="{{ route('group.export', $group->id) }}"
       class="btn btn-success d-sm-none btn-icon" aria-label="Download CSV">
        <i class="bx bx-download"></i>
    </a>

    {{-- Kembali --}}
    <a href="{{route('groups')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('scrapp.back_to')}}
    </a>
    <a href="{{route('groups')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('scrapp.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    {{__('page.scrapp.result')}}
                    <span class="badge bg-success-transparent text-success ms-2" id="totalBadge"></span>
                    <input type="hidden" id="scrapId" value="<?= $group->id; ?>">
                </div>
                <small class="text-muted">
                    <i class="bx bxl-whatsapp text-success"></i>
                    Nama WA tersedia jika kontak pernah berinteraksi dengan device
                </small>
            </div>
            <div class="card-body">
                <table id="storeData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama WA</th>
                            <th>{{__('general.telp')}}</th>
                            <th>{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    $(document).ready(function() {
        const store_table = $('#storeData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Cari nama / nomor',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'asc']],
            ajax: {
                "url": '/app/whatsapp-group/detail/' + $("#scrapId").val(),
                "data": function(d) {
                    d = datatable_pasarsafe_callback(d);
                }
            },
            columns: [
                {
                    // Nama WA (wa_username) — tampil jika ada, else ikon "tidak tersedia"
                    data: 'wa_username',
                    name: 'wa_username',
                    render: function(data, type, row) {
                        if (data) {
                            return `<span class="fw-semibold"><i class="bx bxl-whatsapp text-success me-1"></i>${data}</span>`;
                        }
                        return `<span class="text-muted fst-italic fs-11">— belum tersedia</span>`;
                    }
                },
                {
                    data: 'phone',
                    name: 'phone',
                    render: function(data, type, row) {
                        if (!data) return '';
                        const phone = data.replace(/\D/g, '');
                        return `<a href="https://wa.me/${phone}" class="wa-link" target="_blank">${data}</a>`;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Update badge total setelah data load
        store_table.on('xhr', function() {
            const json = store_table.ajax.json();
            if (json && json.recordsTotal !== undefined) {
                $('#totalBadge').text(json.recordsTotal + ' kontak');
            }
        });
    });
</script>
@endsection
