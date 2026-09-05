<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="title" class="form-label mb-1">Profile Title</label>
        <div class="form-text">A name you will recognize when importing, for example the bank or account.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="title" name="title" value="{{ $values['title'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="skip_rows" class="form-label mb-1">Skip Rows</label>
        <div class="form-text">How many lines to ignore before the header row in the CSV.</div>
    </div>
    <div class="col-md-8">
        <input type="number" class="form-control" id="skip_rows" name="skip_rows" value="{{ $values['skip_rows'] }}" required min="0">
    </div>
</div>

@include('mapping-profiles.partials.csv-column-picker')

<h4 class="mt-4 mb-3">Field Mappings</h4>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="transaction_date" class="form-label mb-1">Transaction Date</label>
        <div class="form-text">Booking or value date column. Expected style is YYYY. MM. DD.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="transaction_date" name="transaction_date" value="{{ $values['transaction_date'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="amount" class="form-label mb-1">Amount</label>
        <div class="form-text">Signed amount. Positive values import as income, negative as expense.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="amount" name="amount" value="{{ $values['amount'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="type" class="form-label mb-1">Type</label>
        <div class="form-text">Bank type column if present. Import currently derives income/expense from the amount sign.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="type" name="type" value="{{ $values['type'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="transaction_title" class="form-label mb-1">Transaction Title</label>
        <div class="form-text">Merchant or payment name shown on the transaction. Also used for keyword categorization.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="transaction_title" name="transaction_title" value="{{ $values['transaction_title'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="description" class="form-label mb-1">Description</label>
        <div class="form-text">Extra statement text such as a memo, details, or payment note.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="description" name="description" value="{{ $values['description'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="counterparty" class="form-label mb-1">Counterparty</label>
        <div class="form-text">Who sent or received the money. If this column is empty, source information is used instead.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="counterparty" name="counterparty" value="{{ $values['counterparty'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="location" class="form-label mb-1">Source Information</label>
        <div class="form-text">Bank location or source text. Used as a fallback when counterparty is blank.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="location" name="location" value="{{ $values['location'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="reference_id" class="form-label mb-1">Reference ID</label>
        <div class="form-text">Unique bank reference. Used to skip or overwrite duplicate imports.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="reference_id" name="reference_id" value="{{ $values['reference_id'] }}" required>
    </div>
</div>

<div class="row mb-3 py-2 align-items-start">
    <div class="col-md-4">
        <label for="card_number" class="form-label mb-1">Card Number</label>
        <div class="form-text">Optional. Card used for the payment; middle digits are masked when stored.</div>
    </div>
    <div class="col-md-8">
        <input type="text" class="form-control" id="card_number" name="card_number" value="{{ $values['card_number'] }}">
    </div>
</div>
