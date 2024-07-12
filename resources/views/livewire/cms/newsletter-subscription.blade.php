<div x-data="{ open: $wire.entangle('isModalOpen') }" class="flex flex-col space-y-4">
    <p class="text-sm font-brand">
        Oppure rimani sempre aggiornato in ambito cleaning, iscrivendoti alla nostra newsletter!
    </p>
    <div class="flex justify-start">
        <!-- Trigger -->
        <button wire:click="openModal" type="button"
        class="button__brand">
            {{ __('newsletter.btn-subscribe') }}
        </button>

        @if ($isModalOpen)
            <!-- Modal -->
            <div x-on:keydown.escape.prevent.stop="open = false" role="dialog" aria-modal="true" x-id="['modal-title']"
                :aria-labelledby="$id('modal-title')" class="fixed inset-0 z-10 overflow-y-auto">
                <!-- Overlay -->
                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50"></div>

                <!-- Panel -->
                <div x-show="open" x-transition wire:click="closeModal"
                    class="relative flex items-center justify-center min-h-screen p-4">
                    <div x-on:click.stop x-trap.noscroll.inert="open"
                        class="relative w-full max-w-2xl p-12 overflow-y-auto bg-white shadow-lg rounded-xl">
                        <!-- Title -->
                        <h2 class="mb-4 text-3xl font-bold font-brand" :id="$id('modal-title')">
                            {{ __('newsletter.btn-subscribe') }}
                        </h2>

                        <!-- Content -->
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <form wire:submit='packData' name="{{ $formData['form']['name'] }}"
                            id="{{ $formData['form']['id'] }}">
                            <x-honeypot />
                            <div class="flex flex-col w-full space-y-4">
                                @foreach ($formData['elements'] as $field)
                                @if (in_array($field['type'], $allowedField) && $field['name'] !== 'newsletter_ids')
                                    @if ($field['name'] !== 'consenso_privacy')
                                        <x-dynamic-component :component="$this->getComponentName($field['type'])" :data="$field" />
                                    @else
                                        <x-dynamic-component :component="$this->getComponentName($field['type'])" :data="$field" :additionalLabel="$privacyPolicy" />
                                    @endif
                                @endif
                            @endforeach
                            </div>
                        </form>

                        <!-- Buttons -->
                        <div class="flex mt-8 space-x-2">
                            <button type="submit" for="{{ $formData['form']['id'] }}"
                                class="button__brand">
                                {{ __('newsletter.btn-confirm') }}
                            </button>

                            <button type="button" wire:click="closeModal"
                                class="button__brand--inverted">
                                {{ __('newsletter.btn-cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
