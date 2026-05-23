<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="فاکتور فروش - {{ $factor->code }}">
    <title>فاکتور فروش - {{ $factor->code }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazirmatn', sans-serif;
        }
        
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
        }
        
        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
        }
        
        .company-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 42px;
            height: 42px;
            background: #1d4ed8;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #1d4ed8;
            letter-spacing: 0;
        }
        
        .invoice-title {
            text-align: center;
            flex-grow: 1;
        }
        
        .invoice-title h1 {
            font-size: 20px;
            font-weight: 700;
            color: #000;
            letter-spacing: 1px;
        }
        
        .invoice-meta {
            text-align: left;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .invoice-meta div {
            margin-bottom: 2px;
            color: #1f2937;
            direction: rtl;
        }
        
        .invoice-meta strong {
            font-weight: 700;
        }
        
        /* Section Headers */
        .section-header {
            background: #f3f4f6;
            padding: 8px 15px;
            margin-top: 15px;
            margin-bottom: 0;
            border: 1px solid #d1d5db;
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            text-align: center;
        }
        
        /* Info Boxes */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            margin-bottom: 0;
        }
        
        .info-grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 0;
            margin-bottom: 0;
        }
        
        .info-box {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            border-right: none;
            border-top: none;
        }
        
        .info-box:first-child {
            border-right: 1px solid #d1d5db;
        }
        
        .info-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        
        /* Table Styles */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 0;
        }
        
        .invoice-table thead {
            background: #f9fafb;
        }
        
        .invoice-table th {
            padding: 8px 6px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .invoice-table td {
            padding: 7px 6px;
            text-align: center;
            border: 1px solid #d1d5db;
            font-size: 12px;
            color: #1f2937;
        }
        
        .invoice-table tbody tr:hover {
            background: #fafafa;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .text-center {
            text-align: center !important;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        /* Summary Row */
        .summary-row {
            background: #f3f4f6;
            font-weight: 600;
        }
        
        /* Total Payable Row */
        .total-row {
            background: #000000;
            color: white;
            font-weight: 700;
        }
        
        .total-row td {
            background: #000000;
            color: white;
        }
        
        /* Small text in amounts */
        small {
            font-size: 10px;
            font-weight: normal;
        }
        
        /* Footer Section */
        .invoice-footer {
            margin-top: 20px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-top: 15px;
            border: 1px solid #d1d5db;
        }
        
        .signature-box {
            padding: 45px 15px 12px;
            text-align: center;
            border-left: 1px solid #d1d5db;
        }
        
        .signature-box:first-child {
            border-left: none;
        }
        
        .signature-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 12px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 10mm;
                max-width: 100%;
                border: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
        
        /* Action Buttons */
        .action-buttons {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            display: flex;
            justify-content: space-between;
            z-index: 1000;
        }
        
        .print-button, .back-button {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .print-button:hover, .back-button:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .print-button {
            background: #1d4ed8;
            color: white;
            border-color: #1d4ed8;
        }
        
        .print-button:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        
        /* Number Formatting */
        .number {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="print-button">
            چاپ فاکتور
        </button>
        <a href="{{ url()->previous() }}" class="back-button">
            بازگشت
        </a>
    </div>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-logo">
                <div class="logo-icon">Y</div>
                <div class="company-name">{{ $company['name'] }}</div>
            </div>
            
            <div class="invoice-title">
                <h1>فاکتور فروش</h1>
            </div>
            
            <div class="invoice-meta">
                <div><strong>شماره :</strong> {{ $factor->code ?? '---' }}</div>
                <div><strong>تاریخ :</strong> {{ \Morilog\Jalali\Jalalian::fromDateTime($factor->created_at)->format('Y/m/d') }}</div>
            </div>
        </div>
        
        <!-- Buyer Information -->
        <div class="section-header">مشخصات خریدار</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">نام شخص حقیقی / حقوقی</div>
                <div class="info-value">{{ $factor->customer->name ?? 'فروش روزانه حضوری' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">شماره ثبت / شماره ملی</div>
                <div class="info-value">{{ $factor->customer->national_id ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">تلفن</div>
                <div class="info-value">{{ $factor->customer->phone ?? '-' }}</div>
            </div>
        </div>
        
        <!-- Seller Information -->
        <div class="section-header">مشخصات فروشنده</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">نام شخص حقیقی / حقوقی</div>
                <div class="info-value">{{ $company['name'] }}</div>
            </div>
            <div class="info-box" style="grid-column: span 2;">
                <div class="info-label">نشانی</div>
                <div class="info-value">{{ $company['address'] }}</div>
            </div>
        </div>
        <div class="info-grid-2">
            <div class="info-box">
                <div class="info-label">تلفن</div>
                <div class="info-value">{{ $company['phone'] }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">واحد</div>
                <div class="info-value">{{ $company['unit'] }}</div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 40px;">ردیف</th>
                    <th style="width: 35%;">شرح کالا یا خدمات</th>
                    <th style="width: 100px;">کد کالا</th>
                    <th style="width: 60px;">تعداد</th>
                    <th style="width: 60px;">واحد</th>
                    <th style="width: 120px;">مبلغ واحد <small>(تومان)</small></th>
                    <th style="width: 120px;">مالیات بر ارزش افزوده <small>(تومان)</small></th>
                    <th style="width: 120px;">جمع کل <small>(تومان)</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach($factor->items as $index => $item)
                    @php
                        $product = $item->model;
                        $productCode = $product->code ?? str_pad($product->id ?? 0, 9, '0', STR_PAD_LEFT);
                        $itemTotal = $item->unit_price * $item->count;
                        $itemVat = 0; // Set to $itemTotal * 0.09 if you want to add VAT
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-right">{{ $product->name ?? 'محصول' }}</td>
                        <td class="number">{{ $productCode }}</td>
                        <td>{{ $item->count }}</td>
                        <td>عدد</td>
                        <td class="number">{{ number_format($item->unit_price) }}</td>
                        <td class="number">{{ number_format($itemVat) }}</td>
                        <td class="number">{{ number_format($itemTotal) }}</td>
                    </tr>
                @endforeach
                
                <!-- Summary Row -->
                <tr class="summary-row">
                    <td colspan="5" class="text-right">جمع کل ردیف‌ها</td>
                    <td class="number">{{ number_format($subtotal) }}</td>
                    <td class="number">{{ number_format($totalVat) }}</td>
                    <td class="number">{{ number_format($subtotal) }}</td>
                </tr>
                
                <!-- Discount Row -->
                <tr>
                    <td colspan="4"></td>
                    <td class="text-center" colspan="3">تخفیف</td>
                    <td class="text-center">{{ number_format($totalDiscount) }}</td>
                </tr>
                
                <!-- VAT Row -->
                <tr>
                    <td colspan="4"></td>
                    <td class="text-center" colspan="3">مالیات بر ارزش افزوده</td>
                    <td class="text-center">{{ number_format($totalVat) }}</td>
                </tr>
                
                <!-- Total Payable Row -->
                <tr class="total-row">
                    <td colspan="4"></td>
                    <td class="text-center" colspan="3">مبلغ قابل پرداخت</td>
                    <td class="text-center">{{ number_format($totalPayable) }}</td>
                </tr>
                
                <!-- Comments Row -->
                <tr>
                    <td colspan="8" class="text-right" style="padding: 10px;">
                        توضیحات:<br>
                        {{ $factor->description ?? '' }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer / Signatures -->
        <div class="invoice-footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-label">مهر و امضای فروشنده</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">مهر و امضای خریدار</div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>

