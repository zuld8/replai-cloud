<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\InternalSetting;
use App\Models\Merchant\Merchant;
use App\Models\Package\Package;
use App\Models\Package\PackageTransaction;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Observers\Saas\BankObserver;
use App\Observers\Saas\DuitkuObserver;
use App\Observers\Saas\MerchantObserver;
use App\Observers\Saas\NotificationObserver;
use App\Observers\Saas\PricingObserver;
use App\Observers\Saas\TransactionObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Services\Sistem\WabaNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    protected $merchantObserver;
    protected $notificationObserver;
    protected $wabaNotifService;
    protected $whatsappServiceObserver;
    protected $pricingObserver;
    protected $duitkuObserver;
    protected $transactionObserver;
    protected $banksObserver;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantObserver $merchantObserver, NotificationObserver $notificationObserver, WhatsappServiceObserver $whatsappServiceObserver, PricingObserver $pricingObserver, DuitkuObserver $duitkuObserver, TransactionObserver $transactionObserver, BankObserver $banksObserver)
    {
        $this->merchantObserver         = $merchantObserver;
        $this->notificationObserver     = $notificationObserver->getData();
        $this->wabaNotifService         = new WabaNotificationService();
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
        $this->pricingObserver          = $pricingObserver;
        $this->duitkuObserver           = $duitkuObserver;
        $this->transactionObserver      = $transactionObserver;
        $this->banksObserver            = $banksObserver;
        $this->middleware('guest');
    }


    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $internalSetting    = InternalSetting::first(['logo', 'register', 'icon']);

        if ($internalSetting->register == 'no') {
            return redirect()->route('login');
        }

        $packageDetail      = null;

        if ($request->package) {
            $packageDetail  = Package::find($request->package);
        }

        if ($internalSetting->register == 'no') {
            return redirect()->route('login');
        }

        $packages           = $this->pricingObserver->getData($request)->where('show', 'yes')->get();

        $categories         = $this->merchantObserver->businessCategories($request)->get(['id', 'name']);
        $banks              = $this->banksObserver->getData($request)->get();
        $setting            = InternalSetting::first(['tax', 'method', 'merchant_code', 'api_key', 'is_production']);
        $datetime           = date('Y-m-d H:i:s');
        $payments           = [
            'va'            => [],
            'wallet'        => [],
            'qris'          => [],
            'retail'        => []
        ];

        if ($setting->method == 'duitku') {
            $paymentMetode  = $this->duitkuObserver->getPaymentMethods([
                'merchant_code'     => $setting->merchant_code,
                'amount'            => 100000,
                'date'              => $datetime,
                'signature'         => hash('sha256', $setting->merchant_code . 100000 . $datetime . $setting->api_key)
            ], $setting->is_production);

            $wallets            = json_decode($paymentMetode->body())->paymentFee;
            $virtualAccount     = [];
            $eWallet            = [];
            $qris               = [];
            $retail             = [];

            foreach ($wallets as $payment) {
                if (strpos($payment->paymentName, 'VA') !== false) {
                    $virtualAccount[] = $payment;
                } elseif (in_array($payment->paymentMethod, ['OV', 'DA', 'LA', 'SA', 'OL', 'LF', 'SL'])) {
                    $eWallet[] = $payment;
                } elseif (in_array($payment->paymentMethod, ['SP', 'NQ', 'GQ', 'SQ'])) {
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


        return view('auth.register', ['page' => 'Daftar Akun'], compact('categories', 'internalSetting', 'packages', 'payments', 'setting', 'banks', 'packageDetail'));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorData(Request $request)
    {
        $formattedPhone = '62' . ltrim($request->phone, '0');

        return Validator::make($request->all(), [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8'],
            'phone'         => [
                'required',
                'numeric',
                'digits_between:10,13',
                Rule::unique('users', 'phone')->where(function ($query) use ($formattedPhone) {
                    return $query->where('phone', $formattedPhone);
                })
            ],
            'business_name' => ['required', 'string', 'max:255'],
            'package_id'    => ['required', 'uuid', 'max:255'],
            'category'      => ['required'],
            'payment_method' => ['required', 'in:duitku,bank'],
            'image' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->payment_method === 'bank' &&
                        (float) $request->nominal > 0 &&
                        !$request->hasFile('image')
                    ) {
                        $fail('Bukti transfer wajib diunggah.');
                    } elseif ($request->hasFile('image') && !in_array($request->file('image')->getClientOriginalExtension(), ['jpeg', 'jpg', 'png'])) {
                        $fail('Format gambar harus jpeg, jpg, atau png.');
                    }
                }
            ],
            'bank_name' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->payment_method === 'bank' &&
                        (float) $request->nominal > 0 &&
                        empty($value)
                    ) {
                        $fail('Nama bank wajib diisi.');
                    }
                }
            ],
            'to_bank' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        (float) $request->nominal > 0 &&
                        empty($value)
                    ) {
                        $fail('Metode Pembayaran wajib diisi.');
                    }
                }
            ],
            'bank_number' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->payment_method === 'bank' &&
                        (float) $request->nominal > 0 &&
                        empty($value)
                    ) {
                        $fail('Nomor rekening wajib diisi.');
                    }
                }
            ],
        ], [
            'name.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal 8 karakter.',
            'phone.required'         => 'Nomor telepon wajib diisi.',
            'phone.numeric'          => 'Nomor telepon harus berupa angka.',
            'phone.digits_between'   => 'Nomor telepon harus antara 10-13 digit.',
            'phone.unique'           => 'Nomor telepon sudah digunakan.',
            'business_name.required' => 'Nama bisnis wajib diisi.',
            'package_id.required'    => 'Paket wajib dipilih.',
            'package_id.uuid'        => 'ID paket tidak valid.',
            'to_bank.required'       => 'Metode Pembayaran perlu diisi.',
            'category.required'      => 'Kategori wajib diisi.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createUser(Request $request)
    {
        $this->validatorData($request)->validate();

        $internalSetting    = InternalSetting::first(['app_name']);

        try {

            DB::beginTransaction();

            $users  = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'password'  => Hash::make($request->password),
            ]);

            $businessMerchant = Merchant::create([
                'name'                      => $request->business_name,
                'merchant_category_id'      => $request->category,
                'owner_id'                  => $users->id,
            ]);

            $business = Setting::create([
                'mailer'            => 'SMTP',
                'name'              => $request->business_name,
                'local_api_key'     => Uuid::uuid4()->toString(),
                'mail_encryption'   => 'tls',
                'use_whatsapp'      => 'internal',
                'default_lang'      => 'en',
                'merchant_id'       => $businessMerchant->id
            ]);

            $adminRole = Role::create([
                'name'          => 'Administrator',
                'guard_name'    => 'web',
                'merchant_id'   => $businessMerchant->id
            ]);

            $allPermissions = Permission::all();
            if ($allPermissions->count() > 0) {
                $adminRole->givePermissionTo($allPermissions);
            }

            $users->assignRole($adminRole);

            $users->update([
                'role_id'       => $adminRole->id,
                'business_id'   => $business->id,
                'merchant_id'   => $businessMerchant->id
            ]);


            $package        = Package::findOrFail($request->package_id);
            $transaction    = $this->transactionObserver->createData($business, $package);


            if ($this->notificationObserver->whatsapp_register == 'yes' && $this->notificationObserver->device) {

                if ($this->notificationObserver->register_template_whatsapp) {
                    $message    = $this->notificationObserver->register_template_whatsapp->message;
                    $file       = $this->notificationObserver->register_template_whatsapp->image;
                    $type       = $this->notificationObserver->register_template_whatsapp->type_content;
                    $datas      = json_decode($this->notificationObserver->register_template_whatsapp->button_or_list, true);
                    $message    = str_replace(
                        ['{business_name}', '{name}', '{phone}', '{email}', '{date}', '{app_name}'],
                        [$businessMerchant->name, $users->name, $users->phone, $users->email, substr($users->created_at, 0, 10), $internalSetting->app_name],
                        $message
                    );

                    $messageVariable = array(
                        'message'           => urldecode($message),
                        'template_body'     => array(),
                        'whatsapp_key'      => $this->notificationObserver->device->id,
                        'whatsapp_session'  => $this->notificationObserver->device->id,
                        'file'              => $file != null ? asset($file) : '',
                        'phone'             => $users->phone
                    );

                    // Prefer WABA if configured, fallback to legacy device
                    if ($this->notificationObserver->wabaDevice) {
                        $this->wabaNotifService->sendText($messageVariable['phone'], $messageVariable['message'], $this->notificationObserver->wabaDevice);
                    } else {
                        $this->whatsappServiceObserver->sendMessage($messageVariable['phone'], $messageVariable['whatsapp_key'], $messageVariable['message'], $messageVariable['file'], $type, $datas);
                    }
                }
            }

            if ($this->notificationObserver->email_register == 'yes') {

                if ($this->notificationObserver->register_template_email) {
                    $message    = $this->notificationObserver->register_template_email->html;
                    $message    = str_replace(
                        ['{business_name}', '{name}', '{phone}', '{email}', '{date}', '{app_name}'],
                        [$businessMerchant->name, $users->name, $users->phone, $users->email, substr($users->created_at, 0, 10), $internalSetting->app_name],
                        $message
                    );

                    $this->whatsappServiceObserver->sendEmail($this->notificationObserver->received_email_notification, $message, $this->notificationObserver->register_template_email);
                }
            }

            if ($transaction->final_total > 0) {
                if ($request->payment_method == 'bank') {
                    $image  = '';

                    if ($request->image) {
                        $image =  $this->uploadImage($request, 'image', 'payments');
                    }

                    $payment    = $this->transactionObserver->createPayment($request, $transaction, $image);

                    $transaction->update([
                        'status'        => 'process'
                    ]);

                    if ($this->notificationObserver->whatsapp_package_payment == 'yes' && $this->notificationObserver->device) {
                        if ($this->notificationObserver->package_payment_template_whatsapp) {
                            $message    = $this->notificationObserver->package_payment_template_whatsapp->message;
                            $file       = $this->notificationObserver->package_payment_template_whatsapp->image;
                            $type       = $this->notificationObserver->package_payment_template_whatsapp->type_content;
                            $datas      = json_decode($this->notificationObserver->package_payment_template_whatsapp->button_or_list, true);
                            $message    = str_replace(
                                ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                                [($transaction->merchant->name ?? ''), $users->name, ($transaction->package->name ?? ''), number_format($payment->amount), substr($transaction->created_at, 0, 10), $internalSetting->app_name, $payment->bank_name, ($payment->bank->name ?? '')],
                                $message
                            );

                            $messageVariable = array(
                                'message'           => urldecode($message),
                                'template_body'     => array(),
                                'whatsapp_key'      => $this->notificationObserver->device->id,
                                'whatsapp_session'  => $this->notificationObserver->device->id,
                                'file'              => $file != null ? asset($file) : '',
                                'phone'             => $this->notificationObserver->received_notification
                            );

                            if ($messageVariable['phone'] != null) {
                                // Prefer WABA if configured
                                if ($this->notificationObserver->wabaDevice) {
                                    $this->wabaNotifService->sendText($messageVariable['phone'], $messageVariable['message'], $this->notificationObserver->wabaDevice);
                                } else {
                                    $this->whatsappServiceObserver->sendMessage($messageVariable['phone'], $messageVariable['whatsapp_key'], $messageVariable['message'], $messageVariable['file'], $type, $datas);
                                }
                            }
                        }
                    }

                    if ($this->notificationObserver->email_package_payment == 'yes') {

                        if ($this->notificationObserver->package_payment_template_email) {
                            $message    = $this->notificationObserver->package_payment_template_email->html;
                            $message    = str_replace(
                                ['{business_name}', '{name}', '{package_name}', '{payment_amount}', '{date}', '{app_name}', '{from_bank}', '{to_bank}'],
                                [($transaction->merchant->name ?? ''),$users->name, ($transaction->package->name ?? ''), number_format($payment->amount), substr($transaction->created_at, 0, 10), $internalSetting->app_name, $payment->bank_name, ($payment->bank->name ?? '')],
                                $message
                            );

                            $this->whatsappServiceObserver->sendEmail($this->notificationObserver->received_email_notification, $message, $this->notificationObserver->package_payment_template_email);
                        }
                    }
                }
            } else {
                $transaction->update([
                    'status'    => 'success'
                ]);
            }


            DB::commit();

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $users->refresh();
        // Save affiliate referral code
        try {
            $refCode = session('affiliate_code') ?? request()->cookie('affiliate_ref') ?? request()->get('ref');
            if ($refCode && \App\Models\Affiliate\Affiliate::where('code', $refCode)->exists()) {
                $users->referred_by = $refCode;
                $users->referred_by_month = 0;
                $users->save();
                session()->forget('affiliate_code');
            }
        } catch (\Exception $e) { \Log::warning('Affiliate ref save error: ' . $e->getMessage()); }


            Auth::login($users);

            $users->sendEmailVerificationNotification();

            if ($transaction->final_total > 0) {
                if ($request->payment_method == 'bank') {
                    return redirect()->route('starter.transactions.detail', $transaction->id);
                } else if ($request->payment_method == 'duitku') {
                    return $this->createTokenDuitku($request->to_bank, $transaction);
                }
            } else {
                return redirect()->route('starter.transactions.detail', $transaction->id);
            }
        } catch (\Exception $e) {

            DB::rollBack();
 
            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }

    public function createTokenDuitku($toBank, PackageTransaction $transaction)
    {

        $settings   = InternalSetting::first(['tax', 'method', 'merchant_code', 'api_key', 'is_production']);
        $orderId    = $transaction->id;
        $amount     = (float)($transaction->final_total);
        $fullName   = $transaction->merchant->owner->name ?? '';
        $nameParts  = explode(' ', $fullName, 2);

        $firstName  = $nameParts[0] ?? '';
        $lastName   = $nameParts[1] ?? '';

        $data = [
            'merchantCode'      => $settings->merchant_code,
            'paymentAmount'     => $amount,
            'paymentMethod'     => $toBank,
            'merchantOrderId'   => $orderId,
            'productDetails'    => $transaction->package->name ?? '',
            'customerVaName'    => $transaction->merchant->owner->name ?? '',
            'email'             => $transaction->merchant->owner->email ?? '',
            'phoneNumber'       => $transaction->merchant->owner->phone ?? '',
            'itemDetails'       => [
                [
                    'name'          => $transaction->package->name ?? '',
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
            'returnUrl'         => route('starter.transactions.detail', $transaction->id),
            'callbackUrl'       => config('app.url') . '/api-app/payments/callback',
            'signature'         => hash('md5', $settings->merchant_code . $orderId . $amount . $settings->api_key),
            'expiryPeriod'      => 1440,
        ];

        $createToken = $this->duitkuObserver->createTransaction($data, $settings->api_key, $settings->is_production);

        if ($createToken->status() == 200) {
            $response       = json_decode($createToken->body());
            return redirect($response->paymentUrl);
        }

        return redirect()->route('starter.transactions.detail', $transaction->id)->with(['gagal' => 'Terjadi kesalahan saat menghubungkan ke metode pembayaran, silahkan coba kembali']);
    }
}
