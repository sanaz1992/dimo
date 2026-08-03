@props([
    'color' => 'blue' // رنگ پیش‌فرض در صورت ست نکردن
])

@php
    // نقشه‌برداری نام رنگ‌ها به کلاس‌های دقیق تیلویند
    $colors = [
        'gray' => 'bg-gray-50 text-gray-700 ring-gray-600/10',
        'slate' => 'bg-slate-50 text-slate-700 ring-slate-600/10',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/10',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
        'green' => 'bg-green-50 text-green-700 ring-green-600/10',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/10',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/10',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/10',
        'red' => 'bg-red-50 text-red-700 ring-red-600/10',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/10',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/10',
    ];

    // اگر رنگ ورودی در لیست بالا نبود، از خاکستری استفاده شود
    $colorClass = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset " . $colorClass
]) }}>
    {{ $slot }}
</span>
