@extends('layouts.app')

@section('title', 'Security Settings - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Security</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Security Settings</h1>
                <p class="text-gray-600 mt-1">Manage your account security and authentication</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Update Security Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Password Change -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div class="flex items-end">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Update Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Two-Factor Authentication -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Two-Factor Authentication</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Enable 2FA</h4>
                    <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                </div>
                <input type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
            </div>
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Authentication Method</p>
                        <p class="text-sm text-gray-500">SMS: +255 712 345 678</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Sessions -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Active Sessions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Windows PC - Chrome</p>
                            <p class="text-xs text-gray-500">192.168.1.100 - Dar es Salaam, Tanzania</p>
                            <p class="text-xs text-gray-500">Last active: 2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Current</span>
                        <button class="text-red-600 hover:text-red-900 text-sm">Revoke</button>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">iPhone - Safari</p>
                            <p class="text-xs text-gray-500">192.168.1.101 - Dar es Salaam, Tanzania</p>
                            <p class="text-xs text-gray-500">Last active: 1 hour ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="text-red-600 hover:text-red-900 text-sm">Revoke</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Log -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Security Log</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Successful login from Windows PC</p>
                        <p class="text-xs text-gray-500">Today, 9:30 AM - 192.168.1.100</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Failed login attempt from unknown device</p>
                        <p class="text-xs text-gray-500">Yesterday, 11:45 PM - 192.168.1.200</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Password changed successfully</p>
                        <p class="text-xs text-gray-500">April 14, 2025, 3:20 PM - 192.168.1.100</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
