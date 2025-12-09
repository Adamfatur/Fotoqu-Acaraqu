<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Frame - {{ $session->session_code }}</title>
    @php
        // Paper options: 100x148mm (Postcard/A6 width x height) or 4x6in
        $paper = request('paper', '100x148mm');
        if (!in_array($paper, ['100x148mm', '4x6in'])) {
            $paper = '100x148mm';
        }
        if ($paper === '100x148mm') {
            $PAGE_W = '100mm';
            $PAGE_H = '148mm';
            $LABEL = '100×148 mm (Postcard)';
            // 300 DPI physical pixels (for fallbacks/docs)
            $PX_W = 1181;
            $PX_H = 1748;
        } else { // 4x6in
            $PAGE_W = '4in';
            $PAGE_H = '6in';
            $LABEL = '4×6 in';
            $PX_W = 1200;
            $PX_H = 1800;
        }
        $autoPrint = request()->boolean('autoprint');
        // Vertical nudge to remove tiny top white gap (mm, negative moves image up)
        // Default to -0.5mm on 100×148 mm to compensate typical top band
        $defaultNudge = ($paper === '100x148mm') ? -0.5 : 0;
        $offsetY = (float) str_replace(',', '.', request('offsetY', $defaultNudge));
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-container {
            max-width: 100%;
            text-align: center;
        }

        :root {
            --offsetY:
                {{ $offsetY }}
                mm;
        }

        .frame-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Apply live preview nudge as well */
            transform: translateY(var(--offsetY));
        }

        .print-info {
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .print-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #8b5cf6;
            color: white;
        }

        .btn-print:hover {
            background: #7c3aed;
        }

        .btn-close {
            background: #6b7280;
            color: white;
        }

        .btn-close:hover {
            background: #4b5563;
        }

        .btn-outline {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-outline.active {
            background: #eef2ff;
            border-color: #6366f1;
            color: #3730a3;
        }

        @media print {

            .print-info,
            .print-buttons,
            #print-instructions {
                display: none !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white;
                overflow: hidden;
            }

            .print-container {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-inside: avoid;
            }

            .frame-image {
                /* Exact paper size (dynamic) */
                width:
                    {{ $PAGE_W }}
                    !important;
                height:
                    {{ $PAGE_H }}
                    !important;
                max-width:
                    {{ $PAGE_W }}
                    !important;
                max-height:
                    {{ $PAGE_H }}
                    !important;
                min-width:
                    {{ $PAGE_W }}
                    !important;
                min-height:
                    {{ $PAGE_H }}
                    !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                object-fit: cover !important;
                /* Full bleed - cover entire paper */
                object-position: center !important;
                display: block !important;

                /* Fallback untuk browser yang tidak support physical units */
                width:
                    {{ $PX_W }}
                    px !important;
                height:
                    {{ $PX_H }}
                    px !important;
                /* Apply print nudge to remove tiny top white gap */
                transform: translateY(var(--offsetY)) !important;
            }

            /* Strict page settings for borderless */
            @page {
                size:
                    {{ $PAGE_W }}
                    {{ $PAGE_H }}
                ;
                /* Exact target */
                margin: 0 !important;
                /* Full bleed - no margins */
                padding: 0 !important;
                border: none;
            }

            /* Ensure full page coverage */
            html,
            body {
                width:
                    {{ $PAGE_W }}
                    !important;
                height:
                    {{ $PAGE_H }}
                    !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }

            /* Hide any potential scrollbars */
            ::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <div class="print-info">
            <h2>Frame Siap Print - {{ $session->session_code }}</h2>
            <p><strong>📏 Ukuran: {{ $LABEL }} • Target Full Bleed • 300 DPI</strong></p>
            <p>Customer: {{ $session->customer_name }}</p>
            <div style="margin-top: 10px; padding: 10px; background: #f0f9ff; border-radius: 5px; font-size: 14px;">
                💡 <strong>Petunjuk Printer:</strong><br>
                • Paper: {{ $LABEL }}<br>
                • Margins: None (0) • Scale: 100% / Actual Size<br>
                • Mode: Borderless / Full Bleed (jika tersedia di printer)<br>
                • Quality: High/Fine
            </div>
            <div style="margin-top:10px; display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                <a class="btn btn-outline {{ $paper === '100x148mm' ? 'active' : '' }}"
                    href="?paper=100x148mm{{ $autoPrint ? '&autoprint=1' : '' }}">100×148 mm</a>
                <a class="btn btn-outline {{ $paper === '4x6in' ? 'active' : '' }}"
                    href="?paper=4x6in{{ $autoPrint ? '&autoprint=1' : '' }}">4×6 in</a>
            </div>
            <div
                style="margin-top:10px; display:flex; gap:8px; align-items:center; justify-content:center; flex-wrap:wrap;">
                <span style="font-size:13px; color:#374151">Nudge Vertikal (mm):</span>
                <div style="display:flex; gap:6px;">
                    <button class="btn btn-outline" type="button" onclick="adjustOffset(-1)">↑ 1.0</button>
                    <button class="btn btn-outline" type="button" onclick="adjustOffset(-0.5)">↑ 0.5</button>
                    <button class="btn btn-outline" type="button" onclick="setOffset(0)">Reset</button>
                    <button class="btn btn-outline" type="button" onclick="adjustOffset(0.5)">↓ 0.5</button>
                    <button class="btn btn-outline" type="button" onclick="adjustOffset(1)">↓ 1.0</button>
                </div>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input id="offsetYInput" type="number" step="0.1" min="-3" max="3" value="{{ $offsetY }}"
                        style="width:80px; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <button class="btn btn-outline" type="button" onclick="applyOffsetFromInput()">Terapkan</button>
                </div>
            </div>
        </div>

        <img src="{{ route('photobox.serve-frame', ['frame' => $session->frame->id]) }}"
            alt="Frame {{ $session->session_code }}" class="frame-image">

        <div class="print-buttons">
            {{-- Buttons are primarily for testing if opened directly --}}
            <button onclick="window.print()" class="btn btn-print">🖨️ Print Frame</button>
        </div>
    </div>

    <script>
        // Optional auto-print mode for kiosk (Chrome --kiosk-printing)
        const urlParams = new URLSearchParams(location.search);
        const auto = urlParams.get('autoprint') === '1';
        function getOffsetY() { return parseFloat(urlParams.get('offsetY') || '{{ $offsetY }}') || 0; }
        function setCssOffset(val) {
            document.documentElement.style.setProperty('--offsetY', (val || 0) + 'mm');
            const input = document.getElementById('offsetYInput');
            if (input) input.value = (Math.round((val || 0) * 10) / 10).toString();
        }
        function updateUrlParam(name, value) {
            const qp = new URLSearchParams(location.search);
            if (value === null || value === undefined) {
                qp.delete(name);
            } else {
                qp.set(name, value);
            }
            history.replaceState(null, '', '?' + qp.toString());
        }
        function setOffset(val) {
            setCssOffset(val);
            updateUrlParam('offsetY', String(val));
        }
        function adjustOffset(delta) { setOffset((getOffsetY() || 0) + delta); }
        function applyOffsetFromInput() {
            const input = document.getElementById('offsetYInput');
            const v = parseFloat(input.value || '0') || 0;
            setOffset(v);
        }
        window.addEventListener('load', function () {
            // Initialize CSS var from URL on load
            setCssOffset(getOffsetY());
            if (auto) {
                // Wait for image to settle
                setTimeout(() => { window.print(); }, 500);
            }
        });

        // Handle after print
        window.addEventListener('afterprint', function () {
            console.log('Print dialog closed');
        });

        function printWithDialog() {
            // Brief tip overlay
            const tip = document.createElement('div');
            tip.id = 'print-instructions';
            tip.innerHTML = `
                <div style="position: fixed; top: 10px; left: 10px; background: #333; color: white; padding: 10px; border-radius: 5px; z-index: 9999; font-size: 12px; max-width: 320px;">
                    🖨️ <strong>Pastikan di dialog print:</strong><br>
                    • Paper: {{ $LABEL }} • Margins: None<br>
                    • Scale: 100% (Actual Size) • Borderless ON
                </div>
            `;
            document.body.appendChild(tip);
            setTimeout(() => { tip.remove(); window.print(); }, 1200);
        }

        function kioskAutoPrint() {
            // In normal browser this still shows dialog; in Chrome with --kiosk-printing it prints silently
            const qp = new URLSearchParams(location.search);
            qp.set('autoprint', '1');
            // Preserve current offsetY value
            const currentOffset = (document.getElementById('offsetYInput')?.value) || '{{ $offsetY }}';
            if (currentOffset !== undefined && currentOffset !== null) {
                qp.set('offsetY', String(currentOffset));
            }
            location.search = qp.toString();
        }
    </script>
</body>

</html>