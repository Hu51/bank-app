<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\MappingProfile;
use Illuminate\Http\Request;
use League\Csv\Reader;
use App\Models\Category;
use App\Models\CategoryKeyword;

class TransactionImportController extends Controller
{
    public function index()
    {
        $mappingProfiles = MappingProfile::pluck('title', 'id')->toArray();
        return view('transactions.import', [
            'mappingProfiles' => $mappingProfiles
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:8192'
        ]);

        $mappingProfile = MappingProfile::find($request->mapping_profile);

        try {
            if (!$mappingProfile) {
                throw new \Exception('Invalid mapping profile');
            }

            $file = $request->file('csv_file');
            $csvFile = Reader::createFromPath($file->getPathname());

            $csvFile->setHeaderOffset($mappingProfile->skip_rows);

            $csvFile->setDelimiter(';');
            $records = $csvFile->getRecords();
            $imported = 0;
            $errors = [];

            foreach ($records as $record) {
                if ($record[$mappingProfile->fields['amount']] === null) {
                    continue;
                }

                try {
                    // Parse amount - remove spaces and replace comma with dot
                    $amount = str_replace(' ', '', $record[$mappingProfile->fields['amount']]);
                    $amount = str_replace(',', '.', $amount);

                    // Determine transaction type based on amount
                    $type = floatval($amount) >= 0 ? 'income' : 'expense';

                    // Parse date - assuming format YYYY. MM. DD
                    $date = str_replace('.', '-', rtrim($record[$mappingProfile->fields['transaction_date']], '.'));

                    // Detect category based on description and transaction type
                    $description = strtolower($record[$mappingProfile->fields['transaction_title']] . ' ' . $record[$mappingProfile->fields['description']]);

                    $counterparty = trim($record[$mappingProfile->fields['counterparty']]);
                    if (empty($counterparty)) {
                        $counterparty = trim($record[$mappingProfile->fields['location']]);
                    }

                    // Get all categories with their keywords
                    $categoryID = Category::where('is_default', true)->first()->id;

                    $description = str_replace(' ', '', $record[$mappingProfile->fields['description']]);
                    $referenceID = ltrim($record[$mappingProfile->fields['reference_id']] ?? null, '0');
                    $referenceID = (empty($referenceID)) ? null : $referenceID;

                    $cardNumber = $record[$mappingProfile->fields['card_number']] ?? null;
                    // mask card middle
                    $cardNumber = substr($cardNumber, 0, 4) . '****' . substr($cardNumber, -4);

                    // Create transaction
                    if ($request->has('overwrite') && $request->overwrite) {
                        // Delete existing transactions with the same reference_id
                        Transaction::where('reference_id', $referenceID)
                            ->where('user_id', auth()->id() ?? 1)
                            ->delete();
                    }
                    try {
                        $transaction = Transaction::create([
                            'user_id' => auth()->id() ?? 1,
                            'category_id' => $categoryID,
                            'amount' => abs(floatval($amount)),
                            'type' => $type,
                            'transaction_title' => $record[$mappingProfile->fields['transaction_title']],
                            'description' => $description,
                            'counterparty' => $counterparty,
                            'transaction_date' => $date,
                            'source' => 'bank',
                            'reference_id' => $referenceID,
                            'metadata' => $record,
                            'card_number' => $cardNumber
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        if (str_contains($e->getMessage(), 'Duplicate entry')) {
                            $errors[] = "Duplicate entry: " . $record[$mappingProfile->fields['reference_id']];
                            continue;
                        }
                        if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
                            $errors[] = "Integrity constraint violation: " . $record[$mappingProfile->fields['reference_id']];
                            continue;
                        }
                        throw $e;
                    }                    
                    $transaction->categorize();                        
                    $imported++;                                     
                } catch (\Exception $e) {
                    $errors[] = "Error processing row: " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} transactions.";
            if (!empty($errors)) {
                $error = " Encountered " . count($errors) . " errors. <br>" . implode('<br>', $errors);
                return redirect()->route('transactions.import')
                    ->with('error', $error);
            }

            return redirect()->route('transactions.import')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('transactions.import')
                ->with('error', 'Error importing transactions: ' . $e->getMessage());
        }
    }
}