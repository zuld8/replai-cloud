<?php
namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Affiliate\Affiliate;
use App\Models\Affiliate\AffiliateCommission;
use App\Models\Affiliate\AffiliateWithdrawal;
use Illuminate\Http\Request;

class AffiliateAdminController extends Controller
{
    public function index(Request $request)
    {
        $affiliates = Affiliate::with('user')
            ->when($request->q, function($q, $s) { $q->whereHas('user', fn($u) => $u->where('name','like',"%$s%")); })
            ->orderBy('total_earned','desc')
            ->paginate(20);

        return view('administrator.affiliate.index', compact('affiliates'), [
            'page' => 'Affiliate Management',
            'breadcumb' => [['label' => 'Affiliate']],
        ]);
    }

    public function withdrawals(Request $request)
    {
        $withdrawals = AffiliateWithdrawal::with('affiliate.user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('created_at','desc')
            ->paginate(20);

        return view('administrator.affiliate.withdrawals', compact('withdrawals'), [
            'page' => 'Affiliate Withdrawals',
            'breadcumb' => [['label' => 'Affiliate'], ['label' => 'Withdrawals']],
        ]);
    }

    public function approveWithdrawal(AffiliateWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with(['gagal' => 'Status tidak valid.']);
        }

        $affiliate = $withdrawal->affiliate;
        $available = $affiliate->getAvailableBalance();

        if ($withdrawal->amount > $available) {
            return back()->with(['gagal' => 'Saldo affiliator tidak mencukupi.']);
        }

        // Mark commissions as withdrawn (oldest first)
        $remaining = $withdrawal->amount;
        $comms = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('status', 'available')
            ->orderBy('created_at')
            ->get();

        foreach ($comms as $comm) {
            if ($remaining <= 0) break;
            if ($comm->amount <= $remaining) {
                $comm->update(['status' => 'withdrawn']);
                $remaining -= $comm->amount;
            } else {
                // Split not needed - just mark all available
                $comm->update(['status' => 'withdrawn']);
                $remaining = 0;
            }
        }

        $withdrawal->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        $affiliate->increment('total_withdrawn', $withdrawal->amount);

        return back()->with(['flash' => 'Withdrawal berhasil disetujui. Rp ' . number_format($withdrawal->amount)]);
    }

    public function rejectWithdrawal(Request $request, AffiliateWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with(['gagal' => 'Status tidak valid.']);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'notes'  => $request->notes ?? 'Ditolak oleh admin.',
        ]);

        return back()->with(['flash' => 'Withdrawal ditolak.']);
    }

    public function banAffiliate(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'banned']);
        return back()->with(['flash' => 'Affiliator telah di-ban.']);
    }

    public function unbanAffiliate(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'active']);
        return back()->with(['flash' => 'Affiliator diaktifkan kembali.']);
    }
}
