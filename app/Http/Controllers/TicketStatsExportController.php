<?php

namespace App\Http\Controllers;

use App\Exports\TicketStatsExport;
use Maatwebsite\Excel\Facades\Excel;

class TicketStatsExportController extends Controller
{
    /**
     * Export statistik tiket ke Excel
     */
    public function exportTicketStats()
    {
        return Excel::download(new TicketStatsExport, 'statistik_tiket.xlsx');
    }
} 