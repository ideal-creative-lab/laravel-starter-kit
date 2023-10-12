<main class="w-full max-w-md mx-auto p-6" id="login-wrapper">
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Sign in</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account yet?
                    <a class="text-blue-600 decoration-2 hover:under    line font-medium" href="{{ route('register.form') }}">Sign up here</a>
                </p>
            </div>

            <div class="mt-5">
                <form wire:submit.prevent="login">
                    <div class="grid gap-y-4">
                        <!-- Form Group -->
                        <div>
                            <label for="email" class="block text-sm mb-2 dark:text-white">Email address</label>
                            <div class="relative">
                                <input type="email" wire:model="email" id="email" name="email" class="py-3 px-4 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" required>
                            </div>
                            @error('email')
                            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- End Form Group -->

                        <!-- Form Group -->
                        <div>
                            <div class="flex justify-between items-center">
                                <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                                <a class="text-sm text-blue-600 decoration-2 hover:underline font-medium" href="{{ route('password.forget.form') }}">Forgot password?</a>
                            </div>
                            <div class="relative">
                                <input type="password" wire:model="password" id="password" name="password" class="py-3 px-4 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" required>
                            </div>
                            @error('password')
                            <p class="text-xs text-red-600 mt-2" id="password-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- End Form Group -->

                        <!-- Checkbox -->
                        <div class="flex items-center">
                            <div class="flex">
                                <input id="remember" wire:model="remember" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 pointer-events-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                            </div>
                            <div class="ml-3">
                                <label for="remember" class="text-sm dark:text-white">Remember me</label>
                            </div>
                        </div>
                        <!-- End Checkbox -->
                        @if ($error)
                            <p class="text-xs text-red-600 mt-2">{{ $error }}</p>
                        @endif
                        <button type="submit" class="py-3 px-4 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800">
                            Sign in
                        </button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div>
</main>
