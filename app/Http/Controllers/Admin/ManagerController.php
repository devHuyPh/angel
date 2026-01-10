<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $managers = DB::table('ec_customer_manager')
      ->join('ec_customers', 'ec_customer_manager.customer_id', '=', 'ec_customers.id')
      ->join('states', 'ec_customer_manager.state_id', '=', 'states.id')
      ->select(
        DB::raw('MIN(ec_customer_manager.id) as manager_id'),
        'ec_customer_manager.name as manager_name',
        'ec_customers.name as customer_name',
        'ec_customer_manager.customer_id',
        DB::raw('MIN(ec_customer_manager.hash) as hash'),
        DB::raw('GROUP_CONCAT(DISTINCT states.name SEPARATOR ", ") as state_names')
      )
      ->groupBy('ec_customer_manager.name', 'ec_customer_manager.customer_id', 'ec_customers.name')
      ->paginate(5);

    return view('admin.manager.index', compact('managers'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $customers = DB::table('ec_customers')->get();

    // Lấy danh sách state đã được gán cho bất kỳ manager nào
    $assignedStateIds = DB::table('ec_customer_manager')->pluck('state_id')->toArray();

    // Chỉ lấy các state chưa bị quản lý
    $states = DB::table('states')
      ->whereNotIn('id', $assignedStateIds)
      ->get();

    return view('admin.manager.create', compact('customers', 'states'));
  }


  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'manager_name' => 'required|string|max:255|unique:ec_customer_manager,name',
      'customer_id' => 'required|exists:ec_customers,id',
      'state_ids' => 'required|array',
      'hash' => 'required|string|max:255',
      'state_ids.*' => 'exists:states,id',
    ]);

    foreach ($request->state_ids as $stateId) {

      $exists = DB::table('ec_customer_manager')->where('state_id', $stateId)->exists();

      if ($exists) {
        return redirect()->back()->withErrors([
          'state_ids' => "Khu vực đã được phân công người quản lý khác."
        ])->withInput();
      }


      DB::table('ec_customer_manager')->insert([
        'name' => $request->manager_name,
        'customer_id' => $request->customer_id,
        'state_id' => $stateId,
        'hash' => $request->hash,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    return redirect()->route('admin.manager.index')->with('success', 'Người quản lý đã được tạo thành công!');
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($customer_id, $manager_name)
  {
    // Lấy thông tin manager để chỉnh sửa
    $manager = DB::table('ec_customer_manager')
      ->where('customer_id', $customer_id)
      ->where('name', $manager_name)
      ->first();

    if (!$manager) {
      return redirect()->route('admin.manager.index')->withErrors(['error' => 'Manager not found']);
    }

    // Lấy danh sách tất cả customers
    $customers = DB::table('ec_customers')->get();

    // Lấy các state đã được gán cho bất kỳ manager nào khác (khác customer_id & name)
    $excludedStateIds = DB::table('ec_customer_manager')
      ->where(function ($query) use ($customer_id, $manager_name) {
        $query->where('customer_id', '!=', $customer_id)
          ->orWhere('name', '!=', $manager_name);
      })
      ->pluck('state_id')
      ->toArray();

    // Lấy các state đã chọn của manager hiện tại
    $selectedStates = DB::table('ec_customer_manager')
      ->where('customer_id', $customer_id)
      ->where('name', $manager_name)
      ->pluck('state_id')
      ->toArray();

    // Lấy danh sách state hợp lệ: chưa bị quản lý bởi người khác hoặc đang thuộc về manager hiện tại
    $states = DB::table('states')
      ->whereNotIn('id', array_diff($excludedStateIds, $selectedStates))
      ->get();

    return view('admin.manager.edit', compact('manager', 'customers', 'states', 'selectedStates'));
  }


  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $customer_id, $manager_name)
  {
    // Validate dữ liệu
    $request->validate([
      'manager_name' => 'required|string|max:255',
      'customer_id' => 'required|exists:ec_customers,id',
      'state_ids' => 'required|array',
      'state_ids.*' => 'exists:states,id',
      'hash' => 'required|string|max:255',
    ]);

    // Kiểm tra từng state_id có bị trùng với manager khác không
    foreach ($request->state_ids as $stateId) {
      $exists = DB::table('ec_customer_manager')
        ->where('state_id', $stateId)
        ->where(function ($query) use ($customer_id, $manager_name) {
          $query->where('customer_id', '!=', $customer_id)
            ->orWhere('name', '!=', $manager_name);
        })
        ->exists();

      if ($exists) {
        return redirect()->back()->withErrors([
          'state_ids' => "Khu vực đã được gán cho người quản lý khác."
        ])->withInput();
      }
    }

    // Xoá toàn bộ state_id cũ của manager này
    DB::table('ec_customer_manager')
      ->where('customer_id', $customer_id)
      ->where('name', $manager_name)
      ->delete();

    // Thêm lại các state_id mới
    foreach ($request->state_ids as $stateId) {
      DB::table('ec_customer_manager')->insert([
        'customer_id' => $request->customer_id,
        'state_id' => $stateId,
        'name' => $request->manager_name,
        'hash' => $request->hash,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    return redirect()->route('admin.manager.index')->with('success', 'Customer Manager updated successfully.');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy($hash)
  {
    // Kiểm tra xem có bản ghi nào với hash không
    $managers = DB::table('ec_customer_manager')->where('hash', $hash)->get();

    if ($managers->isEmpty()) {
      return redirect()->back()->with('error', 'Không tìm thấy bản ghi cần xóa.');
    }

    // Thực hiện xóa
    DB::table('ec_customer_manager')->where('hash', $hash)->delete();

    return redirect()->route('admin.manager.index')
      ->with('success', 'Quản lý đã bị xóa thành công.');
  }



}
