<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Donezo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'settings'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                        <p class="text-gray-600 text-sm">Manage system preferences and configurations</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Settings Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Settings Navigation -->
                    <div class="bg-white rounded-xl shadow-lg mb-8">
                        <div class="border-b border-gray-200">
                            <nav class="flex space-x-8 px-6">
                                <button class="py-4 px-1 border-b-2 border-green-500 font-medium text-sm text-green-600">
                                    General
                                </button>
                                <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Account
                                </button>
                                <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Security
                                </button>
                                <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Notifications
                                </button>
                                <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Integrations
                                </button>
                            </nav>
                        </div>

                        <!-- General Settings -->
                        <div class="p-8">
                            <div class="space-y-8">
                                <!-- Company Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                            <input type="text" value="Techold Engineering" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option>Engineering & Construction</option>
                                                <option>Software Development</option>
                                                <option>Manufacturing</option>
                                                <option>Consulting</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option>UTC-05:00 (Eastern Time)</option>
                                                <option>UTC-08:00 (Pacific Time)</option>
                                                <option>UTC+00:00 (GMT)</option>
                                                <option>UTC+01:00 (Central European Time)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option>USD - US Dollar</option>
                                                <option>EUR - Euro</option>
                                                <option>GBP - British Pound</option>
                                                <option>CAD - Canadian Dollar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Project Defaults -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Defaults</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Project Status</label>
                                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option>Planning</option>
                                                <option>In Progress</option>
                                                <option>On Hold</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Task Priority</label>
                                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option>Medium</option>
                                                <option>Low</option>
                                                <option>High</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Working Hours per Day</label>
                                            <input type="number" value="8" min="1" max="24" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Working Days per Week</label>
                                            <input type="number" value="5" min="1" max="7" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- System Preferences -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Preferences</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Auto-save Changes</h4>
                                                <p class="text-sm text-gray-600">Automatically save form changes</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" checked class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Dark Mode</h4>
                                                <p class="text-sm text-gray-600">Use dark theme for the interface</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Email Notifications</h4>
                                                <p class="text-sm text-gray-600">Receive email updates for important events</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" checked class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Advanced Features</h4>
                                                <p class="text-sm text-gray-600">Enable experimental features and beta functionality</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Management -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Management</h3>
                                    <div class="bg-gray-50 rounded-lg p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-2">Backup & Export</h4>
                                                <p class="text-sm text-gray-600 mb-4">Download a complete backup of your data</p>
                                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-download mr-2"></i>
                                                    Export Data
                                                </button>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-2">Data Retention</h4>
                                                <p class="text-sm text-gray-600 mb-4">Configure how long data is kept</p>
                                                <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    <option>Keep forever</option>
                                                    <option>1 year</option>
                                                    <option>2 years</option>
                                                    <option>5 years</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end pt-6 border-t border-gray-200">
                                    <button class="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

