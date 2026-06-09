<?php

namespace App\Livewire\Dashboard\Settings\Banners;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class BannerData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];

    public function updateStatus($itemId, $newStatus)
    {
        $item = Banner::find($itemId);
        if (!$item) return;
        $item->status = $newStatus;
        $item->save();
        $this->dispatch('StatusUpdateMS');
    }

    public function deleteItem($id)
    {
        $item = Banner::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
        if (!$item) return;
    }


    public function render()
    {
        $data = Banner::latest()
            ->paginate(10);
        return view('dashboard.settings.banners.banner-data', compact('data'));
    }
}
