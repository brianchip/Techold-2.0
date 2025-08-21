<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOQ Preview - {{ $boq_reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d5aa0;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-header {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #e9ecef;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        .items-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        .summary-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        .summary-table .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
        }
        .terms {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .terms-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .terms-grid {
            display: table;
            width: 100%;
        }
        .terms-row {
            display: table-row;
        }
        .terms-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .terms-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">TECHOLD ENGINEERING</div>
        <div style="font-size: 12px; color: #666;">Professional Engineering Solutions</div>
        <div class="document-title">BILL OF QUANTITIES (BOQ)</div>
        <div style="font-size: 14px; color: #666;">{{ $boq_reference }}</div>
    </div>

    <!-- Project & BOQ Information -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Project:</div>
            <div class="info-value">{{ $project->project_name }} ({{ $project->project_code }})</div>
        </div>
        <div class="info-row">
            <div class="info-label">Client:</div>
            <div class="info-value">{{ $project->client->company_name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">BOQ Reference:</div>
            <div class="info-value">{{ $boq_reference }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Version:</div>
            <div class="info-value">{{ $boq_version }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Prepared By:</div>
            <div class="info-value">{{ $prepared_by->full_name ?? 'N/A' }} ({{ $prepared_by->position ?? 'N/A' }})</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date:</div>
            <div class="info-value">{{ $preparation_date ? \Carbon\Carbon::parse($preparation_date)->format('F j, Y') : \Carbon\Carbon::now()->format('F j, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Currency:</div>
            <div class="info-value">{{ $currency }}</div>
        </div>
        @if($valid_until)
        <div class="info-row">
            <div class="info-label">Valid Until:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($valid_until)->format('F j, Y') }}</div>
        </div>
        @endif
    </div>

    <!-- BOQ Sections and Items -->
    @foreach($sections as $sectionIndex => $section)
    <div class="section">
        <div class="section-header">
            {{ $sectionIndex + 1 }}. {{ $section['name'] }}
            @if(!empty($section['code']))
                ({{ $section['code'] }})
            @endif
        </div>
        
        @if(!empty($section['description']))
        <div style="padding: 10px; font-style: italic; background-color: #f8f9fa; border: 1px solid #ddd; border-top: none;">
            {{ $section['description'] }}
        </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Item</th>
                    <th style="width: 12%;">Code</th>
                    <th style="width: 35%;">Description</th>
                    <th style="width: 8%;">Unit</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 12%;">Rate ({{ $currency }})</th>
                    <th style="width: 15%;">Total ({{ $currency }})</th>
                </tr>
            </thead>
            <tbody>
                @php $sectionTotal = 0; @endphp
                @foreach($section['items'] as $itemIndex => $item)
                @php 
                    $itemTotal = ($item['quantity'] ?? 0) * ($item['rate'] ?? 0);
                    $sectionTotal += $itemTotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $itemIndex + 1 }}</td>
                    <td>{{ $item['code'] }}</td>
                    <td>
                        {{ $item['description'] }}
                        @if(!empty($item['notes']))
                            <br><small style="color: #666; font-style: italic;">{{ $item['notes'] }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['unit'] }}</td>
                    <td class="text-right">{{ number_format($item['quantity'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item['rate'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($itemTotal, 2) }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td colspan="6" class="text-right">Section Total:</td>
                    <td class="text-right">{{ number_format($sectionTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach

    <!-- Summary -->
    <div class="summary">
        <table class="summary-table">
            <tr>
                <td>Number of Sections:</td>
                <td class="text-right">{{ $sections_count }}</td>
            </tr>
            <tr>
                <td>Number of Items:</td>
                <td class="text-right">{{ $items_count }}</td>
            </tr>
            <tr>
                <td>Average Item Cost:</td>
                <td class="text-right">{{ $currency }} {{ $items_count > 0 ? number_format($total_amount / $items_count, 2) : '0.00' }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL AMOUNT:</td>
                <td class="text-right">{{ $currency }} {{ number_format($total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Terms and Conditions -->
    @if($payment_terms || $delivery_timeline || $warranty_period || $special_conditions)
    <div class="page-break"></div>
    <div class="terms">
        <div class="terms-title">TERMS AND CONDITIONS</div>
        
        <div class="terms-grid">
            @if($payment_terms)
            <div class="terms-row">
                <div class="terms-label">Payment Terms:</div>
                <div class="terms-value">{{ ucwords(str_replace('_', ' ', $payment_terms)) }}</div>
            </div>
            @endif
            
            @if($delivery_timeline)
            <div class="terms-row">
                <div class="terms-label">Delivery Timeline:</div>
                <div class="terms-value">{{ $delivery_timeline }}</div>
            </div>
            @endif
            
            @if($warranty_period)
            <div class="terms-row">
                <div class="terms-label">Warranty Period:</div>
                <div class="terms-value">{{ $warranty_period }}</div>
            </div>
            @endif
        </div>
        
        @if($special_conditions)
        <div style="margin-top: 15px;">
            <div class="terms-label">Special Conditions:</div>
            <div style="margin-top: 5px; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd;">
                {{ $special_conditions }}
            </div>
        </div>
        @endif
        
        <div style="margin-top: 20px; font-size: 11px;">
            <div><strong>Additional Notes:</strong></div>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @if($include_taxes)
                <li>All prices include applicable taxes and duties.</li>
                @else
                <li>All prices are exclusive of applicable taxes and duties.</li>
                @endif
                
                @if($subject_to_approval)
                <li>This quotation is subject to management approval.</li>
                @endif
                
                <li>This BOQ is valid until {{ $valid_until ? \Carbon\Carbon::parse($valid_until)->format('F j, Y') : 'further notice' }}.</li>
                <li>Prices are subject to change without prior notice for items not yet ordered.</li>
                <li>Any variations to the scope of work will be charged separately.</li>
            </ul>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</div>
        <div style="margin-top: 5px;">
            TECHOLD ENGINEERING - Professional Engineering Solutions<br>
            This document was generated electronically and is valid without signature.
        </div>
    </div>
</body>
</html>
