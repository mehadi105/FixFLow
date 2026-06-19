<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 ff-glass px-4 sm:gap-x-6 sm:px-6 lg:px-8">
  <button type="button" id="sidebar-open" class="rounded-xl p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 lg:hidden" aria-label="Open sidebar">
    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
    </svg>
  </button>

  <div class="flex flex-1 items-center justify-between gap-x-4">
    <div class="hidden sm:block">
      <p class="text-sm font-medium text-slate-800">Electronic Device Repair</p>
      <p class="text-xs text-slate-500">Management Dashboard</p>
    </div>

    <div class="flex items-center gap-x-3">
      <details class="relative">
        <summary class="flex cursor-pointer list-none items-center gap-x-3 rounded-xl border border-slate-200/80 bg-white/80 px-2 py-1.5 shadow-sm transition-colors hover:bg-white [&::-webkit-details-marker]:hidden">
          <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-blue-600 text-sm font-semibold text-white shadow-md shadow-indigo-500/25">
            J
          </span>
          <span class="hidden text-left sm:block">
            <span class="block text-sm font-semibold text-slate-900">John Customer</span>
            <span class="block text-xs capitalize text-slate-500">{{ $role ?? 'customer' }}</span>
          </span>
          <svg class="hidden h-4 w-4 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
          </svg>
        </summary>
        <div class="absolute right-0 z-50 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200/80 bg-white py-1 shadow-xl shadow-slate-900/10">
          <a href="#" class="block px-4 py-2.5 text-sm text-slate-700 transition-colors hover:bg-slate-50">Profile</a>
          <a href="#" class="block px-4 py-2.5 text-sm text-slate-700 transition-colors hover:bg-slate-50">Settings</a>
          <hr class="my-1 border-slate-100">
          <a href="#" class="block px-4 py-2.5 text-sm text-red-600 transition-colors hover:bg-red-50">Log out</a>
        </div>
      </details>
    </div>
  </div>
</header>
