<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Friends List') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("All users from database are shown, it should to show current user friends list only.") }}
        </p>
    </header>

    <div class="p-6 text-gray-900">
        {{ __("Here is a list of all friends:") }}
        @empty($friends)
            <p>The variable is empty or not set.</p>
        @else
            <ul>
                @foreach ($friends as $person)
                    <li><a href="{{ url('/@'.$person->name) }}">{{ $person->name }}</a> - {{ $person->email }}</li>
                @endforeach
            </ul>
        @endempty
    </div>

</section>