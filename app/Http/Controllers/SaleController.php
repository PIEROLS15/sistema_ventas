<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Identificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'identificacion_id' => 'required|integer|exists:identificaciones,id',
            'numero_identificacion' => 'required|string|max:20',
            'cliente_correo' => 'nullable|email',
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.cantidad' => 'required|integer|min:1',
        ]);

        $codigoVenta = 'v' . str_pad(Sale::count() + 1, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            // Crear la venta
            $venta = Sale::create([
                'codigo' => $codigoVenta,
                'cliente_nombre' => $request->cliente_nombre,
                'identificacion_id' => $request->identificacion_id,
                'numero_identificacion' => $request->numero_identificacion,
                'cliente_correo' => $request->cliente_correo,
                'vendedor_id' => Auth::id(),
                'fecha_venta' => now(),
                'monto_total' => 0,
            ]);

            $montoTotal = 0;
            $productosNoEncontrados = [];
            $productosSinStock = [];

            // Validar existencia de productos antes de procesar la venta
            foreach ($request->products as $productsData) {
                $products = Product::find($productsData['id']);

                if (!$products) {
                    $productosNoEncontrados[] = $productsData['id'];
                    continue;
                }

                // Verificar stock
                if ($products->stock < $productsData['cantidad']) {
                    $productosSinStock[] = [
                        'id' => $products->id,
                        'nombre' => $products->nombre,
                        'stock_disponible' => $products->stock
                    ];
                }
            }

            if (!empty($productosNoEncontrados)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Algunos productos no existen.',
                    'productos_no_encontrados' => $productosNoEncontrados
                ], 400);
            }

            // Si hay productos sin stock, devolvemos error
            if (!empty($productosSinStock)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Algunos productos no tienen stock suficiente.',
                    'productos_sin_stock' => $productosSinStock
                ], 400);
            }

            // Procesar cada producto en la venta
            foreach ($request->products as $productsData) {
                $products = Product::find($productsData['id']);

                $subtotal = $products->precio_unitario * $productsData['cantidad'];

                // Crear el detalle de la venta
                $venta->detalles()->create([
                    'product_id' => $products->id,
                    'cantidad' => $productsData['cantidad'],
                    'precio_unitario' => $products->precio_unitario,
                    'subtotal' => $subtotal,
                ]);

                // Reducir stock
                $products->decrement('stock', $productsData['cantidad']);

                $montoTotal += $subtotal;
            }

            // Actualizar el monto total de la venta
            $venta->update(['monto_total' => $montoTotal]);

            DB::commit();

            return response()->json(['message' => 'Venta registrada exitosamente', 'venta' => $venta], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al registrar la venta',
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ], 500);
        }
    }
}