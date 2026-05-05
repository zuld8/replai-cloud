@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <span class="d-none d-sm-inline">
        <a href="{{route('waba.sync_template',$meta->id)}}" class="btn btn-dark">
            <i class="ti ti-refresh me-1"></i> {{__('waba.sync_template')}}
        </a>
    </span>
    <a href="{{route('waba.template.create',$meta->id)}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a> 
    <a href="{{route('waba.template.create',$meta->id)}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection


@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$meta->id}}"></x-waba-sidebar-update-component>
                <div class="col-12 col-md-10">
                    <div class="card-body table-responsive">
                        <table id="templateData" class="table table-bordered text-nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">{{__('general.number')}}</th>
                                    <th scope="col">{{__('general.name')}}</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Bahasa</th>
                                    <th scope="col" title="Quality score dari Meta">Quality</th>
                                    <th scope="col" title="Total pesan terkirim via template ini">Terkirim</th>
                                    <th scope="col" title="Persentase pesan yang delivered">Delivered%</th>
                                    <th scope="col" title="Persentase pesan yang dibaca">Read%</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $no = 1;
                                @endphp
                                @foreach ($templates as $template)
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $template->name; ?></td>
                                    <td><?= $template->category; ?></td>
                                    <td><?= $template->lang; ?></td>
                                    <td>
                                        @if(isset($template->quality_score) && $template->quality_score === 'GREEN')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2">🟢 HIGH</span>
                                        @elseif(isset($template->quality_score) && $template->quality_score === 'YELLOW')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">🟡 MEDIUM</span>
                                        @elseif(isset($template->quality_score) && $template->quality_score === 'RED')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">🔴 LOW</span>
                                        @elseif(isset($template->quality_score) && $template->quality_score === 'UNKNOWN')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2" title="Belum cukup data dari Meta">⏳ Pending</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2" title="Sync template untuk lihat quality">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($template->total_sent) && $template->total_sent > 0)
                                            <span class="fw-semibold">{{ number_format($template->total_sent) }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($template->delivery_rate) && $template->delivery_rate !== null)
                                            <span class="{{ $template->delivery_rate >= 90 ? 'text-success fw-bold' : ($template->delivery_rate >= 70 ? 'text-warning fw-semibold' : 'text-danger fw-semibold') }}">
                                                {{ $template->delivery_rate }}%
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($template->read_rate) && $template->read_rate !== null)
                                            <span class="{{ $template->read_rate >= 50 ? 'text-success fw-bold' : ($template->read_rate >= 20 ? 'text-warning fw-semibold' : 'text-danger fw-semibold') }}">
                                                {{ $template->read_rate }}%
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td><?= $template->waba_status_template; ?></td>
                                    <td>
                                        <a href="<?= route('waba.template.update', [$meta->id, $template->id]); ?>" class="btn btn-outline-warning btn-icon fs-16">
                                            <i class="bx bx-pencil "></i>
                                        </a>
                                        <a href="<?= route('waba.template.delete', [$meta->id, $template->id]); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                            <i class="bx bx-trash "></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
    $(function(e) {
        'use strict';

        $('#templateData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.template.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection