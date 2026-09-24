<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Posts;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /** Products at or below this stock level count as "low stock". */
    private const LOW_STOCK = 10;

    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        $now = now();

        // --- Sales (revenue = paid + shipped + completed orders) ---------------
        $last30 = $now->copy()->subDays(30);
        $prev30 = $now->copy()->subDays(60);

        $revenue30 = (float) Order::revenue()->where('created_at', '>=', $last30)->sum('total');
        $revenuePrev30 = (float) Order::revenue()->whereBetween('created_at', [$prev30, $last30])->sum('total');
        $revenueGrowth = $revenuePrev30 > 0 ? round(($revenue30 - $revenuePrev30) / $revenuePrev30 * 100) : null;

        $orders30 = Order::where('status', '!=', Order::CANCELLED)->where('created_at', '>=', $last30)->count();
        $paidOrders30 = Order::revenue()->where('created_at', '>=', $last30)->count();
        $avgOrder = $paidOrders30 > 0 ? $revenue30 / $paidOrders30 : 0;
        $pendingOrders = Order::where('status', Order::PENDING)->count();

        $salesMonths = collect(range(5, 0))->map(fn ($i) => $now->copy()->startOfMonth()->subMonths($i));
        $monthly = Order::revenue()->where('created_at', '>=', $salesMonths->first())
            ->get(['total', 'created_at'])
            ->groupBy(fn ($order) => $order->created_at->format('Y-m'))
            ->map(fn ($group) => round((float) $group->sum('total'), 2));
        $revenueLabels = $salesMonths->map(fn ($m) => $m->format('M Y'))->all();
        $revenueData = $salesMonths->map(fn ($m) => $monthly->get($m->format('Y-m'), 0))->all();

        $statusColors = [
            Order::PENDING => '#f59e0b', Order::PAID => '#4f46e5', Order::SHIPPED => '#0ea5e9',
            Order::COMPLETED => '#10b981', Order::CANCELLED => '#ef4444',
        ];
        $statusCounts = Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $statuses = collect(Order::STATUSES)->filter(fn ($s) => $statusCounts->get($s, 0) > 0);
        $statusLabels = $statuses->map(fn ($s) => ucfirst($s))->values()->all();
        $statusData = $statuses->map(fn ($s) => (int) $statusCounts[$s])->values()->all();
        $statusPalette = $statuses->map(fn ($s) => $statusColors[$s])->values()->all();

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', Order::REVENUE_STATUSES)
            ->where('orders.created_at', '>=', $last30)
            ->select('order_items.product_name', DB::raw('SUM(order_items.line_total) as revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('revenue')->limit(6)->get();

        // --- Users -------------------------------------------------------
        $usersTotal = User::count();
        $usersLast30 = User::where('created_at', '>=', $now->copy()->subDays(30))->count();
        $usersPrev30 = User::whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])->count();
        $usersGrowth = $usersPrev30 > 0 ? round(($usersLast30 - $usersPrev30) / $usersPrev30 * 100) : null;

        // New users per month, last 6 months (grouped in PHP so it runs on MySQL and SQLite alike).
        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->startOfMonth()->subMonths($i));
        $signups = User::where('created_at', '>=', $months->first())
            ->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m'));
        $signupLabels = $months->map(fn ($m) => $m->format('M Y'))->all();
        $signupData = $months->map(fn ($m) => $signups->get($m->format('Y-m'), 0))->all();

        // --- Catalogue ---------------------------------------------------
        $productsTotal = Product::count();
        $productsActive = Product::where('status', 'active')->count();
        $lowStock = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', self::LOW_STOCK)->count();
        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();

        $byCategory = Product::select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')->orderByDesc('total')->limit(6)->get();
        $categoryLabels = $byCategory->map(fn ($row) => $row->category ?: 'Uncategorised')->all();
        $categoryData = $byCategory->pluck('total')->all();

        $lowestStock = Product::orderBy('stock_quantity')->limit(8)->get(['name', 'stock_quantity']);

        // --- Community ---------------------------------------------------
        $followsTotal = DB::table('user_has_followers')->count();
        $postsTotal = Schema::hasTable('posts') ? Posts::count() : 0;

        return view('admin.dashboard', [
            'stats' => compact(
                'usersTotal', 'usersLast30', 'usersGrowth',
                'productsTotal', 'productsActive', 'lowStock', 'outOfStock',
                'followsTotal', 'postsTotal',
            ),
            'signupLabels' => $signupLabels,
            'signupData' => $signupData,
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            'stockLabels' => $lowestStock->pluck('name')->all(),
            'stockData' => $lowestStock->pluck('stock_quantity')->all(),
            'recentUsers' => User::latest()->limit(6)->get(['id', 'name', 'email', 'is_admin', 'created_at']),
            'lowStockThreshold' => self::LOW_STOCK,
            'sales' => compact('revenue30', 'revenueGrowth', 'orders30', 'avgOrder', 'pendingOrders'),
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'statusLabels' => $statusLabels,
            'statusData' => $statusData,
            'statusPalette' => $statusPalette,
            'topLabels' => $topProducts->pluck('product_name')->all(),
            'topData' => $topProducts->map(fn ($row) => round((float) $row->revenue, 2))->all(),
            'recentOrders' => Order::with('user')->latest()->limit(6)->get(),
        ]);
    }
}
