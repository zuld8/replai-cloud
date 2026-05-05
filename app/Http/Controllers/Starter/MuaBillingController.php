<?php

namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\InternalSetting;
use App\Models\Package\PackageTransaction;
use App\Models\Setting;
use App\Observers\Saas\BankObserver;
use App\Observers\Saas\DuitkuObserver;
use App\Observers\Saas\NotificationObserver;
use App\Observers\Saas\TransactionObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MuaBillingController extends Controller
{
    protected $transactionObserver;
    protected $notificationObserver;
    protected $whatsappServiceObserver;
    protected $banksObserver;
    protected $duitkuObserver;

    public function __construct(
        TransactionObserver $transactionObserver, 
        NotificationObserver $notificationObserver, 
        WhatsappServiceObserver $whatsappServiceObserver, 
        BankObserver $banksObserver, 
        DuitkuObserver $duitkuObserver
    ) {
        $this->transactionObserver      = $transactionObserver;
        $this->notificationObserver     = $notificationObserver->getData();
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
        $this->banksObserver            = $banksObserver;
        $this->duitkuObserver           = $duitkuObserver;
    }

    public function index(Request $request)
    {
        $settings       = Setting::where('id', my_business())->first(['id', 'name']);
        $internal       = InternalSetting::first(['mua_per_price', 'price_mua']);
        $transactions   = $this->transactionObserver->getData($request, 'mua')->where('business_id', $settings->id)->get();

        $activePackage  = $settings->package_active; 
        $activeTopup    = $settings->package_active_mua; // Ini khusus untuk type='mua'
 
        // Hitung MAU usage real-time
        $currentMuaUsage = HistoryChatDetail::distinct('history_chat_id')
            ->whereHas('history', function ($q) {
                return $q->where('store_id', my_business());
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->count('history_chat_id');
 
        // Total limit dari package subscription + topup MUA
        $packageMuaLimit = $activePackage ? (int)$activePackage->mua_limit : 0;
        $topupMuaLimit   = $activeTopup ? (int)$activeTopup->mua_limit : 0;

        $totalMuaLimit   = $packageMuaLimit + $topupMuaLimit;
        $sisaMuaLimit    = max(0, $totalMuaLimit - $currentMuaUsage);
        $percentage      = $totalMuaLimit > 0 ? ($currentMuaUsage / $totalMuaLimit) * 100 : 0;

        $summaries = [
            'package_mua_limit' => $packageMuaLimit,
            'topup_mua_limit'   => $topupMuaLimit,
            'total_mua_limit'   => $totalMuaLimit,
            'current_mua_usage' => $currentMuaUsage,
            'sisa_mua_limit'    => $sisaMuaLimit,
            'percentage'        => round($percentage, 2)
        ];

        return view('billing.mua-index', ['page' => 'MUA Billing Information'], compact('summaries', 'transactions', 'internal'));
    }

    public function detail(Request $request, PackageTransaction $transaction)
    {
        $setting        = InternalSetting::first(['tax', 'method', 'merchant_code', 'api_key', 'is_production']);
        $datetime       = date('Y-m-d H:i:s');
        $payments       = null;

        if ($setting->method == 'duitku') {
            $paymentMetode  = $this->duitkuObserver->getPaymentMethods([
                'merchant_code'     => $setting->merchant_code,
                'amount'            => (float)$transaction->final_total,
                'date'              => $datetime,
                'signature'         => hash('sha256', $setting->merchant_code . (float)$transaction->final_total . $datetime . $setting->api_key)
            ], $setting->is_production);

            $wallets            = json_decode($paymentMetode->body())->paymentFee;
            $virtualAccount     = [];
            $eWallet            = [];
            $qris               = [];
            $retail             = [];

            foreach ($wallets as $payment) {
                if (strpos($payment->paymentName, 'VA') !== false) {
                    $virtualAccount[] = $payment;
                } elseif (in_array($payment->paymentMethod, ['OV', 'DA', 'LA', 'SA', 'OL','LF','SL'])) {
                    $eWallet[] = $payment;
                } elseif (in_array($payment->paymentMethod, ['SP', 'NQ', 'GQ','SQ'])) {
                    $qris[] = $payment;
                } elseif (in_array($payment->paymentMethod, ['IR', 'FT'])) {
                    $retail[] = $payment;
                }
            }

            $payments   = [
                'va'            => $virtualAccount,
                'wallet'        => $eWallet,
                'qris'          => $qris,
                'retail'        => $retail
            ];
        }

        $banks  = $this->banksObserver->getData($request)->get();
        return view('billing.mua-detail', ['page' => 'TopUp MUA'], compact('transaction', 'banks', 'payments', 'setting'));
    }

    public function createTokenDuitku(Request $request, PackageTransaction $transaction)
    {
        $settings   = InternalSetting::first(['tax', 'method', 'merchant_code', 'api_key', 'is_production']);
        $orderId    = $transaction->id;
        $amount     = (float)$transaction->final_total;
        $fullName   = $transaction->merchant->owner->name ?? '';
        $nameParts  = explode(' ', $fullName, 2);

        $firstName  = $nameParts[0] ?? '';
        $lastName   = $nameParts[1] ?? '';

        $data = [
            'merchantCode'      => $settings->merchant_code,
            'paymentAmount'     => $amount,
            'paymentMethod'     => $request->to_bank,
            'merchantOrderId'   => $orderId,
            'productDetails'    => 'TopUp MUA ' . (int)$transaction->new_order_mua_limit,
            'customerVaName'    => $transaction->merchant->owner->name ?? '',
            'email'             => $transaction->merchant->owner->email ?? '',
            'phoneNumber'       => $transaction->merchant->owner->phone ?? '',
            'itemDetails'       => [
                [
                    'name'          => 'TopUp MUA ' . (int)$transaction->new_order_mua_limit,
                    'price'         => $amount,
                    'quantity'      => 1
                ]
            ],
            'customerDetail'    => [
                'firstName'         => $firstName,
                'lastName'          => $lastName,
                'email'             => $transaction->merchant->owner->email ?? '',
                'phoneNumber'       => $transaction->merchant->owner->phone ?? '',
            ],
            'returnUrl'         => route('mua-billing.detail', $transaction->id),
            'callbackUrl'       => config('app.url') . '/api-app/payments/callback',
            'signature'         => hash('md5', $settings->merchant_code . $orderId . $amount . $settings->api_key),
            'expiryPeriod'      => 1440,
        ];

        $createToken = $this->duitkuObserver->createTransaction($data, $settings->api_key, $settings->is_production);

        if ($createToken->status() == 200) {
            $response = json_decode($createToken->body());
            return redirect($response->paymentUrl);
        }

        return redirect()->back()->with(['gagal' => 'Terjadi kesalahan, silahkan coba kembali']);
    }

    public function createMuaTransaction(Request $request)
    {
        $business = Setting::where('id', my_business())->first(['id', 'merchant_id']);

        if ($business->package_transaction_mua_pending == false) {
            return redirect()->back()->with(['gagal' => __('starter.validation_package')]);
        }

        $this->validate($request, [
            'qty' => 'required|numeric|min:1'
        ]);

        try {
            DB::beginTransaction();

            $internalSetting = InternalSetting::first(['app_name']);
            
            // Pakai method createMua dari TransactionObserver
            $transaction = $this->transactionObserver->createMua($business, $request);

            // Notifikasi WhatsApp
            if ($this->notificationObserver->whatsapp_buy_package == 'yes' && $this->notificationObserver->device) {
                if ($this->notificationObserver->buy_package_template_whatsapp) {
                    $message = $this->notificationObserver->buy_package_template_whatsapp->message;
                    $file    = $this->notificationObserver->buy_package_template_whatsapp->image;
                    $type    = $this->notificationObserver->buy_package_template_whatsapp->type_content;
                    $datas   = json_decode($this->notificationObserver->buy_package_template_whatsapp->button_or_list, true);
                    
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{subtotal}', '{date}', '{app_name}'],
                        [
                            ($transaction->business->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($transaction->final_total), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name
                        ],
                        $message
                    );

                    $messageVariable = [
                        'message'           => urldecode($message),
                        'template_body'     => [],
                        'whatsapp_key'      => $this->notificationObserver->device->id,
                        'whatsapp_session'  => $this->notificationObserver->device->id,
                        'file'              => $file != null ? asset($file) : '',
                        'phone'             => $this->notificationObserver->received_notification
                    ];

                    if ($messageVariable['phone'] != null) {
                        $this->whatsappServiceObserver->sendMessage(
                            $messageVariable['phone'], 
                            $messageVariable['whatsapp_key'], 
                            $messageVariable['message'], 
                            $messageVariable['file'], 
                            $type, 
                            $datas
                        );
                    }
                }
            }

            // Notifikasi Email
            if ($this->notificationObserver->email_buy_package == 'yes') {
                if ($this->notificationObserver->buy_package_template_email) {
                    $message = $this->notificationObserver->buy_package_template_email->html;
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{subtotal}', '{date}', '{app_name}'],
                        [
                            ($transaction->business->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($transaction->final_total), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name
                        ],
                        $message
                    );

                    $this->whatsappServiceObserver->sendEmail(
                        $this->notificationObserver->received_email_notification, 
                        $message, 
                        $this->notificationObserver->buy_package_template_email
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with(['flash' => 'Transaksi MUA berhasil dibuat']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function payTransaction(Request $request, PackageTransaction $transaction)
    {
        if ($transaction->merchant->id != my_user()->merchant_id) {
            return redirect()->back()->with(['gagal' => 'No Access']);
        }

        $this->validate($request, [
            'amount'      => 'required|numeric',
            'to_bank'     => 'required',
            'bank_name'   => 'required',
            'bank_number' => 'required',
            'image'       => 'mimes:jpg,jpeg,png',
        ]);

        if ($transaction->status == 'process') {
            return redirect()->back()->with(['gagal' => __('starter.payment_v_process')]);
        }

        if ($transaction->status == 'success') {
            return redirect()->back()->with(['gagal' => __('starter.payment_v_success')]);
        }

        $internalSetting = InternalSetting::first(['app_name']);

        try {
            DB::beginTransaction();

            $image = '';
            if ($request->image) {
                $image = $this->uploadImage($request, 'image', 'payments');
            }

            $payment = $this->transactionObserver->createPayment($request, $transaction, $image);

            $transaction->update(['status' => 'process']);

            // Notifikasi WhatsApp ke Admin
            if ($this->notificationObserver->whatsapp_package_payment == 'yes' && $this->notificationObserver->device) {
                if ($this->notificationObserver->package_payment_template_whatsapp) {
                    $message = $this->notificationObserver->package_payment_template_whatsapp->message;
                    $file    = $this->notificationObserver->package_payment_template_whatsapp->image;
                    $type    = $this->notificationObserver->package_payment_template_whatsapp->type_content;
                    $datas   = json_decode($this->notificationObserver->package_payment_template_whatsapp->button_or_list, true);
                    
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                        [
                            ($transaction->merchant->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($payment->amount), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name, 
                            $payment->bank_name, 
                            ($payment->bank->name ?? '')
                        ],
                        $message
                    );

                    $messageVariable = [
                        'message'           => urldecode($message),
                        'template_body'     => [],
                        'whatsapp_key'      => $this->notificationObserver->device->id,
                        'whatsapp_session'  => $this->notificationObserver->device->id,
                        'file'              => $file != null ? asset($file) : '',
                        'phone'             => $this->notificationObserver->received_notification
                    ];

                    if ($messageVariable['phone'] != null) {
                        $this->whatsappServiceObserver->sendMessage(
                            $messageVariable['phone'], 
                            $messageVariable['whatsapp_key'], 
                            $messageVariable['message'], 
                            $messageVariable['file'], 
                            $type, 
                            $datas
                        );
                    }
                }
            }

            // Email ke Admin
            if ($this->notificationObserver->email_package_payment == 'yes') {
                if ($this->notificationObserver->package_payment_template_email) {
                    $message = $this->notificationObserver->package_payment_template_email->html;
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                        [
                            ($transaction->merchant->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($payment->amount), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name, 
                            $payment->bank_name, 
                            ($payment->bank->name ?? '')
                        ],
                        $message
                    );

                    $this->whatsappServiceObserver->sendEmail(
                        $this->notificationObserver->received_email_notification, 
                        $message, 
                        $this->notificationObserver->package_payment_template_email
                    );
                }
            }

            // WhatsApp ke User
            if ($this->notificationObserver->whatsapp_package_user == 'yes' && $this->notificationObserver->device) {
                if ($this->notificationObserver->package_user_template_whatsapp) {
                    $message = $this->notificationObserver->package_user_template_whatsapp->message;
                    $file    = $this->notificationObserver->package_user_template_whatsapp->image;
                    $type    = $this->notificationObserver->package_user_template_whatsapp->type_content;
                    $datas   = json_decode($this->notificationObserver->package_user_template_whatsapp->button_or_list, true);
                    
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                        [
                            ($transaction->merchant->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($payment->amount), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name, 
                            $payment->bank_name, 
                            ($payment->bank->name ?? '')
                        ],
                        $message
                    );

                    $messageVariable = [
                        'message'           => urldecode($message),
                        'template_body'     => [],
                        'whatsapp_key'      => $this->notificationObserver->device->id,
                        'whatsapp_session'  => $this->notificationObserver->device->id,
                        'file'              => $file != null ? asset($file) : '',
                        'phone'             => $transaction->merchant->owner->phone ?? null
                    ];

                    if ($messageVariable['phone'] != null) {
                        $this->whatsappServiceObserver->sendMessage(
                            $messageVariable['phone'], 
                            $messageVariable['whatsapp_key'], 
                            $messageVariable['message'], 
                            $messageVariable['file'], 
                            $type, 
                            $datas
                        );
                    }
                }
            }

            // Email ke User
            if ($this->notificationObserver->email_package_user == 'yes') {
                if ($this->notificationObserver->package_user_template_email) {
                    $message = $this->notificationObserver->package_user_template_email->html;
                    $message = str_replace(
                        ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                        [
                            ($transaction->merchant->name ?? ''), 
                            my_user()->name, 
                            'TopUp MUA', 
                            number_format($payment->amount), 
                            substr($transaction->created_at, 0, 10), 
                            $internalSetting->app_name, 
                            $payment->bank_name, 
                            ($payment->bank->name ?? '')
                        ],
                        $message
                    );

                    $this->whatsappServiceObserver->sendEmail(
                        $transaction->merchant->owner->email ?? null, 
                        $message, 
                        $this->notificationObserver->package_user_template_email
                    );
                }
            }

            DB::commit();
            return redirect()->route('mua.index')->with(['flash' => __('general.success_add_data')]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function delete(PackageTransaction $transaction)
    {
        if ($transaction->merchant->id != my_user()->merchant_id) {
            return redirect()->back()->with(['gagal' => 'No Access']);
        }

        if ($transaction->status == 'process') {
            return redirect()->back()->with(['gagal' => __('starter.payment_v_process')]);
        }

        if ($transaction->status == 'success') {
            return redirect()->back()->with(['gagal' => __('starter.cant_delete')]);
        }

        $transaction->delete();
        return redirect()->back()->with(['flash' => __('general.success_deleted')]);
    }
}