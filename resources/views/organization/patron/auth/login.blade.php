@extends('organization.patron.disconnected-base')

@section('content')

    <div x-data="{ showLoginForm: true }"
         @@show-login-form.window="showLoginForm = true; "
         class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300 ease-in-out">

        <div x-show="showLoginForm" x-transition
             class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-xl max-w-sm md:max-w-md w-full transition-colors duration-300 ease-in-out">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">Connexion</h2>
            <form action="" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Adresse email:</label>
                    <input type="email" id="email" name="email"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="current-email">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mot de passe:</label>
                    <input type="password" id="password" name="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="current-password">
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-300 ease-in-out">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
