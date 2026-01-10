<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ranking;
use App\Models\RewardHistory;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Http\Request;

class RewardHistoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $this->pageTitle(trans('core/base::layouts.reward_history'));

    $rewardHistories = RewardHistory::with('customer')
        ->orderBy('date_reward', 'desc')
        ->paginate(10);

    return view('admin.reward_history.index', compact('rewardHistories'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
