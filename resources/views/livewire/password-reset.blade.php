<!-- resources/views/livewire/reset-password.blade.php -->

<main class="w-full max-w-md mx-auto p-6" id="reset-password-wrapper">
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Reset password</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Remember your password?
                    <a class="text-blue-600 decoration-2 hover:underline font-medium" href="{{ route('login.form') }}">Sign in here</a>
                </p>
            </div>
            <div class="mt-5">
                <form wire:submit.prevent="resetPassword">
                    @csrf
                    <input type="hidden" name="token" wire:model="token">
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                            <div class="relative">
                                <input wire:model="email" type="email" id="email" name="email"
                                       class="py-3 px-4 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"
                                       required>
                            </div>
                            @error('email')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <div class="flex justify-between items-center">
                                <label for="password" class="block text-sm mb-2 dark:text-white">New password</label>
                            </div>
                            <div class="relative">
                                <input wire:model="password" type="password" id="password" name="password"
                                       class="py-3 px-4 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"
                                       required>
                            </div>
                            @error('password')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @if (session()->has('success'))
                        <div class="text-xs text-green-600 mt-2">{{ session('success') }}</div>
                    @endif
                    @error(session()->has('error'))
                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="py-3 px-4 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800">
                        Reset password
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
