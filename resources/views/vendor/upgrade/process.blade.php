@extends('layouts.minimal')

@section('content')


<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark d-flex justify-content-center">
                <img src="{{asset($settings->logo)}}" class="w-50">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body text-center py-4 p-sm-5">
                <img src="{{$information->image ?? ''}}" class="w-100 rounded" />
                <h1 class="mt-5">Extract File Upgrade</h1>
                <p class="text-secondary">Click the Upgrade File below to start the extraction. During the extraction process, you are not allowed to exit the page.</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $file)
                            <tr>
                                <td>
                                    <a href="javascript:void(0);" onclick="extrackFile('<?= basename($file['path']); ?>')">
                                        <i class="ti ti-file-zip fs-16 me-1"></i>{{basename($file['path'])}}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row align-items-center mt-3">
            <div class="col-4">
                <div class="progress">
                    <div class="progress-bar" style="width: 35%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" aria-label="25% Complete">
                        <span class="visually-hidden">35% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="btn-list justify-content-end">
                    <a href="{{route('upgrade.versions')}}" class="btn btn-link link-secondary backtostart">
                        Back To Previous Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function extrackFile(fileName) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
        });

        let timerInterval;
        Swal.fire({
            title: "Processing Extract",
            text: "Please be patient, we are doing our best...",
            timerProgressBar: true,
            onBeforeOpen: () => {
                Swal.showLoading();
                timerInterval = setInterval(() => {
                    const content = Swal.getHtmlContainer();
                    if (content) {
                        const b = content.querySelector("b");
                        if (b) {
                            b.textContent = Math.floor(Swal.getTimerLeft() / 1000);
                        }
                    }
                }, 100);
            },
            onClose: () => {
                clearInterval(timerInterval);
            },
        });


        $.ajax({
            type: "POST",
            url: `/upgrade-versions/process/extrack/${fileName}`,
            dataType: "json",
            beforeSend: function() {

            },
            success: function(response) {
                if (response.status == false) {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: response.message,
                    });
                } else {
                    Swal.close();
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message,
                    }).then((result) => {
                        window.location.href = '/upgrade-versions/process/upgrade';
                    });


                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response.message,
                });
            },
        });
    }
</script>
@endsection