<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">
            {{ __('filament-shlink::filament-shlink.save') }}
        </x-filament::button>
    </form>
</x-filament-panels::page>
