@props([
    'title',
    'value',
    'color' => 'blue' // رنگ پیش‌فرض
])

@php
    // کلاس‌های مربوط به هر رنگ
    $themes = [
        'default' => 'theme-chip-on',
        'gray'    => 'border-gray-500 text-gray-600 bg-gray-500/10 shadow-[0_4px_16px_rgba(107,114,128,0.15)] hover:!text-gray-600 hover:!border-gray-500/30',
        'slate'   => 'border-slate-500 text-slate-600 bg-slate-500/10 shadow-[0_4px_16px_rgba(100,116,139,0.15)] hover:!text-slate-600 hover:!border-slate-500/30',
        'amber'   => 'border-amber-600 text-amber-600 bg-amber-600/10 shadow-[0_4px_16px_rgba(217,119,6,0.15)] hover:!text-amber-600 hover:!border-amber-600/30',
        'emerald' => 'border-emerald-800 text-emerald-600 bg-emerald-600/10 shadow-[0_4px_16px_rgba(5,150,105,0.15)] hover:!text-emerald-600 hover:!border-emerald-800/30',
        'green'   => 'border-green-600 text-green-600 bg-green-600/10 shadow-[0_4px_16px_rgba(22,163,74,0.15)] hover:!text-green-600 hover:!border-green-600/30',
        'blue'    => 'border-blue-600 text-blue-600 bg-blue-600/10 shadow-[0_4px_16px_rgba(37,99,235,0.15)] hover:!text-blue-600 hover:!border-blue-600/30',
        'indigo'  => 'border-indigo-600 text-indigo-600 bg-indigo-600/10 shadow-[0_4px_16px_rgba(79,70,229,0.15)] hover:!text-indigo-600 hover:!border-indigo-600/30',
        'violet'  => 'border-violet-600 text-violet-600 bg-violet-600/10 shadow-[0_4px_16px_rgba(124,58,237,0.15)] hover:!text-violet-600 hover:!border-violet-600/30',
        'red'     => 'border-red-600 text-red-600 bg-red-600/10 shadow-[0_4px_16px_rgba(220,38,38,0.15)] hover:!text-red-600 hover:!border-red-600/30',
        'rose'    => 'border-rose-600 text-rose-600 bg-rose-600/10 shadow-[0_4px_16px_rgba(225,29,72,0.15)] hover:!text-rose-600 hover:!border-rose-600/30',
        'sky'     => 'border-sky-600 text-sky-600 bg-sky-600/10 shadow-[0_4px_16px_rgba(3,105,161,0.15)] hover:!text-sky-600 hover:!border-sky-600/30',
    ];

    $themeClass = $themes[$color] ?? $themes['default'];
@endphp

{{-- کلاس‌های اصلی و ساختاری بالا را نگه‌داشته و کلاس‌های رنگی پویا را به آن اضافه می‌کنیم --}}
<div {{ $attributes->merge([
    'class' => "theme-chip  " . $themeClass
]) }}>
    <span>{{ $title }} :</span>
    <span >{{ $value }}</span>
</div>



