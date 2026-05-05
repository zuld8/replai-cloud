@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('directory.districts')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('master.directory.back_to_district')}}
    </a>
    <a href="{{route('directory.districts')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.directory.back_to_district')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('directory.district.edit', $district->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{__('page.district.update')}}
                </div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('sidebar.state')}}</label>
                                <select class="form-control provinces" name="province" required>
                                    <option value="">{{__('master.directory.choose_state')}}</option>
                                    @foreach ($provinces as $province)
                                    <option value="<?= $province->id; ?>" @if(($district->city->province_id ?? '') == $province->id) selected @endif ><?= $province->name; ?></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('sidebar.city')}}</label>
                                <select class="form-control cities" name="city" required>
                                    <option value="<?= $district->city->id ?? ''; ?>">
                                        <?= $district->city->type ?? ''; ?>
                                        <?= $district->city->name ?? ''; ?>
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label">{{__('general.insert_name')}} </label>
                                <input class="form-control" name="name" value="<?= old('name', $district->name); ?>" type="text" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                 <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.provinces').select2();
    });

    $(".provinces").on("change", function() {

        $(".cities").val("");
        $(".districts").val("");

        $('.cities').select2({
            placeholder: '{{__("master.directory.choose_city")}}',
            ajax: {
                url: `/app/master/components/cities?province=${$(this).val()}`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.type + ' ' + item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        });

    })
</script>
@endsection