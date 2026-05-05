@extends('layouts.app')

@section('button')
<div class="btn-list">
    <a href="{{ route('labels') }}" class="btn btn-primary">
        <i class="bx bx-chevron-left"></i>
        {{ __('master.label.label_list') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
    <div class="col-xl-12">
        <form action="<?= route('label.edit', $label->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">{{__('general.insert_name')}}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-label"></i></span>
                            <input class="form-control" name="name" value="<?= $label->name; ?>" type="text" placeholder="{{ __('master.label.name') }}" required>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{ __('master.label.input_keyword') }}</label>
                        <small class="d-block text-muted mb-2">{{ __('master.label.keyword_desc') }}</small>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-hash"></i></span>
                            <input class="form-control" name="tag" type="text" value="{{$label->tag}}" placeholder="{{ __('master.label.tag_placeholder') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">{{ __('master.label.position_index') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-sort"></i></span>
                            <input class="form-control" name="position" type="number" min="1" value="{{$label->position}}" placeholder="1" required>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">{{ __('master.label.label_color') }}</label>
                        <div class="input-group">
                            <input class="form-control" name="color" type="color" value="{{$label->color}}" required style="height: 38px; cursor: pointer;">
                            <span class="input-group-text" id="colorHex">{{$label->color}}</span>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.querySelector('input[name="color"]');
        const colorHex = document.getElementById('colorHex');
        
        colorInput.addEventListener('input', function() {
            colorHex.textContent = this.value;
        });
    });
</script>
@endsection