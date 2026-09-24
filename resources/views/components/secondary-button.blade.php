<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-default bg-white px-4 py-2.5 text-sm font-semibold text-heading shadow-xs transition hover:bg-neutral-secondary-soft focus:outline-none focus:ring-4 focus:ring-neutral-tertiary disabled:opacity-50']) }}>
    {{ $slot }}
</button>
