<div class="flex items-center gap-3">
    <div class="relative">
        @php
            $avatarColors = match ($user->role) {
                'SuperAdmin' => 'bg-danger/10 text-danger',
                'Administrador' => 'bg-purple-500/10 text-purple-600',
                'Director' => 'bg-primary/10 text-primary',
                'Docente' => 'bg-warning/10 text-warning',
                'Auxiliar' => 'bg-success/10 text-success',
                default => 'bg-default-100 text-default-600',
            };
        @endphp
        <div
            class="size-10 rounded-full {{ $avatarColors }} flex items-center justify-center font-bold text-sm uppercase">
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
