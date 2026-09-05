<x-layouts.sidebar-base title="Panel Admin">
    <x-slot name="sidebarLinks">
        <x-layouts.sidebar-admin />
    </x-slot>

    @if (isset($header))
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endif

    {{ $slot }}
</x-layouts.sidebar-base>
