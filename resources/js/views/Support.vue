<template>
  <div class="px-6 py-6">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Internal Support Center</h1>
          <p class="text-sm text-gray-600 mt-1">Manage internal tickets and department support requests</p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="showDepartmentModal = true"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Manage Departments</span>
          </button>
          <button
            @click="showCreateTicket = true"
            class="px-4 py-2 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2"
            style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%)"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create Ticket</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Tickets</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ statistics.total }}</p>
          </div>
          <div class="p-3 rounded-full" style="background: rgba(90, 17, 136, 0.1)">
            <svg class="w-6 h-6" style="color: #5A1188" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm font-medium text-green-600">+12%</span>
          <span class="text-sm text-gray-500 ml-2">from last month</span>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Open Tickets</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ statistics.open }}</p>
          </div>
          <div class="p-3 bg-orange-100 rounded-full">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm font-medium text-orange-600">{{ Math.round((statistics.open / statistics.total) * 100) }}%</span>
          <span class="text-sm text-gray-500 ml-2">of total tickets</span>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">In Progress</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ statistics.inProgress }}</p>
          </div>
          <div class="p-3 bg-blue-100 rounded-full">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm font-medium text-blue-600">{{ Math.round((statistics.inProgress / statistics.total) * 100) }}%</span>
          <span class="text-sm text-gray-500 ml-2">being worked on</span>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Resolved Today</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ statistics.resolvedToday }}</p>
          </div>
          <div class="p-3 bg-green-100 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm font-medium text-green-600">+8</span>
          <span class="text-sm text-gray-500 ml-2">since yesterday</span>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
          <div class="relative">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search tickets..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
              style="--focus-ring-color: rgba(90, 17, 136, 0.2)"
            >
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>

        <!-- Status Filter -->
        <div>
          <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200">
            <option value="">All Statuses</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="waiting_user">Waiting for User</option>
            <option value="waiting_department">Waiting for Department</option>
            <option value="resolved">Resolved</option>
            <option value="closed">Closed</option>
          </select>
        </div>

        <!-- Priority Filter -->
        <div>
          <select v-model="filters.priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200">
            <option value="">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>
        </div>

        <!-- Department Filter -->
        <div>
          <select v-model="filters.department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
          <button
            @click="resetFilters"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Tickets List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Support Tickets</h2>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">{{ filteredTickets.length }} tickets</span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-[#F8F8FC]">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ticket in paginatedTickets" :key="ticket.id" class="hover:bg-gray-50 cursor-pointer" @click="openTicketDetail(ticket)">
              <td class="px-6 py-4">
                <div>
                  <div class="text-sm font-medium text-gray-900">#{{ ticket.id }} - {{ ticket.subject }}</div>
                  <div class="text-sm text-gray-500">{{ ticket.category }}</div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-br from-[#5A1188] to-[#9D38CF] rounded-full flex items-center justify-center">
                    <span class="text-white text-xs font-medium">{{ ticket.requester.name.charAt(0) }}</span>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ ticket.requester.name }}</div>
                    <div class="text-sm text-gray-500">{{ ticket.requester.role }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  {{ getDepartmentName(ticket.departmentId) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="getPriorityClasses(ticket.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusClasses(ticket.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ getStatusText(ticket.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ formatDate(ticket.updatedAt) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center space-x-2">
                  <button
                    @click.stop="openTicketDetail(ticket)"
                    class="p-1 text-gray-400 hover:text-[#5A1188] transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">
            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredTickets.length) }} of {{ filteredTickets.length }} tickets
          </div>
          <div class="flex items-center space-x-2">
            <button
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <span class="px-3 py-1 text-sm font-medium text-gray-700">
              Page {{ currentPage }} of {{ totalPages }}
            </span>
            <button
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Ticket Modal -->
  <div v-if="showCreateTicket" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Create New Support Ticket</h2>
        <button @click="showCreateTicket = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form @submit.prevent="createTicket" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
            <input
              v-model="newTicket.subject"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
              placeholder="Brief description of the issue"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select
              v-model="newTicket.category"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            >
              <option value="">Select Category</option>
              <option value="IT Support">IT Support</option>
              <option value="HR Request">HR Request</option>
              <option value="Equipment">Equipment</option>
              <option value="Billing">Billing</option>
              <option value="Administration">Administration</option>
              <option value="Maintenance">Maintenance</option>
              <option value="Complaint">Complaint</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
            <select
              v-model="newTicket.departmentId"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            >
              <option value="">Select Department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
            <select
              v-model="newTicket.priority"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            >
              <option value="">Select Priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="critical">Critical</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
          <textarea
            v-model="newTicket.description"
            required
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            placeholder="Detailed description of the issue or request"
          ></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showCreateTicket = false"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-6 py-2 text-white rounded-lg transition-colors duration-200"
            style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%)"
          >
            Create Ticket
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Department Management Modal -->
  <div v-if="showDepartmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Manage Departments</h2>
        <button @click="showDepartmentModal = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Add New Department -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Department</h3>
        <form @submit.prevent="addDepartment" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <input
              v-model="newDepartment.name"
              type="text"
              placeholder="Department Name"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            >
          </div>
          <div>
            <input
              v-model="newDepartment.description"
              type="text"
              placeholder="Description"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
            >
          </div>
          <div>
            <button
              type="submit"
              class="w-full px-4 py-2 text-white rounded-lg transition-colors duration-200"
              style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%)"
            >
              Add Department
            </button>
          </div>
        </form>
      </div>

      <!-- Departments List -->
      <div class="space-y-3">
        <h3 class="text-lg font-medium text-gray-900">Current Departments</h3>
        <div v-for="dept in departments" :key="dept.id" class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: dept.color }"></div>
            <div>
              <h4 class="font-medium text-gray-900">{{ dept.name }}</h4>
              <p class="text-sm text-gray-500">{{ dept.description }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button class="p-2 text-gray-400 hover:text-[#5A1188] transition-colors duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button class="p-2 text-gray-400 hover:text-red-500 transition-colors duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ticket Detail Modal -->
  <div v-if="showTicketDetail && selectedTicket" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">#{{ selectedTicket.id }} - {{ selectedTicket.subject }}</h2>
          <div class="flex items-center space-x-3 mt-2">
            <span :class="getPriorityClasses(selectedTicket.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
              {{ selectedTicket.priority.charAt(0).toUpperCase() + selectedTicket.priority.slice(1) }}
            </span>
            <span :class="getStatusClasses(selectedTicket.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
              {{ getStatusText(selectedTicket.status) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
              {{ getDepartmentName(selectedTicket.departmentId) }}
            </span>
          </div>
        </div>
        <button @click="showTicketDetail = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Details -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="font-medium text-gray-900 mb-3">Description</h3>
              <p class="text-gray-700">{{ selectedTicket.description }}</p>
            </div>

            <!-- Comments/Timeline -->
            <div>
              <h3 class="font-medium text-gray-900 mb-4">Activity Timeline</h3>
              <div class="space-y-4">
                <div class="flex items-start space-x-3">
                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                      <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-900">{{ selectedTicket.requester.name }}</span>
                        <span class="text-sm text-gray-500">{{ formatDate(selectedTicket.createdAt) }}</span>
                      </div>
                      <p class="text-gray-700">Created this ticket</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add Comment -->
              <div class="mt-6">
                <form @submit.prevent="addComment" class="space-y-3">
                  <textarea
                    v-model="newComment"
                    rows="3"
                    placeholder="Add a comment or update..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
                  ></textarea>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <label class="flex items-center">
                        <input type="checkbox" v-model="isInternalNote" class="rounded border-gray-300 text-[#5A1188] focus:ring-[#5A1188]">
                        <span class="ml-2 text-sm text-gray-600">Internal note (not visible to requester)</span>
                      </label>
                    </div>
                    <button
                      type="submit"
                      class="px-4 py-2 text-white rounded-lg transition-colors duration-200"
                      style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%)"
                    >
                      Add Comment
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="font-medium text-gray-900 mb-4">Ticket Information</h3>
              <div class="space-y-3">
                <div>
                  <label class="text-sm font-medium text-gray-500">Requester</label>
                  <div class="flex items-center mt-1">
                    <div class="w-6 h-6 bg-gradient-to-br from-[#5A1188] to-[#9D38CF] rounded-full flex items-center justify-center">
                      <span class="text-white text-xs font-medium">{{ selectedTicket.requester.name.charAt(0) }}</span>
                    </div>
                    <div class="ml-2">
                      <div class="text-sm font-medium text-gray-900">{{ selectedTicket.requester.name }}</div>
                      <div class="text-xs text-gray-500">{{ selectedTicket.requester.role }}</div>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-500">Assigned To</label>
                  <div class="mt-1">
                    <span v-if="selectedTicket.assignedTo" class="text-sm text-gray-900">{{ selectedTicket.assignedTo.name }}</span>
                    <span v-else class="text-sm text-gray-500">Unassigned</span>
                  </div>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-500">Created</label>
                  <div class="text-sm text-gray-900 mt-1">{{ formatDate(selectedTicket.createdAt) }}</div>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-500">Last Updated</label>
                  <div class="text-sm text-gray-900 mt-1">{{ formatDate(selectedTicket.updatedAt) }}</div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="font-medium text-gray-900 mb-4">Actions</h3>
              <div class="space-y-2">
                <select
                  v-model="selectedTicket.status"
                  @change="updateTicketStatus"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
                >
                  <option value="open">Open</option>
                  <option value="in_progress">In Progress</option>
                  <option value="waiting_user">Waiting for User</option>
                  <option value="waiting_department">Waiting for Department</option>
                  <option value="resolved">Resolved</option>
                  <option value="closed">Closed</option>
                </select>

                <select
                  v-model="selectedTicket.priority"
                  @change="updateTicketPriority"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
                >
                  <option value="low">Low Priority</option>
                  <option value="medium">Medium Priority</option>
                  <option value="high">High Priority</option>
                  <option value="critical">Critical Priority</option>
                </select>

                <select
                  v-model="selectedTicket.departmentId"
                  @change="updateTicketDepartment"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
                >
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>

                <div class="pt-2 space-y-2">
                  <button class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    Assign to User
                  </button>
                  <button class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    Escalate Ticket
                  </button>
                  <button class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    Delete Ticket
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'Support',
  setup() {
    // Reactive state
    const showCreateTicket = ref(false)
    const showDepartmentModal = ref(false)
    const showTicketDetail = ref(false)
    const selectedTicket = ref(null)
    const currentPage = ref(1)
    const itemsPerPage = ref(10)
    const newComment = ref('')
    const isInternalNote = ref(false)

    // New ticket form
    const newTicket = ref({
      subject: '',
      description: '',
      category: '',
      priority: '',
      departmentId: null
    })

    // New department form
    const newDepartment = ref({
      name: '',
      description: ''
    })

    // Filters
    const filters = ref({
      search: '',
      status: '',
      priority: '',
      department: ''
    })

    // Mock data
    const departments = ref([
      { id: 1, name: 'IT Support', color: '#3B82F6', description: 'Technical support and system issues' },
      { id: 2, name: 'HR Department', color: '#10B981', description: 'Human resources and personnel matters' },
      { id: 3, name: 'Medical', color: '#EF4444', description: 'Medical equipment and clinical support' },
      { id: 4, name: 'Billing', color: '#F59E0B', description: 'Billing inquiries and financial matters' },
      { id: 5, name: 'Administration', color: '#8B5CF6', description: 'General administrative support' }
    ])

    const tickets = ref([
      {
        id: 'T-001',
        subject: 'System login issues after password reset',
        description: 'Users unable to log in after mandatory password reset.',
        category: 'IT Support',
        priority: 'high',
        status: 'open',
        departmentId: 1,
        assignedTo: null,
        requester: { id: 1, name: 'Dr. Sarah Johnson', role: 'Doctor', email: 'sarah.johnson@clinic.com' },
        createdAt: new Date('2024-01-15T09:30:00'),
        updatedAt: new Date('2024-01-15T14:45:00'),
        comments: []
      },
      {
        id: 'T-002',
        subject: 'Request for additional vacation days',
        description: 'Employee requesting 3 additional vacation days for family emergency.',
        category: 'HR Request',
        priority: 'medium',
        status: 'in_progress',
        departmentId: 2,
        assignedTo: { id: 3, name: 'Lisa Chen', role: 'HR Manager' },
        requester: { id: 2, name: 'Mark Rodriguez', role: 'Nurse', email: 'mark.rodriguez@clinic.com' },
        createdAt: new Date('2024-01-14T11:20:00'),
        updatedAt: new Date('2024-01-15T16:30:00'),
        comments: []
      },
      {
        id: 'T-003',
        subject: 'X-Ray machine calibration error',
        description: 'X-Ray machine showing calibration errors and needs immediate attention.',
        category: 'Equipment',
        priority: 'critical',
        status: 'in_progress',
        departmentId: 3,
        assignedTo: { id: 4, name: 'David Kim', role: 'Medical Technician' },
        requester: { id: 5, name: 'Dr. Michael Brown', role: 'Radiologist', email: 'michael.brown@clinic.com' },
        createdAt: new Date('2024-01-15T08:15:00'),
        updatedAt: new Date('2024-01-15T16:00:00'),
        comments: []
      },
      {
        id: 'T-004',
        subject: 'Invoice discrepancy for medical supplies',
        description: 'Discrepancy found in invoice #INV-2024-001 for medical supplies.',
        category: 'Billing',
        priority: 'medium',
        status: 'waiting_department',
        departmentId: 4,
        assignedTo: null,
        requester: { id: 6, name: 'Jennifer Wilson', role: 'Office Manager', email: 'jennifer.wilson@clinic.com' },
        createdAt: new Date('2024-01-13T14:30:00'),
        updatedAt: new Date('2024-01-15T10:20:00'),
        comments: []
      },
      {
        id: 'T-005',
        subject: 'Meeting room booking system not working',
        description: 'Unable to book meeting rooms through the system.',
        category: 'Administration',
        priority: 'low',
        status: 'resolved',
        departmentId: 5,
        assignedTo: { id: 7, name: 'Admin Support', role: 'Administrator' },
        requester: { id: 8, name: 'Dr. Emily Davis', role: 'Doctor', email: 'emily.davis@clinic.com' },
        createdAt: new Date('2024-01-12T16:45:00'),
        updatedAt: new Date('2024-01-15T09:00:00'),
        comments: []
      }
    ])

    // Computed properties
    const statistics = computed(() => ({
      total: tickets.value.length,
      open: tickets.value.filter(t => t.status === 'open').length,
      inProgress: tickets.value.filter(t => t.status === 'in_progress').length,
      resolvedToday: tickets.value.filter(t => {
        const today = new Date()
        const ticketDate = new Date(t.updatedAt)
        return t.status === 'resolved' && 
               ticketDate.toDateString() === today.toDateString()
      }).length
    }))

    const filteredTickets = computed(() => {
      let filtered = tickets.value

      if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        filtered = filtered.filter(ticket => 
          ticket.subject.toLowerCase().includes(search) ||
          ticket.id.toLowerCase().includes(search) ||
          ticket.requester.name.toLowerCase().includes(search)
        )
      }

      if (filters.value.status) {
        filtered = filtered.filter(ticket => ticket.status === filters.value.status)
      }

      if (filters.value.priority) {
        filtered = filtered.filter(ticket => ticket.priority === filters.value.priority)
      }

      if (filters.value.department) {
        filtered = filtered.filter(ticket => ticket.departmentId === parseInt(filters.value.department))
      }

      return filtered
    })

    const totalPages = computed(() => Math.ceil(filteredTickets.value.length / itemsPerPage.value))

    const paginatedTickets = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value
      const end = start + itemsPerPage.value
      return filteredTickets.value.slice(start, end)
    })

    // Methods
    const getDepartmentName = (departmentId) => {
      const dept = departments.value.find(d => d.id === departmentId)
      return dept ? dept.name : 'Unknown'
    }

    const getPriorityClasses = (priority) => {
      const classes = {
        low: 'bg-gray-100 text-gray-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        critical: 'bg-red-100 text-red-800'
      }
      return classes[priority] || classes.medium
    }

    const getStatusClasses = (status) => {
      const classes = {
        open: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-purple-100 text-purple-800',
        waiting_user: 'bg-yellow-100 text-yellow-800',
        waiting_department: 'bg-orange-100 text-orange-800',
        resolved: 'bg-green-100 text-green-800',
        closed: 'bg-gray-100 text-gray-800'
      }
      return classes[status] || classes.open
    }

    const getStatusText = (status) => {
      const texts = {
        open: 'Open',
        in_progress: 'In Progress',
        waiting_user: 'Waiting for User',
        waiting_department: 'Waiting for Department',
        resolved: 'Resolved',
        closed: 'Closed'
      }
      return texts[status] || 'Open'
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const resetFilters = () => {
      filters.value = {
        search: '',
        status: '',
        priority: '',
        department: ''
      }
      currentPage.value = 1
    }

    const openTicketDetail = (ticket) => {
      selectedTicket.value = ticket
      showTicketDetail.value = true
    }

    const createTicket = () => {
      // Generate new ticket ID
      const newId = `T-${String(tickets.value.length + 1).padStart(3, '0')}`
      
      const ticket = {
        id: newId,
        subject: newTicket.value.subject,
        description: newTicket.value.description,
        category: newTicket.value.category,
        priority: newTicket.value.priority,
        status: 'open',
        departmentId: parseInt(newTicket.value.departmentId),
        assignedTo: null,
        requester: { id: 999, name: 'Current User', role: 'Staff', email: 'current.user@clinic.com' },
        createdAt: new Date(),
        updatedAt: new Date(),
        comments: []
      }
      
      tickets.value.unshift(ticket)
      
      // Reset form
      newTicket.value = {
        subject: '',
        description: '',
        category: '',
        priority: '',
        departmentId: null
      }
      
      showCreateTicket.value = false
    }

    const addDepartment = () => {
      const colors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
      const newId = Math.max(...departments.value.map(d => d.id)) + 1
      
      const department = {
        id: newId,
        name: newDepartment.value.name,
        description: newDepartment.value.description,
        color: colors[Math.floor(Math.random() * colors.length)]
      }
      
      departments.value.push(department)
      
      // Reset form
      newDepartment.value = {
        name: '',
        description: ''
      }
    }

    const addComment = () => {
      if (newComment.value.trim() && selectedTicket.value) {
        // Add comment logic here
        console.log('Adding comment:', newComment.value, 'Internal:', isInternalNote.value)
        newComment.value = ''
        isInternalNote.value = false
      }
    }

    const updateTicketStatus = () => {
      if (selectedTicket.value) {
        selectedTicket.value.updatedAt = new Date()
        console.log('Status updated to:', selectedTicket.value.status)
      }
    }

    const updateTicketPriority = () => {
      if (selectedTicket.value) {
        selectedTicket.value.updatedAt = new Date()
        console.log('Priority updated to:', selectedTicket.value.priority)
      }
    }

    const updateTicketDepartment = () => {
      if (selectedTicket.value) {
        selectedTicket.value.updatedAt = new Date()
        console.log('Department updated to:', selectedTicket.value.departmentId)
      }
    }

    onMounted(() => {
      // Initialize component
    })

    return {
      // State
      showCreateTicket,
      showDepartmentModal,
      showTicketDetail,
      selectedTicket,
      currentPage,
      itemsPerPage,
      filters,
      departments,
      tickets,
      newTicket,
      newDepartment,
      newComment,
      isInternalNote,
      
      // Computed
      statistics,
      filteredTickets,
      totalPages,
      paginatedTickets,
      
      // Methods
      getDepartmentName,
      getPriorityClasses,
      getStatusClasses,
      getStatusText,
      formatDate,
      resetFilters,
      openTicketDetail,
      createTicket,
      addDepartment,
      addComment,
      updateTicketStatus,
      updateTicketPriority,
      updateTicketDepartment
    }
  }
}
</script>