<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    private function authedUser()
    {
        return Auth::id() ?? 1;
    }

    public function index(Request $request)
    {
        $categories = Category::orderBy('is_default', 'desc')->orderBy('name')->get();

        // Get date range from request or set defaults
        $startDate = $request->get('start_date') ?? date('Y-m-d', strtotime('first day of this month'));
        $endDate = $request->get('end_date') ?? date('Y-m-d', strtotime('last day of this month'));

        $categoryId = $request->get('category') ?? 'all';
        if (empty($categoryId) || !in_array($categoryId, $categories->pluck('id')->toArray())) {
            $categoryId = 'all';
        }

        $search = $request->get('search') ?? '';
        if (empty($search)) {
            $search = 'all';
        }

        // Add amount range filters
        $minAmount = $request->get('min_amount');
        $maxAmount = $request->get('max_amount');

        $query = Transaction::query()
            ->where('user_id', $this->authedUser())
            ->orderBy('transaction_date', 'desc');

        // Apply date range filter
        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        if ($search != 'all') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('counterparty', 'like', "%{$search}%");
            });
        }

        // Add amount range conditions
        if (!empty($minAmount)) {
            $query->where('amount', '>=', $minAmount);
        }
        if (!empty($maxAmount)) {
            $query->where('amount', '<=', $maxAmount);
        }

        $transactions = $query->paginate(50)->appends($request->all());

        // Get min and max dates from transactions for the datepicker
        $dateRange = Transaction::where('user_id', $this->authedUser())
            ->selectRaw('MIN(transaction_date) as min_date, MAX(transaction_date) as max_date')
            ->first();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'categoryId',
            'startDate',
            'endDate',
            'dateRange',
            'minAmount',
            'maxAmount'
        ));
    }

    public function monthlySummary(Request $request)
    {
        $month =$request->get('month');
        $months = $this->getMonthOptions();
        $year = $request->get('year');
        $years = $this->getYearOptions();
        if (empty($month) || !in_array($month, $months)) {
            $month = date('m');
        }

        if (empty($year) || !in_array($year, $years)) {
            $year = date('Y');
        }

        $query = Transaction::where('user_id', $this->authedUser())
            ->where('transaction_date', 'like', "{$year}-{$month}%");

        $summary = $query->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as total_transactions
            ')
            ->first();

        $categorySummary = Transaction::where('user_id', $this->authedUser())
            ->where('type', 'expense')
            ->where('transaction_date', 'like', "{$year}-{$month}%");

        $categorySummary = $categorySummary->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $categories = Category::orderBy('name')->get();


        return view('transactions.summary', compact(
            'summary',
            'month',
            'months',
            'categories',
            'categorySummary',
            'year',
            'years'
        ));
    }

    /**
     * Update the category of a transaction
     */
    public function updateCategory(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction category updated successfully',
            'transaction' => $transaction->load('category'),
        ]);
    }

    /**
     * Display the specified transaction.
     */
    public function apiShow(int $transactionId)
    {
        $transaction = Transaction::find($transactionId);
        return response()->json($transaction->load('category'));
    }

    /**
     * Get month options for dropdown
     */
    private function getMonthOptions()
    {
        // Get Y-m from transaction_date
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return $months;
    }

    private function getYearOptions()
    {
        $years = Transaction::where('user_id', $this->authedUser())
            ->whereNotNull('transaction_date')
            ->selectRaw('substring(transaction_date, 1, 4) as year')
            ->distinct()
            ->get()->toArray();
        $years = array_column($years, 'year');
        $years = array_filter($years);
        usort($years, function ($a, $b) {
            return $b - $a;
        });
        return $years;
    }

    public function recategorize(Request $request)
    {
        $transactions = Transaction::whereNull('category_id')
            ->where('user_id', $this->authedUser())
            ->orWhere('category_id', 1)
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->categorize();
        }

        return response()->json([
            'success' => true,
            'message' => 'Transactions recategorized successfully',
            'transactions' => $transactions->count(),
        ]);
    }

    public function counterparty(Request $request)
    {
        $query = Transaction::query()
            ->where('user_id', $this->authedUser())
            ->select('counterparty')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income')
            ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses')
            ->selectRaw('MAX(transaction_date) as last_transaction_date')
            ->groupBy('counterparty');

        if ($request->has('search')) {
            $query->where('counterparty', 'like', '%' . $request->search . '%');
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $counterparties = $query->paginate(50)->appends($request->all());

        // Get most common category for each counterparty
        foreach ($counterparties as $counterparty) {
            $mostCommonCategory = Transaction::where('counterparty', $counterparty->counterparty)
                ->where('user_id', $this->authedUser())
                ->whereNotNull('category_id')
                ->select('category_id')
                ->selectRaw('COUNT(*) as category_count')
                ->groupBy('category_id')
                ->orderByDesc('category_count')
                ->with('category')
                ->first();

            $counterparty->most_common_category = $mostCommonCategory ? $mostCommonCategory->category : null;
        }

        return view('transactions.counterparty', compact('counterparties'));
    }

    public function yearSummary(Request $request)
    {
        $year = $request->get('year') ?? date('Y');

        // Get available years
        $years = Transaction::where('user_id', $this->authedUser())
            ->whereNotNull('transaction_date')
            ->selectRaw('substring(transaction_date, 1, 4) as year')
            ->distinct()
            ->orderBy('substring(transaction_date, 1, 4)', 'desc')
            ->get()->toArray();

        $years = array_column($years, 'year');
        $years = array_filter($years);
        usort($years, function ($a, $b) {
            return $b - $a;
        });

        if (!in_array($year, $years)) {
            $year = $years[0];
        }
        // Get monthly totals for the year
        $query = Transaction::where('user_id', $this->authedUser())
            ->whereRaw('substring(transaction_date, 1, 4) = ?', [$year])
            ->selectRaw('
                substring(transaction_date, 1, 7) as month,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as transaction_count
            ')
            ->groupBy('month')
            ->orderBy('month');

        \DB::enableQueryLog();
        $monthlyTotals = $query->get();
        $lastQuery = \DB::getQueryLog();

        // Get yearly totals
        $yearlyTotal = Transaction::where('user_id', $this->authedUser())
            ->whereRaw('substring(transaction_date, 1, 4) = ?', [$year])
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as total_transactions
            ')
            ->first();

        // Get category totals for the year
        $categoryTotals = Transaction::where('user_id', $this->authedUser())
            ->whereRaw('substring(transaction_date, 1, 4) = ?', [$year])
            ->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()->toArray();

        // Get top counterparties
        $topCounterparties = Transaction::where('user_id', $this->authedUser())
            ->whereRaw('substring(transaction_date, 1, 4) = ?', [$year])
            ->select('counterparty')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income')
            ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses')
            ->groupBy('counterparty')
            ->orderByRaw('total_income DESC, total_expenses DESC')
            ->limit(10)
            ->get()->toArray();

        $topCounterpartiesExpenses = Transaction::where('user_id', $this->authedUser())
            ->whereRaw('substring(transaction_date, 1, 4) = ?', [$year])
            ->select('counterparty')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income')
            ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses')
            ->groupBy('counterparty')
            ->orderByRaw('total_expenses DESC, total_income DESC')
            ->limit(10)
            ->get()->toArray();

        return view('transactions.year-summary', compact(
            'year',
            'years',
            'monthlyTotals',
            'yearlyTotal',
            'categoryTotals',
            'topCounterparties',
            'topCounterpartiesExpenses'
        ));
    }

    public function updateComment(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update multiple transactions' category and comment
     */
    public function massUpdate(Request $request)
    {
try {
        $validated = $request->validate([
            'transaction_ids' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'comment' => 'nullable|string|max:255',
        ]);
        $transactionIds = explode(',', $validated['transaction_ids']);
} catch (\Exception $e) {
    dd($e);
}

        $updates = [];
        if (!empty($validated['category_id'])) {
            $updates['category_id'] = $validated['category_id'];
        }
        if (!empty($validated['comment'])) {
            $updates['comment'] = $validated['comment'];
        }

        if (empty($updates)) {
            return redirect()->back()->with('error', 'No changes specified');
        }

        $count = Transaction::whereIn('id', $transactionIds)
            ->where('user_id', $this->authedUser())
            ->update($updates);

        return redirect()->back()->with('success', "Updated {$count} transactions successfully");
    }
}
