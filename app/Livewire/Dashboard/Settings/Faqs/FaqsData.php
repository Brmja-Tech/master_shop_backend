<?php

namespace App\Livewire\Dashboard\Settings\Faqs;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class FaqsData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateStatus($itemId, $newStatus)
    {
        $item = Faq::find($itemId);
        if (!$item) return;
        $item->status = $newStatus;
        $item->save();
        $this->dispatch('StatusUpdateMS');
    }

    public function deleteItem($id)
    {
        $item = Faq::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
        if (!$item) return;
    }


    public function render()
    {
        $data = Faq::where(function ($query) {
            $query->where('question', 'like', '%' . $this->search . '%')
                ->orWhere('answer', 'like', '%' . $this->search . '%');
        })
            ->latest()
            ->paginate(10);
        return view('dashboard.settings.faqs.faqs-data', compact('data'));
    }
}
