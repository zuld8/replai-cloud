@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('master.category.add_new_category')}}
    </a>
</div>

@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card"> 
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('master.directory.total_customer')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($categories as $category)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $category->name; ?></td>

                            <td>
                                <a class="text-info" href="<?= route('stores'); ?>?category=<?= $category->id; ?>">
                                    {{number_format($category->store_count ?? 0)}} {{__('sidebar.customers')}}
                                </a>
                            </td>
                            <td>
                                <a href="<?= route('categories.update', $category->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('categories.delete', $category->id); ?>" class="btn btn-outline-danger btn-icon fs-16"
                                   onclick="return confirmDeleteCategory('<?= addslashes($category->name) ?>', <?= $category->store_count ?? 0 ?>)">
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
@endsection


@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    $(function(e) {
        'use strict';

        $('#provinceData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.category.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });

    function confirmDeleteCategory(name, count) {
        const countStr = count.toLocaleString('id-ID');
        const msg = count > 0
            ? `⚠️ Hapus kategori "${name}"?\n\n` +
              `${countStr} kontak di dalamnya akan IKUT TERHAPUS PERMANEN.\n\n` +
              `Tindakan ini tidak bisa dibatalkan!\n\n` +
              `Yakin lanjutkan?`
            : `Hapus kategori "${name}"?\n\nKategori ini tidak punya kontak.\n\nLanjutkan?`;
        return confirm(msg);
    }
</script>
@endsection
