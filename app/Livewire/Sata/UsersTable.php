<?php

namespace App\Livewire\Sata;

use App\Exports\UsersExport;
use App\Models\User;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Livewire\Attributes\On;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;

    // Temporary storage for pending bulk action IDs
    public array $pendingBulkIds = [];

    /**
     * Placeholder skeleton mientras el componente carga (lazy loading).
     */
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
                <div class="h-10 bg-default-50 rounded"></div>
                <div class="h-10 bg-default-100 rounded"></div>
            </div>
        </div>
        HTML;
    }

    #[On('refreshDatatable')]
    public function refreshTable(): void
    {
        // Rappasoft re-renders automatically
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSearchDebounce(400)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setCollapsingColumnsEnabled()
            ->setSearchPlaceholder('Buscar por nombre, email o DNI...')
            ->setEmptyMessage('No se encontraron usuarios.')
            // ─── Bulk Actions ───
            ->setBulkActionsEnabled()
            ->setHideBulkActionsWhenEmptyEnabled()
            ->setBulkActions([
                'exportExcel' => 'Exportar Excel',
                'exportCsv' => 'Exportar CSV',
                'exportPdf' => 'Exportar PDF',
                'bulkActivate' => 'Activar seleccionados',
                'bulkDeactivate' => 'Desactivar seleccionados',
            ])
            // ─── Column Select ───
            ->setColumnSelectEnabled()
            ->setColumnSelectHiddenOnTablet()
            // ─── Query String ───
            ->setQueryStringEnabled()
            ->setQueryStringForColumnSelectDisabled()
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
                'class' => 'transition-colors hover:bg-default-50' . (!$row->is_active ? ' opacity-50' : ''),
            ])
            ->setSearchFieldAttributes([
                'default' => false,
                'class' => 'form-input text-sm',
            ])
            // ─── Bulk Actions Styling ───
            ->setBulkActionsButtonAttributes([
                'class' => 'btn btn-sm bg-default-100 text-default-700 border border-default-200',
            ])
            ->setBulkActionsMenuAttributes([
                'class' => 'bg-white shadow-lg rounded-md border border-default-200 py-1 min-w-[180px]',
            ])
            ->setBulkActionsMenuItemAttributes([
                'class' => 'block w-full text-left px-4 py-2 text-sm text-default-700 hover:bg-default-50',
            ])
            ->setBulkActionsTdCheckboxAttributes([
                'class' => 'size-4 rounded border-default-300 text-primary focus:ring-primary',
            ])
            ->setBulkActionsThCheckboxAttributes([
                'class' => 'size-4 rounded border-default-300 text-primary focus:ring-primary',
            ]);
    }

    public function builder(): Builder
    {
        return User::query()->with('tenant')->orderByDesc('users.created_at');
    }

    public function filters(): array
    {
        $tenants = Tenant::orderBy('nombre')->pluck('nombre', 'id')->toArray();

        return [
            SelectFilter::make('Rol', 'role')
                ->options(['' => 'Todos los Roles'] + array_combine(
                    ['SuperAdmin', 'Administrador', 'Director', 'Docente', 'Auxiliar'],
                    ['SuperAdmin', 'Administrador', 'Director', 'Docente', 'Auxiliar']
                ))
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('role', $value);
                }),

            SelectFilter::make('Estado', 'status')
                ->options(['' => 'Todos', 'active' => 'Activos', 'inactive' => 'Inactivos'])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('is_active', $value === 'active');
                }),

            SelectFilter::make('Institución', 'tenant_id')
                ->options(['' => 'Todas las I.E.'] + $tenants)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('tenant_id', $value);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('Usuario', 'name')
                ->searchable()
                ->sortable()
                ->format(fn($value, $row) => view('livewire.sata.partials.user-cell', ['user' => $row]))
                ->html(),

            Column::make('DNI', 'dni')
                ->searchable()
                ->sortable()
                ->format(fn($value) => '<span class="font-mono text-primary">' . e($value ?? '--------') . '</span>')
                ->html(),

            Column::make('Institución', 'tenant.nombre')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn($value) => '<span class="text-default-600">' . e($value ?? 'UGEL Huacaybamba') . '</span>')
                ->html(),

            Column::make('Cargo', 'cargo')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn($value) => '<span class="text-default-600">' . e($value ?? '—') . '</span>')
                ->html(),

            Column::make('Rol', 'role')
                ->sortable()
                ->format(fn($value) => view('livewire.sata.partials.role-badge', ['role' => $value]))
                ->html(),

            Column::make('Estado', 'is_active')
                ->sortable()
                ->collapseOnTablet()
                ->format(fn($value, $row) => view('livewire.sata.partials.status-toggle', [
                    'user' => $row,
                    'isSelf' => $row->id === auth()->id(),
                ]))
                ->html(),

            Column::make('Último Acceso', 'last_login_at')
                ->sortable()
                ->collapseOnTablet()
                ->format(function ($value) {
                    if (!$value) {
                        return '<span class="text-default-400 italic text-xs">Nunca</span>';
                    }
                    return '<span title="' . $value->format('d/m/Y H:i') . '">' . $value->diffForHumans() . '</span>';
                })
                ->html(),

            Column::make('Acciones', 'id')
                ->format(fn($value, $row) => view('livewire.sata.partials.actions-cell', [
                    'user' => $row,
                    'isSelf' => $row->id === auth()->id(),
                ]))
                ->html()
                ->unclickable()
                ->excludeFromColumnSelect(),
        ];
    }

    // ─── Bulk Action Methods ───

    public function exportExcel()
    {
        $ids = $this->getSelected();
        $this->clearSelected();

        return Excel::download(new UsersExport($ids), 'usuarios-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCsv()
    {
        $ids = $this->getSelected();
        $this->clearSelected();

        return Excel::download(new UsersExport($ids), 'usuarios-' . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf()
    {
        $ids = $this->getSelected();
        $this->clearSelected();

        $query = User::with('tenant')->orderBy('name');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $pdf = Pdf::loadView('exports.users-pdf', ['users' => $query->get()])
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'usuarios-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function bulkActivate()
    {
        $ids = $this->getSelected();
        if (empty($ids))
            return;

        $this->pendingBulkIds = $ids;
        $this->dispatch(
            'confirmBulkAction',
            action: 'doActivate',
            title: '¿Activar usuarios?',
            text: count($ids) . ' usuario(s) serán activados.',
            icon: 'question',
            confirmText: 'Sí, activar',
            confirmColor: '#22c55e',
        );
    }

    public function bulkDeactivate()
    {
        $ids = $this->getSelected();
        if (empty($ids))
            return;

        $this->pendingBulkIds = $ids;
        $this->dispatch(
            'confirmBulkAction',
            action: 'doDeactivate',
            title: '¿Desactivar usuarios?',
            text: count($ids) . ' usuario(s) serán desactivados.',
            icon: 'warning',
            confirmText: 'Sí, desactivar',
            confirmColor: '#ef4444',
        );
    }

    #[On('executeBulkAction')]
    public function executeBulkAction(string $action): void
    {
        if (empty($this->pendingBulkIds))
            return;

        $ids = $this->pendingBulkIds;
        $this->pendingBulkIds = [];
        $this->clearSelected();

        if ($action === 'doActivate') {
            User::whereIn('id', $ids)
                ->where('id', '!=', auth()->id())
                ->update(['is_active' => true]);

            $this->dispatch('refreshDatatable');
            $this->dispatch('swal', icon: 'success', title: count($ids) . ' usuario(s) activado(s).');
        }

        if ($action === 'doDeactivate') {
            User::whereIn('id', $ids)
                ->where('id', '!=', auth()->id())
                ->update(['is_active' => false]);

            $this->dispatch('refreshDatatable');
            $this->dispatch('swal', icon: 'success', title: count($ids) . ' usuario(s) desactivado(s).');
        }
    }
}
