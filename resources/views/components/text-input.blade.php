@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand disabled:bg-gray-50']) }}>
