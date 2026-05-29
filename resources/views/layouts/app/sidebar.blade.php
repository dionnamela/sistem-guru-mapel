<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    <flux:sidebar
        sticky
        collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

        {{-- Header --}}
        <flux:sidebar.header>

            <x-app-logo
                :sidebar="true"
                href="{{ route('dashboard') }}"
                wire:navigate />

            <flux:sidebar.collapse class="lg:hidden" />

        </flux:sidebar.header>

        {{-- Navigation --}}
        <flux:sidebar.nav>

            <flux:sidebar.group
                :heading="__('Platform')"
                class="grid">

                {{-- Dashboard --}}
                <flux:sidebar.item
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate>

                    {{ __('Dashboard') }}

                </flux:sidebar.item>

                {{-- Data Siswa --}}
                <flux:sidebar.item
                    icon="users"
                    :href="route('student')"
                    :current="request()->routeIs('student')"
                    wire:navigate>

                    {{ __('Data Siswa') }}

                </flux:sidebar.item>

                {{-- Absen --}}
                <flux:sidebar.item
                    icon="clipboard-document-check"
                    :href="route('attendance')"
                    :current="request()->routeIs('attendance')"
                    wire:navigate>

                    {{ __('Absen') }}

                </flux:sidebar.item>

                {{-- Rekap Absen --}}
                <flux:sidebar.item
                    icon="book-open-text"
                    :href="route('attendance.recap')"
                    :current="request()->routeIs('attendance.recap')"
                    wire:navigate>

                    {{ __('Rekap Absen') }}

                </flux:sidebar.item>

                {{-- Nilai --}}
                <flux:sidebar.item
                    icon="academic-cap"
                    :href="route('scores')"
                    :current="request()->routeIs('scores')"
                    wire:navigate>

                    {{ __('Nilai Siswa') }}

                </flux:sidebar.item>

            </flux:sidebar.group>

        </flux:sidebar.nav>

        <flux:spacer />

        {{-- Footer Menu --}}
        <flux:sidebar.nav>

            <flux:sidebar.item
                icon="folder-git-2"
                href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">

                {{ __('Repository') }}

            </flux:sidebar.item>

            <flux:sidebar.item
                icon="book-open-text"
                href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">

                {{ __('Documentation') }}

            </flux:sidebar.item>

        </flux:sidebar.nav>

        {{-- Desktop User Menu --}}
        <x-desktop-user-menu
            class="hidden lg:block"
            :name="auth()->user()->name" />

    </flux:sidebar>

    {{-- Mobile Header --}}
    <flux:header class="lg:hidden">

        <flux:sidebar.toggle
            class="lg:hidden"
            icon="bars-2"
            inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">

            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down" />

            <flux:menu>

                <flux:menu.radio.group>

                    <div class="p-0 text-sm font-normal">

                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">

                            <flux:avatar
                                :name="auth()->user()->name"
                                :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">

                                <flux:heading class="truncate">
                                    {{ auth()->user()->name }}
                                </flux:heading>

                                <flux:text class="truncate">
                                    {{ auth()->user()->email }}
                                </flux:text>

                            </div>

                        </div>

                    </div>

                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- Settings --}}
                <flux:menu.radio.group>

                    <flux:menu.item
                        :href="route('profile.edit')"
                        icon="cog"
                        wire:navigate>

                        {{ __('Settings') }}

                    </flux:menu.item>

                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="w-full">

                    @csrf

                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer"
                        data-test="logout-button">

                        {{ __('Log out') }}

                    </flux:menu.item>

                </form>

            </flux:menu>

        </flux:dropdown>

    </flux:header>

    {{-- Main Content --}}
    {{ $slot }}

    {{-- Toast --}}
    @persist('toast')

    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>

    @endpersist

    @fluxScripts

</body>

</html>