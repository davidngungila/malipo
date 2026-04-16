@extends('layouts.app')

@section('title', 'Logout - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-400">Account</span></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Logout</span></li>
            </ol>
        </nav>
    </div>

    <!-- Logout Confirmation -->
    <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Sign Out</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to sign out of your MUSARIS account?</p>
            
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-medium">JD</span>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">John Doe</p>
                            <p class="text-sm text-gray-500">john.doe@musaris.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-center space-x-4">
                    <button onclick="window.location.href='{{ route('dashboard') }}'" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button onclick="performLogout()" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                        Sign Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Options -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Before you go...</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Clear Session Data</h4>
                <p class="text-sm text-gray-500 mb-3">Remove all session data and cookies from this device</p>
                <label class="flex items-center">
                    <input type="checkbox" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Clear session data</span>
                </label>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Remember Me</h4>
                <p class="text-sm text-gray-500 mb-3">Keep me signed in for faster access next time</p>
                <label class="flex items-center">
                    <input type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Security Notice -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-blue-900">Security Notice</h4>
                <p class="text-sm text-blue-700 mt-1">
                    For your security, please make sure you're on a secure network before signing out. 
                    Always sign out when using shared or public devices.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function performLogout() {
    // Show loading state
    const button = event.target;
    button.disabled = true;
    button.innerHTML = 'Signing out...';
    
    // Simulate logout process
    setTimeout(() => {
        // Redirect to welcome page
        window.location.href = '/';
    }, 1500);
}
</script>
@endsection
