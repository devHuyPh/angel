<?php
namespace App\Http\Controllers;

use App\Models\Card;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::all();
        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        return view('cards.create');
    }

	 public function list_card()
  	{
    	$cards = Card::all();
    	return Theme::scope('cards.list_card', compact('cards'), 'cards.list_card')->render();
  	}
  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string',
      'number' => 'required|string|unique:cards',
      'cashback' => 'required|numeric',
      'value' => 'required|numeric',
      'expiration_date' => 'required|date_format:Y-m',
      'gift_description' => 'required|string',
      'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $data['expiration_date'] .= '-01';

    if ($request->hasFile('image')) {
      $data['image'] = $request->file('image')->store('cards', 'public');
    }

    Card::create($data);

    return redirect()->route('cards.index')->with('success', 'Card created successfully.');
  }


  public function edit(Card $card)
    {
        return view('cards.edit', compact('card'));
    }

    public function update(Request $request, Card $card)
    {
        // dd($card);
        $data = $request->validate([
            'name' => 'required|string',
            'number' => 'required|string|unique:cards,number,' . $card->id,
            'cashback' => 'required|numeric',
            'value' => 'required|numeric',
            'expiration_date' => 'required|date',
            'gift_description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cards', 'public');
        }

        $card->update($data);
        return redirect()->route('cards.index')->with('success', 'Card updated successfully.');
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
    }
}
