@props(['title', 'isOpen' => false])

<div class="border rounded-lg overflow-hidden" id="parent_section_marker_{{ $attributes->whereStartsWith('data-id')->first() }}">
    <button
        @click="toggleItem('accordion-{{ $attributes->whereStartsWith('data-id')->first() }}')"
        class="w-full px-4 py-3 text-left font-medium flex justify-between items-center hover:bg-gray-50 transition"
        style="background: rgb(45, 59, 165);color:#fff;">
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

    <div id="section_marker_{{ $attributes->whereStartsWith('data-id')->first() }}"
        x-ref="accordion-{{ $attributes->whereStartsWith('data-id')->first() }}"
        data-accordion-content="accordion-{{ $attributes->whereStartsWith('data-id')->first() }}"
        class="{{ $isOpen ? '' : 'hidden' }} px-4 py-3 bg-white-50"
    >
        {{ $slot }}
    </div>
</div>

