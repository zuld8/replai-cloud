const confirmation = 'Apakah Anda Yakin ?';
const warning = window.translations.warning;
const yes_sure = window.translations.yes_sure;
const flashData = $(".flash-data").data("flashdata");
const gagal = $(".gagal").data("gagal");
const validation = $(".validation").data("validation");

if (flashData) {
    toastr.success(flashData, {
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1,
    });
}

if (validation) {
    toastr.error(validation, "Validasi", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1,
    });
}

if (gagal) {
    toastr.error(gagal, "Error", {
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1,
    });
}

 

function datatable_pasarsafe_callback(data) {
    for (var i = 0, len = data.columns.length; i < len; i++) {
        if (data.columns[i].searchable === true)
            delete data.columns[i].searchable;
        if (data.columns[i].orderable === true)
            delete data.columns[i].orderable;
        if (data.columns[i].data === data.columns[i].name)
            delete data.columns[i].name;
    }
    delete data.search.regex;
    return data;
}

// deletebutton handled per-page (e.g. users/index.blade.php) — removed global handler
// $(".deletebutton").on("click", ...);

/* page loader */
// function hideLoader() {
//     const loader = document.getElementById("loader");
//     const appdata = document.getElementById('appdata');
//     appdata.classList.remove('d-none')
//     loader.classList.add("d-none");
// }

// window.addEventListener("load", hideLoader);
/* page loader */
  

