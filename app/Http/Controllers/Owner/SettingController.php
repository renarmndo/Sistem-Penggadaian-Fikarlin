<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $interestRate = AppSetting::getValue(AppSetting::KEY_INTEREST_RATE, 10);
        $penaltyRate = AppSetting::getValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, 0.5);
        $defaultTenor = AppSetting::getValue(AppSetting::KEY_DEFAULT_TENOR_DAYS, 30);
        $plafonPercentage = AppSetting::getValue(AppSetting::KEY_PLAFON_PERCENTAGE, 80);

        return view('owner.settings.index', compact(
            'interestRate',
            'penaltyRate',
            'defaultTenor',
            'plafonPercentage'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'penalty_rate_per_day' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_tenor_days' => ['required', 'integer', 'min:1'],
            'plafon_percentage' => ['required', 'numeric', 'min:1', 'max:100'],
        ]);

        AppSetting::setValue(AppSetting::KEY_INTEREST_RATE, $validated['default_interest_rate'], 'Persentase Bunga Gadai (%)');
        AppSetting::setValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, $validated['penalty_rate_per_day'], 'Rate Denda Per Hari (%)');
        AppSetting::setValue(AppSetting::KEY_DEFAULT_TENOR_DAYS, $validated['default_tenor_days'], 'Tenor Baku (Hari)');
        AppSetting::setValue(AppSetting::KEY_PLAFON_PERCENTAGE, $validated['plafon_percentage'], 'Batas Plafon Pinjaman (%)');

        return redirect()->route('owner.settings.index')->with('success', 'Parameter sistem (bunga, denda, tenor, plafon) berhasil diperbarui.');
    }
}
