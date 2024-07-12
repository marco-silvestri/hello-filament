<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class NewsletterSubscription extends Component
{
    use UsesSpamProtection;

    public HoneypotData $extraFields;
    public array $formData;
    public array $filledData = [];
    public string $email;
    public bool $isModalOpen = false;
    public array $privacyPolicy = [];

    public function mount()
    {
        $this->extraFields = new HoneypotData();
    }

    public function openModal()
    {
        $this->formData = $this->buildForm();
        $this->privacyPolicy = array_values(array_filter($this->formData['elements'], function($el){
            return $el['type'] === 'html';
        }))[0];
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->reset('formData','privacyPolicy', 'filledData');
        $this->isModalOpen = false;
    }

    public function rules()
    {
        return [
            'filledData.nome' => 'required|min:2', //May God forgive them, coz I wont
            'filledData.cognome' => 'required|min:2', //May God forgive them, coz I wont
            'filledData.email' => 'required|email',
            'filledData.consenso_privacy' => 'required', //May God forgive them, coz I wont
        ];
    }

    protected function buildForm()
    {
        $baseAddress = config('cms.quine_key.base_address');
        $route = config('cms.quine_key.newsletter_form_route');
        $action = config('cms.quine_key.newsletter_action');
        $quineId = config('cms.quine_key.id');
        $apiKey = config('cms.quine_key.api_key');

        if(substr($baseAddress, -1) !== '/')
        {
            $baseAddress = $baseAddress.'/';
        }
        $url = "{$baseAddress}{$route}";

        //remove when up
        // return Cache::rememberForever('user-test', function () use(
        //     $action, $url, $quineId, $apiKey
        // ){
        //     $res = Http::get($url, [
        //         'action' => $action,
        //         'id' => $quineId,
        //         'apikey' => $apiKey,
        //     ]);
        //     return $res->json()['data'];
        // });

        $res = Http::get($url, [
            'action' => $action,
            'id' => $quineId,
            'apikey' => $apiKey,
        ]);

        if(!$res->successful())
        {
            return [];
        }

        return $res->json()['data'];
    }

    public function getInputKey($el)
    {
        return md5(now()->format('Ymdhmis') . rand() . $el);
    }

    public function packData()
    {
        $this->validate();

        $quineUser = array_values(array_filter($this->formData['elements'], function($el){
            return isset($el['id']) && $el['id'] === 'utente_tm';
        }))[0];

        $this->filledData[$quineUser['id']] = $quineUser['value'];

        $this->postData();
    }

    private function postData()
    {
        $res = Http::asForm()->post($this->formData['form']['action'], $this->filledData);

        if($res->successful())
        {
            $this->closeModal();
            session()->flash('subscription-success', 'Success');
        }
            session()->flash('subscription-error', 'Error');
            //$this->redirect('/');
    }

    public function getComponentName($el)
    {
        return "cms.quine-input.__" . $el;
    }

    public function render()
    {
        $allowedField = [
            'text',
            'email',
            'checkbox',
            'select',
        ];

        return view('livewire.cms.newsletter-subscription')
            ->with('allowedField', $allowedField)
            ->with('privacyPolicy', $this->privacyPolicy);
    }
}
