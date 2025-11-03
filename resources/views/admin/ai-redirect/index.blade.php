@extends('admin.layouts.neo')

@section('title', 'AI Redirect Management - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-route text-white text-xl"></i>
                    </div>
                    AI Redirect Management
                </h1>
                <p class="text-lg text-gray-600 mt-2">Manage AI system redirects and routing</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.ai-redirect.create') }}" class="neo-btn neo-btn neo-btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    New Redirect
                </a>
                <a href="{{ route('admin.ai-redirect.analytics') }}" class="neo-btn neo-btn neo-btn-secondary">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Analytics
                </a>
            </div>
        </div>
    </div>

    <div class="px-6">
        <div class="neo-card p-6">
            <!-- Quick Redirect Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <a href="{{ route('admin.ai-settings.index') }}" class="neo-btn neo-btn neo-btn-primary text-center">
                    <i class="fas fa-cog mr-2"></i>
                    AI Settings
                </a>
                <a href="{{ route('admin.ai.advanced-dashboard') }}" class="neo-btn neo-btn neo-btn-secondary text-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    AI Dashboard
                </a>
                <a href="{{ route('admin.danisman-ai.index') }}" class="neo-btn neo-btn-warning text-center">
                    <i class="fas fa-robot mr-2"></i>
                    Danışman AI
                </a>
                <a href="{{ route('admin.page-analyzer.dashboard') }}" class="neo-btn neo-btn-info text-center">
                    <i class="fas fa-search mr-2"></i>
                    Page Analyzer
                </a>
            </div>

            <!-- Redirect Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="neo-card p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-route text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Redirects</p>
                            <p class="text-2xl font-semibold text-gray-900">1,247</p>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Success Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">99.8%</p>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Avg Response</p>
                            <p class="text-2xl font-semibold text-gray-900">45ms</p>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-star text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Most Used</p>
                            <p class="text-2xl font-semibold text-gray-900">AI Settings</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Redirect Configuration -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Redirect Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Target Route
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage Count
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                AI Settings Redirect
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                admin.ai-settings.index
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                856
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.ai-redirect.edit', 1) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <a href="{{ route('admin.ai-redirect.show', 1) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                AI Dashboard Redirect
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                admin.ai.advanced-dashboard
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                234
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.ai-redirect.edit', 2) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <a href="{{ route('admin.ai-redirect.show', 2) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Danışman AI Redirect
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                admin.danisman-ai.index
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                123
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.ai-redirect.edit', 3) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <a href="{{ route('admin.ai-redirect.show', 3) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Page Analyzer Redirect
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                admin.page-analyzer.dashboard
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                34
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.ai-redirect.edit', 4) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <a href="{{ route('admin.ai-redirect.show', 4) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
