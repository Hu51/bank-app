@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="mb-0">Transactions</h2>
                            @if($transactions->total() > 0)
                                <span class="badge bg-light text-dark fw-normal">{{ number_format($transactions->total()) }} total</span>
                            @endif
                            <span class="small text-muted d-none d-md-inline">Select rows → Mass Actions</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="mass-actions-btn" disabled title="Select rows below first">
                                <i class="fas fa-edit me-1"></i> Mass Actions
                            </button>
                            <a id="recategorize-btn" class="btn btn-outline-primary" href="javascript:void(0)" data-url="{{ route('transactions.recategorize') }}" title="Re-run category rules on uncategorized">
                                <i class="fas fa-sync-alt me-1"></i> Recategorize
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Mass Actions Form -->
                        <form id="mass-actions-form" action="{{ route('transactions.mass-update') }}" method="POST" class="mb-4 d-none border border-primary border-opacity-25 rounded p-3 bg-primary bg-opacity-10">
                            @csrf
                            <input type="hidden" name="transaction_ids" id="mass_transaction_ids">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="mass_category" class="form-label small">Category</label>
                                    <select name="category_id" id="mass_category" class="form-select form-select-sm">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mass_comment" class="form-label small">Comment</label>
                                    <input type="text" name="comment" id="mass_comment" class="form-control form-control-sm" placeholder="Add a comment">
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cancel-mass-actions">Cancel</button>
                                </div>
                            </div>
                        </form>

                        <!-- Filters -->
                        <div class="rounded border bg-light bg-opacity-50 p-3 mb-4">
                            <form method="GET" action="{{ route('transactions.index') }}">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-lg-auto">
                                        <label class="form-label small text-muted mb-1">Date</label>
                                        <div class="input-group input-group-sm">
                                            <input type="date" name="start_date" class="form-control"
                                                   value="{{ $startDate }}"
                                                   min="{{ $dateRange->min_date }}"
                                                   max="{{ $dateRange->max_date }}"
                                                   aria-label="Start date">
                                            <span class="input-group-text">–</span>
                                            <input type="date" name="end_date" class="form-control"
                                                   value="{{ $endDate }}"
                                                   min="{{ $dateRange->min_date }}"
                                                   max="{{ $dateRange->max_date }}"
                                                   aria-label="End date">
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <span class="quick-date badge bg-primary pointer" data-range="this-month">This Month</span>
                                            <span class="quick-date badge bg-primary pointer" data-range="last-month">Last Month</span>
                                            <span class="quick-date badge bg-primary pointer" data-range="last-3-months">Last 3 Months</span>
                                            <span class="quick-date badge bg-primary pointer" data-range="this-year">This Year</span>
                                            <span class="quick-date badge bg-primary pointer" data-range="last-year">Last Year</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg-auto">
                                        <label class="form-label small text-muted mb-1">Category</label>
                                        <select name="category" class="form-select form-select-sm">
                                            <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>All</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-lg">
                                        <label class="form-label small text-muted mb-1">Search</label>
                                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}">
                                    </div>
                                    <div class="col-6 col-lg-auto">
                                        <label class="form-label small text-muted mb-1">Amount</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="min_amount" class="form-control" placeholder="Min" value="{{ $minAmount ?? '' }}" aria-label="Min">
                                            <span class="input-group-text">–</span>
                                            <input type="number" name="max_amount" class="form-control" placeholder="Max" value="{{ $maxAmount ?? '' }}" aria-label="Max">
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg-auto d-flex gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Filter</button>
                                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <nav aria-label="Page navigation" class="w-100">
                                {{ $transactions->links('pagination::bootstrap-5') }}
                            </nav>
                        </div>

                        @if($transactions->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                <p class="mb-1">No transactions match your filters.</p>
                                <p class="small mb-0"><a href="{{ route('transactions.import') }}">Import transactions</a> or <a href="{{ route('transactions.index') }}">clear filters</a>.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 2.5rem;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all" aria-label="Select all">
                                            </div>
                                        </th>
                                        <th style="width: 2.5rem;" class="text-nowrap"><span class="visually-hidden">View</span></th>
                                        <th>Date</th>
                                        <th>Title</th>
                                        <th>Counterparty</th>
                                        <th class="d-none d-lg-table-cell">Description</th>
                                        <th class="text-end">Amount</th>
                                        <th class="d-none d-xl-table-cell">Card</th>
                                        <th>Category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input transaction-checkbox" type="checkbox"
                                                           name="transaction_ids[]" value="{{ $transaction->id }}" aria-label="Select row">
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-secondary view-transaction py-0 px-1"
                                                    data-bs-toggle="modal" data-bs-target="#transactionModal"
                                                    data-transaction-id="{{ $transaction->id }}"
                                                    aria-label="View details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                            <td class="text-nowrap">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td><span class="text-truncate d-inline-block" style="max-width: 12rem;" title="{{ $transaction->transaction_title }}">{{ $transaction->transaction_title }}</span></td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 10rem;" title="{{ $transaction->counterparty }}">
                                                    {{ $transaction->counterparty }}
                                                </span>
                                                @if($transaction->comment)
                                                    <small class="text-muted d-block">{{ Str::limit($transaction->comment, 20) }}</small>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell small text-muted">{{ Str::limit($transaction->description ?? '-', 25) }}</td>
                                            <td class="text-end fw-medium {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                               {{ number_format($transaction->amount, 2) }}
                                            </td>                                         
                                            <td class="d-none d-xl-table-cell small">{{ $transaction->card_number }}</td>
                                            <td>
                                                @if($transaction->category)
                                                    <span class="d-flex align-items-center">
                                                        @if($transaction->category->icon)
                                                            <i class="{{ $transaction->category->icon }} me-1 small"></i>
                                                        @endif
                                                        <span style="color: {{ $transaction->category->color ?? '#000000' }}">{{ $transaction->category->name }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                            <nav aria-label="Page navigation" class="w-100">
                                {{ $transactions->links('pagination::bootstrap-5') }}
                            </nav>
                        </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header align-items-start py-3 px-4 bg-light border-0">
                    <div class="d-flex flex-grow-1 min-w-0 me-3">
                        <div class="min-w-0 flex-grow-1">
                            <p class="mb-1 small text-muted lh-1">
                                <span id="modal-transaction-date"></span>
                                <span class="mx-1">·</span>
                                <span id="modal-transaction-type" class="badge align-text-bottom"></span>
                            </p>
                            <h5 class="modal-title mb-0 text-truncate fs-6 fw-semibold" id="transactionModalLabel">Transaction Details</h5>
                        </div>
                        <div class="text-end flex-shrink-0 ms-3">
                            <span id="modal-transaction-amount" class="fs-4 fw-bold lh-1"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close flex-shrink-0 mt-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="d-none" id="modal-transaction-title"></div>

                    <section class="mb-4">
                        <h6 class="text-uppercase small fw-semibold text-muted mb-3">Details</h6>
                        <dl class="row mb-0 g-2 small">
                            <dt class="col-sm-3 text-muted">Description</dt>
                            <dd class="col-sm-9 mb-0" id="modal-transaction-description">—</dd>
                            <dt class="col-sm-3 text-muted">Counterparty</dt>
                            <dd class="col-sm-9 mb-0" id="modal-transaction-counterparty">—</dd>
                            <dt class="col-sm-3 text-muted">Source</dt>
                            <dd class="col-sm-9 mb-0" id="modal-transaction-source">—</dd>
                            <dt class="col-sm-3 text-muted">Reference</dt>
                            <dd class="col-sm-9 mb-0" id="modal-transaction-reference">—</dd>
                            <dt class="col-sm-3 text-muted">Card</dt>
                            <dd class="col-sm-9 mb-0" id="modal-transaction-card-number">—</dd>
                        </dl>
                    </section>

                    <section class="mb-4">
                        <h6 class="text-uppercase small fw-semibold text-muted mb-2">Category</h6>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span id="modal-transaction-category-display" class="d-flex align-items-center">
                                <i id="modal-transaction-category-icon" class="me-2"></i>
                                <span id="modal-transaction-category-name" class="fw-medium"></span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="change-category-btn">Change</button>
                        </div>
                        <div id="category-select-container" class="mt-2 d-none">
                            <select id="category-select" class="form-select form-select-sm">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-icon="{{ $category->icon }}" data-color="{{ $category->color }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" id="save-category-btn">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-category-btn">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <section class="mb-4">
                        <h6 class="text-uppercase small fw-semibold text-muted mb-2">Comment</h6>
                        <div class="d-flex align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div id="comment-display">
                                    <span id="modal-transaction-comment" class="text-muted fst-italic">No comment</span>
                                </div>
                                <div id="comment-edit" class="d-none">
                                    <textarea id="comment-input" class="form-control form-control-sm" rows="2" placeholder="Add a comment…"></textarea>
                                    <div class="mt-2 d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="save-comment-btn">Save</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-comment-btn">Cancel</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0" id="edit-comment-btn">Edit</button>
                        </div>
                    </section>

                    <section class="mb-0">
                        <h6 class="text-uppercase small fw-semibold text-muted mb-2">Metadata</h6>
                        <pre id="modal-transaction-metadata" class="bg-dark text-light rounded p-3 small mb-0" style="max-height: 160px; overflow: auto; white-space: pre-wrap; font-size: 0.7rem;"></pre>
                    </section>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const transactionModal = document.getElementById('transactionModal');
            const changeCategoryBtn = document.getElementById('change-category-btn');
            const categorySelectContainer = document.getElementById('category-select-container');
            const categorySelect = document.getElementById('category-select');
            const saveCategoryBtn = document.getElementById('save-category-btn');
            const cancelCategoryBtn = document.getElementById('cancel-category-btn');

            const recategorizeBtn = document.getElementById('recategorize-btn');
            if (recategorizeBtn) {
                recategorizeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = recategorizeBtn.getAttribute('data-url') || '/transactions/recategorize';
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (response) {
                            if (!response.ok) throw new Error('Request failed: ' + response.status);
                            return response.json();
                        })
                        .then(function (data) {
                            if (data.success) {
                                alert(data.message);
                                window.location.reload();
                            } else {
                                alert('Failed to recategorize transactions');
                            }
                        })
                        .catch(function (err) {
                            console.error('Recategorize error:', err);
                            alert('Recategorize failed. Check the console or try again.');
                        });
                });
            }

            let currentTransactionId = null;
            let currentCategoryId = null;

            if (transactionModal) {
                transactionModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    currentTransactionId = button.getAttribute('data-transaction-id');

                    // Fetch transaction details via API
                    fetch(`/api/transactions/${currentTransactionId}`)
                        .then(response => response.json())
                        .then(transaction => {
                            currentCategoryId = transaction.category_id;
                            const transactionDate = new Date(transaction.transaction_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            const transactionTitle = transaction.transaction_title;
                            const transactionCounterparty = transaction.counterparty;
                            const transactionCategory = transaction.category?.name;
                            const transactionCategoryIcon = transaction.category?.icon;
                            const transactionCategoryColor = transaction.category?.color;
                            const transactionType = transaction.type;
                            const transactionAmount = transaction.amount;
                            const transactionDescription = transaction.description;
                            const transactionSource = transaction.source;
                            const transactionReference = transaction.reference_id;
                            const transactionMetadata = transaction.metadata;
                            const transactionCardNumber = transaction.card_number;
                            const transactionComment = transaction.comment;

                            document.getElementById('transactionModalLabel').textContent = transactionTitle;

                            // Update transaction details
                            document.getElementById('modal-transaction-date').textContent = transactionDate;
                            document.getElementById('modal-transaction-title').textContent = transactionTitle;
                            document.getElementById('modal-transaction-counterparty').textContent = transactionCounterparty;

                            // Update category
                            document.getElementById('modal-transaction-category-name').textContent = transactionCategory;
                            document.getElementById('modal-transaction-category-name').style.color = transactionCategoryColor || '#000000';

                            // Update category icon
                            const iconElement = document.getElementById('modal-transaction-category-icon');
                            if (transactionCategoryIcon) {
                                iconElement.className = transactionCategoryIcon + ' me-2';
                                iconElement.style.display = 'inline-block';
                            } else {
                                iconElement.style.display = 'none';
                            }

                            // Set the current category in the select dropdown
                            if (currentCategoryId) {
                                categorySelect.value = currentCategoryId;
                            } else {
                                categorySelect.value = '';
                            }

                            // Update transaction type
                            const typeElement = document.getElementById('modal-transaction-type');
                            typeElement.textContent = transactionType.charAt(0).toUpperCase() + transactionType.slice(1);
                            typeElement.className = 'badge ' + (transactionType === 'income' ? 'bg-success' : 'bg-danger');

                            // Update transaction amount
                            const amountElement = document.getElementById('modal-transaction-amount');
                            amountElement.textContent = transactionAmount;
                            amountElement.className = 'fs-4 fw-bold lh-1 ' + (transactionType === 'income' ? 'text-success' : 'text-danger');

                            // Update transaction description
                            document.getElementById('modal-transaction-description').textContent = transactionDescription;

                            // Update transaction source and reference
                            document.getElementById('modal-transaction-source').textContent = transactionSource;
                            document.getElementById('modal-transaction-reference').textContent = transactionReference;

                            // Update transaction metadata
                            document.getElementById('modal-transaction-metadata').textContent = JSON.stringify(transactionMetadata);

                            // Update card number
                            document.getElementById('modal-transaction-card-number').textContent = 
                                transactionCardNumber || 'N/A';

                            // Update comment
                            document.getElementById('modal-transaction-comment').textContent = 
                                transactionComment || 'N/A';

                            // Hide the category select container
                            categorySelectContainer.classList.add('d-none');
                        })
                        .catch(error => console.error('Error fetching transaction:', error));


                });
            }

            // Change category button click handler
            if (changeCategoryBtn) {
                changeCategoryBtn.addEventListener('click', function () {
                    categorySelectContainer.classList.remove('d-none');
                });
            }

            // Cancel category change button click handler
            if (cancelCategoryBtn) {
                cancelCategoryBtn.addEventListener('click', function () {
                    categorySelectContainer.classList.add('d-none');
                });
            }

            // Save category button click handler
            if (saveCategoryBtn) {
                saveCategoryBtn.addEventListener('click', function () {
                    const categoryId = categorySelect.value;

                    if (!categoryId) {
                        alert('Please select a category');
                        return;
                    }

                    // Get the selected option
                    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                    const categoryIcon = selectedOption.getAttribute('data-icon');
                    const categoryColor = selectedOption.getAttribute('data-color');
                    const categoryName = selectedOption.textContent;

                    // Update the category display
                    document.getElementById('modal-transaction-category-name').textContent = categoryName;
                    document.getElementById('modal-transaction-category-name').style.color = categoryColor || '#000000';

                    // Update the category icon
                    const iconElement = document.getElementById('modal-transaction-category-icon');
                    if (categoryIcon) {
                        iconElement.className = categoryIcon + ' me-2';
                        iconElement.style.display = 'inline-block';
                    } else {
                        iconElement.style.display = 'none';
                    }

                    // Hide the category select container
                    categorySelectContainer.classList.add('d-none');

                    // Send the update to the server
                    fetch(`/transactions/${currentTransactionId}/category`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            category_id: categoryId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the current category ID
                                currentCategoryId = categoryId;

                                // Show success message
                                const successAlert = document.createElement('div');
                                successAlert.className = 'alert alert-success alert-dismissible fade show mt-3';
                                successAlert.innerHTML = `
                                        ${data.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    `;
                                document.querySelector('.modal-body').appendChild(successAlert);

                                // Remove the alert after 3 seconds
                                setTimeout(() => {
                                    successAlert.remove();
                                }, 3000);
                            } else {
                                alert('Failed to update category');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the category');
                        });
                });
            }

            // Date range validation
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    if (endDateInput.value && startDateInput.value > endDateInput.value) {
                        endDateInput.value = startDateInput.value;
                    }
                });

                endDateInput.addEventListener('change', function() {
                    if (startDateInput.value && endDateInput.value < startDateInput.value) {
                        startDateInput.value = endDateInput.value;
                    }
                });

                // Quick date range selection
                document.querySelectorAll('.quick-date').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const range = this.dataset.range;
                        let start, end;

                        switch(range) {
                            case 'this-month':
                                start = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
                                end = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0);
                                break;
                            case 'last-month':
                                start = new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1);
                                end = new Date(new Date().getFullYear(), new Date().getMonth(), 0);
                                break;
                            case 'last-3-months':
                                start = new Date(new Date().getFullYear(), new Date().getMonth() - 3, 1);
                                end = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0);
                                break;
                            case 'this-year':
                                start = new Date(new Date().getFullYear(), 0, 1);
                                end = new Date(new Date().getFullYear(), 11, 31);
                                break;
                            case 'last-year':
                                start = new Date(new Date().getFullYear() - 1, 0, 1);
                                end = new Date(new Date().getFullYear() - 1, 11, 31);
                                break;
                        }

                        startDateInput.value = start.toISOString().split('T')[0];
                        endDateInput.value = end.toISOString().split('T')[0];
                    });
                });
            }

            // Edit comment button click handler
            const editCommentBtn = document.getElementById('edit-comment-btn');
            const commentEdit = document.getElementById('comment-edit');
            const commentDisplay = document.getElementById('comment-display');
            const commentInput = document.getElementById('comment-input');
            const saveCommentBtn = document.getElementById('save-comment-btn');
            const cancelCommentBtn = document.getElementById('cancel-comment-btn');

            if (editCommentBtn) {
                editCommentBtn.addEventListener('click', function () {
                    commentEdit.classList.remove('d-none');
                    commentDisplay.classList.add('d-none');
                });
            }

            if (cancelCommentBtn) {
                cancelCommentBtn.addEventListener('click', function () {
                    commentEdit.classList.add('d-none');
                    commentDisplay.classList.remove('d-none');
                    commentInput.value = document.getElementById('modal-transaction-comment').textContent;
                });
            }

            // Save comment button click handler        
            if (saveCommentBtn) {
                saveCommentBtn.addEventListener('click', function () {
                    commentEdit.classList.add('d-none');
                    commentDisplay.classList.remove('d-none');

                    // Send the update to the server
                    fetch(`/api/transactions/${currentTransactionId}/comment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            comment: commentInput.value
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                            } else {
                                alert('Failed to update comment');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the comment');
                        });
                });
            }

            const selectAll = document.getElementById('select-all');
            const transactionCheckboxes = document.querySelectorAll('.transaction-checkbox');
            const massActionsBtn = document.getElementById('mass-actions-btn');
            const massActionsForm = document.getElementById('mass-actions-form');
            const cancelMassActions = document.getElementById('cancel-mass-actions');

            if (selectAll && massActionsBtn && massActionsForm) {
                // Update mass actions button state
                function updateMassActionsButton() {
                    const checkedCount = document.querySelectorAll('.transaction-checkbox:checked').length;
                    const transactionIds = Array.from(transactionCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);
                    const massIdsEl = document.getElementById('mass_transaction_ids');
                    if (massIdsEl) massIdsEl.value = transactionIds.join(',');
                    massActionsBtn.disabled = checkedCount === 0;
                    if (checkedCount > 0) {
                        massActionsBtn.textContent = 'Mass Actions (' + checkedCount + ' selected)';
                    } else {
                        massActionsBtn.textContent = 'Mass Actions';
                    }
                }

                // Select all functionality
                selectAll.addEventListener('change', function() {
                    transactionCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateMassActionsButton();
                });

                // Individual checkbox change
                transactionCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateMassActionsButton);
                });

                // Show/hide mass actions form
                massActionsBtn.addEventListener('click', function() {
                    massActionsForm.classList.toggle('d-none');
                });

                if (cancelMassActions) {
                    cancelMassActions.addEventListener('click', function() {
                        massActionsForm.classList.add('d-none');
                        selectAll.checked = false;
                        transactionCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        updateMassActionsButton();
                    });
                }
            }

        });
    </script>
@endpush