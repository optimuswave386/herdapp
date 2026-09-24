
<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    
                    <div class="mt-6 space-y-6">
                    
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">
                                {{ __('Title') }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                {{ $post->title }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">
                                {{ __('Content') }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                {{ $post->content }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">
                                {{ __('Excerpt') }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                {{ $post->excerpt }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">
                                {{ __('Author') }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                {{ $post->author }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">
                                {{ __('Image') }}
                            </h3>
                            <div class="mt-1">
                            @if($post->hasMedia('images'))
                                <img src="{{ $post->getFirstMediaUrl('images', 'thumb') }}" alt="{{ $post->title }}" class="max-w-full h-auto">
                            @else
                                <p class="text-gray-600">No image available.</p>
                            @endif
                            </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>