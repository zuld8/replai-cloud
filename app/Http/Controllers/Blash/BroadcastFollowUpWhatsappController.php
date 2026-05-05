<?php

namespace App\Http\Controllers\Blash;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashWhatsapp;
use App\Observers\Blash\BlashDetailObserver;
use App\Observers\Blash\BroadcastFollowUpWhatsappObserver;
use App\Observers\Master\CategoryObserver;
use App\Observers\Master\LabelObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappDeviceObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BroadcastFollowUpWhatsappController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Blash Whastapp Controllers
    |--------------------------------------------------------------------------
    */

    protected $blashWhatsappObserver;
    protected $categoryObserver;
    protected $blashDetailObserver;
    protected $templateObserver;
    protected $labelsObserver;
    protected $whatsappDeviceObserver;

    public function __construct(
        BroadcastFollowUpWhatsappObserver $blashWhatsappObserver,
        CategoryObserver $categoryObserver,
        BlashDetailObserver $blashDetailObserver,
        TemplateObserver $templateObserver,
        LabelObserver $labelsObserver,
        WhatsappDeviceObserver $whatsappDeviceObserver
    ) {
        $this->blashWhatsappObserver    = $blashWhatsappObserver;
        $this->categoryObserver         = $categoryObserver;
        $this->blashDetailObserver      = $blashDetailObserver;
        $this->templateObserver         = $templateObserver;
        $this->labelsObserver           = $labelsObserver;
        $this->whatsappDeviceObserver   = $whatsappDeviceObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Blash List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $queryArray = $request->all();
        $params = http_build_query($queryArray);

        if ($request->ajax()) {
            $blashs = $this->blashWhatsappObserver->getData($request);

            // Apply additional filters
            if ($request->status_filter) {
                $blashs->where('status', $request->status_filter);
            }

            if ($request->frequency_filter) {
                $blashs->where('schedule_frequency', $request->frequency_filter);
            }

            return DataTables::of($blashs)
                ->addColumn('campaign_info', function ($row) {
                    $deviceCount = !empty($row->devices) ? count(explode(',', $row->devices)) : 0;
                    $delayText = $row->delay ? $row->delay . ' ' . __('broadcast.upselling.seconds') : '60 ' . __('broadcast.upselling.seconds');

                    $html = '
                <div>
                    <div class="campaign-title">' . htmlspecialchars($row->name) . '</div>
                    <div class="campaign-subtitle">
                        <i class="ti ti-device-mobile text-muted me-1"></i>' . $deviceCount . ' ' . __('broadcast.upselling.devices_count') . '
                        <span class="mx-2">•</span>
                        <i class="ti ti-clock text-muted me-1"></i>' . __('broadcast.upselling.delay_label') . ' ' . $delayText . '
                    </div>
                    <div class="campaign-subtitle mt-1">
                        <span class="badge bg-light text-muted">' . ucfirst($row->whatsapp_sender_notif) . '</span>
                    </div>
                </div>';

                    return $html;
                })
                ->addColumn('schedule_info', function ($row) {
                    $frequencyLabels = [
                        'once' => __('broadcast.upselling.frequency_once'),
                        'daily' => __('broadcast.upselling.frequency_daily'),
                        'monthly' => __('broadcast.upselling.frequency_monthly'),
                        'yearly' => __('broadcast.upselling.frequency_yearly')
                    ];

                    $frequencyClass = 'freq-' . $row->schedule_frequency;
                    $frequencyLabel = $frequencyLabels[$row->schedule_frequency] ?? 'Unknown';

                    $html = '<div class="schedule-info">';
                    $html .= '<span class="frequency-badge ' . $frequencyClass . '">' . $frequencyLabel . '</span>';

                    // Schedule details based on frequency
                    if ($row->schedule_frequency === 'once') {
                        $html .= '<div class="schedule-detail">' . __('broadcast.upselling.once_send') . '</div>';
                    } elseif ($row->schedule_frequency === 'daily') {
                        $days = !empty($row->days) ? explode(',', $row->days) : [];
                        $dayLabels = [
                            'monday' => __('broadcast.upselling.day_monday_short'),
                            'tuesday' => __('broadcast.upselling.day_tuesday_short'),
                            'wednesday' => __('broadcast.upselling.day_wednesday_short'),
                            'thursday' => __('broadcast.upselling.day_thursday_short'),
                            'friday' => __('broadcast.upselling.day_friday_short'),
                            'saturday' => __('broadcast.upselling.day_saturday_short'),
                            'sunday' => __('broadcast.upselling.day_sunday_short')
                        ];
                        $dayNames = array_map(function ($day) use ($dayLabels) {
                            return $dayLabels[$day] ?? $day;
                        }, $days);
                        $html .= '<div class="schedule-detail">' . implode(', ', $dayNames) . '</div>';
                    } elseif ($row->schedule_frequency === 'monthly') {
                        $date = $row->schedule ?? 1;
                        $html .= '<div class="schedule-detail">' . __('broadcast.upselling.date_prefix') . ' ' . $date . ' ' . __('broadcast.upselling.every_month') . '</div>';
                    } elseif ($row->schedule_frequency === 'yearly') {
                        $monthNames = [
                            1 => __('broadcast.upselling.month_jan'),
                            2 => __('broadcast.upselling.month_feb'),
                            3 => __('broadcast.upselling.month_mar'),
                            4 => __('broadcast.upselling.month_apr'),
                            5 => __('broadcast.upselling.month_may'),
                            6 => __('broadcast.upselling.month_jun'),
                            7 => __('broadcast.upselling.month_jul'),
                            8 => __('broadcast.upselling.month_aug'),
                            9 => __('broadcast.upselling.month_sep'),
                            10 => __('broadcast.upselling.month_oct'),
                            11 => __('broadcast.upselling.month_nov'),
                            12 => __('broadcast.upselling.month_dec')
                        ];
                        $month = $monthNames[$row->month] ?? '';
                        $date = $row->schedule ?? 1;
                        $html .= '<div class="schedule-detail">' . $date . ' ' . $month . ' ' . __('broadcast.upselling.every_year') . '</div>';
                    }

                    // Time and date range
                    if ($row->time) {
                        $html .= '<div class="schedule-detail"><i class="ti ti-clock text-muted me-1"></i>' . substr($row->time, 0, 5) . '</div>';
                    }

                    if ($row->start_date) {
                        $startDate = date('d/m/Y', strtotime($row->start_date));
                        $endDate = $row->end_date ? date('d/m/Y', strtotime($row->end_date)) : __('broadcast.upselling.ongoing');
                        $html .= '<div class="schedule-detail"><i class="ti ti-calendar text-muted me-1"></i>' . $startDate;
                        if ($row->end_date) {
                            $html .= ' - ' . $endDate;
                        }
                        $html .= '</div>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('target_info', function ($row) {
                    $html = '<div class="target-info">';

                    // Category
                    if ($row->category) {
                        $html .= '<div><a class="text-primary fw-semibold" href="' . route('broadcast') . '?category=' . $row->category_id . '">';
                        $html .= '<i class="ti ti-category text-muted me-1"></i>' . htmlspecialchars($row->category->name);
                        $html .= '</a></div>';
                    } else {
                        $html .= '<div class="text-muted"><i class="ti ti-category text-muted me-1"></i>' . __('broadcast.upselling.all_categories') . '</div>';
                    }

                    // Labels
                    if (!empty($row->labels)) {
                        $labels = explode(',', $row->labels);
                        $labelCount = count($labels);
                        $html .= '<div class="mt-1">';
                        $html .= '<i class="ti ti-tags text-muted me-1"></i>';
                        if ($labelCount <= 2) {
                            $html .= '<span class="badge bg-light text-muted">' . implode('</span> <span class="badge bg-light text-muted">', $labels) . '</span>';
                        } else {
                            $html .= '<span class="badge bg-light text-muted">' . $labelCount . ' ' . __('broadcast.upselling.labels_count') . '</span>';
                        }
                        $html .= '</div>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('method_info', function ($row) {
                    $html = '<div>';

                    if ($row->broadcast_method == 'template') {
                        $html .= '<span class="method-badge method-template">';
                        $html .= '<i class="ti ti-template"></i> ' . __('broadcast.upselling.template_label');
                        $html .= '</span>';

                        if ($row->template) {
                            $html .= '<div class="mt-2">';
                            $html .= '<a class="text-primary small" href="' . route('broadcast') . '?template=' . $row->template_id . '" title="' . htmlspecialchars($row->template->name) . '">';
                            $html .= '<i class="ti ti-external-link me-1"></i>' . Str::limit($row->template->name, 20);
                            $html .= '</a></div>';
                        }
                    } else {
                        $html .= '<span class="method-badge method-ai">';
                        $html .= '<i class="ti ti-robot"></i> ' . __('broadcast.upselling.ai_generated');
                        $html .= '</span>';

                        if ($row->ai_prompt) {
                            $html .= '<div class="mt-2 small text-muted" title="' . htmlspecialchars($row->ai_prompt) . '">';
                            $html .= '<i class="ti ti-message-dots me-1"></i>' . Str::limit($row->ai_prompt, 30);
                            $html .= '</div>';
                        }
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status_info', function ($row) {
                    $isActive = $row->status === 'pending';
                    $statusText = $isActive ? __('broadcast.upselling.status_active') : __('broadcast.upselling.status_inactive');
                    $statusClass = $isActive ? 'success' : 'secondary';
                    $checked = !$isActive ? '' : 'checked';

                    $html = '<div class="campaign-status">';
                    $html .= '<div>';
                    $html .= '<span class="badge bg-' . $statusClass . ' mb-1">' . $statusText . '</span>';
                    $html .= '<div>';
                    $html .= '<label class="form-check form-switch mb-0">';
                    $html .= '<input class="form-check-input" type="checkbox" onchange="activationData(\'' . $row->id . '\', this)" ' . $checked . '>';
                    $html .= '</label>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group-actions">';

                    // View/Detail button
                    $html .= '<a href="' . route('broadcast.detail', $row->id) . '" class="btn btn-outline-info btn-sm" title="' . __('broadcast.upselling.btn_view_detail') . '">';
                    $html .= '<i class="ti ti-eye"></i>';
                    $html .= '</a>';

                    // Edit button
                    $html .= '<a href="' . route('broadcast.update', $row->id) . '" class="btn btn-outline-warning btn-sm" title="' . __('broadcast.upselling.btn_edit_campaign') . '">';
                    $html .= '<i class="ti ti-edit"></i>';
                    $html .= '</a>';

                    // Duplicate button
                    $html .= '<a href="' . route('broadcast.create') . '?duplicate=' . $row->id . '" class="btn btn-outline-secondary btn-sm" title="' . __('broadcast.upselling.btn_duplicate_campaign') . '">';
                    $html .= '<i class="ti ti-copy"></i>';
                    $html .= '</a>';

                    // Delete button
                    $html .= '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCampaign(\'' . $row->id . '\')" title="' . __('broadcast.upselling.btn_delete_campaign') . '">';
                    $html .= '<i class="ti ti-trash"></i>';
                    $html .= '</button>';

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['campaign_info', 'schedule_info', 'target_info', 'method_info', 'status_info', 'action'])
                ->make(true);
        }

        return view('broadcast.index', [
            'page' => 'Upselling Campaign',
            'breadcumb' => true
        ], compact('params'));
    }

    // Helper method to get campaign statistics
    public function getStatistics(Request $request)
    {
        $total = $this->blashWhatsappObserver->getData($request)->count();
        $active = $this->blashWhatsappObserver->getData($request)->where('status', 'pending')->count();
        $scheduled = $this->blashWhatsappObserver->getData($request)
            ->whereIn('schedule_frequency', ['daily', 'monthly', 'yearly'])
            ->where('status', 'pending')
            ->count();
        $completed = $this->blashWhatsappObserver->getData($request)->where('status', '!=', 'pending')->count();

        return response()->json([
            'total' => $total,
            'active' => $active,
            'scheduled' => $scheduled,
            'completed' => $completed
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $devices        = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $templates      = $this->templateObserver->getData($request)->where('type', 'whatsapp')->get(['id', 'name']);
        $labels         = $this->labelsObserver->getData($request)->get(['id', 'name']);
        return view('broadcast.create', ['page' => __('page.wa.add'), 'breadcumb' => true], compact('categories', 'templates', 'labels', 'devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, BlashWhatsapp $broadcast)
    {
        $devices        = $this->whatsappDeviceObserver->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $labels         = $this->labelsObserver->getData($request)->get(['id', 'name']);
        $templates      = $this->templateObserver->getData($request)->where("type", 'whatsapp')->get(['id', 'name']);
        return view('broadcast.update', ['page' => __('page.wa.update'), 'breadcumb' => true], compact('categories', 'labels', 'broadcast', 'templates', 'devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Custom validation rules based on schedule frequency
        $rules = [
            'name' => 'required|string|max:255',
            'delay' => 'required|integer|min:1|max:3600',
            'devices' => 'required|array|min:1',
            'whatsapp_sender_notif' => 'required|in:sequence,spin,random',
            'schedule_frequency' => 'required|in:once,daily,monthly,yearly',
            'broadcast_method' => 'required|in:ai,template',
            'category' => 'nullable|exists:categories,id',
            'label' => 'nullable|array',
            'label.*' => 'exists:labels,id',
        ];

        // Add conditional validation based on schedule frequency
        if ($request->schedule_frequency === 'daily') {
            $rules['schedule_days'] = 'required|array|min:1';
            $rules['schedule_days.*'] = 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday';
            $rules['schedule_time'] = 'required|date_format:H:i';
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'nullable|date|after:start_date';
        } elseif ($request->schedule_frequency === 'monthly') {
            $rules['schedule_date'] = 'required|integer|min:1|max:31';
            $rules['schedule_time'] = 'required|date_format:H:i';
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'nullable|date|after:start_date';
        } elseif ($request->schedule_frequency === 'yearly') {
            $rules['schedule_month'] = 'required|integer|min:1|max:12';
            $rules['schedule_yearly_date'] = 'required|integer|min:1|max:31';
            $rules['schedule_time'] = 'required|date_format:H:i';
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'nullable|date|after:start_date';
        }

        // Add conditional validation based on broadcast method
        if ($request->broadcast_method === 'ai') {
            $rules['ai_prompt'] = 'required|string|min:10|max:1000';
        } elseif ($request->broadcast_method === 'template') {
            $rules['template'] = 'required';
        }

        // Custom error messages
        $messages = [
            'name.required' => 'Judul campaign wajib diisi.',
            'name.max' => 'Judul campaign maksimal 255 karakter.',
            'delay.required' => 'Delay pengiriman wajib diisi.',
            'delay.min' => 'Delay minimal 1 detik.',
            'delay.max' => 'Delay maksimal 3600 detik (1 jam).',
            'devices.required' => 'Minimal pilih 1 perangkat.',
            'devices.min' => 'Minimal pilih 1 perangkat.',
            'whatsapp_sender_notif.required' => 'Opsi penggunaan device wajib dipilih.',
            'whatsapp_sender_notif.in' => 'Opsi penggunaan device tidak valid.',
            'schedule_frequency.required' => 'Frekuensi pengiriman wajib dipilih.',
            'schedule_frequency.in' => 'Frekuensi pengiriman tidak valid.',
            'schedule_days.required' => 'Pilih minimal 1 hari untuk pengiriman harian.',
            'schedule_days.min' => 'Pilih minimal 1 hari untuk pengiriman harian.',
            'schedule_days.*.in' => 'Hari yang dipilih tidak valid.',
            'schedule_date.required' => 'Tanggal pengiriman bulanan wajib diisi.',
            'schedule_date.min' => 'Tanggal minimal 1.',
            'schedule_date.max' => 'Tanggal maksimal 31.',
            'schedule_month.required' => 'Bulan pengiriman tahunan wajib dipilih.',
            'schedule_month.min' => 'Bulan minimal 1 (Januari).',
            'schedule_month.max' => 'Bulan maksimal 12 (Desember).',
            'schedule_yearly_date.required' => 'Tanggal pengiriman tahunan wajib diisi.',
            'schedule_yearly_date.min' => 'Tanggal minimal 1.',
            'schedule_yearly_date.max' => 'Tanggal maksimal 31.',
            'schedule_time.required' => 'Waktu pengiriman wajib diisi.',
            'schedule_time.date_format' => 'Format waktu tidak valid (HH:MM).',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'end_date.date' => 'Format tanggal berakhir tidak valid.',
            'end_date.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
            'broadcast_method.required' => 'Metode broadcast wajib dipilih.',
            'broadcast_method.in' => 'Metode broadcast tidak valid.',
            'ai_prompt.required' => 'Prompt AI wajib diisi.',
            'ai_prompt.min' => 'Prompt AI minimal 10 karakter.',
            'ai_prompt.max' => 'Prompt AI maksimal 1000 karakter.',
            'template.required' => 'Template wajib dipilih.',
            'category.exists' => 'Kategori yang dipilih tidak valid.',
            'label.*.exists' => 'Label yang dipilih tidak valid.',
        ];

        $this->validate($request, $rules, $messages);

        // Additional business logic validation
        $this->validateBusinessRules($request);

        try {
            $campaign = $this->blashWhatsappObserver->createData($request);

            return redirect()->route('broadcast')->with([
                'flash' => 'Campaign "' . $request->name . '" berhasil dibuat!',
                'flash_type' => 'success'
            ]);
        } catch (\Exception $e) {

            return redirect()->back()->withInput()->with([
                'flash' => 'Gagal membuat campaign. Silakan coba lagi.',
                'flash_type' => 'error'
            ]);
        }
    }

    private function validateBusinessRules(Request $request)
    {
        // Validate date logic for monthly campaigns
        if ($request->schedule_frequency === 'monthly' && $request->schedule_date > 28) {
            // Warn about months with fewer than 31 days
            if ($request->schedule_date === 31) {
                // Will skip February, April, June, September, November
                session()->flash('warning', 'Pengiriman pada tanggal 31 akan dilewati pada bulan yang hanya memiliki 30 hari atau kurang.');
            } elseif ($request->schedule_date > 28) {
                // Will skip February in non-leap years
                session()->flash('warning', 'Pengiriman pada tanggal ' . $request->schedule_date . ' akan dilewati pada bulan Februari.');
            }
        }

        // Validate yearly date logic
        if ($request->schedule_frequency === 'yearly') {
            if ($request->schedule_month == 2 && $request->schedule_yearly_date > 28) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['schedule_yearly_date' => ['Tanggal 29-31 tidak valid untuk bulan Februari.']]
                );
            }

            $monthsWith30Days = [4, 6, 9, 11]; // April, June, September, November
            if (in_array($request->schedule_month, $monthsWith30Days) && $request->schedule_yearly_date > 30) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['schedule_yearly_date' => ['Tanggal 31 tidak valid untuk bulan yang dipilih.']]
                );
            }
        }
    }


    private function prepareScheduleData(Request $request)
    {
        $scheduleData = [
            'days' => null,
            'schedule_date' => null,
            'month' => null,
            'schedule_yearly_date' => null,
            'time' => null,
            'start_date' => null,
            'end_date' => null,
        ];

        if ($request->schedule_frequency === 'daily') {
            $scheduleData['days'] = !empty($request->schedule_days) ? implode(",", $request->schedule_days) : null;
            $scheduleData['time'] = $request->schedule_time;
            $scheduleData['start_date'] = $request->start_date;
            $scheduleData['end_date'] = $request->end_date;
        } elseif ($request->schedule_frequency === 'monthly') {
            $scheduleData['schedule_date'] = $request->schedule_date;
            $scheduleData['time'] = $request->schedule_time;
            $scheduleData['start_date'] = $request->start_date;
            $scheduleData['end_date'] = $request->end_date;
        } elseif ($request->schedule_frequency === 'yearly') {
            $scheduleData['month'] = $request->schedule_month;
            $scheduleData['schedule_yearly_date'] = $request->schedule_yearly_date;
            $scheduleData['time'] = $request->schedule_time;
            $scheduleData['start_date'] = $request->start_date;
            $scheduleData['end_date'] = $request->end_date;
        }

        return $scheduleData;
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, BlashWhatsapp $broadcast)
    {
        $this->validate($request, [
            'name'                  => 'required',
            'delay'                 => 'required',
            'devices'               => 'required',
            'whatsapp_sender_notif' => 'required|in:sequence,spin,random',
            'schedule_frequency'    => 'required|in:once,daily,monthly,yearly',
            'schedule_days'         => 'required_if:schedule_frequency,daily',
            'schedule_date'         => 'required_if:schedule_frequency,monthly,yearly',
            'schedule_month'        => 'required_if:schedule_frequency,monthly',
            'schedule_time'         => 'required_if:schedule_frequency,monthly,yearly,daily',
            'start_date'            => 'required_if:schedule_frequency,monthly,yearly,daily',
            'end_date'              => 'required_if:schedule_frequency,monthly,yearly,daily',
            'broadcast_method'      => 'required|in:ai,template',
            'ai_prompt'             => 'required_if:broadcast_method,ai',
            'template'              => 'required_if:broadcast_method,template'
        ]);

        $this->blashWhatsappObserver->updateData($request, $broadcast);

        return redirect()->route('broadcast')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(BlashWhatsapp $broadcast)
    {
        $this->blashWhatsappObserver->deleteData($broadcast);

        return response()->json([
            'status'    => true,
            'message'   => __('general.success_deleted'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Details List
    |--------------------------------------------------------------------------
    */

    public function detail(Request $request, BlashWhatsapp $broadcast)
    {
        if ($request->ajax()) {

            $blashs         = $this->blashDetailObserver->getData($request, $broadcast);

            return DataTables::of($blashs)
                ->addColumn('store', function ($row) {
                    return $row->store->name ?? '';
                })
                ->addColumn('date', function ($row) {
                    return tanggal_indo(substr($row->created_at, 0, 10)) . ' ' . substr($row->created_at, 11, 16);
                })->addColumn('status_attribute', function ($row) {
                    $status = $row->status == 'yes' ? '' : 'checked';
                    $html = '<label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onclick="activationData(`' . $row->id . '`,this)" ' . $status . '>
                                </label>';
                    return $html;
                })->rawColumns(['store',  'date', 'status_attribute'])
                ->make(true);
        }

        return view('broadcast.detail', ['page'  => __('page.wa.detail'), 'breadcumb' => true], compact('broadcast'));
    }
}
