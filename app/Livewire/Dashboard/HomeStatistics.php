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
        ]);
    }
}
