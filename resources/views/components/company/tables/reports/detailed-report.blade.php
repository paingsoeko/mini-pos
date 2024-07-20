<table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
    <thead class="divide-y divide-gray-200 dark:divide-white/5">
        <tr class="bg-gray-50 dark:bg-white/5">
            @foreach($report->getHeaders() as $index => $header)
                <th wire:key="header-{{ $index }}" class="px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 {{ $report->getAlignmentClass($index) }}">
                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $header }}
                    </span>
                </th>
            @endforeach
        </tr>
    </thead>
    @foreach($report->getCategories() as $categoryIndex => $category)
        <tbody wire:key="category-{{ $categoryIndex }}" class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
            <tr class="bg-gray-50 dark:bg-white/5">
                @foreach($category->header as $headerIndex => $header)
                    <x-filament-tables::cell wire:key="category-{{ $categoryIndex }}-header-{{ $headerIndex }}" class="{{ $report->getAlignmentClass($headerIndex) }}">
                        <div class="px-3 py-2 text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $header }}
                        </div>
                    </x-filament-tables::cell>
                @endforeach
            </tr>
            @foreach($category->data as $dataIndex => $account)
                <tr wire:key="category-{{ $categoryIndex }}-data-{{ $dataIndex }}">
                    @foreach($account as $cellIndex => $cell)
                        <x-filament-tables::cell wire:key="category-{{ $categoryIndex }}-data-{{ $dataIndex }}-cell-{{ $cellIndex }}" class="{{ $report->getAlignmentClass($cellIndex) }}">
                            <div class="px-3 py-4 text-sm leading-6 text-gray-950 dark:text-white">
                                {{ $cell }}
                            </div>
                        </x-filament-tables::cell>
                    @endforeach
                </tr>
            @endforeach
            <tr wire:key="category-{{ $categoryIndex }}-summary">
                @foreach($category->summary as $summaryIndex => $cell)
                    <x-filament-tables::cell wire:key="category-{{ $categoryIndex }}-summary-{{ $summaryIndex }}" class="{{ $report->getAlignmentClass($summaryIndex) }}">
                        <div class="px-3 py-2 text-sm leading-6 font-semibold text-gray-950 dark:text-white">
                            {{ $cell }}
                        </div>
                    </x-filament-tables::cell>
                @endforeach
            </tr>
            <tr wire:key="category-{{ $categoryIndex }}-spacer">
                <x-filament-tables::cell colspan="{{ count($report->getHeaders()) }}">
                    <div class="px-3 py-2 leading-6 invisible">Hidden Text</div>
                </x-filament-tables::cell>
            </tr>
        </tbody>
    @endforeach
    <tfoot>
        <tr class="bg-gray-50 dark:bg-white/5">
            @foreach($report->getOverallTotals() as $index => $total)
                <x-filament-tables::cell wire:key="footer-total-{{ $index }}" class="{{ $report->getAlignmentClass($index) }}">
                    <div class="px-3 py-2 text-sm leading-6 font-semibold text-gray-950 dark:text-white">
                        {{ $total }}
                    </div>
                </x-filament-tables::cell>
            @endforeach
        </tr>
    </tfoot>
</table>
