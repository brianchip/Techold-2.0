<!-- Sidebar -->
<div class="bg-white w-64 shadow-xl flex flex-col">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-gray-800">Donezo</span>
        </div>
    </div>

    <!-- Search -->
    <div class="p-4 border-b border-gray-200">
        <div class="relative">
            <input type="text" placeholder="Search task" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            <span class="absolute right-3 top-2 text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">⌘F</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 p-4">
        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">MENU</h3>
            <nav class="space-y-2">
                <a href="/" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'dashboard' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-th-large mr-3 {{ $active === 'dashboard' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'dashboard' ? 'font-medium' : '' }}">Dashboard</span>
                </a>
                <a href="/projects" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'projects' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-project-diagram mr-3 {{ $active === 'projects' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'projects' ? 'font-medium' : '' }}">Projects</span>
                </a>
                <a href="/tasks" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'tasks' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-tasks mr-3 {{ $active === 'tasks' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'tasks' ? 'font-medium' : '' }}">Tasks</span>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">02</span>
                </a>
                <a href="/resources" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'resources' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-users mr-3 {{ $active === 'resources' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'resources' ? 'font-medium' : '' }}">Resources</span>
                </a>
                <a href="/budget" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'budget' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-dollar-sign mr-3 {{ $active === 'budget' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'budget' ? 'font-medium' : '' }}">Budget</span>
                </a>
                <a href="/documents" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'documents' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-file-alt mr-3 {{ $active === 'documents' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'documents' ? 'font-medium' : '' }}">Documents</span>
                </a>
                <a href="/risks" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'risks' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-exclamation-triangle mr-3 {{ $active === 'risks' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'risks' ? 'font-medium' : '' }}">Risks</span>
                </a>
                <a href="/reports" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'reports' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-chart-bar mr-3 {{ $active === 'reports' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'reports' ? 'font-medium' : '' }}">Reports</span>
                </a>
            </nav>
        </div>

        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">GENERAL</h3>
            <nav class="space-y-2">
                <a href="/settings" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg {{ $active === 'settings' ? 'bg-green-50 border-r-2 border-green-500' : '' }}">
                    <i class="fas fa-cog mr-3 {{ $active === 'settings' ? 'text-green-600' : '' }}"></i>
                    <span class="{{ $active === 'settings' ? 'font-medium' : '' }}">Settings</span>
                </a>
                <a href="/help" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg">
                    <i class="fas fa-question-circle mr-3"></i>
                    <span>Help</span>
                </a>
                <a href="/logout" class="sidebar-item flex items-center px-3 py-2 text-gray-700 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Mobile App Download -->
    <div class="p-4 border-t border-gray-200">
        <div class="gradient-dark text-white p-4 rounded-xl">
            <div class="flex items-start mb-3">
                <i class="fas fa-mobile-alt text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-sm">Download our Mobile App</h4>
                    <p class="text-xs text-gray-300 mt-1">Get the app for your mobile device</p>
                </div>
            </div>
            <button class="w-full bg-green-500 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                Download
            </button>
        </div>
    </div>
</div>

