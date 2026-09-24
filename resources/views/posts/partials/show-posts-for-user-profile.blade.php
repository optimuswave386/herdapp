
<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            <a href="{{ url('/posts') }}">{{ __('Posts') }}</a>
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Show posts for user profile') }}
        </p>
    </header>

    @isset($posts)
        <ul>
        @foreach ($posts as $post)
            <li>{{ $post->id }} -> {{ $post->title }}</li>
        @endforeach
        </ul>
    @else
        <p>No posts to show</p>
    @endisset

</section>