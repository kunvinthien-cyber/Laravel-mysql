@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
    'badge' => null
])

<div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

    <div class="flex justify-between items-center">

        <div
            class="w-11 h-11 rounded-xl flex items-center justify-center bg-{{ $color }}-100 text-{{ $color }}-600">
            {!! $icon !!}
        </div>

        @if($badge)
            <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">
                {{ $badge }}
            </span>
        @endif

    </div>

    <p class="mt-5 text-gray-500 text-sm uppercase">
        {{ $title }}
    </p>

    <h2 class="text-3xl font-bold mt-2">
        {{ $value }}
    </h2>

</div>
