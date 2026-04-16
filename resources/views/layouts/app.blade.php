<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MUSARIS Student Portal')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Manrope:wght@200..800&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-900 text-white min-h-screen transition-all duration-300 flex-shrink-0 flex flex-col">
            <!-- Fixed Profile Section -->
            <div class="p-4 lg:p-6 border-b border-gray-800 flex-shrink-0">
                <!-- Profile Section -->
                <div class="text-center">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gray-700 rounded-full mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-10 h-10 lg:w-12 lg:h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-xs lg:text-sm font-medium truncate px-2">Payment System</p>
                    <p class="text-xs lg:text-sm text-gray-400">Administrator</p>
                </div>
            </div>

            <!-- Scrollable Navigation Menu -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-6 pt-0">
                <!-- Navigation Menu -->
                <nav class="space-y-1">
                    <!-- Dashboard Section -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">Dashboard</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 bg-green-700 rounded-lg hover:bg-green-600 transition-colors">
                            <span class="text-lg">Overview</span>
                            <span class="text-xs lg:text-sm truncate">Main Dashboard</span>
                        </a>
                        <a href="{{ route('dashboard.analytics') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <span class="text-lg">Analytics</span>
                            <span class="text-xs lg:text-sm truncate">Data Analysis</span>
                        </a>
                    </div>

                    <!-- CRM Section -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">CRM</div>
                        <a href="{{ route('customers') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <span class="text-lg">Customers</span>
                            <span class="text-xs lg:text-sm truncate">Client Management</span>
                        </a>
                    </div>

                    <!-- Payments Dropdown -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">Payments</div>
                        <div class="relative">
                            <button onclick="toggleDropdown('paymentsDropdown')" class="w-full flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                                <span class="text-lg">Payments</span>
                                <span class="text-xs lg:text-sm truncate">Payment Center</span>
                                <svg class="w-4 h-4 ml-auto transition-transform" id="paymentsDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="paymentsDropdown" class="hidden mt-1 space-y-1">
                                <a href="{{ route('payments.all') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">All Transactions</span>
                                </a>
                                <a href="{{ route('payments.ussd') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">USSD</span>
                                </a>
                                <a href="{{ route('payments.card') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Card</span>
                                </a>
                                <a href="{{ route('payments.status') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Status</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- BillPay Dropdown -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">BillPay</div>
                        <div class="relative">
                            <button onclick="toggleDropdown('billpayDropdown')" class="w-full flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                                <span class="text-lg">BillPay</span>
                                <span class="text-xs lg:text-sm truncate">Bill Management</span>
                                <svg class="w-4 h-4 ml-auto transition-transform" id="billpayDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="billpayDropdown" class="hidden mt-1 space-y-1">
                                <a href="{{ route('billpay.control') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Control Numbers</span>
                                </a>
                                <a href="{{ route('billpay.customer') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Customer Bills</span>
                                </a>
                                <a href="{{ route('billpay.bulk') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Bulk Generation</span>
                                </a>
                                <a href="{{ route('billpay.status') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">System Status</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Dropdown -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">Transactions</div>
                        <div class="relative">
                            <button onclick="toggleDropdown('transactionsDropdown')" class="w-full flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                                <span class="text-lg">Transactions</span>
                                <span class="text-xs lg:text-sm truncate">History</span>
                                <svg class="w-4 h-4 ml-auto transition-transform" id="transactionsDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="transactionsDropdown" class="hidden mt-1 space-y-1">
                                <a href="{{ route('transactions.all') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">All Records</span>
                                </a>
                                <a href="{{ route('transactions.payment-history') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Payment Records</span>
                                </a>
                                <a href="{{ route('transactions.payout-history') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Payout Records</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Finance Dropdown -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">Finance</div>
                        <div class="relative">
                            <button onclick="toggleDropdown('financeDropdown')" class="w-full flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                                <span class="text-lg">Finance</span>
                                <span class="text-xs lg:text-sm truncate">Reports</span>
                                <svg class="w-4 h-4 ml-auto transition-transform" id="financeDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="financeDropdown" class="hidden mt-1 space-y-1">
                                <a href="{{ route('finance.revenue') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Revenue</span>
                                </a>
                                <a href="{{ route('finance.payout') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Payouts</span>
                                </a>
                                <a href="{{ route('finance.balance') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Balance</span>
                                </a>
                                <a href="{{ route('finance.statement') }}" class="flex items-center space-x-3 px-3 lg:px-6 py-2 lg:py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                    <span class="text-sm">Statement</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Account Section -->
                    <div class="mb-2">
                        <div class="px-3 lg:px-4 py-2 text-xs lg:text-sm font-semibold text-gray-400 uppercase tracking-wider">Account</div>
                        <a href="{{ route('account.profile') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <span class="text-lg">👤</span>
                            <span class="text-xs lg:text-sm truncate">My Profile</span>
                        </a>
                        <a href="{{ route('account.security') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <span class="text-lg">🔒</span>
                            <span class="text-xs lg:text-sm truncate">Security</span>
                        </a>
                        <a href="{{ route('account.logout') }}" class="flex items-center space-x-3 px-3 lg:px-4 py-2 lg:py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <span class="text-lg">🚪</span>
                            <span class="text-xs lg:text-sm truncate">Logout</span>
                        </a>
                    </div>
                    
                    <!-- API Status Indicator -->
                    <div class="mb-4 p-3 bg-gray-800 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div id="apiStatus" class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-sm text-green-400 font-medium">API Connected</span>
                            </div>
                            <button onclick="checkApiStatus()" class="text-gray-400 hover:text-white text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v1a1 1 0 00-1 1h10a1 1 0 001 1v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="apiStatusText" class="text-xs text-gray-400">Last checked: Never</div>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="bg-green-900 text-white shadow-lg flex-shrink-0">
                <!-- Top Bar - Mobile Optimized -->
                <div class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4">
                    <div class="flex items-center justify-between">
                        <!-- Left Section - Menu + Title -->
                        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 flex-1 min-w-0">
                            <button id="menuToggle" class="text-white hover:bg-green-800 p-2 rounded transition-colors flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-green-600">
                                <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            <div class="flex-1 min-w-0">
                                <h1 class="text-sm sm:text-base lg:text-xl font-semibold truncate">Welcome to Payment System</h1>
                            </div>
                        </div>
                        
                        <!-- Right Section - Professional Elements -->
                        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 ml-2 sm:ml-4">
                            <!-- Search Button -->
                            <button class="text-white hover:bg-green-800 p-2 rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <!-- Notifications Button -->
                            <button class="relative text-white hover:bg-green-800 p-2 rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <!-- Notification Badge -->
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            </button>

                            <!-- Settings Button -->
                            <button class="text-white hover:bg-green-800 p-2 rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>

                            <!-- Profile Button -->
                            <div class="relative">
                                <button onclick="toggleProfileDropdown()" class="flex items-center space-x-2 text-white hover:bg-green-800 px-3 sm:px-4 py-2 rounded-lg transition-colors flex-shrink-0 border border-green-700">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 bg-green-700 rounded-full flex items-center justify-center">
                                        <span class="text-xs sm:text-sm font-medium">PS</span>
                                    </div>
                                    <span class="hidden lg:block text-sm font-medium">Payment System</span>
                                    <svg class="w-4 h-4 hidden lg:block transition-transform duration-200" id="profileDropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Profile Dropdown Menu -->
                                <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                    <div class="py-1">
                                        <a href="{{ route('account.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Profile
                                        </a>
                                        <a href="{{ route('account.security') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Security
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <a href="{{ route('account.logout') }}" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>

                                                    </div>
                    </div>
                </div>

                <!-- User Info Bar - Collapsible on Mobile -->
                <div class="bg-green-800 px-3 sm:px-4 lg:px-6 py-2 sm:py-2 lg:py-3 border-t border-green-700">
                    <div class="flex items-center justify-between">
                        <!-- User Info -->
                        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 flex-1 min-w-0">
                            <!-- Profile Icon for Mobile -->
                            <div class="sm:hidden w-8 h-8 bg-green-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            
                                                    </div>
                        
                        <!-- Mobile Menu Toggle for User Actions -->
                        <button class="sm:hidden text-green-200 hover:text-white p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-3 lg:p-6 overflow-auto bg-gray-50">
                @yield('content')
            </main>

            </div>
    </div>

    <!-- Footer - Only under content area, not sidebar -->
    <footer class="bg-green-900 text-white shadow-lg flex-shrink-0 lg:ml-64" id="mainFooter">
        <div class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4">
            <div class="flex items-center justify-between">
                <!-- Left Section - Copyright -->
                <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 flex-1 min-w-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-xs lg:text-sm truncate">&copy; {{ date('Y') }} MUSARIS Payment System. All rights reserved.</p>
                    </div>
                </div>
                
                <!-- Right Section - Links + Status -->
                <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 ml-2 sm:ml-4">
                    <!-- Footer Links - Hidden on very small screens -->
                    <div class="hidden sm:flex items-center space-x-3 text-xs sm:text-xs lg:text-sm">
                        <a href="#" class="hover:text-green-200 transition-colors">Privacy</a>
                        <a href="#" class="hover:text-green-200 transition-colors">Terms</a>
                        <a href="#" class="hover:text-green-200 transition-colors">Support</a>
                    </div>
                    
                    <!-- System Status -->
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-xs sm:text-xs lg:text-sm hidden sm:inline">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const footer = document.getElementById('mainFooter');
            sidebar.classList.toggle('-translate-x-full');
            
            // Adjust footer margin on mobile when sidebar toggles
            if (window.innerWidth < 1024) {
                if (sidebar.classList.contains('-translate-x-full')) {
                    footer.style.marginLeft = '0';
                } else {
                    footer.style.marginLeft = '16rem';
                }
            }
        });

        // Dropdown toggle functionality
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(dropdownId + 'Icon');
            
            // Close all other dropdowns
            const allDropdowns = ['paymentsDropdown', 'billpayDropdown', 'transactionsDropdown', 'financeDropdown'];
            allDropdowns.forEach(id => {
                if (id !== dropdownId) {
                    document.getElementById(id).classList.add('hidden');
                    document.getElementById(id + 'Icon').classList.remove('rotate-180');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Profile dropdown toggle functionality - Make globally accessible
        window.toggleProfileDropdown = function() {
            const dropdown = document.getElementById('profileDropdown');
            const arrow = document.getElementById('profileDropdownArrow');
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
            
            // Rotate arrow
            if (dropdown.classList.contains('hidden')) {
                arrow.classList.remove('rotate-180');
            } else {
                arrow.classList.add('rotate-180');
            }
        };

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                const allDropdowns = ['paymentsDropdown', 'billpayDropdown', 'transactionsDropdown', 'financeDropdown'];
                allDropdowns.forEach(id => {
                    document.getElementById(id).classList.add('hidden');
                    document.getElementById(id + 'Icon').classList.remove('rotate-180');
                });
                
                // Close profile dropdown
                const profileDropdown = document.getElementById('profileDropdown');
                const profileArrow = document.getElementById('profileDropdownArrow');
                if (profileDropdown) {
                    profileDropdown.classList.add('hidden');
                }
                if (profileArrow) {
                    profileArrow.classList.remove('rotate-180');
                }
            }
        });

        // API Status Check Function
        function checkApiStatus() {
            const statusElement = document.getElementById('apiStatus');
            const statusTextElement = document.getElementById('apiStatusText');
            
            // Show loading state
            statusElement.className = 'w-3 h-3 rounded-full bg-yellow-500 animate-pulse';
            statusTextElement.textContent = 'Checking...';
            statusTextElement.className = 'text-xs text-yellow-600';
            
            fetch('/api/status-check')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusElement.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
                        statusTextElement.textContent = 'API Connected';
                        statusTextElement.className = 'text-xs text-green-400';
                        
                        // Update last checked time
                        localStorage.setItem('lastApiCheck', new Date().toISOString());
                    } else {
                        statusElement.className = 'w-3 h-3 rounded-full bg-red-500 animate-pulse';
                        statusTextElement.textContent = 'API Error';
                        statusTextElement.className = 'text-xs text-red-600';
                    }
                })
                .catch(error => {
                    statusElement.className = 'w-3 h-3 rounded-full bg-red-500 animate-pulse';
                    statusTextElement.textContent = 'Connection Failed';
                    statusTextElement.className = 'text-xs text-red-600';
                });
        }

        // Auto-check API status every 30 seconds
        setInterval(checkApiStatus, 30000);

        // Show last check time on load
        document.addEventListener('DOMContentLoaded', function() {
            const lastCheck = localStorage.getItem('lastApiCheck');
            const statusTextElement = document.getElementById('apiStatusText');
            if (lastCheck && statusTextElement) {
                const lastCheckDate = new Date(lastCheck);
                const now = new Date();
                const diffMinutes = Math.floor((now - lastCheckDate) / 60000);
                
                if (diffMinutes > 1) {
                    statusTextElement.textContent = `Last checked: ${diffMinutes} min ago`;
                } else {
                    statusTextElement.textContent = 'Last checked: Just now';
                }
            }
        });
    </script>
</body>
</html>
