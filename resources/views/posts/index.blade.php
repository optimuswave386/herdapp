@php use Illuminate\Support\Str; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-xl">

                        <x-primary-button onClick="window.location.href='{{ route('posts.create') }}'">
                            {{ __('Create Post') }}
                        </x-primary-button>

                        <x-primary-button onClick="window.location.href='{{ route('posts.index') }}'">
                            {{ __('All Posts') }}
                        </x-primary-button>

                    <br />

                    <div class="flex flex-row gap-1 mt-1">

                        {{-- Filter Posts by User:
                        <select id="filterSelect" onchange="location = this.value;">
                            <option value="">{{ __('Select User') }}</option>
                            @php
                                $users = \App\Models\User::all();
                            @endphp
                            @foreach ($users as $user)
                            <option value="{{ route('posts.showByUser', ['userId' => $user->id]) }}">
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select> --}}

                        {{-- <x-dropdown>
                            <x-slot name="trigger">
                                <x-primary-button>
                                    {{ __('Filter Posts by User') }}
                                </x-primary-button>
                            </x-slot>
                            <x-slot name="content">
                                @php
                                    $users = \App\Models\User::all();
                                @endphp
                                @foreach ($users as $user)
                                    <x-dropdown-link href="{{ route('posts.showByUser', ['userId' => $user->id]) }}">
                                        {{ $user->name }}
                                    </x-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-dropdown> --}}

                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-primary-button>
                                    {{ __('Filter Posts by Category') }}
                                </x-primary-button>
                            </x-slot>
                            <x-slot name="content">
                                @php
                                    $categories = \App\Models\Category::all();
                                @endphp
                                @foreach ($categories as $category)
                                    <x-dropdown-link href="{{ route('posts.showByCategory', ['categoryId' => $category->id]) }}">
                                        {{ $category->name }}
                                    </x-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-dropdown>

                    </div>

                    <br />
                    
                    @isset($posts)
                    <ul class="mb-5">
                        @foreach ($posts as $post)
                            <li id="{{ $post->id }}" class="mb-1 flex items-center justify-between">
                                {{ Str::limit($post->title, 20, '...') }} 
                                {{ Str::words($post->excerpt, 3, '...') }} 
                                <span class="flex gap-2">
                                <a href="{{ route('posts.edit', ['id' => $post->id]) }}">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="w-4 h-4" alt="Logo">
                                </a> 
                                <a href="{{ route('posts.delete', ['id' => $post->id]) }}">
                                    <img src="{{ asset('images/file-x.svg') }}" class="w-4 h-4" alt="Logo">
                                </a>
                                <a href="{{ route('posts.view', ['id' => $post->id]) }}">
                                    <img src="{{ asset('images/file-post.svg') }}" class="w-4 h-4" alt="Logo">
                                </a>
                                </span>
                            </li>
                        @endforeach
                    </ul>

                        <!-- Change to true to enable pagination -->
                        @if (true === true) 
                            {{ $posts->links() }} <!-- Pagination links -->
                        @endif

                    @else
                        <p>No posts to show</p>
                    @endisset
                    
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
