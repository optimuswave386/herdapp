@props(['user', 'followers'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ 'Followers' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-lg">

                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Followers of {{ $user->name }}
                    </h3>

                    <ul>
                        @foreach ($followers as $follower)
                            @php
                                $followerUser = \App\Models\User::find($follower->follower_id);
                            @endphp
                            @if ($followerUser)
                                <li class="mb-2">
                                    <a href="{{ route('user.index', ['username' => $followerUser->name]) }}" class="text-blue-500 hover:underline">
                                        {{ $followerUser->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>
