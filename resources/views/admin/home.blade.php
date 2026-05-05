@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-8 col-sm-12 mb-4">
        <div class="card">
            <div class="row row-bordered g-0">
                <div class="col-12 card custom-card">
                    <div class="card-header" style="display: block;">
                        <div class="card-title mb-0">{{ __('dashboard.analysis_ai_usage') }}</div>
                        <small class="card-subtitle">{{ __('dashboard.trend_ai_usage') }}</small>
                    </div>
                    <div class="card-body">
                        <div id="responseAiChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Total Income -->
    </div>
    <div class="col-lg-4 col-sm-12 row p-0 m-0">

        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.merchants') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['merchants'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-buildings fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.business') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['business'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-building fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.package_name') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['packages'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-id-card fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.earn_topup') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['topup'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-buildings fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.human_agent') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['users'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-user-circle fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between mb-3">
                        <span class="text-muted">{{ __('dashboard.agent_ai') }}</span>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="flex-1">
                            <h5 class="mb-2">{{number_format($summary['finetunnels'])}}</h5>
                        </div>
                        <span class="ms-2 avatar bg-primary-transparent">
                            <i class="bx bx-bot fs-22"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-lg-3 col-sm-6">
        <div class="card-background flex-fill">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="fw-medium mb-1 text-muted">{{ __('dashboard.whatsapp_device') }}</p>
                            <h5 class="mb-0">{{number_format($summary['devices'])}}</h5>
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
                            <p class="fw-medium mb-1 text-muted">{{ __('dashboard.whatsapp_blast') }}</p>
                            <h5 class="mb-0">{{number_format($summary['blast_w'])}}</h5>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bx-send fs-20"></i>
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
                            <p class="fw-medium mb-1 text-muted">{{ __('dashboard.email_blast') }}</p>
                            <h5 class="mb-0">{{number_format($summary['blast_e'])}}</h5>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bx-envelope fs-20"></i>
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
                            <p class="fw-medium mb-1 text-muted">{{ __('dashboard.scraping') }}</p>
                            <h5 class="mb-0">{{number_format($summary['scraping'])}}</h5>
                        </div>
                        <div class="avatar avatar-md br-4 bg-primary-transparent ms-auto">
                            <i class="bx bx-map fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-lg-6 col-sm-12 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title m-0 me-2">{{ __('dashboard.expiring_packages') }}</div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 font-weight-semibold">
                    @foreach($mustFollow as $follow)

                    <li class="list-item mb-4">
                        <div class="flex-between">
                            <div class="flex-1 d-flex align-items-center">
                                <div class="flex-1 pos-relative">
                                    <a aria-label="anchor" href="javscript:void(0);" class="masked-link"></a>
                                    <h6 class="fs-14 mb-0">{{$follow->business->name ?? ''}}</h6>
                                    <span class="fs-12 text-muted">{{$follow->business->merchant->name ?? ''}} </span>
                                </div>
                            </div>
                            <div class="min-w-fit-content text-end">
                                <a href="javascript:void(0);" class="d-block text-primary op-9"> {{$follow->last_expire_date}} </a>
                                <span class="fs-12 tx-muted">{{$follow->package->name ?? ''}}</span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-12 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title m-0 me-2">{{ __('dashboard.unpaid') }}</div>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush mb-0">
                    @foreach($notPayment as $payment)
                    <li class="list-group-item p-0 mb-2">
                        <a href="javascript:void(0);">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="fw-semibold mb-0 fs-14">{{$payment->business->name ?? ''}} </p>
                                        <span class="text-muted fs-12">{{$payment->type == 'package' ? ($payment->package->name ?? '') : 'TopUp' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold d-flex align-items-center fs-16">
                                        {{number_format($payment->final_total)}}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title m-0 me-2">{{ __('dashboard.business_not_subscribed') }}</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-hover border table-bordered">
                        <thead class="border-top">
                            <tr>
                                <th scope="row" class="border-bottom-0">{{ __('dashboard.business_name') }}</th>
                                <th scope="row" class="border-bottom-0">{{ __('dashboard.merchants') }}</th>
                                <th scope="row" class="border-bottom-0">{{ __('dashboard.owner') }}</th>
                                <th scope="row" class="border-bottom-0">{{ __('dashboard.registration_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($merchantNotPackage as $not)
                            <tr>
                                <td>
                                    {{$not->name}}
                                </td>
                                <td>{{$not->merchant->name ?? ''}} </td>
                                <td>{{$not->merchant->owner->name ?? ''}} </td>
                                <td style="position: relative;">
                                    {{$not->created_at->format('Y-m-d')}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/apexcharts/apexcharts.js')}}"></script>
<script>
    const responseAiChartEl = document.querySelector("#responseAiChart");

    let chart1;

    if (responseAiChartEl) {
        fetch("/administrator/dashboard/response-ai")
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
                    chart1 = new ApexCharts(responseAiChartEl, options);
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