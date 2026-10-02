<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PawnTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DueDateController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $dueScope = $request->input('due_scope', 'all');
        $status = $request->input('status', 'all');
        $dateFrom = $request->input('due_date_from');
        $dateTo = $request->input('due_date_to');
        $search = $request->input('search');

        $query = PawnTransaction::with(['customer', 'item'])
            ->whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG]);

        // Filter Status Kontrak
        if ($status !== 'all' && !empty($status)) {
            $query->where('status', $status);
        }

        // Filter Kategori Jatuh Tempo
        if ($dueScope === 'today') {
            $query->whereDate('due_date', $today);
        } elseif ($dueScope === 'near_3') {
            $query->whereBetween('due_date', [$today, $today->copy()->addDays(3)]);
        } elseif ($dueScope === 'near_7') {
            $query->whereBetween('due_date', [$today, $today->copy()->addDays(7)]);
        } elseif ($dueScope === 'overdue') {
            $query->where('due_date', '<', $today);
        } elseif ($dueScope === 'safe') {
            $query->where('due_date', '>', $today->copy()->addDays(7));
        }

        // Filter Rentang Tanggal Manual
        if ($dateFrom) {
            $query->whereDate('due_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('due_date', '<=', $dateTo);
        }

        // Pencarian Teks
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('barcode_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('identity_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%")
                         ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();

        // Statistik KPI Kontrak
        $stats = [
            'total_active' => PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])->count(),
            'due_today' => PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
                ->whereDate('due_date', $today)->count(),
            'near_3' => PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
                ->whereBetween('due_date', [$today, $today->copy()->addDays(3)])->count(),
            'overdue' => PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
                ->where('due_date', '<', $today)->count(),
            'total_loan' => PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
                ->sum('loan_amount'),
        ];

        return view('admin.duedate.index', compact('transactions', 'today', 'stats', 'dueScope'));
    }
}
