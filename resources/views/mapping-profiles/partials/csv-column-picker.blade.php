@push('styles')
<style>
    #csv-import-preview {
        min-width: 0;
        max-width: 100%;
    }
    .csv-import-preview-frame {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        background-color: #fff;
        overflow: hidden;
        max-width: 100%;
    }
    .csv-import-preview-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        max-width: 100%;
    }
    #csv-import-preview-table {
        font-size: 0.7rem;
        width: max-content;
        max-width: none;
        margin-bottom: 0;
    }
    #csv-import-preview-table th,
    #csv-import-preview-table td {
        font-size: 0.7rem;
        white-space: nowrap;
        vertical-align: middle;
    }
    #csv-import-preview-table th:first-child,
    #csv-import-preview-table td:first-child {
        position: sticky;
        left: 0;
        z-index: 1;
        background-color: #f8f9fa;
    }
    #csv-import-preview-table tr.table-primary th:first-child {
        background-color: #cfe2ff;
    }
    #csv-import-preview-table tr.table-primary th.csv-column-drag {
        cursor: grab;
        user-select: none;
    }
    #csv-import-preview-table tr.table-primary th.csv-column-drag:active {
        cursor: grabbing;
    }
    .csv-column-chip {
        cursor: grab;
        user-select: none;
    }
    .csv-column-chip:active {
        cursor: grabbing;
    }
    .mapping-drop-target.drag-over {
        outline: 2px dashed #0d6efd;
        outline-offset: 2px;
        background-color: #e7f1ff;
    }
    .csv-column-samples {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.35rem;
        line-height: 1.35;
    }
    .csv-column-samples div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

<div class="alert alert-info">
    <div class="row align-items-end">
        <div class="col-md-8">
            <label for="sample_csv" class="form-label mb-1">Test CSV (not saved)</label>
            <input type="file" class="form-control" id="sample_csv" accept=".csv,.txt">
            <div class="form-text">Upload a sample file, then drag column names onto the mapping fields. Parsing happens on the server; the file is not saved as a profile.</div>
        </div>
        <div class="col-md-4" id="csv-column-status"></div>
    </div>
    <div id="csv-import-preview" class="mt-3 d-none">
        <div class="form-label mb-1">Import preview (10 rows after skipped lines)</div>
        <div class="csv-import-preview-frame">
            <div class="csv-import-preview-scroll">
                <table class="table table-sm table-bordered mb-0" id="csv-import-preview-table"></table>
            </div>
        </div>
        <div class="form-label mt-3 mb-1">Drag columns to mapping fields</div>
        <div id="csv-column-chips" class="d-flex flex-wrap gap-1"></div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const previewUrl = @json(route('mapping-profiles.preview-csv'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const mappingFieldIds = [
        'transaction_date',
        'amount',
        'type',
        'transaction_title',
        'description',
        'counterparty',
        'location',
        'reference_id',
        'card_number',
    ];

    const fileInput = document.getElementById('sample_csv');
    const skipRowsInput = document.getElementById('skip_rows');
    const statusEl = document.getElementById('csv-column-status');
    const previewWrap = document.getElementById('csv-import-preview');
    const previewTable = document.getElementById('csv-import-preview-table');
    const chipsEl = document.getElementById('csv-column-chips');

    let lastFocusedMapping = null;
    let columnSamples = {};
    let skipTimer = null;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function normalize(value) {
        return (value || '').toLowerCase().replace(/[^a-z0-9]+/g, '');
    }

    function guessHeader(fieldId, headers) {
        const aliases = {
            transaction_title: ['title', 'transactiontitle', 'name', 'kozlemeny', 'közlemény'],
            description: ['description', 'desc', 'details', 'megjegyzes', 'megjegyzés'],
            counterparty: ['counterparty', 'partner', 'payee', 'name', 'partnernev', 'partnernév'],
            location: ['location', 'source', 'sourceinformation', 'hely', 'forras'],
            transaction_date: ['date', 'transactiondate', 'valuedate', 'konyveles', 'könyvelés', 'datum', 'dátum'],
            amount: ['amount', 'osszeg', 'összeg', 'value'],
            type: ['type', 'debitcredit', 'irany', 'irány'],
            reference_id: ['reference', 'referenceid', 'ref', 'id', 'azonosito', 'azonosító'],
            card_number: ['card', 'cardnumber', 'kartya', 'kártya'],
        };

        const wanted = [normalize(fieldId)].concat(aliases[fieldId] || []);

        return headers.find((header) => {
            const headerNorm = normalize(header);
            return wanted.some((alias) => headerNorm.includes(alias) || alias.includes(headerNorm));
        }) || '';
    }

    function ensureSampleBox(input) {
        let box = input.nextElementSibling;
        if (!box || !box.classList.contains('csv-column-samples')) {
            box = document.createElement('div');
            box.className = 'csv-column-samples';
            input.insertAdjacentElement('afterend', box);
        }
        return box;
    }

    function showColumnSamples(input, columnName) {
        const box = ensureSampleBox(input);
        const values = columnSamples[columnName] || [];

        if (!columnName || !values.length) {
            box.innerHTML = '';
            return;
        }

        box.innerHTML = values.map((value) => {
            const text = value || '—';
            return '<div title="' + escapeHtml(text) + '">' + escapeHtml(text) + '</div>';
        }).join('');
    }

    function bindColumnDrag(element, columnName) {
        element.draggable = true;
        element.addEventListener('dragstart', function (event) {
            event.dataTransfer.setData('text/plain', columnName);
            event.dataTransfer.effectAllowed = 'copy';
        });
    }

    function bindDropTarget(input) {
        if (!input || input.dataset.dropBound === '1') {
            return;
        }

        input.dataset.dropBound = '1';
        input.classList.add('mapping-drop-target');

        input.addEventListener('focus', function () {
            lastFocusedMapping = input;
        });

        input.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
            input.classList.add('drag-over');
        });

        input.addEventListener('dragleave', function () {
            input.classList.remove('drag-over');
        });

        input.addEventListener('drop', function (event) {
            event.preventDefault();
            input.classList.remove('drag-over');
            const columnName = event.dataTransfer.getData('text/plain');
            if (columnName) {
                input.value = columnName;
                showColumnSamples(input, columnName);
            }
        });
    }

    function applyHeaders(headers) {
        mappingFieldIds.forEach((id) => {
            const field = document.getElementById(id);
            if (!field) {
                return;
            }

            bindDropTarget(field);

            if (!headers.length) {
                return;
            }

            if (!field.value || !headers.includes(field.value)) {
                const guessed = guessHeader(id, headers);
                if (guessed) {
                    field.value = guessed;
                }
            }

            if (field.value) {
                showColumnSamples(field, field.value);
            }
        });
    }

    function renderChips(headers) {
        chipsEl.innerHTML = '';
        headers.forEach((header) => {
            const chip = document.createElement('span');
            chip.className = 'badge text-bg-primary csv-column-chip';
            chip.textContent = header;
            chip.title = 'Drag onto a mapping field';
            bindColumnDrag(chip, header);
            chip.addEventListener('click', function () {
                if (lastFocusedMapping) {
                    lastFocusedMapping.value = header;
                    showColumnSamples(lastFocusedMapping, header);
                    lastFocusedMapping.focus();
                }
            });
            chipsEl.appendChild(chip);
        });
    }

    function renderPreview(preview) {
        if (!preview.length) {
            previewTable.innerHTML = '';
            previewWrap.classList.remove('d-none');
            return;
        }

        const body = preview.map((row) => {
            const tag = row.is_header ? 'th' : 'td';
            const rowClass = row.is_header ? 'table-primary' : '';
            const cellsHtml = row.cells.map((cell) => {
                if (row.is_header) {
                    return '<' + tag + ' class="csv-column-drag">' + escapeHtml(cell) + '</' + tag + '>';
                }
                return '<' + tag + '>' + escapeHtml(cell) + '</' + tag + '>';
            }).join('');

            return '<tr class="' + rowClass + '"><th class="text-muted">' + row.line + '</th>' + cellsHtml + '</tr>';
        }).join('');

        previewTable.innerHTML = '<tbody>' + body + '</tbody>';
        previewTable.querySelectorAll('.csv-column-drag').forEach((cell) => {
            bindColumnDrag(cell, cell.textContent);
        });
        previewWrap.classList.remove('d-none');
    }

    function parseCsv() {
        const file = fileInput?.files?.[0];
        if (!file) {
            return;
        }

        const formData = new FormData();
        formData.append('csv_file', file);
        formData.append('skip_rows', skipRowsInput?.value || '0');

        statusEl.innerHTML = '<span class="text-muted">Parsing CSV…</span>';

        fetch(previewUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok) {
                    const message = data.message || Object.values(data.errors || {}).flat()[0] || 'Could not parse CSV.';
                    throw new Error(message);
                }
                return data;
            })
            .then((data) => {
                columnSamples = data.column_samples || {};
                const headers = data.headers || [];
                applyHeaders(headers);
                renderPreview(data.preview || []);
                renderChips(headers);

                if (!headers.length) {
                    statusEl.innerHTML = '<span class="text-danger">No header row found. Check Skip Rows.</span>';
                    return;
                }

                statusEl.innerHTML = '<span class="text-success">' + headers.length + ' columns loaded — drag them onto the fields</span>';
            })
            .catch((error) => {
                statusEl.innerHTML = '<span class="text-danger">' + escapeHtml(error.message) + '</span>';
            });
    }

    mappingFieldIds.forEach((id) => bindDropTarget(document.getElementById(id)));

    fileInput?.addEventListener('change', parseCsv);

    skipRowsInput?.addEventListener('input', function () {
        if (!fileInput?.files?.length) {
            return;
        }

        clearTimeout(skipTimer);
        skipTimer = setTimeout(parseCsv, 250);
    });
})();
</script>
@endpush
