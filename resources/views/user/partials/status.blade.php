@props([ 'recentActivities' => [] ])

<section>

    <h2 class="text-lg font-medium text-gray-900">
        {{ __('Status') }}
    </h2>
    <label for="message" class="block mb-2.5 text-sm font-medium text-heading">{{ __('Update status') }}</label>
    <textarea id="message" rows="4" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-2xs placeholder:text-body" placeholder="Write your thoughts here..."></textarea>
    
    <br />

    <h2 class="font-medium text-lg mt-1 text-gray-900">Recent Activities</h2>
    <ul class="list list-inside">
    @if(isset($recentActivities) && count($recentActivities) > 0)
        @foreach($recentActivities as $activity)
            <li>{{ $activity->description }} - <span class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</span></li>
        @endforeach
    @else
        <li><span class="text-sm text-gray-500">No recent activities found.</span></li>
    @endif
    </ul>

    <br />

</section>