<div>
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
    <form wire:submit='packData' name="{{ $formData['form']['name'] }}" id="{{ $formData['form']['id'] }}">
        <x-honeypot />
        @foreach ($formData['elements'] as $field)
            @if (in_array($field['type'], $allowedField) && $field['name'] !== 'newsletter_ids')
                @if ($field['name'] !== 'consenso_privacy')
                    <x-dynamic-component :component="$this->getComponentName($field['type'])" :data="$field" />
                @else
                    <x-dynamic-component :component="$this->getComponentName($field['type'])" :data="$field" :additionalLabel="$privacyPolicy" />
                @endif
            @endif
        @endforeach
        <button type="submit">ciao</button>
    </form>
</div>
