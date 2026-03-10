<?php

namespace App\Livewire\Sata;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;

class RolesTable extends DataTableComponent
{
    protected $model = Role::class;

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="animate-pulse space-y-3 py-4">
            <div class="flex items-center gap-3 px-4">
                <div class="h-8 w-64 bg-default-200 rounded"></div>
                <div class="h-8 w-32 bg-default-200 rounded ms-auto"></div>
            </div>
            <div class="space-y-2 px-4">
                <div class="h-10 bg-default-100 rounded"></div>
                <div class="h-10 bg-default-50 rounded"></div>
                <div class="h-10 bg-default-100 rounded"></div>
            </div>
        </div>
        HTML;
    }

    #[On('refreshRolesTable')]
    public function refreshTable(): void
    {
        // Rappasoft re-renders automatically
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSearchDebounce(400)
            ->setPerPageAccepted([10, 25, 50])
            ->setPerPage(10)
            ->setSearchPlaceholder('Buscar rol...')
            ->setEmptyMessage('No se encontraron roles.')
            ->setColumnSelectDisabled()
            ->setQueryStringEnabled()
            // ─── Styling ───
            ->setTableWrapperAttributes([
                'default' => false,
                'class' => 'overflow-x-auto',
            ])
            ->setTableAttributes([
                'default' => false,
                'class' => 'min-w-full divide-y divide-default-200',
            ])
            ->setTheadAttributes([
                'default' => false,
                'class' => 'bg-default-100/50',
            ])
            ->setTbodyAttributes([
                'default' => false,
                'class' => 'divide-y divide-default-100',
            ])
            ->setThAttributes(fn(Column $column) => [
                'default' => false,
                'class' => 'px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider',
            ])
            ->setThSortButtonAttributes(fn(Column $column) => [
                'default' => false,
                'class' => 'flex items-center gap-1 text-left text-xs font-medium text-default-500 uppercase tracking-wider group focus:outline-none',
            ])
            ->setTdAttributes(fn(Column $column, $row, int $colIndex, int $rowIndex) => [
                'default' => false,
                'class' => 'px-3.5 py-2.5 whitespace-nowrap text-sm text-default-700',
            ])
            ->setTrAttributes(fn($row, int $rowIndex) => [
                'default' => false,
                'class' => 'transition-colors hover:bg-default-50',
            ])
            ->setSearchFieldAttributes([
                'default' => false,
                'class' => 'form-input text-sm',
            ]);
    }

    public function builder(): Builder
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->where('guard_name', 'web')
            ->orderBy('name');
    }

    public function columns(): array
    {
        $protectedRoles = UserRole::values();

        return [
            Column::make('Rol', 'name')
                ->searchable()
                ->sortable()
                ->format(function ($value) use ($protectedRoles) {
                    $isProtected = in_array($value, $protectedRoles);
                    $badge = $isProtected
                        ? '<span class="inline-flex items-center gap-1 text-xs text-default-400 ms-1.5"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Protegido</span>'
                        : '';
                    return '<span class="font-semibold text-default-800">' . e($value) . '</span>' . $badge;
                })
                ->html(),

            Column::make('Permisos', 'id')
                ->label(fn($row) => '<span class="inline-flex items-center justify-center size-7 rounded-full bg-primary/10 text-primary text-xs font-bold">' . $row->permissions_count . '</span>')
                ->html(),

            Column::make('Usuarios', 'guard_name')
                ->label(fn($row) => '<span class="inline-flex items-center justify-center size-7 rounded-full bg-success/10 text-success text-xs font-bold">' . $row->users_count . '</span>')
                ->html(),

            Column::make('Creado', 'created_at')
                ->sortable()
                ->format(fn($value) => '<span class="text-default-500 text-xs">' . $value->format('d/m/Y') . '</span>')
                ->html(),

            Column::make('Acciones', 'updated_at')
                ->label(fn($row) => view('livewire.sata.partials.role-actions-cell', [
                    'role' => $row,
                    'isProtected' => in_array($row->name, UserRole::values()),
                ]))
                ->html()
                ->unclickable(),
        ];
    }
}
