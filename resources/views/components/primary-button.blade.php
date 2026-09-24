<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-strong focus:outline-none focus:ring-4 focus:ring-brand-medium disabled:opacity-50']) }}>
    {{ $slot }}
</button>
