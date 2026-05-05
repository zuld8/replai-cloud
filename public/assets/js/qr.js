"use strict";

const device_id = $("#device_id").val();
const base_url = $("#base_url").val();
const device_status = $("#device_status").val();

const warningLang = $("#warningLang").val();
const expiredLang = $("#expiredLang").val();
const expiredAlertLang = $("#expiredAlertLang").val();
const refreshPageLang = $("#refreshPageLang").val();
const connectedLang = $("#connectedLang").val();
const serverLossLang = $("#serverLossLang").val();
const areyouSureLang = $("#areyouSureLang").val();
const logoutLang = $("#logoutLang").val();
const waitingLang = $("#waitingLang").val();
const closeLang = $("#closeLang").val();
const okLang = $("#okLang").val();

var attampt = 0;
var session_attampt = 0;

checkSession();

//create session request for this device
function createSession() {
    attampt++;

    if (attampt == 6) {
        clearInterval(sessionMake);
        const image = `<img src="${base_url}/assets/img/icons/waiting.svg" class="w-25"><h4>${expiredLang}</h4>`;
        $(".qr-area").html(image);
        $(".loader-area").addClass("d-none");
        Swal.fire({
            title: warningLang,
            text: expiredAlertLang,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Tutup",
            confirmButtonText: refreshPageLang,
        }).then((result) => {
            if (result.value == true) {
                location.reload();
            }
        });
        return false;
    }
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    //sending ajax request
    $.ajax({
        type: "POST",
        url: base_url + "/app/device/sessions/create/" + device_id,
        dataType: "json",
        success: function (response) {
            const image = `<img src="${response.qr}" class="w-25">`;
            $(".qr-area").html(image);
            $(".loader-area").addClass("d-none");
        },
        error: function (xhr, status, error) {
            const image = `<img src="${base_url}/assets/img/icons/no-connected.svg" class="w-25"><h4>${serverLossLang}</h4>`;
            $(".qr-area").html(image);
            $(".loader-area").addClass("d-none");
            if (xhr.status == 500) {
                clearInterval(checkSessionRecurr);
                clearInterval(sessionMake);
            }
        },
    });
}

//check is device logged in
function checkSession() {
    session_attampt++;
    if (session_attampt >= 10) {
        clearInterval(checkSessionRecurr);
        return false;
    }

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: base_url + "/app/device/sessions/check/" + device_id,
        dataType: "json",
        success: function (response) {
            if (response.connected === true) {
                clearInterval(checkSessionRecurr);
                clearInterval(sessionMake);

                $(".loader-area").addClass("d-none");
                $(".loggout_area").removeClass("d-none");
                const image = `<img src="${base_url}/assets/img/icons/connected.svg" class="w-25"><h4>${connectedLang}</h4>`;
                $(".qr-area").html(image);
                $(".logged-alert").show();
            }
        },
        error: function (xhr, status, error) {
            if (xhr.status == 500) {
                clearInterval(checkSessionRecurr);
                clearInterval(sessionMake);
                const image = `<img src="${base_url}/assets/img/icons/no-connected.svg" class="w-25"><h4>${serverLossLang}</h4>`;
                $(".loader-area").addClass("d-none");
                $(".qr-area").html(image);
                $(".server_disconnect").show();
            }
        },
    });
}

//if click logout button
$(".logout-btn").on("click", function () {
    Swal.fire({
        title: areyouSureLang,
        text: logoutLang,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: closeLang,
        confirmButtonText: okLang,
    }).then((result) => {
        if (result.value == true) {
            let previous_btn = $(".logout-btn").html();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                type: "POST",
                url: base_url + "/app/device/sessions/logout/" + device_id,
                dataType: "json",
                beforeSend: function () {
                    $(".logout-btn").html(
                        `<i class="fas fa-spinner me-3"><i>${waitingLang}`
                    );
                    $(".logout-btn").attr("disabled", "");
                },
                success: function (response) {
                    
                    $(".logout-btn").html(previous_btn);
                    $(".logout-btn").hide();
                    $(".logout-btn").removeAttr("disabled");

                    location.reload();
                },
                error: function (xhr, status, error) { 
                    $(".logout-btn").html(previous_btn);
                    $(".logout-btn").removeAttr("disabled");
                },
            });
        }
    });
});

const sessionMake = setInterval(function () {
    createSession();
}, 12000);

const checkSessionRecurr = setInterval(function () {
    checkSession();
}, 5000);
