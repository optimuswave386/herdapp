@props(['userId', 'user', 'friends', 'photos', 'posts', 'categories', 'username', 'roles', 'subscription', 'followers', 'isFollowing'])

@php
// dd($followers);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- {{ __('User') }} --}}
            {{ $username[0]->name ?? 'Profile' }}
            {{ $roles ?? ""}}
        </h2>

        <div x-data="{ status, followers: {{ $followers }}, following: {{ $isFollowing ? 'true' : 'false' }} }">
            <a href="{{ url('/@' . $username[0]->name . '/followers') }}"><span x-text="followers"></span> Followers</a>
            <br />

            <!-- Display follow/unfollow button only if not viewing own profile -->
            @if (auth()->id() !== $userId)
            <button 
                @click="
                    following = !following;
                    following ? followers++ : followers--;
                    // Call Laravel backend here (e.g., via fetch/axios)
                    fetch('/toggle-follow/{{ $userId }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ follow: following })
                    }).then(response => response.json())
                      .then(data => {
                          status = data.status;
                        });
                "
                x-text="following ? 'Unfollow' : 'Follow'"
                class="px-4 py-2 text-white bg-blue-500 rounded-sm"
            >
            </button>
            @endif
            
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-lg">



<div class="mb-4 border-b border-default">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-base" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
        </li>
        <li role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand" id="friends-tab" data-tabs-target="#friends" type="button" role="tab" aria-controls="friends" aria-selected="false">Friends</button>
        </li>
                <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand" id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
        </li>
    </ul>
</div>
<div id="default-tab-content">
    <div class="hidden p-4 rounded-base bg-neutral-secondary-soft" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <p class="text-sm text-body"></p>
        @include('user.partials.status')
        @include('user.partials.photos')
    </div>
    <div class="hidden p-4 rounded-base bg-neutral-secondary-soft" id="friends" role="tabpanel" aria-labelledby="friends-tab">
        <p class="text-sm text-body"></p>
        @include('user.partials.friends')
    </div>
        <div class="hidden p-4 rounded-base bg-neutral-secondary-soft" id="settings" role="tabpanel" aria-labelledby="settings-tab">
        <p class="text-sm text-body"></p>
        @include('user.partials.settings')
    </div>
</div>


                </div>               
            </div>

            
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
            
                <div class="max-w-xl">
                    @include('posts.partials.show-posts-for-user-profile')
                </div>
                
                @if ($user->hasRole('admin-role'))                
                <div class="mt-4">
                    <a href="{{ route('posts.index') }}" class="text-blue-500 underline">Go to Posts</a>
                </div>
                @endif
            
            </div>
        

        </div>
    </div>

</x-app-layout>