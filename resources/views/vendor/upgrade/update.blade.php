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
                <h1 class="mt-5">Update Process {{$information->name}} ( {{$information->code}} ) </h1>
                <p class="text-secondary">One more step to upgrade Version. Make sure your connection is stable and do not leave the page when you start the Upgrade process below </p>

                <div class="mt-4">
                    <button class="btn btn-primary w-100" onclick="startProcess('<?= $information->code; ?>')" type="button">
                        Start Upgrade Process<i class="ti ti-check fs-16 ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row align-items-center mt-3">
            <div class="col-4">
                <div class="progress">
                    <div class="progress-bar" style="width: 55%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" aria-label="25% Complete">
                        <span class="visually-hidden">55% Complete</span>
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
    function startProcess(fileName) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
        });

        let timerInterval;
        Swal.fire({
            title: "Processing Upgrade",
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
            url: `/upgrade-versions/process/start/${fileName}`,
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
                        window.location.href = '/upgrade-versions';
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