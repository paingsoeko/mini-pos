<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $report->getTitle() }}</title>
    <style>
        @page {
            size: A4;
            margin: 8.5mm 8.5mm 30mm 8.5mm;
        }

        .header {
            color: #374151;
            margin-bottom: 1rem;
        }

        .table-head {
            display: table-row-group;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .table-class th,
        .table-class td {
            color: #374151;
        }

        .header .title,
        .header .company-name,
        .header .date-range {
            margin-bottom: 0.125rem; /* Uniform space between header elements */
        }

        .title {
            font-size: 1.5rem;
        }

        .company-name {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .date-range {
            font-size: 0.875rem;
        }

        .table-class {
            width: 100%;
            border-collapse: collapse;
        }

        .table-class th,
        .table-class td {
            padding: 0.75rem;
            font-size: 0.75rem;
            line-height: 1rem;
            border-bottom: 1px solid #d1d5db; /* Gray border for all rows */
        }

        .category-header-row > td {
            background-color: #f3f4f6; /* Gray background for category names */
            font-weight: 600;
        }

        .table-body tr {
            background-color: #ffffff; /* White background for other rows */
        }

        .spacer-row > td {
            height: 0.75rem;
        }

        .category-summary-row > td,
        .table-footer-row > td {
            font-weight: 600;
            background-color: #ffffff; /* White background for footer */
        }
    </style>
</head>
<body>
<div class="header">
    <div class="title">{{ $report->getTitle() }}</div>
    <div class="company-name">{{ $company->name }}</div>
    <div class="date-range">Date Range: {{ $startDate }} to {{ $endDate }}</div>
</div>
<table class="table-class">
    <thead class="table-head">
    <tr>
        @foreach($report->getHeaders() as $index => $header)
            <th class="{{ $report->getAlignmentClass($index) }}">
                {{ $header }}
            </th>
        @endforeach
    </tr>
    </thead>
    @foreach($report->getCategories() as $category)
        <tbody>
        <tr class="category-header-row">
            @foreach($category->header as $index => $header)
                <td class="{{ $report->getAlignmentClass($index) }}">
                    {{ $header }}
                </td>
            @endforeach
        </tr>
        @foreach($category->data as $account)
            <tr>
                @foreach($account as $index => $cell)
                    <td class="{{ $report->getAlignmentClass($index) }}">
                        {{ $cell }}
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr class="category-summary-row">
            @foreach($category->summary as $index => $cell)
                <td class="{{ $report->getAlignmentClass($index) }}">
                    {{ $cell }}
                </td>
            @endforeach
        </tr>
        <tr class="spacer-row">
            <td colspan="{{ count($report->getHeaders()) }}"></td>
        </tr>
        </tbody>
    @endforeach
    <tfoot>
    <tr class="table-footer-row">
        @foreach ($report->getOverallTotals() as $index => $total)
            <td class="{{ $report->getAlignmentClass($index) }}">
                {{ $total }}
            </td>
        @endforeach
    </tr>
    </tfoot>
</table>
</body>
</html>
