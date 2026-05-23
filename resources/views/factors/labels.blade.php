<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="چاپ برچسب محصولات - فاکتور {{ $factor->code }}">
    <title>چاپ برچسب - فاکتور {{ $factor->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Vazirmatn', sans-serif;
        }
        
        .label-container {
            width: {{ $settings['label_width'] }}mm;
            height: {{ $settings['label_height'] }}mm;
            page-break-inside: avoid;
            break-inside: avoid;
            margin: {{ $settings['label_gap'] }}mm;
            border-style: {{ $settings['border_style'] }};
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .label-container {
                border: 1px solid #ddd !important;
                margin: {{ $settings['label_gap'] }}mm;
            }
            
            .labels-grid {
                display: grid !important;
                grid-template-columns: repeat({{ $settings['columns_per_row'] }}, 1fr) !important;
                gap: {{ $settings['label_gap'] }}mm !important;
            }
            
            @page {
                size: auto;
                margin: 10mm;
            }
            
            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }
        
        .settings-panel {
            position: sticky;
            top: 0;
            z-index: 50;
            background: white;
        }
        
        .labels-grid {
            display: grid;
            grid-template-columns: repeat({{ $settings['columns_per_row'] }}, 1fr);
            gap: {{ $settings['label_gap'] }}mm;
        }
        
        /* Accessibility improvements */
        .focus-visible:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Loading animation */
        .loading {
            opacity: 0.5;
            pointer-events: none;
        }
        
        /* Smooth transitions */
        input, select, button {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Settings Panel (collapsible) -->
    <div class="no-print settings-panel shadow-md border-b border-gray-200" role="banner">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">تنظیمات چاپ برچسب</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            فاکتور: {{ $factor->code }} | مشتری: {{ $factor->customer->name ?? 'نامشخص' }} | تعداد برچسب: {{ count($labels) }}
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <button 
                            type="button"
                            id="toggleSettingsPanelBtn"
                            class="px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors"
                            aria-expanded="true"
                            aria-controls="settingsPanelContent"
                        >
                            <span id="toggleSettingsPanelLabel">بستن تنظیمات</span>
                            <svg id="toggleSettingsPanelIcon" class="inline-block w-4 h-4 transition-transform ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <button 
                            type="button"
                            onclick="resetSettings()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors"
                            aria-label="بازنشانی تنظیمات به حالت پیش‌فرض"
                        >
                            بازنشانی
                        </button>
                    </div>
                </div>
                
                <div id="settingsPanelContent">
                    <form method="GET" action="{{ route('factors.labels', $factor) }}" id="settingsForm" class="space-y-6">
                        <!-- Dimensions Section -->
                        <fieldset class="border border-gray-200 rounded-lg p-4">
                            <legend class="text-lg font-semibold text-gray-800 px-2">ابعاد و چیدمان</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <label for="label_width" class="block text-sm font-medium text-gray-700 mb-2">
                                        عرض برچسب (mm)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="label_width"
                                        name="label_width" 
                                        value="{{ $settings['label_width'] }}"
                                        min="30"
                                        max="200"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-describedby="label_width_help"
                                    >
                                    <p id="label_width_help" class="text-xs text-gray-500 mt-1">30-200 میلی‌متر</p>
                                </div>
                                
                                <div>
                                    <label for="label_height" class="block text-sm font-medium text-gray-700 mb-2">
                                        ارتفاع برچسب (mm)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="label_height"
                                        name="label_height" 
                                        value="{{ $settings['label_height'] }}"
                                        min="20"
                                        max="150"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-describedby="label_height_help"
                                    >
                                    <p id="label_height_help" class="text-xs text-gray-500 mt-1">20-150 میلی‌متر</p>
                                </div>
                                
                                <div>
                                    <label for="columns_per_row" class="block text-sm font-medium text-gray-700 mb-2">
                                        تعداد ستون در هر ردیف
                                    </label>
                                    <input 
                                        type="number" 
                                        id="columns_per_row"
                                        name="columns_per_row" 
                                        value="{{ $settings['columns_per_row'] }}"
                                        min="1"
                                        max="10"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-describedby="columns_help"
                                    >
                                    <p id="columns_help" class="text-xs text-gray-500 mt-1">1-10 ستون</p>
                                </div>
                                
                                <div>
                                    <label for="rows_per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                        تعداد ردیف در هر صفحه
                                    </label>
                                    <input 
                                        type="number" 
                                        id="rows_per_page"
                                        name="rows_per_page" 
                                        value="{{ $settings['rows_per_page'] }}"
                                        min="1"
                                        max="50"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-describedby="rows_help"
                                    >
                                    <p id="rows_help" class="text-xs text-gray-500 mt-1">1-50 ردیف</p>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Appearance Section -->
                        <fieldset class="border border-gray-200 rounded-lg p-4">
                            <legend class="text-lg font-semibold text-gray-800 px-2">ظاهر و نمایش</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <label for="font_size" class="block text-sm font-medium text-gray-700 mb-2">
                                        اندازه فونت (px)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="font_size"
                                        name="font_size" 
                                        value="{{ $settings['font_size'] }}"
                                        min="8"
                                        max="24"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                
                                <div>
                                    <label for="barcode_height" class="block text-sm font-medium text-gray-700 mb-2">
                                        ارتفاع بارکد (mm)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="barcode_height"
                                        name="barcode_height" 
                                        value="{{ $settings['barcode_height'] }}"
                                        min="10"
                                        max="40"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                
                                <div>
                                    <label for="label_gap" class="block text-sm font-medium text-gray-700 mb-2">
                                        فاصله بین برچسب‌ها (mm)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="label_gap"
                                        name="label_gap" 
                                        value="{{ $settings['label_gap'] }}"
                                        min="0"
                                        max="20"
                                        step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                
                                <div>
                                    <label for="border_style" class="block text-sm font-medium text-gray-700 mb-2">
                                        نوع حاشیه
                                    </label>
                                    <select 
                                        id="border_style"
                                        name="border_style" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="solid" {{ $settings['border_style'] == 'solid' ? 'selected' : '' }}>پیوسته</option>
                                        <option value="dashed" {{ $settings['border_style'] == 'dashed' ? 'selected' : '' }}>خط‌چین</option>
                                        <option value="dotted" {{ $settings['border_style'] == 'dotted' ? 'selected' : '' }}>نقطه‌چین</option>
                                        <option value="none" {{ $settings['border_style'] == 'none' ? 'selected' : '' }}>بدون حاشیه</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Page Settings Section -->
                        <fieldset class="border border-gray-200 rounded-lg p-4">
                            <legend class="text-lg font-semibold text-gray-800 px-2">تنظیمات صفحه</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <label for="viewport_height" class="block text-sm font-medium text-gray-700 mb-2">
                                        ارتفاع صفحه (mm)
                                    </label>
                                    <select 
                                        id="viewport_height"
                                        name="viewport_height" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="297" {{ $settings['viewport_height'] == 297 ? 'selected' : '' }}>A4 (297mm)</option>
                                        <option value="210" {{ $settings['viewport_height'] == 210 ? 'selected' : '' }}>A5 (210mm)</option>
                                        <option value="420" {{ $settings['viewport_height'] == 420 ? 'selected' : '' }}>A3 (420mm)</option>
                                        <option value="custom">سفارشی...</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="logo_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        متن لوگو
                                    </label>
                                    <input 
                                        type="text" 
                                        id="logo_text"
                                        name="logo_text" 
                                        value="{{ $settings['logo_text'] }}"
                                        maxlength="50"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="نام فروشگاه"
                                    >
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Display Options Section -->
                        <fieldset class="border border-gray-200 rounded-lg p-4">
                            <legend class="text-lg font-semibold text-gray-800 px-2">گزینه‌های نمایش</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div class="flex items-center h-10">
                                    <label class="flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            id="show_logo"
                                            name="show_logo" 
                                            value="1"
                                            {{ $settings['show_logo'] ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            aria-label="نمایش لوگو در برچسب‌ها"
                                        >
                                        <span class="mr-2 text-sm font-medium text-gray-700">نمایش لوگو</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center h-10">
                                    <label class="flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            id="show_price"
                                            name="show_price" 
                                            value="1"
                                            {{ $settings['show_price'] ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            aria-label="نمایش قیمت در برچسب‌ها"
                                        >
                                        <span class="mr-2 text-sm font-medium text-gray-700">نمایش قیمت</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center h-10">
                                    <label class="flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            id="show_item_number"
                                            name="show_item_number" 
                                            value="1"
                                            {{ $settings['show_item_number'] ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            aria-label="نمایش شماره آیتم"
                                        >
                                        <span class="mr-2 text-sm font-medium text-gray-700">نمایش شماره آیتم</span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200">
                            <button 
                                type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                aria-label="اعمال تنظیمات و به‌روزرسانی پیش‌نمایش"
                            >
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    اعمال تنظیمات
                                </span>
                            </button>
                            
                            <button 
                                type="button"
                                onclick="window.print()"
                                class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                aria-label="چاپ برچسب‌ها"
                            >
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    چاپ برچسب‌ها
                                </span>
                            </button>
                            
                            <a 
                                href="{{ url()->previous() }}"
                                class="px-6 py-2.5 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                                aria-label="بازگشت به صفحه قبل"
                            >
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    بازگشت
                                </span>
                            </a>
                            
                            <div class="mr-auto text-sm text-gray-600">
                                <kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs">Ctrl+P</kbd>
                                برای چاپ سریع
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Simple collapsible logic
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleSettingsPanelBtn');
            const panelContent = document.getElementById('settingsPanelContent');
            const labelSpan = document.getElementById('toggleSettingsPanelLabel');
            const chevron = document.getElementById('toggleSettingsPanelIcon');

            let isOpen = true;

            toggleBtn.addEventListener('click', function () {
                isOpen = !isOpen;
                if (isOpen) {
                    panelContent.style.display = '';
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    labelSpan.textContent = 'بستن تنظیمات';
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    panelContent.style.display = 'none';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    labelSpan.textContent = 'باز کردن تنظیمات';
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
        });
    </script>
    <!-- Labels Grid -->
    <main class="max-w-7xl mx-auto px-4 py-8" role="main">
        @if(count($labels) === 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center" role="alert">
                <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-yellow-800 mb-2">هیچ برچسبی برای چاپ وجود ندارد</h2>
                <p class="text-yellow-700">این فاکتور هیچ آیتمی ندارد یا تمام آیتم‌ها فاقد محصول معتبر هستند.</p>
            </div>
        @else
            <div class="labels-grid">
                @foreach($labels as $index => $label)
                    <div class="label-container border-2 border-gray-300 rounded-lg p-3 bg-white flex flex-col items-center justify-center gap-2" 
                         role="article" 
                         aria-label="برچسب محصول {{ $label['name'] }}">
                        <!-- Logo (optional) -->
                        @if($settings['show_logo'] && $settings['logo_text'])
                            <div class="w-full flex justify-center mb-1">
                                <div class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded" 
                                     style="font-size: {{ $settings['font_size'] * 0.75 }}px;">
                                    {{ $settings['logo_text'] }}
                                </div>
                            </div>
                        @endif
                        
                        <!-- Barcode -->
                        <div class="flex justify-center w-full" role="img" aria-label="بارکد محصول {{ $label['code'] }}">
                            <img 
                                src="data:image/png;base64,{{ $label['barcode'] }}" 
                                alt="بارکد {{ $label['code'] }}" 
                                class="max-w-full h-auto"
                                style="max-height: {{ $settings['barcode_height'] }}mm;"
                                loading="lazy"
                            >
                        </div>
                        
                        <!-- Code -->
                        <div class="text-center font-mono font-semibold text-gray-800" 
                             style="font-size: {{ $settings['font_size'] }}px;">
                            {{ $label['code'] }}
                        </div>
                        
                        <!-- Product Name -->
                        <div class="text-center text-gray-700 line-clamp-2 px-1 leading-tight" 
                             style="font-size: {{ $settings['font_size'] * 0.85 }}px;">
                            {{ $label['name'] }}
                        </div>
                        
                        <!-- Price (optional) -->
                        @if($settings['show_price'])
                            <div class="text-center font-medium text-blue-600 mt-1" 
                                 style="font-size: {{ $settings['font_size'] * 0.9 }}px;">
                                {{ $label['price'] }} تومان
                            </div>
                        @endif
                        
                        <!-- Item Number (optional) -->
                        @if($settings['show_item_number'])
                            <div class="text-center text-xs text-gray-500" 
                                 style="font-size: {{ $settings['font_size'] * 0.7 }}px;">
                                {{ $label['item_number'] }}/{{ $label['total_count'] }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Page break calculation -->
                    @php
                        $labelsPerPage = $settings['rows_per_page'] * $settings['columns_per_row'];
                        $shouldBreak = ($index + 1) % $labelsPerPage === 0 && ($index + 1) < count($labels);
                    @endphp
                    
                    @if($shouldBreak)
                        <div class="page-break" style="grid-column: 1 / -1;"></div>
                    @endif
                @endforeach
            </div>
            
            <!-- Summary -->
            <div class="mt-8 bg-white rounded-lg shadow p-6 no-print">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">خلاصه</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ count($labels) }}</div>
                        <div class="text-sm text-blue-700">تعداد کل برچسب‌ها</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $factor->items->count() }}</div>
                        <div class="text-sm text-green-700">تعداد محصولات</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        @php
                            $labelsPerPage = $settings['rows_per_page'] * $settings['columns_per_row'];
                            $totalPages = ceil(count($labels) / $labelsPerPage);
                        @endphp
                        <div class="text-2xl font-bold text-purple-600">{{ $totalPages }}</div>
                        <div class="text-sm text-purple-700">تعداد صفحات</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-orange-600">{{ $settings['columns_per_row'] }}</div>
                        <div class="text-sm text-orange-700">ستون در هر ردیف</div>
                    </div>
                </div>
            </div>
        @endif
    </main>
    
    <!-- Print Instructions -->
    <aside class="no-print max-w-7xl mx-auto px-4 py-6" role="complementary">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                راهنمای چاپ
            </h3>
            <ul class="text-sm text-blue-700 space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>برای چاپ بهینه، از مرورگر Chrome یا Edge استفاده کنید</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>در تنظیمات چاپ، گزینه "حاشیه" را روی "بدون حاشیه" یا "حداقل" قرار دهید</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>مقیاس را روی 100% نگه دارید</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>برای برچسب‌های کوچک‌تر، عرض و ارتفاع را کاهش دهید</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>می‌توانید تنظیمات را تغییر داده و دوباره اعمال کنید تا پیش‌نمایش به‌روز شود</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>برای چاپ سریع از کلید میانبر <kbd class="px-1 py-0.5 bg-white border border-blue-300 rounded text-xs">Ctrl+P</kbd> استفاده کنید</span>
                </li>
            </ul>
        </div>
    </aside>
    
    <script>
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Ctrl/Cmd + S to submit form (apply settings)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('settingsForm').submit();
            }
        });
        
        // Auto-save settings to localStorage
        const form = document.getElementById('settingsForm');
        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                const formData = new FormData(form);
                const settings = {};
                for (let [key, value] of formData.entries()) {
                    settings[key] = value;
                }
                localStorage.setItem('labelSettings', JSON.stringify(settings));
            });
        });
        
        // Reset settings function
        function resetSettings() {
            if (confirm('آیا مطمئن هستید که می‌خواهید تنظیمات را به حالت پیش‌فرض بازگردانید؟')) {
                localStorage.removeItem('labelSettings');
                window.location.href = '{{ route('factors.labels', $factor) }}';
            }
        }
        
        // Form validation
        form.addEventListener('submit', function(e) {
            const labelWidth = parseInt(document.getElementById('label_width').value);
            const labelHeight = parseInt(document.getElementById('label_height').value);
            
            if (labelWidth < 30 || labelWidth > 200) {
                e.preventDefault();
                alert('عرض برچسب باید بین 30 تا 200 میلی‌متر باشد');
                return;
            }
            
            if (labelHeight < 20 || labelHeight > 150) {
                e.preventDefault();
                alert('ارتفاع برچسب باید بین 20 تا 150 میلی‌متر باشد');
                return;
            }
        });
        
        // Loading state
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>در حال اعمال...</span>';
        });
        
        // Print event tracking
        window.addEventListener('beforeprint', function() {
            console.log('Printing started');
        });
        
        window.addEventListener('afterprint', function() {
            console.log('Printing completed');
        });
        
        // Accessibility: Focus management
        const firstInput = form.querySelector('input');
        if (firstInput && !document.activeElement || document.activeElement === document.body) {
            setTimeout(() => firstInput.focus(), 100);
        }
    </script>
</body>
</html>
