<x-app-layout :role="$role ?? 'admin'">
    <x-page-header title="Users" description="Manage customers, technicians, and administrators" />

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="ff-stats-grid mb-6">
        <x-stat-card title="All Users" :value="$counts['all']" />
        <x-stat-card title="Customers" :value="$counts['customer']" />
        <x-stat-card title="Technicians" :value="$counts['technician']" />
        <x-stat-card title="Admins" :value="$counts['admin']" />
    </div>

    <x-dashboard-card>
        <form method="GET" action="{{ route('users.index') }}" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="ff-field sm:w-56">
                <label for="role" class="sr-only">Filter by role</label>
                <select id="role" name="role" class="ff-input" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="customer" @selected($roleFilter === 'customer')>Customers</option>
                    <option value="technician" @selected($roleFilter === 'technician')>Technicians</option>
                    <option value="admin" @selected($roleFilter === 'admin')>Admins</option>
                </select>
            </div>
        </form>

        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Requests</th>
                        <th>Joined</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="cell-strong">
                                {{ $user->name }}
                                @if ($user->id === auth()->id())
                                    <span class="ml-1 text-xs font-normal text-slate-400">(you)</span>
                                @endif
                            </td>
                            <td class="cell-muted">{{ $user->email }}</td>
                            <td class="cell-muted">{{ $user->repair_requests_count }}</td>
                            <td class="cell-muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if ($user->id === auth()->id())
                                    <x-status-badge :status="$user->role" />
                                @else
                                    <form method="POST" action="{{ route('users.update-role', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="ff-input !w-auto !py-1.5 text-sm" onchange="this.form.submit()">
                                            <option value="customer" @selected($user->role === 'customer')>Customer</option>
                                            <option value="technician" @selected($user->role === 'technician')>Technician</option>
                                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="mt-6">{{ $users->links() }}</div>
        @endif
    </x-dashboard-card>
</x-app-layout>
