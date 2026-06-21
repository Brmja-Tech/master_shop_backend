<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\About;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function genralSetting()
    {
        $settings = Setting::first();
        return view('dashboard.settings.index', compact('settings'));
    } //End genralSetting method


    public function deliverySetting()
    {
        return view('dashboard.settings.delivery.index');
    }


    public function aboutSetting()
    {
        $about = About::first();
        return view('dashboard.settings.about.index', compact('about'));
    } //End genralSetting method


    public function faqs()
    {
        return view('dashboard.settings.faqs.index');
    } //End faqs method

    public function storeTypes()
    {
        return view('dashboard.settings.store-types.index');
    }

    public function subcategories()
    {
        return view('dashboard.settings.subcategories.index');
    }



    public function privacy()
    {
        return view('dashboard.settings.privacy.index');
    }

    public function terms()
    {
        return view('dashboard.settings.terms.index');
    }

    public function banners()
    {
        return view('dashboard.settings.banners.index');
    }

    public function withdrawRequests()
    {
        return view('dashboard.withdraw-requests.index');
    }

    public function contacts()
    {
        return view('dashboard.contacts.index');
    }

    public function vendors()
    {
        return view('dashboard.settings.vendors.index', ['is_request_page' => false]);
    }

    public function requests()
    {
        return view('dashboard.settings.vendors.index', ['is_request_page' => true]);
    }

    public function vendorProfile($id)
    {
        $vendor = \App\Models\Vendor::with(['products.images', 'storeType'])->findOrFail($id);
        return view('dashboard.settings.vendors.profile', compact('vendor'));
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->query('status') ?? $request->input('status');

        if (!in_array($status, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'الحالة المرسلة غير صالحة.');
        }

        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->update([
            'approval_status' => $status,
            'is_verified' => $status === 'approved',
        ]);

        $messageKey = $status === 'approved' 
            ? 'dashboard.vendor_approved_successfully' 
            : 'dashboard.vendor_rejected_successfully';

        return redirect()->back()->with('success', __($messageKey));
    }

    public function toggleBan($id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->update([
            'ban' => !$vendor->ban
        ]);

        return redirect()->back()->with('success', __('dashboard.status-change'));
    }
}
