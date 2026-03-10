@aware(['tableName', 'isTailwind', 'isBootstrap'])
@props(['rowIndex', 'hidden' => false])

@if ($this->collapsingColumnsAreEnabled && $this->hasCollapsedColumns)
    <td x-data="{ open: false }" wire:key="{{ $tableName }}-collapsingIcon-{{ $rowIndex }}-{{ md5(now()) }}"
        @class([
            'px-2 py-2.5 table-cell text-center',
            'sm:hidden' =>
                !$this->shouldCollapseAlways() && !$this->shouldCollapseOnTablet(),
            'md:hidden' =>
                !$this->shouldCollapseAlways() &&
                !$this->shouldCollapseOnTablet() &&
                $this->shouldCollapseOnMobile(),
            'lg:hidden' =>
                !$this->shouldCollapseAlways() &&
                ($this->shouldCollapseOnTablet() || $this->shouldCollapseOnMobile()),
        ])>
        @if (!$hidden)
            <button
                x-on:click.prevent="$dispatch('toggle-row-content', {'tableName': '{{ $tableName }}', 'row': {{ $rowIndex }}}); open = !open"
                class="p-0 border-0 bg-transparent focus:outline-none">
                <svg x-cloak x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="size-5 text-success">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 12h8" />
                    <path d="M12 8v8" />
                </svg>
                <svg x-cloak x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="size-5 text-warning">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 12h8" />
                </svg>
            </button>
        @endif
    </td>
@endif
