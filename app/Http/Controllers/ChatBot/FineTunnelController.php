<?php

namespace App\Http\Controllers\ChatBot;

use App\Http\Controllers\Controller;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\FineTunnelDocument;
use App\Models\Master\SubDisctrict;
use App\Models\Setting;
use App\Observers\ChatBot\FineTunnelObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\Master\CourierObserver;
use App\Observers\Master\DirectoryObserver;
use App\Observers\Master\LabelObserver;
use App\Observers\UserObserver;
use App\Services\ChatBot\RagService;
use App\Services\GoogleUserSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FineTunnelController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Fine Tunnel Ai Controllers
    |--------------------------------------------------------------------------
    */

    protected $fineTunnelObserver;
    protected $openAiServiceObserver;
    protected $labelsObserver;
    protected $courierObserver;
    protected $directoryObserver;
    protected $usersObserver;
    protected $googleSheetService;
    protected $ragDocumentService;
    protected $geminiAiServiceObserver;

    public function __construct(
        FineTunnelObserver $fineTunnelObserver,
        OpenAiServiceObserver $openAiServiceObserver,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        LabelObserver $labelObserver,
        CourierObserver $courierObserver,
        DirectoryObserver $directoryObserver,
        UserObserver $usersObserver,
        GoogleUserSheetService $googleSheetService,
        RagService $ragDocumentService
    ) {
        $this->fineTunnelObserver       = $fineTunnelObserver;
        $this->openAiServiceObserver    = $openAiServiceObserver;
        $this->labelsObserver           = $labelObserver;
        $this->courierObserver          = $courierObserver;
        $this->directoryObserver        = $directoryObserver;
        $this->usersObserver            = $usersObserver;
        $this->googleSheetService       = $googleSheetService;
        $this->ragDocumentService       = $ragDocumentService;
        $this->geminiAiServiceObserver  = $geminiAiServiceObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Fine Tunnel Ai List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $finetunnels        = $this->fineTunnelObserver->getData($request)->select(['id', 'name', 'model_ai', 'history_limit', 'context_limit', 'delay', 'message_limit'])->paginate(12);
        $pagination         = array(
            'current_page'      => $finetunnels->currentPage(),
            'to_page'           => $finetunnels->lastPage(),
            'per_page'          => $finetunnels->perPage(),
            'first_item'        => $finetunnels->firstItem(),
            'last_item'         => $finetunnels->lastItem(),
            'links'             => $finetunnels->linkCollection()->toArray()
        );

        return view('finetunnel.index', ['page'  => __('page.fine_tunnel.page'), 'breadcumb' => true], compact('finetunnels', 'pagination'));
    }

    public function components(Request $request)
    {
        $finetunnels   = $this->fineTunnelObserver->getData($request)->get(['id', 'name']);
        return response()->json($finetunnels, 200);
    }


    public function uploadRagDocument(Request $request, FineTunnel $fineTunnel)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,docx,xlsx,xls,csv|max:8192',
        ]);

        // Check OpenAI Key
        $openAiKey = Setting::withoutGlobalScopes()
            ->whereNull('merchant_id')
            ->value('open_ai_key');

        if (!$openAiKey) {
            return response()->json([
                'success' => false,
                'message' => 'OpenAI API key belum dikonfigurasi. Hubungi administrator.'
            ], 400);
        }

        $file = $request->file('document');
        $fileSize = $file->getSize();
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        // Get package limits
        $packageActive = $fineTunnel->business->package_active;

        if (!$packageActive && $fineTunnel->business->merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki paket aktif. Silakan upgrade paket terlebih dahulu.'
            ], 400);
        }

        $maxPerUpload = (float)($packageActive->max_per_upload ?? 10);
        $maxTotalRag = (float)($packageActive->max_total_rag ?? 20);

        // Check per file size limit
        if ($fileSizeMB > $maxPerUpload) {
            return response()->json([
                'success' => false,
                'message' => "Ukuran file melebihi batas maksimal {$maxPerUpload}MB per upload. File Anda: {$fileSizeMB}MB"
            ], 400);
        }

        // Check storage for this FineTunnel
        $storageCheck = $this->checkRagStorage($fineTunnel, $fileSize);

        if (!$storageCheck['available']) {
            return response()->json([
                'success' => false,
                'message' => "Storage RAG tidak mencukupi. Total limit: {$storageCheck['total_storage']}MB, Terpakai: {$storageCheck['used_storage']}MB, File baru: {$fileSizeMB}MB"
            ], 400);
        }

        // Check All Storage
        $generalStorage     = $this->checkStorage($fineTunnel->business, $fileSize);

        if (!$generalStorage['available']) {
            return response()->json([
                'success' => false,
                'message' => "Storage tidak mencukupi. Total limit: {$storageCheck['total_storage']}MB, Terpakai: {$storageCheck['used_storage']}MB, File baru: {$fileSizeMB}MB"
            ], 400);
        }


        try {
            // Process document (synchronous)
            $result = $this->ragDocumentService->processDocument(
                $fineTunnel,
                $file
            );

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diproses',
                'data' => [
                    'id' => $result['document']->id,
                    'filename' => $result['document']->filename,
                    'file_size' => $result['document']->file_size,
                    'file_size_mb' => $fileSizeMB,
                    'total_chunks' => $result['total_chunks'],
                    'successful_chunks' => $result['successful_chunks'],
                    'failed_chunks' => $result['failed_chunks'],
                    'total_images' => $result['total_images'],
                    'status' => $result['document']->status,
                    'file_path' => asset('storage/' . $result['document']->file_path)
                ],
                'storage' => [
                    'used' => $storageCheck['used_storage'] + $fileSizeMB,
                    'total' => $storageCheck['total_storage'],
                    'remaining' => $storageCheck['remaining_storage'] - $fileSizeMB
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Check RAG storage availability for a FineTunnel
     */
    private function checkRagStorage(FineTunnel $fineTunnel, $newFileSize = 0)
    {
        $merchant = $fineTunnel->business->merchant;

        $totalSize          = 0;
        $generalPath        = "uploads/folders/{$fineTunnel->business_id}/ai-training";
        $aiTrainigPath      = "uploads/folders/{$fineTunnel->business_id}/ai-training/{$fineTunnel->id}";

        if (Storage::disk('local')->exists($aiTrainigPath)) {
            $files = Storage::disk('local')->allFiles($aiTrainigPath);
            foreach ($files as $file) {
                $totalSize += Storage::disk('local')->size($file);
            }
        }

        $usedStorageMB = round($totalSize / 1024 / 1024, 2);
        $fileSizeMB = round($newFileSize / 1024 / 1024, 2);

        if ($merchant) {
            $packageActive = $merchant->package_active;

            if (!$packageActive) {
                return [
                    'available' => false,
                    'total_storage' => 0,
                    'used_storage' => 0,
                    'remaining_storage' => 0,
                    'file_size' => round($newFileSize / 1024 / 1024, 2),
                    'has_package' => false
                ];
            }

            $maxTotalRagMB = (float)$packageActive->max_total_rag; // in MB 
            $remainingStorage = $maxTotalRagMB - $usedStorageMB;

            return [
                'available' => $maxTotalRagMB > 0 && ($usedStorageMB + $fileSizeMB) <= $maxTotalRagMB,
                'total_storage' => $maxTotalRagMB,
                'used_storage' => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size' => $fileSizeMB,
                'has_package' => true
            ];
        } else {
            // Admin unlimited storage
            return [
                'available' => true,
                'total_storage' => 9999999,
                'used_storage' => $usedStorageMB,
                'remaining_storage' => 9999999,
                'file_size' => round($newFileSize / 1024 / 1024, 2),
                'has_package' => true
            ];
        }
    }

    public function checkStorage($setting, $fileSize = 0)
    {
        if ($setting->merchant) {
            $totalSize  = 0;
            $path       = "uploads/folders/{$setting->id}";

            if (Storage::disk('local')->exists($path)) {
                $files = Storage::disk('local')->allFiles($path);
                foreach ($files as $file) {
                    $totalSize += Storage::disk('local')->size($file);
                }
            }

            // Convert to MB
            $usedStorageMB  = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB     = round($fileSize / 1024 / 1024, 2);

            // Get total storage
            $storageFromSubscribe   = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons      = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage           = $storageFromSubscribe + $storageFromAddons;

            // Check if storage is available
            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available'         => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage'     => $totalStorage,
                'used_storage'      => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size'         => $fileSizeMB,
                'has_package'       => $totalStorage > 0
            ];
        } else {
            return [
                'available'         => true,
                'total_storage'     => 9999999,
                'used_storage'      => 0,
                'remaining_storage' => 9999,
                'file_size'         => 9999,
                'has_package'       => 9999
            ];
        }
    }

    /**
     * Get RAG Documents List
     */
    public function getRagDocuments(FineTunnel $fineTunnel)
    {
        $documents = $fineTunnel->documents()
            ->with('chunks')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                $imageCount = $doc->chunks()->whereNotNull('image_path')->count();

                return [
                    'id' => $doc->id,
                    'filename' => $doc->filename,
                    'file_type' => $doc->file_type,
                    'file_size' => $doc->file_size,
                    'file_size_formatted' => $this->formatBytes($doc->file_size),
                    'total_chunks' => $doc->total_chunks,
                    'image_count' => $imageCount,
                    'status' => $doc->status,
                    'created_at' => $doc->created_at->diffForHumans(),
                    'file_path' => asset($doc->file_path)
                ];
            });

        // Get storage info
        $storageCheck = $this->checkRagStorage($fineTunnel, 0);
        // Get package limits
        $packageActive = $fineTunnel->business->package_active;
        $maxPerUpload = $packageActive ? (float)$packageActive->max_per_upload : 10;
        $maxTotalRag = $packageActive ? (float)$packageActive->max_total_rag : 20;

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'storage' => [
                'used' => $storageCheck['used_storage'],
                'total' => $storageCheck['total_storage'],
                'remaining' => $storageCheck['remaining_storage'],
                'used_formatted' => $this->formatBytes($storageCheck['used_storage'] * 1024 * 1024),
                'total_formatted' => $this->formatBytes($storageCheck['total_storage'] * 1024 * 1024),
                'remaining_formatted' => $this->formatBytes($storageCheck['remaining_storage'] * 1024 * 1024),
                'percentage' => $storageCheck['total_storage'] > 0
                    ? round(($storageCheck['used_storage'] / $storageCheck['total_storage']) * 100, 2)
                    : 0
            ],
            'limits' => [
                'max_per_upload' => $maxPerUpload,
                'max_per_upload_formatted' => $this->formatBytes($maxPerUpload * 1024 * 1024),
                'max_total_rag' => $maxTotalRag,
                'max_total_rag_formatted' => $this->formatBytes($maxTotalRag * 1024 * 1024)
            ]
        ]);
    }

    /**
     * Delete RAG Document
     */
    public function deleteRagDocument(FineTunnelDocument $document)
    {
        try {
            $fineTunnel = $document->fineTunnel;

            

            $document->delete();

            // Get updated storage info
            $storageCheck = $this->checkRagStorage($fineTunnel, 0);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen dan semua gambar berhasil dihapus',
                'storage' => [
                    'used' => $storageCheck['used_storage'],
                    'total' => $storageCheck['total_storage'],
                    'remaining' => $storageCheck['remaining_storage']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, FineTunnel $fineTunnel)
    {
        $labels     = $this->labelsObserver->getData($request)->where('type', 'keyword')->get(['id', 'name']);
        $couriers   = $this->courierObserver->getData($request)->get(['id', 'name', 'code', 'service']);
        $provinces  = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        $users      = $this->usersObserver->getData($request)->get(['id', 'name']);

        $courierStatus  = true;
        $gsheet         = true;
        $business   = Setting::find(my_business());

        if ($business->merchant_id != null && $business->package_active) {
            if ($business->package_active->cek_ongkir == 'no') {
                $courierStatus  = false;
            }

            if ($business->package_active->google_sheet == 'no') {
                $gsheet  = false;
            }
        }


        return view('finetunnel.update', ['page' => __('page.fine_tunnel.edit'), 'breadcumb' => true], compact('fineTunnel', 'labels', 'couriers', 'courierStatus', 'provinces', 'users', 'gsheet'));
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'              => 'required',
            'model_ai'          => 'required|in:standart,advanced',
            'history_limit'     => 'required|numeric|min:1',
            'context_limit'     => 'required|numeric|min:1',
            'delay'             => 'required|numeric|min:1',
            'message_limit'     => 'required|numeric|min:1',
        ]);

        $validationCheck = $this->fineTunnelObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.chatbot_limit')]);
        }


        try {

            $fineTunnel   = $this->fineTunnelObserver->createData($request, '', '');
            return redirect()->route('finetunnel.update', $fineTunnel->id)->with(['flash'    => __('general.success_add_data')]);
        } catch (\Exception $e) {

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, FineTunnel $fineTunnel)
    {
        $this->validate($request, [
            'name'              => 'required',
            'model_ai'          => 'required|in:standart,advanced',
            'history_limit'     => 'required|numeric|min:1',
            'context_limit'     => 'required|numeric|min:1',
            'delay'             => 'required|numeric|min:1',
            'message_limit'     => 'required|numeric|min:1',
            'description'       => 'required|max:15000',
            'image'             => 'mimes:jpg,jpeg,png,webp',
        ]);



        try {

            DB::beginTransaction();


            $description    = $request->description;

            $image  = '';

            if ($request->image) {
                $this->unlinkFile($fineTunnel->welcome_imge);
                $image =  $this->uploadImage($request, 'image', 'finetunnels');
            }

            $this->fineTunnelObserver->updateData($request, $fineTunnel, $description, $image);
            $this->fineTunnelObserver->createFollowUps($request, $fineTunnel);
            $this->fineTunnelObserver->createDetails($request, $fineTunnel);
            $this->fineTunnelObserver->createGSheet($request, $fineTunnel);

            $fineTunnel->couriers()->delete();
            if (isset($request->couriers) && count($request->couriers) > 0) {
                $this->fineTunnelObserver->createCouriers($fineTunnel, $request);
            }

            if (!empty($request->sub_district_id)) {
                $subdistrict    = SubDisctrict::find($request->sub_district_id);

                if ($subdistrict) {
                    $province       = $subdistrict->district->city->province->name ?? '';
                    $cityType       = $subdistrict->district->city->type ?? '';
                    $cityName       = $subdistrict->district->city->name ?? '';
                    $district       = $subdistrict->district->name ?? '';
                    $sub            = $subdistrict->name ?? '';
                    $fineTunnel->update([
                        'address'           => 'Provinsi ' . $province . ', ' . $cityType . ' ' . $cityName . ', Kecamatan ' . $district . ', ' . $sub,
                        'zip_code'          => $subdistrict->postal_code,
                        'sub_district_id'   => $subdistrict->id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('finetunnel')->with(['flash'    => __('general.success_update')]);
        } catch (\Exception $e) {

            DB::rollBack();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(FineTunnel $fineTunnel)
    {
        $this->unlinkFile($fineTunnel->filejson);

        $settings = Setting::first(['open_ai_key']);
        if ($fineTunnel->fine_tunnel_id != null) {
            $response = $this->openAiServiceObserver->getFileTun($fineTunnel, $settings->open_ai_key);

            if ($response->status() == 200) {
                $response = $this->openAiServiceObserver->deleteFileTun($fineTunnel, $settings->open_ai_key);
            }
        }

        $fineTunnel->couriers()->delete();
        $this->fineTunnelObserver->deleteData($fineTunnel);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Upload DataSet To Open Ai
    |--------------------------------------------------------------------------
    */

    public function uploadDataSet(FineTunnel $fineTunnel)
    {
        $settings = Setting::first(['open_ai_key']);

        // Check Api key
        if ($settings->open_ai_key == null) {
            return redirect()->back()->with(['gagal'    => __('finetunnel.please_set_open_ai_api_key')]);
        }

        // Check File Tunnel Ready or Not and Delete old file
        if ($fineTunnel->fine_tunnel_id != null) {
            $response = $this->openAiServiceObserver->getFileTun($fineTunnel, $settings->open_ai_key);

            if ($response->status() == 200) {
                $response = $this->openAiServiceObserver->deleteFileTun($fineTunnel, $settings->open_ai_key);
                if ($response->status() != 200) {
                    $responseBody   = json_decode($response->body());
                    return redirect()->back()->with(['gagal'    => $responseBody->error->message]);
                }
            }
        }

        // Upload New FineTunnel
        $fileJson   = explode("/", $fineTunnel->filejson);
        $response   = $this->openAiServiceObserver->uploadFileTune($fineTunnel, $settings->open_ai_key, $fileJson[2]);

        if ($response->status() == 200) {
            $responseBody   = json_decode($response->body());
            $fineTunnel->update([
                'fine_tunnel_id'    => $responseBody->id,
                'status'            => 'processed'
            ]);
            return redirect()->back()->with(['flash'    => __('finetunnel.success_upload_fine_tunnel')]);
        } else {
            $responseBody   = json_decode($response->body());
            return redirect()->back()->with(['gagal'    => $responseBody->error->message]);
        }
    }

    public function validateSpreadsheet(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $validation = $this->googleSheetService->validateSpreadsheetStructure($request->url);

            return response()->json($validation, $validation['valid'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Preview data dari Google Spreadsheet sebelum disimpan
     */
    public function previewSpreadsheet(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $previewData = $this->googleSheetService->getPreviewData($request->url);

            return response()->json([
                'success' => true,
                'message' => 'Preview data berhasil diambil',
                'data' => $previewData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Test AI Chat - FineTunnel Version (FIXED)
    |--------------------------------------------------------------------------
    | Response format: responses array dengan type dan content
    */

    public function testAiChat(Request $request)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string',
                'description' => 'nullable|string',
                'model_ai' => 'nullable|in:standart,advanced',
                'history' => 'nullable|array',
                'data_trainings' => 'nullable|array',
                'data_trainings.*.command' => 'nullable|string',
                'data_trainings.*.answer' => 'nullable|string',
                'google_sheets' => 'nullable|array',
                'google_sheets.*.url' => 'nullable|string',
                'fine_tunnel_id' => 'nullable|exists:fine_tunnels,id',
            ]);

            // Get settings
            $generalSetting = Setting::withoutGlobalScopes()->whereNull('merchant_id')->first();
            $business = Setting::find(my_business());

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan Refresh dan coba ulang'
                ], 500);
            }

            // 🆕 DETERMINE AI PROVIDER
            $aiOption = $generalSetting->ai_option ?? 'chatgpt';

            // Check API Key based on provider
            if ($aiOption === 'gemini') {
                $apiKey = $generalSetting->open_ai_key;
                if (!$apiKey) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gemini API key tidak ditemukan'
                    ], 500);
                }
            } else {
                $apiKey = $generalSetting->open_ai_key;
                if (!$apiKey) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OpenAI API key tidak ditemukan'
                    ], 500);
                }
            }

            // Check credit limits
            if ($business->merchant) {
                $topup_limit = $business->package_active_topup->sisa_credit ?? 0;
                $package_credit = $business->package_active->sisa_credit ?? 0;

                if ($topup_limit < 1 && $package_credit < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kredit Ai anda tidak cukup'
                    ], 500);
                }
            }

            // Build conversations from history
            $conversations = $this->buildConversationsFromHistory($validated['history'] ?? []);

            // 🆕 GET RAG CONTEXT - HANYA UNTUK OPENAI
            $ragContext = '';
            if ($aiOption === 'chatgpt' && !empty($validated['fine_tunnel_id'])) {
                $fineTunnel = FineTunnel::find($validated['fine_tunnel_id']);
                if ($fineTunnel) {
                    $conversationsData = [];
                    foreach ($conversations as $conv) {
                        if (!empty($conv['message'])) {
                            $conversationsData[] = [
                                'role' => $conv['from'] === 'user' ? 'user' : 'assistant',
                                'content' => $conv['message'],
                            ];
                        }
                    }

                    $ragContext = $this->getRagContext($fineTunnel, $validated['message'], $conversationsData);
                }
            }

            // 🆕 DETECT INTENT - ROUTE TO PROVIDER
            $intentResult = $this->detectIntent(
                $validated['message'],
                $apiKey,
                $conversations,
                $validated,
                $ragContext,
                $aiOption
            );

            if (!$intentResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendeteksi intent'
                ], 500);
            }

            $intent = $intentResult['intent'];
            $tokensUsed = $intentResult['tokensUsed'];

            // 🆕 HANDLE INTENT - ROUTE TO PROVIDER
            $responseResult = $this->handleIntent(
                $intent,
                $validated['message'],
                $apiKey,
                $conversations,
                $validated,
                $ragContext,
                $aiOption
            );

            if (!$responseResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses response'
                ], 500);
            }

            // 🆕 CALCULATE CREDIT - DIFFERENT PER PROVIDER
            $totalTokens = $tokensUsed + $responseResult['tokensUsed'];

            if ($aiOption === 'gemini') {
                $creditUsed = $this->geminiAiServiceObserver->calculateCompletions(
                    $validated['model_ai'] ?? 'standart',
                    $totalTokens,
                    0
                );
            } else {
                $creditUsed = $this->openAiServiceObserver->calculateCompletions(
                    $validated['model_ai'] ?? 'standart',
                    $totalTokens
                );
            }

            if ($business->merchant) {
                $this->deductCredits($business, $creditUsed);
            }

            return response()->json([
                'success' => true,
                'responses' => $responseResult['responses'],
                'metadata' => [
                    'intent' => $intent->intent ?? 'question',
                    'provider' => $aiOption === 'gemini' ? 'Gemini' : 'OpenAI',
                    'model' => $validated['model_ai'] === 'advanced' ? 'Advanced' : 'Standart',
                    'tokens_used' => $totalTokens,
                    'credit_used' => $creditUsed,
                    'training_used' => $responseResult['training_used'] ?? null,
                    'rag_used' => !empty($ragContext)
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Test AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RAG Context for Test AI
     */
    private function getRagContext($fineTunnel, string $query, $conversations = []): string
    {
        try {
            $similarChunks = $this->ragDocumentService->searchSimilarChunks($fineTunnel, $query, $conversations, 5);

            if (empty($similarChunks)) {
                return '';
            }

            $context = '';

            foreach ($similarChunks as $item) {
                $chunk = $item['chunk'];
                $similarity = $item['similarity'];

                if ($similarity < 0.4) {
                    continue;
                }

                $metadata = $chunk->metadata;
                $source = "Dokumen: {$chunk->document->filename}";

                // Add source info
                if (isset($metadata['page'])) {
                    $source .= ", Halaman: {$metadata['page']}";
                } elseif (isset($metadata['sheet'])) {
                    $source .= ", Sheet: {$metadata['sheet']}, Baris: {$metadata['row']}";
                } elseif (isset($metadata['section'])) {
                    $source .= ", Bagian: {$metadata['section']}";
                }

                $similarityPercent = round($similarity * 100, 1);
                $source .= " (Relevansi: {$similarityPercent}%)";

                $context .= "\n[{$source}]\n";
                $context .= $chunk->content . "\n";

                if (isset($metadata['image_url'])) {
                    $context .= "URL Gambar: {$metadata['image_url']}\n";
                }
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('RAG Context Error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Detect Intent - Router
     */
    private function detectIntent($message, $apiKey, $conversations, $validated, $ragContext = '', $aiOption = 'chatgpt')
    {
        if ($aiOption === 'gemini') {
            return $this->detectIntentWithGemini($message, $apiKey, $conversations, $validated);
        }

        return $this->detectIntentWithOpenAI($message, $apiKey, $conversations, $validated, $ragContext);
    }

    /**
     * Detect Intent with OpenAI
     */
    private function detectIntentWithOpenAI($message, $openAiKey, $conversations, $validated, $ragContext = '')
    {
        try {
            $messages = [];

            // System prompt with RAG
            $systemPrompt = $this->buildSystemPrompt($validated, $ragContext);
            $systemPrompt .= PHP_EOL . PHP_EOL . prompt_detect_intent();

            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];

            // Add conversation history
            foreach ($conversations as $conv) {
                if (!empty($conv['message'])) {
                    $messages[] = [
                        'role' => $conv['from'] === 'user' ? 'user' : 'assistant',
                        'content' => $conv['message']
                    ];
                }
            }

            // Add current message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            // Schema
            $schema = [
                "type" => "object",
                "properties" => [
                    "intent" => [
                        "type" => "string",
                        "enum" => [
                            "media",
                            "question"
                        ]
                    ],
                    "multi_data_training_title" => [
                        "type" => "array",
                        "items" => ["type" => "string"]
                    ],
                    "medias" => [
                        "type" => "array",
                        "items" => [
                            "type" => "string",
                            "format" => "uri"
                        ]
                    ],
                    "message" => [
                        "type" => ["string", "null"]
                    ]
                ],
                "required" => ["intent"]
            ];

            // Call OpenAI
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $openAiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => ($validated['model_ai'] ?? 'standart') === 'standart' ? 'gpt-4o-mini' : 'gpt-4o',
                'messages' => $messages,
                'temperature' => 0,
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'intent_entity',
                        'schema' => $schema
                    ]
                ]
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API failed: ' . $response->status());
            }

            $responseData = $response->json();
            $intent = json_decode($responseData['choices'][0]['message']['content']);
            $tokensUsed = $responseData['usage']['total_tokens'] ?? 0;

            return [
                'success' => true,
                'intent' => $intent,
                'tokensUsed' => $tokensUsed
            ];
        } catch (\Exception $e) {
            Log::error('Detect Intent OpenAI Error: ' . $e->getMessage());
            return [
                'success' => true,
                'intent' => (object)['intent' => 'question'],
                'tokensUsed' => 0
            ];
        }
    }

    /**
     * Detect Intent with Gemini
     */
    private function detectIntentWithGemini($message, $geminiKey, $conversations, $validated)
    {
        try {
            // System prompt tanpa RAG
            $systemPrompt = $this->buildSystemPrompt($validated, '');
            $systemPrompt .= PHP_EOL . PHP_EOL . prompt_detect_intent();

            // Build contents
            $contents = [];

            // Add system instruction as first user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Baik, saya siap membantu dengan instruksi tersebut.']]
            ];

            // Add conversation history
            foreach ($conversations as $conv) {
                if (!empty($conv['message'])) {
                    $contents[] = [
                        'role' => $conv['from'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $conv['message']]]
                    ];
                }
            }

            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            // Schema
            $schema = [
                "type" => "object",
                "properties" => [
                    "intent" => [
                        "type" => "string",
                        "enum" => ["media", "question"]
                    ],
                    "multi_data_training_title" => [
                        "type" => "array",
                        "items" => ["type" => "string"]
                    ],
                    "medias" => [
                        "type" => "array",
                        "items" => ["type" => "string"]
                    ],
                    "message" => [
                        "type" => "string",
                        "nullable" => true
                    ]
                ],
                "required" => ["intent"]
            ];

            // Determine model
            $model = ($validated['model_ai'] ?? 'standart') === 'standart'
                ? 'gemini-2.5-flash'
                : 'gemini-1.5-pro-latest';

            // Call Gemini API
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $schema
                ]
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gemini API failed: ' . $response->status());
            }

            $result = $response->json();
            $intentText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $intent = json_decode($intentText);

            // Calculate tokens
            $tokensUsed = ($result['usageMetadata']['promptTokenCount'] ?? 0) +
                ($result['usageMetadata']['candidatesTokenCount'] ?? 0);

            return [
                'success' => true,
                'intent' => $intent,
                'tokensUsed' => $tokensUsed
            ];
        } catch (\Exception $e) {
            Log::error('Detect Intent Gemini Error: ' . $e->getMessage());
            return [
                'success' => true,
                'intent' => (object)['intent' => 'question'],
                'tokensUsed' => 0
            ];
        }
    }


    /**
     * Handle Intent - Router
     */
    private function handleIntent($intent, $message, $apiKey, $conversations, $validated, $ragContext = '', $aiOption = 'chatgpt')
    {
        $intentType = $intent->intent ?? 'question';

        switch ($intentType) {
            case 'media':
                return $this->handleMediaIntent($intent, $message, $apiKey, $conversations, $validated);

            case 'question':
            default:
                return $this->handleQuestionIntent($message, $apiKey, $conversations, $validated, $ragContext, $aiOption);
        }
    }

    /**
     * Handle Question Intent - Router
     */
    private function handleQuestionIntent($message, $apiKey, $conversations, $validated, $ragContext = '', $aiOption = 'chatgpt')
    {
        if ($aiOption === 'gemini') {
            return $this->handleQuestionWithGemini($message, $apiKey, $conversations, $validated);
        }

        return $this->handleQuestionWithOpenAI($message, $apiKey, $conversations, $validated, $ragContext);
    }

    /**
     * Handle Question with Gemini
     */
    private function handleQuestionWithGemini($message, $geminiKey, $conversations, $validated)
    {
        try {
            // System prompt tanpa RAG
            $systemPrompt = $this->buildSystemPrompt($validated, '');
            $systemPrompt .= " Jika kamu ingin mengirim link, kirim HANYA teks link-nya saja, misalnya: https://whatsmail.org. Jangan tambahkan karakter seperti [], {}, (), <>, atau markdown apapun di sekitarnya.";

            // Build contents
            $contents = [];

            // Add system instruction
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Baik, saya siap membantu dengan instruksi tersebut.']]
            ];

            // Add conversation history
            foreach ($conversations as $conv) {
                if (!empty($conv['message'])) {
                    $contents[] = [
                        'role' => $conv['from'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $conv['message']]]
                    ];
                }
            }

            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            // Build tools dari training data
            $tools = $this->buildGeminiToolsFromTraining($validated['data_trainings'] ?? []);

            // Determine model
            $model = ($validated['model_ai'] ?? 'standart') === 'standart'
                ? 'gemini-2.5-flash'
                : 'gemini-1.5-pro-latest';

            // Call Gemini API
            $requestData = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7
                ]
            ];

            if (!empty($tools)) {
                $requestData['tools'] = [['functionDeclarations' => $tools]];
            }

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", $requestData);

            if (!$response->successful()) {
                throw new \Exception('Gemini API failed');
            }

            $result = $response->json();
            $tokensUsed = ($result['usageMetadata']['promptTokenCount'] ?? 0) +
                ($result['usageMetadata']['candidatesTokenCount'] ?? 0);

            $candidate = $result['candidates'][0] ?? null;
            $parts = $candidate['content']['parts'] ?? [];

            $aiMessage = '';
            $trainingUsed = null;

            // Check if function was called
            $functionCall = null;
            $textResponse = '';

            foreach ($parts as $part) {
                if (isset($part['functionCall'])) {
                    $functionCall = $part['functionCall'];
                }
                if (isset($part['text'])) {
                    $textResponse .= $part['text'];
                }
            }

            if ($functionCall) {
                $functionName = $functionCall['name'];

                // Extract training ID
                if (strpos($functionName, 'training_') === 0) {
                    $trainingIndex = str_replace('training_', '', $functionName);
                    $trainingUsed = $trainingIndex;
                }

                $functionArgs = $functionCall['args'] ?? [];
                $aiMessage = $functionArgs['message'] ?? '';

                // If no message from function, call again
                if (empty($aiMessage)) {
                    $contents[] = $candidate['content'];
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [[
                            'functionResponse' => [
                                'name' => $functionName,
                                'response' => ['result' => 'Silakan berikan jawaban yang sesuai.']
                            ]
                        ]]
                    ];

                    $secondResponse = Http::timeout(60)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
                        'contents' => $contents
                    ]);

                    if ($secondResponse->successful()) {
                        $secondResult = $secondResponse->json();
                        $aiMessage = $secondResult['candidates'][0]['content']['parts'][0]['text'] ?? '';
                        $tokensUsed += ($secondResult['usageMetadata']['promptTokenCount'] ?? 0) +
                            ($secondResult['usageMetadata']['candidatesTokenCount'] ?? 0);
                    }
                }
            } else {
                $aiMessage = $textResponse;
            }

            if (empty($aiMessage)) {
                throw new \Exception('AI tidak memberikan response');
            }

            return [
                'success' => true,
                'responses' => [
                    [
                        'type' => 'text',
                        'content' => $aiMessage
                    ]
                ],
                'tokensUsed' => $tokensUsed,
                'training_used' => $trainingUsed
            ];
        } catch (\Exception $e) {
            Log::error('Handle Question Gemini Error: ' . $e->getMessage());
            return [
                'success' => true,
                'responses' => [
                    [
                        'type' => 'text',
                        'content' => 'Maaf, saya tidak bisa memproses pertanyaan Anda saat ini.'
                    ]
                ],
                'tokensUsed' => 0
            ];
        }
    }

    /**
     * Build Gemini Tools dari data training
     */
    private function buildGeminiToolsFromTraining($dataTrainings)
    {
        $tools = [];

        foreach ($dataTrainings as $index => $training) {
            if (!empty($training['command']) && !empty($training['answer'])) {
                $tools[] = [
                    'name' => 'training_' . $index,
                    'description' => $training['answer'],
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => [
                                'type' => 'string',
                                'description' => 'Gunakan fungsi ini untuk menjawab pertanyaan yang relevan terkait topic ini.'
                            ]
                        ]
                    ]
                ];
            }
        }

        return $tools;
    }

    /**
     * Handle Question with OpenAI
     */
    private function handleQuestionWithOpenAI($message, $openAiKey, $conversations, $validated, $ragContext = '')
    {
        try {
            $messages = [];

            // System prompt with RAG
            $systemPrompt = $this->buildSystemPrompt($validated, $ragContext);
            $systemPrompt .= " Jika kamu ingin mengirim link, kirim HANYA teks link-nya saja, misalnya: https://whatsmail.org. Jangan tambahkan karakter seperti [], {}, (), <>, atau markdown apapun di sekitarnya.";

            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];

            // Add conversation history
            foreach ($conversations as $conv) {
                if (!empty($conv['message'])) {
                    $messages[] = [
                        'role' => $conv['from'] === 'user' ? 'user' : 'assistant',
                        'content' => $conv['message']
                    ];
                }
            }

            // Add current message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            // Build tools
            $tools = $this->buildToolsFromTraining($validated['data_trainings'] ?? []);

            // Call OpenAI
            $requestData = [
                'model' => ($validated['model_ai'] ?? 'standart') === 'standart' ? 'gpt-4o-mini' : 'gpt-4o',
                'messages' => $messages,
            ];

            if (!empty($tools)) {
                $requestData['tools'] = $tools;
                $requestData['tool_choice'] = 'auto';
            }

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $openAiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', $requestData);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API failed');
            }

            $result = $response->json();
            $tokensUsed = $result['usage']['total_tokens'] ?? 0;
            $messageData = $result['choices'][0]['message'] ?? null;

            $aiMessage = '';
            $trainingUsed = null;

            // Check if tool was called
            if (isset($messageData['tool_calls']) && !empty($messageData['tool_calls'])) {
                $toolCall = $messageData['tool_calls'][0];
                $functionName = $toolCall['function']['name'];

                // Extract training ID
                if (strpos($functionName, 'training_') === 0) {
                    $trainingIndex = str_replace('training_', '', $functionName);
                    $trainingUsed = $trainingIndex;
                }

                // Get message from function arguments
                $arguments = json_decode($toolCall['function']['arguments'], true);
                $aiMessage = $arguments['message'] ?? '';

                // If no message, call again
                if (empty($aiMessage)) {
                    $messages[] = $messageData;
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content' => 'Silakan berikan jawaban yang sesuai.'
                    ];

                    $secondResponse = Http::timeout(60)->withHeaders([
                        'Authorization' => 'Bearer ' . $openAiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => ($validated['model_ai'] ?? 'standart') === 'standart' ? 'gpt-4o-mini' : 'gpt-4o',
                        'messages' => $messages
                    ]);

                    if ($secondResponse->successful()) {
                        $secondResult = $secondResponse->json();
                        $aiMessage = $secondResult['choices'][0]['message']['content'] ?? '';
                        $tokensUsed += $secondResult['usage']['total_tokens'] ?? 0;
                    }
                }
            } else {
                // Regular response
                $aiMessage = $messageData['content'] ?? '';
            }

            if (empty($aiMessage)) {
                throw new \Exception('AI tidak memberikan response');
            }

            return [
                'success' => true,
                'responses' => [
                    [
                        'type' => 'text',
                        'content' => $aiMessage
                    ]
                ],
                'tokensUsed' => $tokensUsed,
                'training_used' => $trainingUsed
            ];
        } catch (\Exception $e) {
            Log::error('Handle Question OpenAI Error: ' . $e->getMessage());
            return [
                'success' => true,
                'responses' => [
                    [
                        'type' => 'text',
                        'content' => 'Maaf, saya tidak bisa memproses pertanyaan Anda saat ini.'
                    ]
                ],
                'tokensUsed' => 0
            ];
        }
    }


    /**
     * Handle Media Intent
     * Return format: responses array with images
     */
    private function handleMediaIntent($intent, $message, $openAiKey, $conversations, $validated)
    {
        try {
            // Get medias dari intent
            $medias = $intent->medias ?? [];
            $intentMessage = $intent->message ?? '';

            if (empty($medias)) {
                // Fallback to question
                return $this->handleQuestionIntent($message, $openAiKey, $conversations, $validated);
            }

            // Build responses array
            $responses = [];

            // Add text response first
            if (!empty($intentMessage)) {
                $responses[] = [
                    'type' => 'text',
                    'content' => $intentMessage
                ];
            }

            // Add image responses
            foreach ($medias as $mediaUrl) {
                $responses[] = [
                    'type' => 'image',
                    'url' => $mediaUrl,
                    'caption' => ''
                ];
            }

            return [
                'success' => true,
                'responses' => $responses,
                'tokensUsed' => 0
            ];
        } catch (\Exception $e) {
            Log::error('Handle Media Error: ' . $e->getMessage());
            return [
                'success' => true,
                'responses' => [
                    [
                        'type' => 'text',
                        'content' => 'Maaf, saya tidak bisa memproses permintaan Anda saat ini.'
                    ]
                ],
                'tokensUsed' => 0
            ];
        }
    }

    /**
     * Build System Prompt
     */
    private function buildSystemPrompt($validated, $ragContext = '')
    {
        $prompt = $validated['description'] ?? '';
        $prompt .= PHP_EOL . PHP_EOL;

        // 🆕 ADD RAG CONTEXT (PRIORITAS PERTAMA)
        if (!empty($ragContext)) {
            $prompt .= "\n\n=== INFORMASI DARI DOKUMEN ===\n";
            $prompt .= $ragContext;
            $prompt .= "\n=== AKHIR INFORMASI DOKUMEN ===\n";
            $prompt .= "\nGunakan informasi di atas untuk menjawab pertanyaan user jika relevan. Jika informasi tidak cukup, jawab berdasarkan pengetahuan umummu.\n\n";
        }

        // Add data training
        if (!empty($validated['data_trainings'])) {
            foreach ($validated['data_trainings'] as $training) {
                if (!empty($training['command']) && !empty($training['answer'])) {
                    $prompt .= $training['command'] . PHP_EOL;
                    $prompt .= $training['answer'] . PHP_EOL . PHP_EOL;
                }
            }
        }

        // Add Google Sheets data
        if (!empty($validated['google_sheets'])) {
            $sheetData = $this->fetchGoogleSheetsData($validated['google_sheets']);
            if (!empty($sheetData)) {
                $prompt .= "Berikut data dari google sheet sebagai tambahan informasi: " .
                    $this->formatDataForGPT($sheetData);
            }
        }

        return $prompt;
    }

    /**
     * Build Tools dari data training
     */
    private function buildToolsFromTraining($dataTrainings)
    {
        $tools = [];

        foreach ($dataTrainings as $index => $training) {
            if (!empty($training['command']) && !empty($training['answer'])) {
                $tools[] = [
                    'type' => 'function',
                    'function' => [
                        'name' => 'training_' . $index,
                        'description' => $training['answer'],
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'description' => 'Gunakan fungsi ini untuk menjawab pertanyaan yang relevan terkait topic ini.'
                                ]
                            ]
                        ]
                    ]
                ];
            }
        }

        return $tools;
    }

    /**
     * Build conversations from history
     */
    private function buildConversationsFromHistory($history)
    {
        $conversations = [];
        foreach ($history as $item) {
            if (isset($item['role']) && isset($item['message'])) {
                $conversations[] = [
                    'from' => $item['role'] === 'user' ? 'user' : 'device',
                    'message' => $item['message']
                ];
            }
        }
        return $conversations;
    }

    /**
     * Fetch Google Sheets Data
     */
    private function fetchGoogleSheetsData($googleSheets)
    {
        $allData = [];

        foreach ($googleSheets as $sheet) {
            if (!empty($sheet['url'])) {
                try {
                    $sheetData = $this->googleSheetService->getAllDataForGPT($sheet['url']);
                    $allData = array_merge($allData, $sheetData);
                } catch (\Exception $e) {
                    Log::error('Fetch Google Sheet Error: ' . $e->getMessage());
                }
            }
        }

        return $allData;
    }

    /**
     * Format data for GPT
     */
    private function formatDataForGPT(array $data): string
    {
        $formatted = [];

        foreach ($data as $index => $item) {
            $itemText = [];
            foreach ($item as $key => $value) {
                if (!empty(trim($value))) {
                    $itemText[] = "{$key}: {$value}";
                }
            }
            $formatted[] = ($index + 1) . ". " . implode(", ", $itemText);
        }

        return implode("\n", $formatted);
    }

    /**
     * Clear test chat
     */
    public function clearTestChat(Request $request)
    {
        session()->forget('test_chat_temp');

        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil dihapus'
        ]);
    }

    private function deductCredits(Setting $business, int $total_credit_using): void
    {

        $package_credit = $business->package_active->sisa_credit ?? 0;

        if ($package_credit > 0) {
            $business->package_active->update([
                'using_credit_limit' => $business->package_active->using_credit_limit + $total_credit_using
            ]);
        } else {
            $business->package_active_topup->update([
                'using_credit_limit' => $business->package_active_topup->using_credit_limit + $total_credit_using
            ]);
        }
    }
}
