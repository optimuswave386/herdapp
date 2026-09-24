@props(['categories' => []])

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
        {{ __('Create Post') }}
    </h2>
    
    <form method="post" action="{{ route('posts.create') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
    @csrf
    
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="" required autofocus autocomplete="title" />
            <x-input-error class="mt-2" messages="" />
        </div>
        <div>
            <x-input-label for="excerpt" :value="__('Excerpt')" />
            <x-text-input id="excerpt" name="excerpt" type="text" class="mt-1 block w-full" value="" required autofocus autocomplete="excerpt" />
            <x-input-error class="mt-2" messages="" />
        </div>
        <div>
            <x-input-label for="content" :value="__('Content')" />
            <x-text-area id="content" name="content" class="mt-1 block w-full" required autofocus autocomplete="content"></x-text-area>
            <x-input-error class="mt-2" messages="" />
        </div>

        <div>
            <x-input-label for="author" :value="__('Author')" />
            <x-text-input id="author" name="author" type="text" class="mt-1 block w-full" value="{{ auth()->user()->name }}" required autofocus autocomplete="author" />
            <x-input-error class="mt-2" messages="" />
        </div>

        <div>
            <x-input-label for="image" :value="__('Imagepath')" />
            <x-text-input id="image" name="image" type="file" class="mt-1 block w-full" value="" accept="image/jpeg, image/jpg, image/svg+xml, image/png" required autofocus autocomplete="image" />
            <x-input-error class="mt-2" messages="" />
        </div>

        <div>
            <x-input-label for="slug" :value="__('Slug')" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ md5(uniqid()) }}" required autofocus autocomplete="slug" readonly />
            <x-input-error class="mt-2" messages="" />
        </div>

        <div class="mt-1">
            <x-input-label for="category_id" :value="__('Category ID')" />
            <x-text-input id="category_id" name="category_id" type="number" class="mt-1 block w-full" value="" required autofocus autocomplete="category_id" />
            <x-input-error class="mt-2" messages="" />
        </div>

        <div>
            <label for="category_options">Category:</label>
            <select name="category_options" id="category_options" onChange="document.getElementById('category_id').value = this.value;" class="mt-1 block w-full">
                <option value="">-- Select a Category --</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="user_id" :value="__('User ID')" />
            <x-text-input id="user_id" name="user_id" type="number" class="mt-1 block w-full" value="{{ auth()->user()->id }}" required autofocus autocomplete="user_id" />
            <x-input-error class="mt-2" messages="" />
        </div>

        {{-- <button type="submit">Submit</button> --}}
        <x-primary-button class="mt-4">{{ __('Submit Post') }}</x-primary-button>

    </form>

                </div>
            </div>
        </div>
    </div>


</x-app-layout>