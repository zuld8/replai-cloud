@extends('layouts.admin')

@section('content')

<div class="row">

    <div class="col-lg-3 col-sm-6">
        <div class="card-background flex-fill">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="fw-medium mb-1 text-muted">Whatsapp Unofficial</p>
                            <h3 class="mb-0">{{number_format($summary['unofficial'])}}</h3>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bxl-whatsapp fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card-background flex-fill">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="fw-medium mb-1 text-muted">Whatsapp Official</p>
                            <h3 class="mb-0">{{number_format($summary['official'])}}</h3>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bxl-whatsapp-square fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card-background flex-fill">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="fw-medium mb-1 text-muted">Connected Platform</p>
                            <h3 class="mb-0">{{number_format($summary['livechats'])}}</h3>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bx-layout fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card-background flex-fill">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="fw-medium mb-1 text-muted">Ai Agent</p>
                            <h3 class="mb-0">{{number_format($summary['finetunnels'])}}</h3>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bx-bot fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End Data -->

<!-- Small Data -->
<div class="row">
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Data Kontak</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['stores'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bx-id-card fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Data Kategori</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['categories'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bxs-grid fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Human Agents</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['user'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bx-user-circle fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Blast Whatsapp</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['blast_w'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bxl-whatsapp fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Blast Email</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['blast_e'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bx-envelope fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
        <div class="card">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <span class="text-muted">Scraping</span>
                </div>
                <div class="d-flex align-items-end">
                    <div class="flex-1">
                        <h3 class="mb-2">{{number_format($summary['scraping'])}}</h3>
                    </div>
                    <span class="ms-2 avatar bg-primary-transparent">
                        <i class="bx bx-map fs-22"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Small -->

<div class="row">
    <div class="col-12 mb-4">
        <div class="card custom-card">
            <div class="card-header" style="display: block;">
                <h5 class="card-title mb-0">Conversation Status</h5>
                <small class="card-subtitle">Track conversation status distribution and trends</small>
            </div>
            <div class="card-body">
                <div class="px-2 py-1 flex-wrap border-top-dashed border-bottom-dashed d-sm-flex justify-content-between">
                    <div class="me-1 min-w-fit-content">
                    </div>
                    <div class="d-flex flex-wrap flex-1 justify-content-end my-auto  mt-3 mt-sm-0">
                        <div class="me-4">
                            <span class=" text-muted fs-12">Open</span>
                            <p class="mb-0 text-sm  fw-semibold">{{number_format($interactions['open'])}}</p>
                        </div>
                        <div class="me-4">
                            <span class=" text-muted fs-12">Pending</span>
                            <p class="mb-0 text-sm  fw-semibold">{{number_format($interactions['pending'])}}</p>
                        </div>
                        <div class="me-4">
                            <span class=" text-muted fs-12">Assigned</span>
                            <p class="mb-0 text-sm  fw-semibold">{{number_format($interactions['assign'])}}</p>
                        </div>
                        <div class="me-4">
                            <span class=" text-muted fs-12">Resolved</span>
                            <p class="mb-0 text-sm  fw-semibold">{{number_format($interactions['resolved'])}}</p>
                        </div>
                    </div>
                </div>
                <div id="interactionChart"></div>
            </div>
        </div>
        <!--/ Total Income -->
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/apexcharts/apexcharts.js')}}"></script>
<script>
    const interactionChartEl = document.querySelector("#interactionChart");
    let urlParts = window.location.pathname.split('/');
    let param = urlParts.pop() || urlParts.pop(); // Menghindari trailing slash
    let chart1;

    if (interactionChartEl) {
        fetch(`/administrator/business/analytic/${param}`)
            .then((response) => response.json())
            .then((data) => {
                // Generate full date range for the current month
                const now = new Date();
                const daysInMonth = new Date(
                    now.getFullYear(),
                    now.getMonth() + 1,
                    0
                ).getDate();
                const fullDates = Array.from({
                        length: daysInMonth
                    },
                    (_, i) => {
                        const day = i + 1;
                        return `${now.getFullYear()}-${(now.getMonth() + 1)
                        .toString()
                        .padStart(2, "0")}-${day
                        .toString()
                        .padStart(2, "0")}`;
                    }
                );

                // Map API data to a dictionary
                const dataMap = data.reduce((acc, item) => {
                    acc[item.date] = item.count;
                    return acc;
                }, {});

                // Create final dataset with missing dates filled with 0
                const categories = fullDates;
                const seriesData = fullDates.map((date) => dataMap[date] || 0);

                // Config untuk ApexCharts
                const options = {
                    chart: {
                        height: 250,
                        type: "area",
                        toolbar: false,
                        dropShadow: {
                            enabled: true,
                            top: 14,
                            left: 2,
                            blur: 3,
                            color: "#7367F0",
                            opacity: 0.15,
                        },
                    },
                    series: [{
                        name: "Interactions",
                        data: seriesData,
                    }, ],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 3,
                        curve: "smooth"
                    },
                    colors: ["#7367F0"],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shade: "dark",
                            shadeIntensity: 0.8,
                            opacityFrom: 0.7,
                            opacityTo: 0.25,
                            stops: [0, 95, 100],
                        },
                    },
                    grid: {
                        show: true,
                        borderColor: "#ebebeb"
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: "13px",
                                colors: "#6e6b7b"
                            },
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => val.toLocaleString(),
                            style: {
                                fontSize: "13px",
                                colors: "#6e6b7b"
                            },
                        },
                    },
                };

                // Buat chart baru atau update jika sudah ada
                if (chart1) {
                    chart1.updateOptions(options);
                } else {
                    chart1 = new ApexCharts(interactionChartEl, options);
                    chart1.render();
                }
            })
            .catch((error) => {
                console.error("Error fetching interaction data:", error);
            });
    }

    // Fungsi untuk update warna chart
    function marketCap() {
        const myVarVal = "#FF5733"; // Ganti dengan warna yang diinginkan
        chart1.updateOptions({
            colors: ["rgb(" + myVarVal + ")"],
        });
    }
</script>
@endsection