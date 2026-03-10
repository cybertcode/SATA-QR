<?php

namespace App\Livewire\Sata;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On;

class PermissionsTable extends DataTableComponent
{
    protected $model = Permission::class;

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="animate-pulse space-y-3 py-4">
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
        // Shared event — refreshes when roles table refreshes
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSearchDebounce(400)
            ->setPerPageAccepted([10, 25, 50])
            ->setPerPage(25)
            ->setSearchPlaceholder('Buscar permiso...')
            ->setEmptyMessage('No se encontraron permisos.')
            ->setColumnSelectDisabled()
            ->setQueryStringForSearchDisabled()
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
        return Permission::query()
            ->withCount('roles')
            ->where('guard_name', 'web')
            ->orderBy('name');
    }

    public function columns(): array
    {
        return [
            Column::make('Permiso', 'name')
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    $parts = explode('.', $value);
                    $module = $parts[0] ?? $value;
                    $action = $parts[1] ?? '';
                    return '<span class="font-mono text-xs">'
                        . '<span class="text-primary font-semibold">' . e($module) . '</span>'
                        . ($action ? '<span class="text-default-400">.</span><span class="text-default-600">' . e($action) . '</span>' : '')
                        . '</span>';
                })
                ->html(),

            Column::make('Roles asignados', 'guard_name')
                ->label(fn($row) => '<span class="inline-flex items-center justify-center size-7 rounded-full bg-primary/10 text-primary text-xs font-bold">' . $row->roles_count . '</span>')
                ->html(),

            Column::make('Acciones', 'id')
                ->label(function ($row) {
                    if ($row->roles_count > 0) {
                        return '<span class="text-default-400 text-xs italic">En uso</span>';
                    }
                    return view('livewire.sata.partials.permission-actions-cell', [
                        'permission' => $row,
                    ]);
                })
                ->html()
                ->unclickable(),
        ];
    }
}
