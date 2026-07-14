<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور سفارش #{{ $order->code ?? $order->unique_code }}</title>
    @vite(['Modules/Core/resources/assets/css/tailwind.css'])
    <style>
        body {
            font-family: 'IRANSansFaNum', Tahoma, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0.5cm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased bg-gray-50 print:bg-white p-4 sm:p-8 print:p-0 print:text-[11px]">

    <div class="max-w-5xl mx-auto print:max-w-none">
        <!-- Content included below -->
        @include('Order::invoice.partials.content')
    </div>

    <script>
    window.addEventListener('load', function() {
        var source = document.getElementById('invoice-source');
        var pagesContainer = document.getElementById('invoice-pages');
        var infoBlock = document.getElementById('order-info-block');
        var table = document.getElementById('items-table');
        if (!infoBlock || !table || !source || !pagesContainer) return;

        var thead = table.querySelector('thead');
        var tfoot = table.querySelector('tfoot');
        var tbody = table.querySelector('tbody');
        if (!tbody) return;

        var rows = Array.from(tbody.children);
        
        // Calculate dynamic page breaks using a "weight" heuristic instead of DOM heights
        // A standard page fits exactly 8 basic rows. So MAX_WEIGHT = 8.5
        var MAX_WEIGHT_PER_PAGE = 9.5;
        
        var pageGroups = [];
        var currentGroup = [];
        var currentWeight = 0;
        
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var weight = 1.0; // Base weight for 1 standard row
            
            // Calculate weight based on actual content complexity
            if (row.classList.contains('bg-gray-50/30')) {
                // Fabric summary row at the bottom (slightly shorter)
                weight = 0.8;
            } else {
                // Main product row: Count extra lines (fabrics, frame color)
                var extraLines = row.querySelectorAll('.flex.items-center').length;
                weight += (extraLines * 0.4); // Each extra line adds ~0.4 of a standard row
                
                // Check product title length for text wrapping
                var titleEl = row.querySelector('.font-bold');
                if (titleEl) {
                    var len = titleEl.innerText.trim().length;
                    if (len > 45) weight += 0.4; // 2nd line
                    if (len > 90) weight += 0.4; // 3rd line
                    if (len > 135) weight += 0.4; // 4th line
                }
            }
            
            // If this row pushes us over the limit, cut the page here
            if (currentWeight + weight > MAX_WEIGHT_PER_PAGE && currentGroup.length > 0) {
                pageGroups.push(currentGroup);
                currentGroup = [];
                currentWeight = 0;
            }
            
            currentGroup.push(i);
            currentWeight += weight;
        }
        
        if (currentGroup.length > 0) {
            pageGroups.push(currentGroup);
        }

        // If it all fits on one page, don't paginate
        if (pageGroups.length <= 1) return;

        var tableOuterWrapper = table.parentElement.parentElement;
        var tableInnerWrapper = table.parentElement;

        for (var p = 0; p < pageGroups.length; p++) {
            var pageDiv = document.createElement('div');
            if (p < pageGroups.length - 1) {
                pageDiv.style.pageBreakAfter = 'always';
            }

            // Clone order info header
            var ic = infoBlock.cloneNode(true);
            ic.removeAttribute('id');
            pageDiv.appendChild(ic);

            // Build table with this chunk of rows
            var ow = tableOuterWrapper.cloneNode(false);
            var iw = tableInnerWrapper.cloneNode(false);
            var nt = document.createElement('table');
            nt.className = table.className;

            if (thead) nt.appendChild(thead.cloneNode(true));

            var nb = document.createElement('tbody');
            nb.className = tbody.className;
            for (var r = 0; r < pageGroups[p].length; r++) {
                nb.appendChild(rows[pageGroups[p][r]].cloneNode(true));
            }
            nt.appendChild(nb);

            // tfoot only on last page
            if (p === pageGroups.length - 1 && tfoot) {
                nt.appendChild(tfoot.cloneNode(true));
            }

            iw.appendChild(nt);
            ow.appendChild(iw);
            pageDiv.appendChild(ow);
            pagesContainer.appendChild(pageDiv);
        }

        source.style.display = 'none';
        pagesContainer.style.display = 'block';
    });
    </script>
    @livewireScripts
</body>
</html>
