<?php

namespace App\Http\Controllers\Admin;
use App\Models\TotalRevenue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TotalRevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const PATH_VIEW='admin.totalrevenue.';
    public function index()
    {
        $totals=TotalRevenue::all();
        return view(self::PATH_VIEW.__FUNCTION__,compact('totals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {
        
        return view(self::PATH_VIEW.__FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'amount'=>'required|min:0',
            'percentage'=>'required|numeric|min:0|max:100'
        ]);
        TotalRevenue::create([
            'amount'=>$data['amount'],
            'percentage'=>$data['percentage']
        ]);
        return redirect()->route('totalrevenue.index')->with('success', 'Thêm thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $total=TotalRevenue::findOrFail($id);
        return view(self::PATH_VIEW.__FUNCTION__,compact('total'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $total=TotalRevenue::findOrFail($id);
        $data=$request->validate([
            'amount'=>'required|min:0',
            'percentage'=>'required|numeric|min:0|max:100'
        ]);
        $total->amount=$data['amount'];
        $total->percentage=$data['percentage'];
        $total->save();
        return redirect()->route('totalrevenue.index')->with('success', 'Sửa đổi thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $total=TotalRevenue::findOrFail($id);
        $total->delete();
        return redirect()->route('totalrevenue.index')->with('success', 'Xóa thành công.');
    }
}
