@aware(['tableName', 'primaryKey', 'isTailwind', 'isBootstrap'])
@props(['row', 'rowIndex'])

@if ($this->collapsingColumnsAreEnabled && $this->hasCollapsedColumns)
    <tr x-data
        @toggle-row-content.window="($event.detail.tableName === '{{ $tableName }}' && $event.detail.row === {{ $rowIndex }}) ? $el.classList.toggle('hidden') : null"
        wire:key="{{ $tableName }}-row-{{ $row->{$primaryKey} }}-collapsed-contents" class="hidden">
        <td colspan="{{ $this->getColspanCount }}" class="text-left pt-4 pb-2 px-4 bg-default-50/50">
            <div class="space-y-2">
                @foreach ($this->getCollapsedColumnsForContent as $colIndex => $column)
                    <div wire:key="{{ $tableName }}-row-{{ $row->{$primaryKey} }}-collapsed-contents-{{ $colIndex }}"
                        @class([
                            'flex items-center gap-2 text-sm',
                            'sm:flex' => $column->shouldCollapseAlways(),
                            'sm:flex md:hidden' =>
                                !$column->shouldCollapseAlways() &&
                                !$column->shouldCollapseOnTablet() &&
                                $column->shouldCollapseOnMobile(),
                            'sm:flex lg:hidden' =>
                                !$column->shouldCollapseAlways() &&
                                ($column->shouldCollapseOnTablet() ||
                                    $column->shouldCollapseOnMobile()),
                        ])>
                        <strong class="text-default-500 shrink-0">{{ $column->getTitle() }}:</strong>
                        <span class="text-default-700">
                            @if ($column->isHtml())
                                {!! $column->setIndexes($rowIndex, $colIndex)->renderContents($row) !!}
                            @else
                                {{ $column->setIndexes($rowIndex, $colIndex)->renderContents($row) }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </td>
    </tr>
@endif
