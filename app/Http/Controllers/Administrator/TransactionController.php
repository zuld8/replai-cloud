<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\InternalSetting;
use App\Models\Package\PackageTransaction;
use App\Models\Package\PackageTransactionPayment;
use App\Observers\Saas\InternalSettingObserver;
use App\Observers\Saas\NotificationObserver;
use App\Observers\Saas\TransactionObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $transactionObserver;
    protected $internalSettingObserver;
    protected $notificationObserver;
    protected $whatsappServiceObserver;

    public function __construct(TransactionObserver $transactionObserver, InternalSettingObserver $internalSettingObserver, NotificationObserver $notificationObserver, WhatsappServiceObserver $whatsappServiceObserver)
    {
        $this->transactionObserver      = $transactionObserver;
        $this->internalSettingObserver  = $internalSettingObserver;
        $this->notificationObserver     = $notificationObserver->getData();
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
    }

    public function index(Request $request)
    {
        $transactions   = $this->transactionObserver->getData($request)->get(['id', 'created_at', 'status', 'merchant_id', 'package_id', 'final_total', 'business_id']);
        return view('admin.transactions.index', ['page'  => __('page.transaction.page'), 'breadcumb' => false], compact('transactions'));
    }

    public function topUp(Request $request)
    {
        $transactions   = $this->transactionObserver->getData($request, 'topup')->get(['id', 'created_at', 'status', 'merchant_id', 'final_total', 'business_id', 'new_order_ai_response']);
        return view('admin.transactions.topup', ['page'  => __('page.transaction.page'), 'breadcumb' => false], compact('transactions'));
    }

    public function mua(Request $request)
    {
        $transactions = $this->transactionObserver->getData($request, 'mua')->get([
            'id',
            'created_at',
            'status',
            'merchant_id',
            'final_total',
            'business_id',
            'new_order_mua_limit',
            'mua_limit'
        ]);

        return view('admin.transactions.mua', [
            'page' => 'MUA Transactions',
            'breadcumb' => false
        ], compact('transactions'));
    }

    public function muaDetail(PackageTransaction $transaction)
    {
        $setting = $this->internalSettingObserver->generalSetting();

        return view('admin.transactions.detail_mua', [
            'page' => 'Detail MUA Transaction',
            'breadcumb' => true
        ], compact('transaction', 'setting'));
    }


    public function topUpDetail(PackageTransaction $transaction)
    {
        $setting    = $this->internalSettingObserver->generalSetting();
        return view('admin.transactions.detail_topup', ['page' => __('page.transaction.detail'), 'breadcumb' => true], compact('transaction', 'setting'));
    }

    public function detail(PackageTransaction $transaction)
    {
        $setting    = $this->internalSettingObserver->generalSetting();
        return view('admin.transactions.detail', ['page' => __('page.transaction.detail'), 'breadcumb' => true], compact('transaction', 'setting'));
    }

    public function storage(Request $request)
    {
        $transactions   = $this->transactionObserver->getData($request, 'storage')->get(['id', 'created_at', 'status', 'merchant_id', 'final_total', 'business_id', 'storage', 'package_id', 'expire_date', 'price', 'add_days', 'days_option']);
        return view('admin.transactions.storage', ['page'  => __('page.transaction.page'), 'breadcumb' => false], compact('transactions'));
    }

    public function storageDetail(PackageTransaction $transaction)
    {
        $setting    = $this->internalSettingObserver->generalSetting();
        return view('admin.transactions.storage_detail', ['page' => __('page.transaction.detail'), 'breadcumb' => true], compact('transaction', 'setting'));
    }

    public function approvalPayment(PackageTransaction $transaction)
    {

        $internalSetting    = InternalSetting::first(['app_name']);

        try {
            DB::beginTransaction();


            $transaction->update([
                'status'        => 'success'
            ]);

            if ($this->notificationObserver->whatsapp_approval_payment == 'yes' && $this->notificationObserver->device) {
                if ($this->notificationObserver->approval_payment_template_whatsapp) {
                    $message    = $this->notificationObserver->approval_payment_template_whatsapp->message;
                    $type       = $this->notificationObserver->approval_payment_template_whatsapp->type_content;
                    $datas      = json_decode($this->notificationObserver->approval_payment_template_whatsapp->button_or_list, true);
                    $file       = $this->notificationObserver->approval_payment_template_whatsapp->image;
                    $message    = str_replace(
                        ['{name}', '{package_name}', '{active_date}', '{expire_date}', '{app_name}'],
                        [($transaction->business->owner->name ?? ''), ($transaction->package->name ?? ''), substr($transaction->created_at, 0, 10), substr($transaction->expire_date, 0, 10),  $internalSetting->app_name],
                        $message
                    );

                    $messageVariable = array(
                        'message'           => urldecode($message),
                        'template_body'     => array(),
                        'whatsapp_key'      => $this->notificationObserver->device->id,
                        'whatsapp_session'  => $this->notificationObserver->device->id,
                        'file'              => $file != null ? asset($file) : '',
                        'phone'             => $transaction->merchant->owner->phone ?? null
                    );

                    if ($messageVariable['phone'] != null) {
                        $this->whatsappServiceObserver->sendMessage($messageVariable['phone'], $messageVariable['whatsapp_key'], $messageVariable['message'], $messageVariable['file'], $type, $datas);
                    }
                }
            }

            if ($this->notificationObserver->email_approval_payment == 'yes') {

                if ($this->notificationObserver->approval_payment_template_email) {
                    $message    = $this->notificationObserver->approval_payment_template_email->html;
                    $message    = str_replace(
                        ['{name}', '{package_name}', '{active_date}', '{expire_date}', '{app_name}'],
                        [($transaction->business->owner->name ?? ''), ($transaction->package->name ?? ''), substr($transaction->created_at, 0, 10), substr($transaction->expire_date, 0, 10),  $internalSetting->app_name],
                        $message
                    );

                    $this->whatsappServiceObserver->sendEmail($transaction->merchant->owner->email ?? null, $message, $this->notificationObserver->approval_payment_template_email);
                }
            }

            DB::commit();
            return redirect()->back()->with(['flash'    => __('transaction.received_payment')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function rejectedPayment(PackageTransactionPayment $payment)
    {
        $payment->transaction->update([
            'status'        => 'pending'
        ]);

        $payment->delete();
        return redirect()->back()->with(['flash'    => __("transaction.rejected_payment")]);
    }
}
