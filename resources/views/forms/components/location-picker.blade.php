@php
    $id = $getId();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ 
        state: $wire.entangle('{{ $getStatePath() }}'),
        openMap() {
            if (this.state && this.state.trim()) {
                window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(this.state), '_blank');
            }
        }
    }" class="relative">
        <input
            x-model="state"
            type="text"
            id="{{ $id }}"
            {!! $placeholder ? 'placeholder="' . e($placeholder) . '"' : '' !!}
            {!! ($max = $getMaxLength()) ? 'maxlength="' . e($max) . '"' : '' !!}
            class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300"
        />

        <button
            type="button"
            x-on:click="openMap"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-primary-500 transition-colors duration-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>
</x-dynamic-component>