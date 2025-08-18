<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Donezo Project Management</title>
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
        @include('partials.sidebar', ['active' => 'documents'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Document Management</h1>
                        <p class="text-gray-600 text-sm">Manage project documents with version control and collaboration</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-upload"></i>
                            <span>Upload Document</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-folder-plus mr-2"></i>
                            New Folder
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Documents Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Document Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-file text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Documents</p>
                                <p class="text-2xl font-bold text-gray-900">1,247</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-folder text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Folders</p>
                                <p class="text-2xl font-bold text-gray-900">89</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Recent Uploads</p>
                                <p class="text-2xl font-bold text-gray-900">24</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i class="fas fa-database text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Storage Used</p>
                                <p class="text-2xl font-bold text-gray-900">2.4 GB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Browser -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <input type="text" placeholder="Search documents..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 w-64">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option>All Types</option>
                                    <option>PDF</option>
                                    <option>DOC/DOCX</option>
                                    <option>XLS/XLSX</option>
                                    <option>Images</option>
                                    <option>CAD Files</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Breadcrumb -->
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <nav class="flex items-center space-x-2 text-sm">
                            <a href="#" class="text-blue-600 hover:text-blue-800">Home</a>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <a href="#" class="text-blue-600 hover:text-blue-800">Projects</a>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <span class="text-gray-600">ERP System</span>
                        </nav>
                    </div>

                    <!-- File Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <!-- Folders -->
                            @for($i = 0; $i < 4; $i++)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-folder text-blue-500 text-2xl mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ ['Specifications', 'Drawings', 'Reports', 'Contracts'][$i] }}</h4>
                                        <p class="text-xs text-gray-500">{{ rand(5, 25) }} files</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                                    <div class="flex space-x-1">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endfor

                            <!-- Files -->
                            @for($i = 0; $i < 12; $i++)
                            @php
                            $fileTypes = [
                                ['ext' => 'pdf', 'icon' => 'fas fa-file-pdf', 'color' => 'text-red-500', 'name' => 'Project Specification.pdf'],
                                ['ext' => 'docx', 'icon' => 'fas fa-file-word', 'color' => 'text-blue-500', 'name' => 'Requirements Document.docx'],
                                ['ext' => 'xlsx', 'icon' => 'fas fa-file-excel', 'color' => 'text-green-500', 'name' => 'Budget Analysis.xlsx'],
                                ['ext' => 'dwg', 'icon' => 'fas fa-file-image', 'color' => 'text-yellow-500', 'name' => 'Floor Plan.dwg'],
                                ['ext' => 'jpg', 'icon' => 'fas fa-file-image', 'color' => 'text-purple-500', 'name' => 'Site Photo.jpg'],
                            ];
                            $file = $fileTypes[rand(0, 4)];
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                                <div class="flex items-center mb-3">
                                    <i class="{{ $file['icon'] }} {{ $file['color'] }} text-2xl mr-3"></i>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $file['name'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ rand(100, 9999) }} KB</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                                    <div class="flex space-x-1">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-download text-xs"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <i class="fas fa-share text-xs"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

