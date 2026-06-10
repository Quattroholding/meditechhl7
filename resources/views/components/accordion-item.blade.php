@props(['title', 'isOpen' => false, 'category' => null])

@php
    // Define color classes based on SOAP categories
    $categoryColors = [
        'subjective' => 'bg-blue-100 border-blue-300 text-blue-900 hover:bg-blue-200',
        'objective' => 'bg-green-100 border-green-300 text-green-900 hover:bg-green-200',
        'assessment' => 'bg-amber-100 border-amber-300 text-amber-900 hover:bg-amber-200',
        'plan' => 'bg-purple-100 border-purple-300 text-purple-900 hover:bg-purple-200',
        'specialty' => 'bg-indigo-100 border-indigo-300 text-indigo-900 hover:bg-indigo-200',
        'system' => 'bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200',
    ];

    // Get color classes for the category, default to gray if not found
    $colorClass = $categoryColors[$category] ?? 'bg-gray-50 border-gray-200 text-gray-900 hover:bg-gray-100';
@endphp

<div class="border rounded-lg overflow-hidden {{ $categoryColors[$category] ?? 'border-gray-200' }}" id="parent_section_marker_{{ $attributes->whereStartsWith('data-id')->first() }}">
    <button
        @click="toggleItem('accordion-{{ $attributes->whereStartsWith('data-id')->first() }}')"
        class="w-full px-4 py-3 text-left font-medium flex justify-between items-center hover:bg-gray-50 transition {{ $colorClass }} btn-primary">
        <span>{{ $title }}</span>
        <svg
            class="w-5 h-5 transform transition-transform duration-200"
            :class="{ 'rotate-180': !$refs['accordion-{{ $attributes->whereStartsWith('data-id')->first() }}'].classList.contains('hidden') }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="section_marker_{{ $attributes->whereStartsWith('data-id')->first() }}" style="background: #fff;"
        x-ref="accordion-{{ $attributes->whereStartsWith('data-id')->first() }}"
        data-accordion-content="accordion-{{ $attributes->whereStartsWith('data-id')->first() }}"
        class="{{ $isOpen ? '' : 'hidden' }} px-4 py-3 bg-white-50"
    >
        {{ $slot }}
    </div>
</div>

