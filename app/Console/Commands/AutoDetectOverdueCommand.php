<?php

namespace App\Console\Commands;

use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnTransaction;
use App\Services\InventoryMutationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoDetectOverdueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pawn:auto-detect-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-detect jatuh tempo tanggal server dan ubah status barang wanprestasi/macet menjadi SIAP LELANG';

    /**
     * Execute the console command.
     */
    public function handle(InventoryMutationService $mutationService): int
    {
        $today = Carbon::today();
        
        $overdueTransactions = PawnTransaction::whereIn('status', [
            PawnTransaction::STATUS_TERSIMPAN,
            PawnTransaction::STATUS_DIPERPANJANG
        ])->where('due_date', '<', $today)->get();

        $count = 0;

        foreach ($overdueTransactions as $transaction) {
            $transaction->update([
                'status' => PawnTransaction::STATUS_SIAP_LELANG,
            ]);

            if ($transaction->item) {
                $mutationService->recordMutation(
                    $transaction->item,
                    InventoryMutation::TYPE_PINDAH_RAK_LELANG,
                    null,
                    "Auto-detect jatuh tempo server pada {$today->toDateString()}"
                );
            }

            $count++;
        }

        $this->info("Berhasil memproses {$count} barang wanprestasi/macet menjadi SIAP LELANG.");

        return Command::SUCCESS;
    }
}
