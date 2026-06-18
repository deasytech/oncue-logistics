<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Set up your account')" :description="__('Enter your details to create a login')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required readonly />
        <flux:input wire:model="email" :label="__('Email address')" type="email" required readonly />
        <flux:input wire:model="password" :label="__('Password')" type="password" required viewable />
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
            viewable />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Complete Setup') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already set up your account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
