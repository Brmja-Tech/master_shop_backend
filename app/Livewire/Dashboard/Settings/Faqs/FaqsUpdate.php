<?php

namespace App\Livewire\Dashboard\Settings\Faqs;

use App\Models\Faq;
use Livewire\Component;
use CodeZero\UniqueTranslation\UniqueTranslationRule;

class FaqsUpdate extends Component
{
    public $faq, $question_ar, $question_en, $answer_ar, $answer_en, $status = 1;
    protected $listeners = ['faqUpdate'];


    public function faqUpdate($id)
    {
        $this->faq               = Faq::find($id);
        $this->question_ar       = $this->faq->getTranslation('question', 'ar');
        $this->question_en       = $this->faq->getTranslation('question', 'en');
        $this->answer_ar         = $this->faq->getTranslation('answer', 'ar');
        $this->answer_en         = $this->faq->getTranslation('answer', 'en');
        $this->status            = $this->faq->status;
        $this->dispatch('updateModalToggle');
    }


    public function rules()
    {
        return [
            'question_ar' => [
                'required',
                'string',
                'min:5',
                UniqueTranslationRule::for('faqs', 'question')->ignore($this->faq->id),
            ],
            'question_en' => [
                'required',
                'string',
                'min:5',
                UniqueTranslationRule::for('faqs', 'question')->ignore($this->faq->id),
            ],

            'answer_ar'          => 'required|string|min:5|',
            'answer_en'          => 'required|string|min:5|',
            'status'             => 'required|in:0,1',
        ];
    }




    public function submit()
    {
        $data = $this->validate();
        // save data in DB
        $data['status'] = $this->status;
        $data['question']  = [
            'ar' => $this->question_ar,
            'en' => $this->question_en,
        ];
        $data['answer']   = [
            'ar' => $this->answer_ar,
            'en' => $this->answer_en,
        ];
        $this->faq->update($data);
        // Hide modal
        $this->dispatch('faqUpdateMS');
        // Reset form fields
        $this->reset(['question_ar', 'question_en', 'answer_ar', 'answer_en', 'status']);
        // Dispatch events
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(FaqsData::class);
    }
    public function render()
    {
        return view('dashboard.settings.faqs.faqs-update');
    }
}
