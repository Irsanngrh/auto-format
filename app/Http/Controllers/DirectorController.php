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
        $request->validate(['name' => 'required', 'position' => 'required']);

        DB::transaction(function () use ($request) {
            $director = Director::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'position' => $request->position
            ]);

            if ($request->has('card_number')) {
                foreach ($request->card_number as $index => $number) {
                    if (!empty($number)) {
                        CreditCard::create([
                            'director_id' => $director->id,
                            'bank_name' => $request->bank_name[$index] ?? 'Bank',
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
        $request->validate(['name' => 'required', 'position' => 'required']);

        DB::transaction(function () use ($request, $id) {
            $director = Director::findOrFail($id);
            $director->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'position' => $request->position
            ]);

            $submittedIds = $request->input('card_id', []); 
            $bankNames = $request->input('bank_name', []);
            $cardNumbers = $request->input('card_number', []);

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