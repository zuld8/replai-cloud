@extends('layouts.minimal')

@section('content')

<audio class="d-none" id="backgroundAudio" loop>
    <source src="{{$information->music}}" type="audio/mpeg">
</audio>

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
                <h1 class="mt-5">{{$information->name ?? ''}} ({{$information->code ?? ''}}) </h1>
                <p class="text-secondary">During the download process, you are prohibited from leaving the page and make sure your connection is stable so that the download process runs smoothly without any problems.</p>

                <button type="button" class="btn btn-primary w-100 downloadbutton">
                    <i class="ti ti-download me-2 fs-16"></i> Start Downloading
                </button>
            </div>
        </div>
        <div class="row align-items-center mt-3">
            <div class="col-4">
                <div class="progress">
                    <div class="progress-bar" style="width: 10%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" aria-label="25% Complete">
                        <span class="visually-hidden">10% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="btn-list justify-content-end">
                    <input type="hidden" id="urlback" value="{{route('upgrade.start')}}" />
                    <a href="{{route('upgrade.start')}}" class="btn btn-link link-secondary backtostart">
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
    $(".downloadbutton").on("click", function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure ?",
            text: "Once you start, you are not allowed to leave this page until the download process is complete.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Lanjutkan",
        }).then((result) => {
            if (result.value) {
                $(this).attr("disabled", true);
                $(".backtostart").attr("href", "javascript:void(0);")
                startAudio();
                startDownload();
            }
        });
    });

    function startDownload() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
        });

        let timerInterval;
        Swal.fire({
            title: "Processing Download",
            text: "Please be patient, we are doing our best....",
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
            url: "/upgrade-versions/download/start",
            dataType: "json",
            beforeSend: function() {

            },
            success: function(response) {
                if (response.status == false) {
                    $(".downloadbutton").removeAttr("disabled");
                    Swal.close();
                    stopAudio();

                    var backUrl = $("#urlback").val();
                    $(".backtostart").attr("href", backUrl)
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: response.message,
                        footer: '<a href="https://replai.org" target="_blank">Manual Download on Server</a>'
                    });
                } else {
                    Swal.close();
                    stopAudio();
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "Download process complete, please continue to the next step.",
                    }).then((result) => {
                        window.location.href = '/upgrade-versions/process';
                    });


                }
            },
            error: function(xhr, status, error) {
                $(".downloadbutton").removeAttr("disabled");
                stopAudio();
                Swal.close();
                var backUrl = $("#urlback").val();
                $(".backtostart").attr("href", backUrl)
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response.message,
                    footer: '<a href="https://replai.org" target="_blank">Manual Download on Server</a>'
                });
            },
        });
    }

    function startAudio() {
        const audioElement = document.getElementById("backgroundAudio");
        audioElement.play().catch(error => console.error("Audio gagal diputar:", error));
    }

    function stopAudio() {
        const audioElement = document.getElementById("backgroundAudio");
        if (audioElement) {
            audioElement.pause();
            audioElement.currentTime = 0;
            console.log("Audio dihentikan");
        }
    }
</script>
@endsection