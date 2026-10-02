<?php

namespace App\Services;

use App\Models\AppSetting;
use Carbon\Carbon;

class PawnCalculationService
{
    /**
     * Hitung nilai plafon pinjaman maksimal berdasarkan taksiran.
     */
    public function calculatePlafon(float $estimatedValue): float
    {
        $percentage = (float) AppSetting::getValue(AppSetting::KEY_PLAFON_PERCENTAGE, 80);
        return round(($estimatedValue * $percentage) / 100, 2);
    }

    /**
     * Hitung nominal bunga berdasarkan pinjaman dan rate bunga.
     */
    public function calculateInterest(float $loanAmount, ?float $rate = null): float
    {
        $rate = $rate ?? (float) AppSetting::getValue(AppSetting::KEY_INTEREST_RATE, 10);
        return round(($loanAmount * $rate) / 100, 2);
    }

    /**
     * Hitung tanggal jatuh tempo otomatis.
     */
    public function calculateDueDate(Carbon $startDate, ?int $tenorDays = null): Carbon
    {
        $tenorDays = $tenorDays ?? (int) AppSetting::getValue(AppSetting::KEY_DEFAULT_TENOR_DAYS, 30);
        return $startDate->copy()->addDays($tenorDays);
    }

    /**
     * Hitung denda keterlambatan berdasarkan tanggal jatuh tempo dan tanggal bayar server.
     */
    public function calculatePenalty(float $loanAmount, Carbon $dueDate, ?Carbon $paymentDate = null): array
    {
        $paymentDate = $paymentDate ?? Carbon::now();
        
        if ($paymentDate->lte($dueDate)) {
            return [
                'days_late' => 0,
                'penalty_rate_per_day' => (float) AppSetting::getValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, 0.5),
                'penalty_amount' => 0.0,
            ];
        }

        $daysLate = (int) $dueDate->diffInDays($paymentDate);
        $penaltyRatePerDay = (float) AppSetting::getValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, 0.5);
        $penaltyAmount = round(($loanAmount * ($penaltyRatePerDay / 100)) * $daysLate, 2);

        return [
            'days_late' => $daysLate,
            'penalty_rate_per_day' => $penaltyRatePerDay,
            'penalty_amount' => $penaltyAmount,
        ];
    }
}
