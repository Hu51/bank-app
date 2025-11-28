@extends('layouts.app')

@section('content')
    <style>
        .pointer {
            cursor: pointer;
        }
    </style>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Transactions</h2>
                        <div>
                            <button type="button" class="btn btn-primary" id="mass-actions-btn" disabled>
                                Mass Actions
                            </button>
                            <a id="recategorize-btn" class="btn btn-primary">Recategorize</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Mass Actions Form -->
                        <form id="mass-actions-form" action="{{ route('transactions.mass-update') }}" method="POST" class="mb-4 d-none">
                            @csrf
                            <input type="hidden" name="transaction_ids" id="mass_transaction_ids">
                            <div class="row g-3 align-items-center bg-light p-3 rounded">
                                <div class="col-md-4">
                                    <label for="mass_category" class="form-label">Category</label>
                                    <select name="category_id" id="mass_category" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mass_comment" class="form-label">Comment</label>
                                    <input type="text" name="comment" id="mass_comment" class="form-control" placeholder="Add a comment">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Apply Changes</button>
                                    <button type="button" class="btn btn-secondary" id="cancel-mass-actions">Cancel</button>
                                </div>
                            </div>
                        </form>

                        <form method="GET" action="{{ route('transactions.index') }}" class="mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-5">
                                    <div class="input-group">
                                        <span class="input-group-text">Date</span>
                                        <input type="date" name="start_date" class="form-control" 
                                               value="{{ $startDate }}"
                                               min="{{ $dateRange->min_date }}" 
                                               max="{{ $dateRange->max_date }}">
                                        <span class="input-group-text">to</span>
                                        <input type="date" name="end_date" class="form-control" 
                                               value="{{ $endDate }}"
                                               min="{{ $dateRange->min_date }}" 
                                               max="{{ $dateRange->max_date }}">
                                    </div>                              
                                </div>
                                <div class="col-4">
                                    <select name="category" class="form-select">
                                        <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-secondary">
                                        Filter
                                    </button>
                                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                        Reset
                                    </a>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search transactions..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="number" name="min_amount" class="form-control" 
                                               placeholder="Min" 
                                               value="{{ $minAmount ?? '' }}"
                                               style="width: 100px;">
                                        <span class="input-group-text">to</span>
                                        <input type="number" name="max_amount" class="form-control" 
                                               placeholder="Max" 
                                               value="{{ $maxAmount ?? '' }}"
                                               style="width: 100px;">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span class="float-left quick-date small badge bg-primary pointer" data-range="this-month"># This Month</span>
                                    <span class="float-left quick-date small badge bg-primary pointer" data-range="last-month"># Last Month</span>
                                    <span class="float-left quick-date small badge bg-primary pointer" data-range="last-3-months"># Last 3 Months</span>
                                    <span class="float-left quick-date small badge bg-primary pointer" data-range="this-year"># This Year</span>
                                    <span class="float-left quick-date small badge bg-primary pointer" data-range="last-year"># Last Year</span>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4">
                            <nav aria-label="Page navigation">
                                {{ $transactions->links('pagination::bootstrap-5') }}
                            </nav>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>Date</th>
                                        <th>Title</th>
                                        <th>Counterparty</th>
                                        <th>Description</th>
                                        <th>Card Number</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input transaction-checkbox" type="checkbox" 
                                                           name="transaction_ids[]" value="{{ $transaction->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-secondary view-transaction"
                                                    data-bs-toggle="modal" data-bs-target="#transactionModal"
                                                    data-transaction-id="{{ $transaction->id }}">
                                                    View
                                                </button>
                                            </td>
                                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td>{{ $transaction->transaction_title }}</td>
                                            <td>{{ $transaction->counterparty }}
                                                @if($transaction->comment)
                                                    <small class="text-muted float-end">{{ $transaction->comment }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->card_number }}</td>
                                            <td>
                                                @if($transaction->category)
                                                    <span class="d-flex align-items-center">
                                                        @if($transaction->category->icon)
                                                            <i class="{{ $transaction->category->icon }} me-1"></i>
                                                        @endif
                                                        <span style="color: {{ $transaction->category->color ?? '#000000' }}">
                                                            {{ $transaction->category->name }}
                                                        </span>
                                                    </span>
                                                @else
                                                    Uncategorized
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $transaction->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td
                                                class="text-end {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($transaction->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <nav aria-label="Page navigation">
                                {{ $transactions->links('pagination::bootstrap-5') }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4 p-3">
                        <h4>Basic Information</h4>
                        <table class="table">
                            <tr>
                                <th>Date:</th>
                                <td id="modal-transaction-date"></td>
                            </tr>
                            <tr>
                                <th>Title:</th>
                                <td id="modal-transaction-title"></td>
                            </tr>

                            <tr>
                                <th>Description:</th>
                                <td id="modal-transaction-description"></td>
                            </tr>
                            <tr>
                                <th>Counter party:</th>
                                <td id="modal-transaction-counterparty"></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <span id="modal-transaction-category-display" class="d-flex align-items-center">
                                                <i id="modal-transaction-category-icon" class="me-2"></i>
                                                <span id="modal-transaction-category-name"></span>
                                            </span>
                                        </div>
                                        <div class="ms-auto">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="change-category-btn">
                                                Change
                                            </button>
                                        </div>
                                    </div>
                                    <div id="category-select-container" class="mt-2 d-none">
                                        <select id="category-select" class="form-select">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" data-icon="{{ $category->icon }}"
                                                    data-color="{{ $category->color }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                id="save-category-btn">Save</button>
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                id="cancel-category-btn">Cancel</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    <span id="modal-transaction-type" class="badge"></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td id="modal-transaction-amount"></td>
                            </tr>
                            <tr>
                                <th>Source:</th>
                                <td id="modal-transaction-source"></td>
                            </tr>
                            <tr>
                                <th>Reference:</th>
                                <td id="modal-transaction-reference"></td>
                            </tr>
                            <tr>
                                <th>Metadata:</th>
                                <td id="modal-transaction-metadata" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto; font-family: monospace;"></td>
                            </tr>
                            <tr>
                                <th>Card Number:</th>
                                <td id="modal-transaction-card-number"></td>
                            </tr>
                            <tr>
                                <th>Comment:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div id="comment-display" class="mb-2">
                                                <span id="modal-transaction-comment"></span>
                                            </div>
                                            <div id="comment-edit" class="d-none">
                                                <textarea id="comment-input" class="form-control" rows="3"></textarea>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-primary" id="save-comment-btn">Save</button>
                                                    <button type="button" class="btn btn-sm btn-secondary" id="cancel-comment-btn">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="edit-comment-btn">
                                                Edit
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                recategorizeBtn.addEventListener('click', function () {
                    fetch('/transactions/recategorize')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                            } else {
                                alert('Failed to recategorize transactions');
                            }
                        });
                });
            }

            let currentTransactionId = null;
            let currentCategoryId = null;

            if (transactionModal) {
                transactionModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
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
                            amountElement.className = 'text-end ' + (transactionType === 'income' ? 'text-success' : 'text-danger');

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

            // Update mass actions button state
            function updateMassActionsButton() {
                const checkedCount = document.querySelectorAll('.transaction-checkbox:checked').length;
                const transactionIds = Array.from(transactionCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
                document.getElementById('mass_transaction_ids').value = transactionIds.join(',');
                massActionsBtn.disabled = checkedCount === 0;
                if (checkedCount > 0) {
                    massActionsBtn.textContent = `Mass Actions (${checkedCount} selected)`;
                } else {
                    massActionsBtn.textContent = 'Mass Actions';
                }
            }

            // Show/hide mass actions form
            massActionsBtn.addEventListener('click', function() {
                massActionsForm.classList.toggle('d-none');
            });

            // Cancel mass actions
            cancelMassActions.addEventListener('click', function() {
                massActionsForm.classList.add('d-none');
                selectAll.checked = false;
                transactionCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateMassActionsButton();
            });

        });
    </script>
@endpush