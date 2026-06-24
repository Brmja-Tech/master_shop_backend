<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\Vendor;
use App\Models\DeliveryUser;
use App\Models\Order;
use App\Enums\OrderStatus;
use Livewire\Component;

class HomeStatistics extends Component
{
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = date('m');
        $this->selectedYear = date('Y');
    }

    public function getMonths()
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            '01' => $isAr ? 'يناير' : 'January',
            '02' => $isAr ? 'فبراير' : 'February',
            '03' => $isAr ? 'مارس' : 'March',
            '04' => $isAr ? 'أبريل' : 'April',
            '05' => $isAr ? 'مايو' : 'May',
            '06' => $isAr ? 'يونيو' : 'June',
            '07' => $isAr ? 'يوليو' : 'July',
            '08' => $isAr ? 'أغسطس' : 'August',
            '09' => $isAr ? 'سبتمبر' : 'September',
            '10' => $isAr ? 'أكتوبر' : 'October',
            '11' => $isAr ? 'نوفمبر' : 'November',
            '12' => $isAr ? 'ديسمبر' : 'December',
        ];
    }

    public function render()
    {
        $months = $this->getMonths();

        // System Counts
        $totalVendors = Vendor::count();
        $totalDeliveries = DeliveryUser::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();

        // Today's Delivered Orders
        $deliveredTodayCount = Order::where('status', OrderStatus::Delivered)
            ->whereDate('delivered_at', today())
            ->count();

        $deliveredTodaySum = Order::where('status', OrderStatus::Delivered)
            ->whereDate('delivered_at', today())
            ->sum('total');

        // Monthly Stats (based on selected month)
        $monthlyDeliveredCount = Order::where('status', OrderStatus::Delivered)
            ->whereMonth('delivered_at', $this->selectedMonth)
            ->whereYear('delivered_at', $this->selectedYear)
            ->count();

        $monthlyDeliveredSum = Order::where('status', OrderStatus::Delivered)
            ->whereMonth('delivered_at', $this->selectedMonth)
            ->whereYear('delivered_at', $this->selectedYear)
            ->sum('total');

        // Weekly Stats (last 7 days)
        $lastSevenDays = collect(range(6, 0))->map(function($i) {
            return today()->subDays($i)->format('Y-m-d');
        });

        $ordersLastSevenDays = Order::where('created_at', '>=', today()->subDays(6)->startOfDay())
            ->selectRaw("DATE(created_at) as date, count(*) as count, SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as revenue")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $weeklyDates = [];
        $weeklyCounts = [];
        $weeklyRevenues = [];

        foreach ($lastSevenDays as $date) {
            $weeklyDates[] = date('d M', strtotime($date));
            $weeklyCounts[] = $ordersLastSevenDays->has($date) ? $ordersLastSevenDays->get($date)->count : 0;
            $weeklyRevenues[] = $ordersLastSevenDays->has($date) ? (float)$ordersLastSevenDays->get($date)->revenue : 0.0;
        }

        // Order Status Distribution
        $ordersByStatus = Order::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [];
        $statusSeries = [];

        foreach (OrderStatus::cases() as $case) {
            $statusLabels[] = __('dashboard.' . $case->value);
            $statusSeries[] = $ordersByStatus[$case->value] ?? 0;
        }

        return view('livewire.dashboard.home-statistics', [
            'months' => $months,
            'totalVendors' => $totalVendors,
            'totalDeliveries' => $totalDeliveries,
            'totalUsers' => $totalUsers,
            'totalOrders' => $totalOrders,
            'deliveredTodayCount' => $deliveredTodayCount,
            'deliveredTodaySum' => $deliveredTodaySum,
            'monthlyDeliveredCount' => $monthlyDeliveredCount,
            'monthlyDeliveredSum' => $monthlyDeliveredSum,
            'weeklyDates' => $weeklyDates,
            'weeklyCounts' => $weeklyCounts,
            'weeklyRevenues' => $weeklyRevenues,
            'statusLabels' => $statusLabels,
            'statusSeries' => $statusSeries,
        ]);
    }
}
