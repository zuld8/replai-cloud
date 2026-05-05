@extends('layouts.app')

@section('styles') 
 <link href="{{asset('assets/css/pages/storage.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAdd" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('media.add_folder')}}
    </a>
    @if($current_folder)
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#uploadMedia" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('media.upload_media')}}
    </a>
    @endif
    @if($current_folder)
    <form action="{{ route('folders.delete-folder') }}" method="POST" class="d-inline deleteForm">
        @csrf
        @method('DELETE')
        <input type="hidden" name="path" value="{{ $path }}">
        <button type="submit" class="btn btn-danger d-none d-sm-inline-block deletebutton">
            <i class="ti ti-trash"></i>
            {{__('media.delete_folder')}}
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
 <div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
    </div>

    <!-- Breadcrumb Navigation -->
    @if(count($directory) > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-header modern-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('folders') }}">
                                <i class="bx bx-home"></i> {{__('master.folder.home')}}
                            </a>
                        </li>
                        @foreach ($directory as $index => $value)
                        @php
                        $pathSegment = implode('/', array_slice($directory, 0, $index + 1));
                        @endphp
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                            @if($loop->last)
                                {{ $value }}
                            @else
                                <a href="{{ route('folders', ['path' => $pathSegment]) }}">{{ $value }}</a>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    @endif

    <!-- Folders Section -->
    @if (count($sub_folders) > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-folder me-2"></i>
                    Folders ({{ count($sub_folders) }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($sub_folders as $folder)
                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="position-relative">
                            <!-- Delete Button for Folder -->
                            <form action="{{ route('folders.delete-folder') }}" method="POST" class="position-absolute top-0 end-0 m-2 deleteForm" style="z-index: 10;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="path" value="{{ $folder['path'] }}">
                                <button type="submit" class="btn btn-sm btn-danger deletebutton" style="opacity: 0.9;">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                            
                            <a href="{{ route('folders', ['path' => $folder['path']]) }}" class="text-decoration-none">
                                <div class="folder-card">
                                    <div class="folder-content">
                                        <div class="folder-icon mx-auto">
                                            <i class="bx bx-folder"></i>
                                        </div>
                                        <h6 class="folder-title">{{ $folder['name'] }}</h6>
                                        <div class="folder-stats">
                                            <i class="bx bx-file"></i> {{ $folder['item_count'] }} {{ __('master.folder.items') }}
                                        </div>
                                        <small class="text-muted d-block mt-2">{{ $folder['size_formatted'] ?? '0 B' }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Media Files Section -->
    @if(count($media) > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-file me-2"></i>
                    {{__('master.folder.media_files')}} ({{ count($media) }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($media as $m)
                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="media-card">
                            <div class="media-image-container">
                                <?php
                                    $filePath = asset($m['path']);
                                    $ext = strtolower($m['format']);

                                    switch ($ext) {
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                        case 'gif':
                                        case 'webp':
                                            $imagePath = $filePath;
                                            break;
                                        case 'xls':
                                        case 'xlsx':
                                        case 'csv':
                                            $imagePath = 'https://app.chatcoaster.id/uploads/folders/default/1745938454_a6993ef8-c5cb-4f8b-888f-e3cbbc4e9fd3.jpg';
                                            break;
                                        case 'mp4':
                                        case 'mov':
                                        case 'avi':
                                            $imagePath = 'https://app.chatcoaster.id/uploads/folders/default/1745938448_4c73f369-3cee-4160-ba88-d8f4fdc4c995.jpg';
                                            break;
                                        case 'pdf':
                                            $imagePath = 'https://app.chatcoaster.id/uploads/folders/default/1745938443_4257a82f-a356-44d3-806d-c55914417823.jpg';
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $imagePath = 'https://app.chatcoaster.id/uploads/folders/default/1745938436_7fdff62a-5bc6-438e-9d40-9cbbc4fe2ed7.jpg';
                                            break;
                                        default:
                                            $imagePath = 'https://app.chatcoaster.id/uploads/folders/default/1745938818_74a8a07b-f617-4836-b9e1-09f48855434c.jpg';
                                            break;
                                    }
                                ?>
                                <img src="<?= $imagePath; ?>" alt="{{ $m['name'] }}" class="media-image" loading="lazy">
                                <div class="media-format-badge">{{ strtoupper($m['format']) }}</div>
                            </div>
                            
                            <div class="media-content">
                                <h6 class="media-title" title="{{ $m['name'] }}">{{ $m['name'] }}</h6>
                                <small class="text-muted d-block mb-2">{{ $m['size_formatted'] }}</small>
                                
                                <div class="media-actions">
                                    <a href="<?= asset($m['path']); ?>" target="_blank" class="action-btn-media btn-view" title="View File">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    
                                    <button type="button" onclick="copyUrl('<?= asset($m['path']); ?>')" class="action-btn-media btn-copy" title="Copy Link">
                                        <i class="bx bx-link"></i>
                                    </button>
                                    
                                    <form action="{{ route('folders.delete-media') }}" method="POST" class="d-inline deleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $m['path'] }}">
                                        <button type="submit" class="action-btn-media btn-delete deletebutton" title="Delete File">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if(count($sub_folders) == 0 && count($media) == 0)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="bx bx-folder-open empty-state-icon"></i>
                    <h5 class="mt-3">{{ __('master.folder.no_files_folders') }}</h5>
                    <p class="mb-4">{{ __('master.folder.no_files_folders_desc') }}</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#modalAdd">
                            <i class="bx bx-plus-circle"></i>
                            {{__('master.folder.add_folder')}}
                        </button>
                        <button type="button" class="btn btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#uploadMedia">
                            <i class="bx bx-cloud-upload"></i>
                            {{__('master.folder.upload_files')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Add Folder Modal -->
<div class="modal fade" id="modalAdd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('folders.create') }}" enctype="multipart/form-data" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title" id="modalAddLabel">
                    <i class="bx bx-folder-plus me-2"></i>
                    {{__('media.add_folder')}}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{__('media.folder_name')}}</label>
                    <input type="text" class="form-control" name="name" value="{{old('name')}}" placeholder="{{__('master.folder.enter_folder_name')}}" required />
                    <input type="hidden" name="current_path" value="{{ $path ?? '' }}" />
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('master.folder.cancel')}}</button>
                <button type="submit" class="btn btn-modern btn-success-modern">
                    <i class="bx bx-plus-circle"></i>
                    {{__('master.folder.create_folder')}}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMedia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadMediaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('folders.upload') }}" enctype="multipart/form-data" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title" id="uploadMediaLabel">
                    <i class="bx bx-cloud-upload me-2"></i>
                    {{__('media.upload_media')}}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{__('master.folder.select_file')}}</label>
                    <input type="file" class="form-control" name="file" required />
                    <input type="hidden" name="current_path" value="{{ $path ?? '' }}" />
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        {{ __('master.folder.format_supported') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('master.folder.cancel')}}</button>
                <button type="submit" class="btn btn-modern btn-success-modern">
                    <i class="bx bx-cloud-upload"></i>
                    {{__('master.folder.upload_files')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
 <script>
    function copyUrl(url) {
        // Create temporary textarea
        const tempTextarea = document.createElement("textarea");
        tempTextarea.value = url;
        document.body.appendChild(tempTextarea);
        
        // Select and copy
        tempTextarea.select();
        tempTextarea.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");
        
        // Remove temporary element
        document.body.removeChild(tempTextarea);

        // Show success notification
        toastr.success("{{__('general.success_copied')}}", "Success!", {
            timeOut: 3000,
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            showDuration: '300',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            tapToDismiss: false,
        });
    }

    // Delete confirmation
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete buttons with confirmation
        document.querySelectorAll('.deletebutton').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: '{{__("master.folder.are_you_sure")}}',
                    text: "{{__("master.folder.not_revert")}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{__("master.folder.delete_confirm")}}',
                    cancelButtonText: '{{__("master.folder.cancel")}}'
                }).then((result) => {
                    if (result.value) {
                        form.submit();
                    }
                });
            });
        });

        // Add loading state to forms
        const forms = document.querySelectorAll('form:not(.deleteForm)');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> {{__("master.folder.processing")}}';
                }
            });
        });
    });
</script>
@endsection