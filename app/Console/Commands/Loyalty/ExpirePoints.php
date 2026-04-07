<?php

namespace App\Console\Commands\Loyalty;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;

#[Signature('loyalty:expire-points')]
#[Description('Otomatisasi kadaluwarsa poin loyalitas berdasarkan FIFO.')]
class ExpirePoints extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai mengecek poin kadaluwarsa...');

        $expiredTransactions = LoyaltyTransaction::where('type', 'earn')
            ->where('is_expired', false)
            ->where('remaining_points', '>', 0)
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredTransactions as $txn) {
            /** @var LoyaltyTransaction $txn */
            DB::transaction(function () use ($txn, &$count) {
                $account = $txn->loyaltyAccount;
                
                // Keep track of remaining points before zeroing them out
                $pointsToExpire = $txn->remaining_points;

                // Deduct from main balance
                if ($account) {
                    $account->decrement('points_balance', $pointsToExpire);
                }

                // Mark transaction as expired
                $txn->update([
                    'is_expired' => true,
                    'remaining_points' => 0
                ]);

                // Record expiration transaction for audit
                LoyaltyTransaction::create([
                    'tenant_id' => $txn->tenant_id,
                    'loyalty_account_id' => $txn->loyalty_account_id,
                    'type' => 'adjust',
                    'points' => (float)$pointsToExpire * -1,
                    'reference_type' => 'Expiration',
                    'reference_id' => $txn->id,
                ]);

                $count++;
            });
        }

        $this->info("Berhasil memproses $count transaksi poin kadaluwarsa.");
    }
}
