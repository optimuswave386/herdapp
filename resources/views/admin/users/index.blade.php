@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')
@section('subheading', number_format($users->total()).' '.Str::plural('account', $users->total()))

@section('admin')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead class="border-b border-default bg-neutral-secondary-soft text-xs uppercase tracking-wider text-body-subtle">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-start font-semibold">User</th>
                        <th scope="col" class="px-5 py-3 text-start font-semibold">Role</th>
                        <th scope="col" class="px-5 py-3 text-end font-semibold">Followers</th>
                        <th scope="col" class="px-5 py-3 text-end font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-neutral-secondary-soft">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-soft text-sm font-bold uppercase text-fg-brand-strong">{{ mb_substr($user->name, 0, 1) }}</span>
                                    <div class="min-w-0">
                                        <a href="{{ url('/@'.$user->name) }}" class="block truncate font-medium text-heading hover:text-fg-brand">{{ $user->name }}</a>
                                        <span class="block truncate text-xs text-body-subtle">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if ($user->is_admin)
                                    <span class="rounded-full bg-brand-soft px-2.5 py-0.5 text-xs font-semibold text-fg-brand-strong">Admin</span>
                                @else
                                    <span class="rounded-full bg-neutral-tertiary px-2.5 py-0.5 text-xs font-medium text-body">Member</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-end tabular-nums">{{ $user->followers_count }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-end text-body-subtle">{{ $user->created_at?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-body-subtle">No users yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
