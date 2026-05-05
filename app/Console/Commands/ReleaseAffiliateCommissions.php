<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Affiliate\AffiliateCommission;
use Carbon\Carbon;

class ReleaseAffiliateCommissions extends Command
{
    protected $signature = 'affiliate:release-commissions';
    protected $description = 'Release pending affiliate commissions that are older than 30 days';

    public function handle()
    {
        $released = AffiliateCommission::where('status', 'pending')
            ->where('available_at', '<=', now())
            ->update(['status' => 'available']);

        $this->info("Released {$released} affiliate commissions.");
        return 0;
    }
}
