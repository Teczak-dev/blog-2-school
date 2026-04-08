<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Email Verification Status -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Email Verification Status') }}
                            </h2>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div>
                                <x-input-label for="email_status" :value="__('Email Address')" />
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ auth()->user()->email }}</span>
                                    @if (auth()->user()->hasVerifiedEmail())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Zweryfikowany
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Niezweryfikowany
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if (auth()->user()->hasVerifiedEmail())
                                <div class="p-4 bg-green-50 border border-green-200 rounded-md">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-green-800">Email zweryfikowany</h3>
                                            <p class="mt-1 text-sm text-green-700">
                                                Twój adres email został pomyślnie zweryfikowany 
                                                {{ auth()->user()->email_verified_at->format('d.m.Y o H:i') }}.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="ml-3 flex-1">
                                            <h3 class="text-sm font-medium text-yellow-800">Wymagana weryfikacja email</h3>
                                            <p class="mt-1 text-sm text-yellow-700">
                                                Aby w pełni korzystać z funkcji serwisu, zweryfikuj swój adres email. 
                                                Sprawdź swoją skrzynkę odbiorczą i kliknij link weryfikacyjny.
                                            </p>
                                            <div class="mt-3">
                                                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition ease-in-out duration-150">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        Wyślij ponownie email weryfikacyjny
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('status') === 'verification-link-sent')
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800">Email wysłany!</h3>
                                            <p class="mt-1 text-sm text-blue-700">
                                                Nowy link weryfikacyjny został wysłany na Twój adres email.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
