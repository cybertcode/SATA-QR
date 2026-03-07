<div class="flex items-center gap-3">
    <div class="relative">
        <div
            class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm uppercase">
            {{ substr($user->name, 0, 1) }}{{ substr(explode(' ', $user->name)[1] ?? '', 0, 1) }}
        </div>
        <span
            class="absolute size-2.5 rounded-full border-2 border-white end-0 bottom-0 {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"></span>
    </div>
    <div>
        <h6 class="text-default-800 font-medium text-sm mb-0.5">{{ $user->name }}</h6>
        <p class="text-[11px] text-default-500">{{ $user->email }}</p>
    </div>
</div>
