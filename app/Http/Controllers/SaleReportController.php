<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Sale;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use Carbon\Carbon;

class SaleReportController extends Controller
{
    public function generateReport(Request $request)
    {
        if (Gate::denies('administrador')) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // Validar fechas
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'format'     => 'required|in:json,xlsx'
        ]);

        // Obtener las ventas en el rango de fechas
        $sales = Sale::whereBetween('fecha_venta', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ])->with(['detalles', 'identificacion'])->get();

        // Formatear los datos del reporte
        $reportData = $sales->map(function ($sale) {
            return [
                'codigo_venta'      => $sale->codigo,
                'cliente_nombre'    => $sale->cliente_nombre,
                'tipo_identificacion' => $sale->identificacion->tipo, 
                'numero_identificacion' => $sale->numero_identificacion,
                'cliente_correo'    => $sale->cliente_correo,
                'total_productos'   => $sale->detalles->sum('cantidad'), 
                'monto_total'       => $sale->monto_total,
                'fecha_venta'       => $sale->fecha_venta
            ];
        });

        // Devolver en JSON
        if ($request->format === 'json') {
            return response()->json($reportData);
        }

        // Exportar en XLSX
        return Excel::download(new SalesExport($reportData), 'reporte_ventas.xlsx');
    }
}