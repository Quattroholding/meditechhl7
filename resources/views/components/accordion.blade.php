<div {{ $attributes->merge(['class' => 'w-full space-y-2']) }} x-data="{
    allowMultiple: @js($allowMultiple),
    activeItem: new URL(window.location.href).searchParams.get('open') || null,

    init() {
        if (this.activeItem) {
            this.toggleItem(this.activeItem, true);
        }
    },

    toggleItem(id, forceOpen = false) {
        const contentEl = this.$refs[id];

        if (forceOpen) {
            contentEl.classList.remove('hidden');
            return;
        }

        if (this.allowMultiple) {
            contentEl.classList.toggle('hidden');
        } else {
            document.querySelectorAll('[data-accordion-content]').forEach((el) => {
                if (el.dataset.accordionContent === id) {
                    el.classList.toggle('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }
    }
}">
    {{ $slot }}
</div>
