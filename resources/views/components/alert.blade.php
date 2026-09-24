@props([
    'type' => 'info', // Default value
])

    <div class="alert-container" id="{{ $id ?? '' }}">

        @if($type === 'success')
            <div class="alert alert-success">
                {{ $slot }}
            </div>
        @elseif($type === 'error')
            <div class="alert alert-danger">
                {{ $slot }}
            </div>
        @elseif($type === 'warning')
            <div class="alert alert-warning">
                {{ $slot }}
            </div>
        @else
            <div class="alert alert-info">
                {{ $slot }}
            </div>
        @endif

    </div>
