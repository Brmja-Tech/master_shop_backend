<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DeliveryUser;
use App\Notifications\DeliveryStatusNotify;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('dashboard.deliveries.index', ['is_request_page' => false]);
    }

    public function requests()
    {
        return view('dashboard.deliveries.index', ['is_request_page' => true]);
    }

    public function show($id)
    {
        $delivery = DeliveryUser::findOrFail($id);
        return view('dashboard.deliveries.profile', compact('delivery'));
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->query('status') ?? $request->input('status');

        if (!in_array($status, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'الحالة المرسلة غير صالحة.');
        }

        $delivery = DeliveryUser::findOrFail($id);
        $delivery->update([
            'approval_status' => $status,
        ]);

        // إرسال نوتيفيكيشن واتساب عبر القناة الجديدة
        try {
            $delivery->notify(new DeliveryStatusNotify($status));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('فشل إرسال إشعار حالة المندوب: ' . $e->getMessage());
        }

        $messageKey = $status === 'approved' 
            ? 'dashboard.delivery_approved_successfully' 
            : 'dashboard.delivery_rejected_successfully';

        return redirect()->back()->with('success', __($messageKey));
    }

    public function toggleBan($id)
    {
        $delivery = DeliveryUser::findOrFail($id);
        $delivery->update([
            'ban' => !$delivery->ban
        ]);

        return redirect()->back()->with('success', __('dashboard.status-change'));
    }

    public function withdrawRequests()
    {
        return view('dashboard.delivery-withdraw-requests.index');
    }
}
