@extends('layouts.app')

@section('title', 'User Profile - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Profile</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Profile</h1>
                <p class="text-gray-600 mt-1">Manage your account information and preferences</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-3xl text-gray-600 font-bold">JD</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">John Doe</h3>
                    <p class="text-sm text-gray-500">System Administrator</p>
                    <div class="mt-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Member Since:</span>
                            <span class="text-sm font-medium">Jan 15, 2024</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Last Login:</span>
                            <span class="text-sm font-medium">Today, 9:30 AM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Account ID:</span>
                            <span class="text-sm font-medium">USR001</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" value="John" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" value="Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" value="john.doe@musaris.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" value="+255 712 345 678" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option selected>System Administration</option>
                                <option>Finance</option>
                                <option>Operations</option>
                                <option>Customer Support</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option selected>Administrator</option>
                                <option>Manager</option>
                                <option>Operator</option>
                                <option>Viewer</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Preferences</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                <p class="text-sm text-gray-500">Receive email updates about your account</p>
                            </div>
                            <input type="checkbox" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">SMS Notifications</label>
                                <p class="text-sm text-gray-500">Receive SMS updates for important transactions</p>
                            </div>
                            <input type="checkbox" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                                <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                            </div>
                            <input type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Logged in to the system</p>
                        <p class="text-xs text-gray-500">Today, 9:30 AM</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Generated revenue report</p>
                        <p class="text-xs text-gray-500">Yesterday, 3:45 PM</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Updated customer information</p>
                        <p class="text-xs text-gray-500">April 14, 2025, 11:20 AM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
