<?php

namespace App\Http\Controllers;

use App\Models\MappingProfile;
use Illuminate\Http\Request;
use League\Csv\Reader;

class MappingProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mappingProfiles = MappingProfile::all();
        return view('mapping-profiles.index', compact('mappingProfiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mapping-profiles.create');
    }

    /**
     * Parse a sample CSV and return headers plus a short preview.
     */
    public function previewCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:8192',
            'skip_rows' => 'required|integer|min:0',
        ]);

        $skip = (int) $request->input('skip_rows');
        $contents = file_get_contents($request->file('csv_file')->getRealPath());

        if ($contents === false || trim($contents) === '') {
            return response()->json(['message' => 'The CSV file is empty.'], 422);
        }

        if (! mb_check_encoding($contents, 'UTF-8')) {
            $contents = mb_convert_encoding($contents, 'UTF-8', 'Windows-1250,ISO-8859-2,ISO-8859-1');
        }

        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;
        $delimiter = substr_count($contents, ';') >= substr_count($contents, ',') ? ';' : ',';

        $csv = Reader::createFromString($contents);
        $csv->setDelimiter($delimiter);
        $csv->setEnclosure('"');
        $csv->setEscape('');

        $rows = [];
        foreach ($csv->getRecords() as $record) {
            $rows[] = array_map(static fn ($cell) => trim((string) $cell), array_values($record));
            if (count($rows) >= 20) {
                break;
            }
        }

        if (! isset($rows[$skip])) {
            return response()->json([
                'headers' => [],
                'preview' => [],
                'column_samples' => [],
            ]);
        }

        $headers = $rows[$skip];
        $preview = [];

        foreach (array_slice($rows, $skip, 10, true) as $index => $cells) {
            $preview[] = [
                'line' => $index + 1,
                'is_header' => $index === $skip,
                'cells' => $cells,
            ];
        }

        $sampleRows = array_slice($rows, $skip + 1, 3);
        $columnSamples = [];

        foreach ($headers as $colIndex => $header) {
            if ($header === '') {
                continue;
            }

            $columnSamples[$header] = [];
            foreach ($sampleRows as $sampleRow) {
                $columnSamples[$header][] = $sampleRow[$colIndex] ?? '';
            }
        }

        return response()->json([
            'headers' => array_values(array_filter($headers, static fn ($header) => $header !== '')),
            'preview' => $preview,
            'column_samples' => $columnSamples,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'skip_rows' => 'required|integer|min:0',
            'transaction_title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'counterparty' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reference_id' => 'required|string|max:255',
            'card_number' => 'nullable|string|max:255',
        ]);

        MappingProfile::create($validated);

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MappingProfile $mappingProfile)
    {
        return view('mapping-profiles.show', compact('mappingProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MappingProfile $mappingProfile)
    {
        return view('mapping-profiles.edit', compact('mappingProfile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MappingProfile $mappingProfile)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'skip_rows' => 'required|integer|min:0',
            'transaction_title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'counterparty' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reference_id' => 'required|string|max:255',
            'card_number' => 'nullable|string|max:255',
        ]);

        $mappingProfile->update($validated);

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MappingProfile $mappingProfile)
    {
        $mappingProfile->delete();

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile deleted successfully.');
    }
}
