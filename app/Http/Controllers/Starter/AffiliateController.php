<?php
namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use App\Models\Affiliate\Affiliate;
use App\Models\Affiliate\AffiliateCommission;
use App\Models\Affiliate\AffiliateWithdrawal;
use App\Models\Package\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    private function getOrCreateAffiliate()
    {
        $user = my_user();
        $aff  = Affiliate::where('user_id', $user->id)->first();
        if (!$aff) {
            // Generate unique code: first 3 chars of name + 4 random digits
            $base = strtoupper(preg_replace('/[^A-Za-z]/', '', $user->name));
            $base = substr($base, 0, 4) ?: 'REP';
            do {
                $code = $base . rand(1000, 9999);
            } while (Affiliate::where('code', $code)->exists());

            $aff = Affiliate::create([
                'user_id' => $user->id,
                'code'    => $code,
            ]);
        }
        return $aff;
    }

    public function index()
    {
        $affiliate    = $this->getOrCreateAffiliate();
        $available    = $affiliate->getAvailableBalance();
        $pending      = $affiliate->getPendingBalance();
        $total_earned = $affiliate->total_earned;
        $total_withdrawn = $affiliate->total_withdrawn;

        // Recent commissions
        $commissions = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->orderBy('created_at','desc')
            ->limit(20)
            ->get();

        // Referral list (unique users that bought via this affiliate)
        $referrals = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->selectRaw('referred_user_id, MAX(created_at) as last_at, SUM(amount) as total_comm, COUNT(*) as trx_count, status')
            ->groupBy('referred_user_id','status')
            ->with('referredUser')
            ->orderBy('last_at','desc')
            ->limit(30)
            ->get();

        // Withdrawal history
        $withdrawals = AffiliateWithdrawal::where('affiliate_id', $affiliate->id)
            ->orderBy('created_at','desc')
            ->limit(10)
            ->get();

        $ref_url = config('app.url') . '/ref/' . $affiliate->code;
        $packages = Package::where('type', 'package')->orderBy('price', 'asc')->get();

        return view('starter.affiliate.index', array_merge(
            compact('affiliate','available','pending','total_earned',
                'total_withdrawn','commissions','referrals','withdrawals','ref_url','packages'),
            ['page' => __('sidebar.affiliate') . ' - ' . 'Affiliate', 'breadcumb' => false]
        ));
    }

    public function withdraw(Request $request)
    {
        $this->validate($request, [
            'amount'       => 'required|numeric|min:100000',
            'bank_name'    => 'required|string',
            'bank_account' => 'required|string',
            'bank_holder'  => 'required|string',
        ]);

        $affiliate = $this->getOrCreateAffiliate();
        $available = $affiliate->getAvailableBalance();

        if ($request->amount > $available) {
            return back()->with(['gagal' => 'Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($available)]);
        }

        // Check no pending withdrawal
        $hasPending = AffiliateWithdrawal::where('affiliate_id', $affiliate->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with(['gagal' => 'Masih ada permintaan withdrawal yang sedang diproses.']);
        }

        AffiliateWithdrawal::create([
            'affiliate_id' => $affiliate->id,
            'amount'       => $request->amount,
            'bank_name'    => $request->bank_name,
            'bank_account' => $request->bank_account,
            'bank_holder'  => $request->bank_holder,
            'status'       => 'pending',
        ]);

        return back()->with(['flash' => 'Permintaan withdrawal berhasil dikirim. Akan diproses 1-3 hari kerja.']);

        // Notify admin via email
        try {
            $adminEmail = \App\Models\InternalSetting::first()->email_contact ?? 'admin@replai.id';
            $affiliateUser = $affiliate->user;
            \Illuminate\Support\Facades\Mail::raw(
                "ADA REQUEST WITHDRAWAL AFFILIATE BARU\n\n" .
                "Affiliator : " . ($affiliateUser->name ?? '-') . "\n" .
                "Email      : " . ($affiliateUser->email ?? '-') . "\n" .
                "Kode       : " . $affiliate->code . "\n" .
                "Bank       : " . $request->bank_name . "\n" .
                "Rekening   : " . $request->bank_account . "\n" .
                "Atas Nama  : " . $request->bank_holder . "\n" .
                "Jumlah     : Rp " . number_format((float)$request->amount, 0, ',', '.') . "\n\n" .
                "Proses di: https://chat.replai.id/administrator/affiliate/withdrawals",
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                            ->subject('[Replai] Withdrawal Request Affiliate - Perlu Diproses');
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Affiliate WD mail failed: ' . $e->getMessage());
        }
    }

    public function trackClick(Request $request, $code)
    {
        $affiliate = Affiliate::where('code', $code)->where('status','active')->first();
        if ($affiliate) {
            // Rate limit: 1 click per IP per hour per affiliate
            $recentClick = \App\Models\Affiliate\AffiliateClick::where('affiliate_id', $affiliate->id)
                ->where('ip_address', $request->ip())
                ->where('created_at', '>=', now()->subHour())
                ->exists();

            if (!$recentClick) {
                \App\Models\Affiliate\AffiliateClick::create([
                    'affiliate_id' => $affiliate->id,
                    'ip_address'   => $request->ip(),
                    'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
                ]);
                $affiliate->increment('total_click');
            }

            // Store in cookie + session
            cookie()->queue('affiliate_ref', $code, 60 * 24 * 30);
            session(['affiliate_code' => $code]);
        }

        return redirect('/register');
    }
}
