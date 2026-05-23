<?php

namespace Mortezaa97\Factors\Http\Controllers;

use Mortezaa97\Factors\Models\Factor;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class FactorLabelController
{
    public function show(Factor $factor, Request $request)
    {
        // Load factor with items and related product data
        $factor->load(['items.model', 'customer']);
        
        // Prepare labels data
        $labels = [];
        foreach ($factor->items as $item) {
            $product = $item->model;
            
            if (!$product) {
                continue;
            }
            
            // Get product code - fallback to ID if code doesn't exist
            $productCode = $product->code ?? str_pad($product->id, 9, '0', STR_PAD_LEFT);
            
            // Generate barcode
            $generator = new BarcodeGeneratorPNG();
            try {
                $barcode = base64_encode($generator->getBarcode(
                    $productCode, 
                    $generator::TYPE_CODE_128,
                    2,
                    50
                ));
            } catch (\Exception $e) {
                // Fallback if barcode generation fails
                $barcode = base64_encode($generator->getBarcode(
                    'ERROR', 
                    $generator::TYPE_CODE_128,
                    2,
                    50
                ));
            }
            
            // Create labels based on count
            $count = (int) $item->count;
            for ($i = 0; $i < $count; $i++) {
                $labels[] = [
                    'name' => $product->name ?? 'محصول',
                    'code' => $productCode,
                    'barcode' => $barcode,
                    'price' => number_format($item->unit_price),
                    'product_id' => $product->id,
                    'item_number' => $i + 1,
                    'total_count' => $count,
                ];
            }
        }
        
        // Get settings from query parameters or use defaults
        // Check if this is the first load (no query params) or a form submission
        $isFirstLoad = !$request->hasAny(['rows_per_page', 'label_width', 'label_height']);
        
        $settings = [
            'rows_per_page' => (int) $request->get('rows_per_page', 10),
            'columns_per_row' => (int) $request->get('columns_per_row', 3),
            // For checkboxes: if first load, use true; otherwise check if present in request
            'show_logo' => $isFirstLoad ? true : $request->has('show_logo'),
            'show_price' => $isFirstLoad ? true : $request->has('show_price'),
            'show_item_number' => $request->has('show_item_number'),
            'label_width' => (int) $request->get('label_width', 60), // mm
            'label_height' => (int) $request->get('label_height', 40), // mm
            'viewport_height' => (int) $request->get('viewport_height', 297), // A4 height in mm
            'font_size' => (int) $request->get('font_size', 12), // px
            'barcode_height' => (int) $request->get('barcode_height', 15), // mm
            'logo_text' => $request->get('logo_text', 'UniTech'),
            'label_gap' => (int) $request->get('label_gap', 4), // mm
            'border_style' => $request->get('border_style', 'solid'),
        ];
        
        return view('factors::factors.labels', [
            'factor' => $factor,
            'labels' => $labels,
            'settings' => $settings,
        ]);
    }

    public function invoice(Factor $factor)
    {
        // Load factor with all necessary relationships
        $factor->load(['items.model', 'customer']);
        
        // Calculate totals
        $subtotal = 0;
        $totalVat = 0;
        $totalDiscount = 0;
        
        foreach ($factor->items as $item) {
            $itemTotal = $item->unit_price * $item->count;
            $subtotal += $itemTotal;
            
            // Calculate VAT (9% in Iran)
            // Uncomment if you want to add VAT calculation
            // $totalVat += $itemTotal * 0.09;
        }
        
        $totalPayable = $subtotal + $totalVat - $totalDiscount;
        
        // Prepare company/seller info (use Persian numbers)
        $company = [
            'name' => 'یونیتک',
            'address' => 'مشهد، بین فرامرز عباسی ۲۶ و ۲۸ - پلاک ۱۷۸ - طبقه اول',
            'phone' => '۰۵۱-۹۱۰۹۰۰۰۲',
            'unit' => 'واحد ۱',
        ];
        
        return view('factors::factors.invoice', [
            'factor' => $factor,
            'subtotal' => $subtotal,
            'totalVat' => $totalVat,
            'totalDiscount' => $totalDiscount,
            'totalPayable' => $totalPayable,
            'company' => $company,
        ]);
    }
}

