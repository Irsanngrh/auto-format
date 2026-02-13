<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DirectorController extends Controller
{
    public function index()
    {
        $directors = Director::with('creditCards')->get();
        return view('directors.index', compact('directors'));
    }

    public function create()
    {
        return view('directors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'card_number' => 'nullable|array',
            'card_number.*' => 'nullable|string|max:50',
            'bank_name' => 'nullable|array',
            'bank_name.*' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $director = Director::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'position' => $validated['position']
            ]);

            if (!empty($validated['card_number'])) {
                $bankNames = $validated['bank_name'] ?? [];
                foreach ($validated['card_number'] as $index => $number) {
                    if (!empty($number)) {
                        CreditCard::create([
                            'director_id' => $director->id,
                            'bank_name' => $bankNames[$index] ?? 'Bank',
                            'card_number' => $number
                        ]);
                    }
                }
            }
        });

        return redirect()->route('directors.index')->with('success', 'Data direksi tersimpan.');
    }

    public function edit($id)
    {
        $director = Director::with('creditCards')->findOrFail($id);
        return view('directors.edit', compact('director'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'card_id' => 'nullable|array',
            'card_id.*' => 'nullable|integer',
            'card_number' => 'nullable|array',
            'card_number.*' => 'nullable|string|max:50',
            'bank_name' => 'nullable|array',
            'bank_name.*' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $director = Director::findOrFail($id);
            $director->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'position' => $validated['position']
            ]);

            $submittedIds = $validated['card_id'] ?? []; 
            $bankNames = $validated['bank_name'] ?? [];
            $cardNumbers = $validated['card_number'] ?? [];

            $existingCardIds = $director->creditCards()->pluck('id')->toArray();
            $idsToDelete = array_diff($existingCardIds, $submittedIds);

            if (!empty($idsToDelete)) {
                try {
                    CreditCard::destroy($idsToDelete);
                } catch (\Exception $e) {}
            }

            foreach ($cardNumbers as $index => $number) {
                if (!empty($number)) {
                    $cardId = $submittedIds[$index] ?? null;
                    $bank = $bankNames[$index] ?? 'Bank';

                    if ($cardId && in_array($cardId, $existingCardIds)) {
                        CreditCard::where('id', $cardId)->update(['bank_name' => $bank, 'card_number' => $number]);
                    } else {
                        CreditCard::create(['director_id' => $director->id, 'bank_name' => $bank, 'card_number' => $number]);
                    }
                }
            }
        });

        return redirect()->route('directors.index')->with('success', 'Data diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $director = Director::findOrFail($id);
            $director->creditCards()->delete();
            $director->delete();
            return redirect()->route('directors.index')->with('success', 'Data dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('directors.index')->with('error', 'Gagal menghapus. Data sedang digunakan.');
        }
    }
}