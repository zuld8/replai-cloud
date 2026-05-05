@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('blash.group') }}" class="btn btn-info d-flex align-items-center">
        <i class="bx bx-chevron-left"></i>
        <span class="ms-1">{{ __('blash.back_to') }}</span>
    </a>
</div>

@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('blash.group.store'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{__('page.wa.add')}}</div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('sidebar.message_template')}}</label>
                        <select class="form-control templates" name="template" required>
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>"><?= $template->name; ?></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('blash.title')}}</label>
                        <input type="text" class="form-control" name="name" required value="<?= old('name'); ?>">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('scrapp.schedule')}}</label>
                        <input type="datetime-local" class="form-control" name="schedule" required value="<?= old('schedule'); ?>">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">Jeda Antar Pengriman (Detik)</label>
                        <input type="number" class="form-control" name="delay" required min="10" max="300" value="<?= old('delay') ?? 60; ?>">
                        <small class="text-muted">Rekomendasi: Isi antara 30 hingga 300 detik. Semakin kecil angkanya, semakin besar resiko akun Whastapp Terblokir</small>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">Perangkat Device</label>
                        <select class="form-control devices" name="devices[]" multiple required>
                            @foreach ($devices as $device)
                            <option value="<?= $device->id; ?>"><?= $device->name; ?> - <?= $device->phone; ?> </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">Group Whatsapp</label>
                        <div class="input-group">
                            <div style="width: 87%;">
                                <select class="form-control groups" name="groups[]" multiple required>
                                    <option value="">Pilih Group Whatsapp</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal">
                                Pilih
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">Opsi Penggunaan Device</label>
                        <select class="form-control" name="whatsapp_sender_notif" required>
                            <option value="sequence">Single Device</option>
                            <option value="spin" selected>AI Choose (Spin)</option>
                            <option value="random">Random (Acak)</option>
                        </select>
                    </div>


                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.add_data')}}</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalLabel">Pilih Group Whatsapp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="groupList" class="row">
                    <!-- Checkbox group akan dimuat via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="selectGroupsBtn" class="btn btn-primary">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $(".templates").select2();
        $(".devices").select2();
        $(".groups").select2();

        // Saat devices berubah, load list group untuk modal
        $('.devices').on('change', function() {
            const selectedDevices = $(this).val();
            if (!selectedDevices || selectedDevices.length === 0) {
                $('#groupList').html('<p class="text-muted">Pilih device terlebih dahulu</p>');
                return;
            }

            $.post('/app/whatsapp-group/components', {
                devices: selectedDevices,
                _token: '{{ csrf_token() }}'
            }, function(data) {
                if (data.length === 0) {
                    $('#groupList').html('<p class="text-muted">Tidak ada group tersedia</p>');
                    return;
                }

                let html = '';
                data.forEach(function(group) {
                    html += `
                    <div class="col-md-4 mb-2">
                        <input type="checkbox" class="form-check-input group-checkbox" value="${group.id}" data-name="${group.name}">
                        <label class="form-check-label">${group.name}</label>
                    </div>
                `;
                });
                $('#groupList').html(html);
            }, 'json');
        });

        // Tambahkan group yang dipilih ke Select2
        $('#selectGroupsBtn').on('click', function() {
            $('.group-checkbox:checked').each(function() {
                let id = $(this).val();
                let name = $(this).data('name');

                if ($('.groups').find("option[value='" + id + "']").length === 0) {
                    let newOption = new Option(name, id, true, true);
                    $('.groups').append(newOption).trigger('change');
                }
            });

            $('#groupModal').modal('hide');
        });
    });
</script>
@endsection