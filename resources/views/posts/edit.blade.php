<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-xl">

    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edit Post') }}
    </h2>
    
    <form method="post" action="{{ route('posts.update', ['id' => $post->id]) }}" class="mt-6 space-y-6">
    @csrf
    
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title)" required autofocus autocomplete="title" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div>
            <x-input-label for="excerpt" :value="__('Excerpt')" />
            <x-text-input id="excerpt" name="excerpt" type="text" class="mt-1 block w-full" :value="old('excerpt', $post->excerpt)" required autofocus autocomplete="excerpt" />
            <x-input-error class="mt-2" :messages="$errors->get('excerpt')" />
        </div>

        <div>
            <x-input-label for="content" :value="__('Content')" />
            <x-text-area id="content" name="content" class="mt-1 block w-full" required>{{ old('content', $post->content) }}</x-text-area>
            <x-input-error class="mt-2" :messages="$errors->get('content')" />
        </div>

        <div>
            <x-input-label for="author" :value="__('Author')" />
            <x-text-input id="author" name="author" type="text" class="mt-1 block w-full" :value="old('author', $post->author)" required autofocus autocomplete="author" />
            <x-input-error class="mt-2" :messages="$errors->get('author')" />
        </div>
    
        {{-- <button type="submit">Update</button> --}}
        <x-primary-button class="mt-4">{{ __('Update Post') }}</x-primary-button>

    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>