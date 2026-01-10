<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;

class ManagerController extends Controller
{
	public function __construct()
  {
    $version = get_cms_version();

    Theme::asset()
      ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

    Theme::asset()
      ->add('front-ecommerce-css', 'vendor/core/plugins/ecommerce/css/front-ecommerce.css', version: $version);

    Theme::asset()
      ->container('footer')
      ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
      ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
      ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
  }
  public function index(Request $request)
  {
    $customer = auth('customer')->user();
    $userId = $customer->id;

    $managedStates = DB::table('ec_customer_manager as cm')
      ->join('states as s', 'cm.state_id', '=', 's.id')
      ->where('cm.customer_id', $userId)
      ->select('s.name as state_name', 's.id as state_id')
      ->get();

    if ($managedStates->isEmpty()) {
      return Theme::scope('manager.no-permission', compact('customer'), 'manager.no-permission')->render();
    }

    // Ngày đầu và cuối tháng
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    // Lọc theo khu vực nếu có
    $selectedStateId = $request->input('state_id');
    $filteredStateIds = $selectedStateId
      ? [$selectedStateId]
      : $managedStates->pluck('state_id')->toArray();

    // Truy vấn đơn hàng
    $ordersQuery = DB::table('ec_orders')
      ->join('ec_order_addresses', 'ec_orders.id', '=', 'ec_order_addresses.order_id')
      ->join('ec_customers', 'ec_orders.user_id', '=', 'ec_customers.id')
      ->whereIn('ec_order_addresses.state', $filteredStateIds)
      ->whereBetween('ec_orders.created_at', [$startOfMonth, $endOfMonth])
      ->select(
        'ec_customers.name as customer_name',
        'ec_orders.amount as total_amount',
        'ec_orders.created_at as order_date'
      );

    $orders = $ordersQuery->paginate(7);

    // Tính toán
    $totalOrders = $ordersQuery->count(); // Tổng số đơn
    $totalRevenue = $ordersQuery->sum('amount'); // Tổng doanh thu
    $commission = $totalRevenue * 0.05; // 5% hoa hồng

    return Theme::scope('manager.index', compact(
      'orders',
      'totalOrders',
      'totalRevenue',
      'commission',
      'managedStates',
      'selectedStateId'
    ), 'manager.index')->render();
  }

}
