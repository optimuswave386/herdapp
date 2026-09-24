<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Photos') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("All photos from database are shown, it should to show current user photos only.") }}
        </p>
    </header>

    <div class="p-6 text-gray-900">
        {{ __("Here is a list of all photos:") }}
        
        @empty($photos)
            <p>The variable is empty or not set.</p>
        @else
            <ul>
            @foreach ($photos as $photo)
                <li>{{ $photo->phototitle }} - {{ $photo->photodescription }}
                    <img src="data:image/jpeg;base64,{{ $photo->photoblob }}" alt="{{ $photo->photodescription }}" />
                </li>
            @endforeach
            </ul>
        @endempty

    </div>

</section>