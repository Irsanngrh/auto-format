<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'bank_name' => 'required|array',
            'card_number' => 'required|array'
        ]);

        $director = Director::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'position' => $request->position
        ]);

        foreach ($request->bank_name as $key => $bank) {
            if (!empty($bank) && !empty($request->card_number[$key])) {
                CreditCard::create([
                    'director_id' => $director->id,
                    'bank_name' => $bank,
                    'card_number' => $request->card_number[$key]
                ]);
            }
        }

        return redirect()->route('directors.index')->with('success', 'Data direksi tersimpan.');
    }

    public function edit($id)
    {
        $director = Director::with('creditCards')->findOrFail($id);
        return view('directors.edit', compact('director'));
    }

    public function update(Request $request, $id)
    {
        $director = Director::findOrFail($id);
        
        $director->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'position' => $request->position
        ]);

        $director->creditCards()->delete();

        if ($request->has('bank_name')) {
            foreach ($request->bank_name as $key => $bank) {
                if (!empty($bank) && !empty($request->card_number[$key])) {
                    CreditCard::create([
                        'director_id' => $director->id,
                        'bank_name' => $bank,
                        'card_number' => $request->card_number[$key]
                    ]);
                }
            }
        }

        return redirect()->route('directors.index')->with('success', 'Data diperbarui.');
    }

    public function destroy($id)
    {
        Director::findOrFail($id)->delete();
        return redirect()->route('directors.index')->with('success', 'Data dihapus.');
    }
}