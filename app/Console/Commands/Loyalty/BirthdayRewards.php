<?php

namespace App\Console\Commands\Loyalty;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;

#[Signature('loyalty:birthday-rewards')]
#[Description('Berikan hadiah poin otomatis untuk customer yang berulang tahun hari ini.')]
class BirthdayRewards extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengecek customer yang berulang tahun hari ini...');

        $today = now()->format('m-d');
        $bonusPoints = config('loyalty.birthday_bonus_points', 50);

        Customer::whereRaw("TO_CHAR(birthday, 'MM-DD') = ?", [$today])->chunk(100, function ($customers) use ($bonusPoints) {
            foreach ($customers as $customer) {
                DB::transaction(function () use ($customer, $bonusPoints) {
                    $account = LoyaltyAccount::firstOrCreate(
                        ['tenant_id' => $customer->tenant_id, 'customer_id' => $customer->id],
                        ['points_balance' => 0]
                    );

                    // Check if already awarded today to prevent double rewards
                    $alreadyAwarded = LoyaltyTransaction::where('loyalty_account_id', $account->id)
                        ->where('type', 'earn')
                        ->where('reference_type', 'BirthdayReward')
                        ->whereDate('created_at', now())
                        ->exists();

                    if (!$alreadyAwarded) {
                        $account->increment('points_balance', $bonusPoints);

                        LoyaltyTransaction::create([
                            'tenant_id' => $customer->tenant_id,
                            'loyalty_account_id' => $account->id,
                            'type' => 'earn',
                            'points' => $bonusPoints,
                            'remaining_points' => $bonusPoints,
                            'expires_at' => now()->addMonths(config('loyalty.points_expiry_months', 12)),
                            'reference_type' => 'BirthdayReward',
                            'reference_id' => $customer->id,
                        ]);

                        $this->line("Hadiah diberikan ke: {$customer->name}");
                    }
                });
            }
        });

        $this->info('Selesai memproses hadiah ulang tahun.');
    }
}
