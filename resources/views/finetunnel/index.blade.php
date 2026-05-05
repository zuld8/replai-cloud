@extends('layouts.app')

@section('button')
<div class="btn-list">
    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createFineTunnelModal">
        <i class="bx bx-plus-circle"></i>
        {{__('finetunnel.create_training_data')}}
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>

    @foreach ($finetunnels as $finetunnel)
    <div class="col-sm-12 col-md-6 col-lg-4">
        <div class="card mb-4 position-relative">
            <!-- Tombol Hapus di pojok kanan atas -->
            <div class="position-absolute top-0 end-0 p-3">
                <a href="{{route('finetunnel.delete',$finetunnel->id)}}"
                    class="btn btn-sm btn-icon btn-outline-danger rounded-pill"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                    <i class="bx bx-trash"></i>
                </a>
            </div>

            <div class="card-body text-center py-4">
                <!-- Avatar/Icon -->
                <div class="mb-3">
                    <div class=" mx-auto">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="bx bx-bot" style="font-size: 2rem;"></i>
                        </span>
                    </div>
                </div>

                <!-- Nama AI Agent -->
                <h5 class="mb-1">{{$finetunnel->name}}</h5>
                <p class="text-muted small mb-3">
                    <i class="bx bx-chip"></i> AI Agent
                </p>

                <!-- Badge Model AI -->
                <div class="mb-3">
                    <span class="badge bg-label-{{ $finetunnel->model_ai == 'standart' ? 'info' : 'success' }} rounded-pill">
                        {{$finetunnel->model_ai == 'standart' ? 'Standart Model' : 'Advanced Model'}}
                    </span>
                </div>

                <!-- Statistik dalam 2 kolom -->
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <i class="bx bx-message-rounded-dots text-warning me-1"></i>
                            <small class="text-muted">{{__('finetunnel.message_limit')}}</small>
                        </div>
                        <h6 class="mb-0">{{number_format($finetunnel->message_limit)}}</h6>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <i class="bx bx-time text-info me-1"></i>
                            <small class="text-muted">{{__('finetunnel.delay')}}</small>
                        </div>
                        <h6 class="mb-0">{{number_format($finetunnel->delay)}}s</h6>
                    </div>
                </div>

                <!-- Info Tambahan (bisa di-expand atau collapse) -->
                <div class="d-flex justify-content-around text-center small mb-3 py-2 bg-lighter rounded">
                    <div>
                        <div class="text-muted">{{__('finetunnel.history')}}</div>
                        <strong>{{number_format($finetunnel->history_limit)}}</strong>
                    </div>
                    <div class="vr"></div>
                    <div>
                        <div class="text-muted">{{__('finetunnel.context')}}</div>
                        <strong>{{number_format($finetunnel->context_limit)}}</strong>
                    </div>
                </div>

                <!-- Tombol Detail (Primary Action) -->
                <a href="{{route('finetunnel.update',$finetunnel->id)}}"
                    class="btn btn-primary w-100">
                    <i class="bx bx-pencil me-1"></i>
                    {{__('finetunnel.detail_and_edit')}}
                </a>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    @if(count($pagination['links']) > 3)
    <div class="col-12 d-flex justify-content-center mt-3">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-round pagination-primary">
                @foreach($pagination['links'] as $paginate)
                @if($paginate['url'] != null)
                @if($paginate['label'] == 'pagination.previous')
                <li class="page-item prev {{ $paginate['active'] ? 'disabled' : '' }}">
                    <a class="page-link" href="{{$paginate['url']}}">
                        <i class="tf-icon bx bx-chevron-left"></i>
                    </a>
                </li>
                @endif

                @if($paginate['label'] != 'pagination.previous' && $paginate['label'] != 'pagination.next')
                <li class="page-item {{ $paginate['active'] ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginate['active'] ? '#' : $paginate['url'] }}">
                        {{$paginate['label']}}
                    </a>
                </li>
                @endif

                @if($paginate['label'] == 'pagination.next')
                <li class="page-item next {{ $paginate['active'] ? 'disabled' : '' }}">
                    <a class="page-link" href="{{$paginate['url']}}">
                        <i class="tf-icon bx bx-chevron-right"></i>
                    </a>
                </li>
                @endif
                @endif
                @endforeach
            </ul>
        </nav>
    </div>
    @endif

    <!-- Modal Create AI Agent -->
    <div class="modal fade" id="createFineTunnelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('finetunnel.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-bot me-2"></i>{{__('finetunnel.create_new_ai_agent')}}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Nama AI Agent -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-bot me-1"></i>{{__('finetunnel.ai_agent_name')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-rename"></i>
                                </span>
                                <input class="form-control"
                                    name="name"
                                    value="{{ old('name') }}"
                                    type="text"
                                    placeholder="{{__('finetunnel.example_ai_agent_name')}}"
                                    required
                                    autofocus>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.name_to_identify_ai_agent')}}
                            </small>
                        </div>

                        <!-- Model AI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-chip me-1"></i>{{__('finetunnel.model_ai')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-brain"></i>
                                </span>
                                <select class="form-control" name="model_ai" required>
                                    <option value="standart" {{ old('model_ai') == 'standart' ? 'selected' : '' }}>
                                        {{__('finetunnel.standard')}} (GPT-4o Mini)
                                    </option>
                                    <option value="advanced" {{ old('model_ai') == 'advanced' ? 'selected' : '' }}>
                                        {{__('finetunnel.advanced')}} (GPT-4o)
                                    </option>
                                </select>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.ai_model_selection_guide')}}
                            </small>
                        </div>

                        <!-- AI History Limit -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-history me-1"></i>{{__('finetunnel.ai_history_limit')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-message-square-detail"></i>
                                </span>
                                <input class="form-control"
                                    name="history_limit"
                                    value="{{ old('history_limit', 20) }}"
                                    type="number"
                                    min="1"
                                    max="100"
                                    required>
                                <span class="input-group-text">{{__('finetunnel.messages')}}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.ai_history_limit_info')}}
                            </small>
                        </div>

                        <!-- AI Context Limit -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-library me-1"></i>{{__('finetunnel.ai_context_limit')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-book-content"></i>
                                </span>
                                <input class="form-control"
                                    name="context_limit"
                                    value="{{ old('context_limit', 10) }}"
                                    type="number"
                                    min="1"
                                    max="50"
                                    required>
                                <span class="input-group-text">{{__('finetunnel.context')}}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.ai_context_limit_info_second')}}
                            </small>
                        </div>

                        <!-- Delay Respon AI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-time-five me-1"></i>{{__('finetunnel.ai_response_delay')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-timer"></i>
                                </span>
                                <input class="form-control"
                                    name="delay"
                                    value="{{ old('delay', 5) }}"
                                    type="number"
                                    min="0"
                                    max="60"
                                    required>
                                <span class="input-group-text">{{__('finetunnel.seconds')}}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.ai_response_delay_info_seconds')}}
                            </small>
                        </div>

                        <!-- Message Limit -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-message-square-minus me-1"></i>{{__('finetunnel.message_limit')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-hash"></i>
                                </span>
                                <input class="form-control"
                                    name="message_limit"
                                    value="{{ old('message_limit', 1000) }}"
                                    type="number"
                                    min="1"
                                    required>
                                <span class="input-group-text">{{__('finetunnel.messages')}}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('finetunnel.message_limit_before_handover')}}
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>{{__('general.close')}}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>{{__('finetunnel.save_training_data')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
    .bg-lighter {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .vr {
        width: 1px;
        background-color: rgba(0, 0, 0, 0.1);
    }
</style>
@endsection