
<div class="p-1">
    
    <h3 class="subtitle">Subscription Details</h3>
    <ul>
        {{-- 
            @foreach ($subscription as $key => $value)
                <li>{{ ucfirst($key) }}: {{ $value }}</li>
            @endforeach 
        --}}
    </ul>

    <div class="mt-4">
        <label for="notifications" class="block text-sm font-medium text-gray-700">Email Notifications</label>
        <select name="notifications" id="notifications" class="mt-1 block w-full border border-gray-300 rounded-md shadow-xs p-2">
            <option value="1">Enabled</option>
            <option value="0" selected>Disabled</option>
        </select>
    </div>

</div>