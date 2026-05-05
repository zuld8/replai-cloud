$("form#activasiLicensi").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    setTimeout(function () {
        $.ajax({
            url: "/license-key/store-license",
            type: "POST",
            data: formData,
            success: function (data, json, errorThrown) {
                if (data.status == "error") {
                    Swal.fire(
                        {
                            title: "Warning",
                            text: data.pesan,
                            icon: "warning",
                            width: "auto",
                            confirmButtonText: "Try Again!",
                            cancelButtonText: "Close",
                            showCancelButton: true,
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#openModal").on("click"); //this is when the form is in a modal
                            }
                        }
                    );
                } else {
                    Swal.fire({
                        title: "Success",
                        text: "Congratulations, your product license activation has been successful, click OK to start the application login page",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.value) {
                            document.location.href = "/authentication";
                        }
                    });
                }
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    }, 130);
});

$("form#updateLicense").on("submit", function (e) {
    e.preventDefault();

    var butttonSubmit = $("#submitLoading");
    butttonSubmit.attr("disabled");
    butttonSubmit.html("Loading....")
    var formData = new FormData(this);
    setTimeout(function () {
        $.ajax({
            url: "/app-license/store-update",
            type: "POST",
            data: formData,
            success: function (data, json, errorThrown) {

                butttonSubmit.removeAttr("disabled");
                butttonSubmit.html('License Activation <i class="bx bx-chevron-right " aria-hidden="true"></i>')
                if (data.status == "error") {
                    Swal.fire(
                        {
                            title: "Warning",
                            text: data.pesan,
                            icon: "warning",
                            width: "auto",
                            confirmButtonText: "Try Again",
                            cancelButtonText: "Close",
                            showCancelButton: true,
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#openModal").on("click"); //this is when the form is in a modal
                            }
                        }
                    );
                } else {
                    Swal.fire({
                        title: "Success",
                        text: "Congratulations, your product license activation has been successful, click OK to start the application login page",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.value) {
                            document.location.href = "/authentication";
                        }
                    });
                }
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    }, 130);
});
