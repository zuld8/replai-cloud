<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Track Your Ticket</h1>
                            <p class="text-sm text-gray-500">Monitor ticket progress in real-time</p>
                        </div>
                    </div>
                    <a href="/login" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                        Sign In
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Search Section -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Track Your Support Ticket</h2>
                <p class="text-lg text-gray-600 mb-8">Enter your ticket ID to view the current status and progress</p>
                
                <!-- Search Form -->
                <div class="max-w-2xl mx-auto">
                    <form @submit.prevent="searchTicket" class="relative">
                        <div class="flex gap-3">
                            <div class="flex-1 relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Enter your ticket ID (e.g., TCK-2024-001)"
                                    class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    :class="{ 'border-red-500': error }"
                                />
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                            </div>
                            <button
                                type="submit"
                                :disabled="loading || !searchQuery"
                                class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-105"
                            >
                                <span v-if="!loading">Track</span>
                                <span v-else class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Searching...
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Error Message -->
                    <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 text-red-700">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ error }}</span>
                    </div>
                </div>
            </div>

            <!-- Ticket Details -->
            <div v-if="ticket && !loading" class="space-y-6 animate-fade-in">
                <!-- Ticket Header Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium mb-1">Ticket ID</p>
                                <h3 class="text-2xl font-bold text-white">{{ ticket.ticket_id }}</h3>
                            </div>
                            <div class="text-right">
                                <span 
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
                                    :class="getStatusBadgeClass(ticket.status)"
                                >
                                    {{ ticket.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-8 py-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-4">{{ ticket.subject }}</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Category</p>
                                    <p class="font-semibold text-gray-900">{{ ticket.category?.name || 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Priority</p>
                                    <span 
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                        :class="getPriorityClass(ticket.priority)"
                                    >
                                        {{ ticket.priority }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Created</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(ticket.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="ticket.description" class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-500 mb-2">Description</p>
                            <p class="text-gray-700">{{ ticket.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Progress Timeline
                    </h4>
                    
                    <div class="space-y-4">
                        <div 
                            v-for="(status, index) in statusTimeline" 
                            :key="index"
                            class="flex gap-4"
                        >
                            <div class="flex flex-col items-center">
                                <div 
                                    class="w-12 h-12 rounded-full flex items-center justify-center border-4"
                                    :class="status.active ? 'bg-blue-600 border-blue-200' : 'bg-gray-200 border-gray-300'"
                                >
                                    <svg v-if="status.active" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <div v-else class="w-3 h-3 rounded-full bg-gray-400"></div>
                                </div>
                                <div 
                                    v-if="index < statusTimeline.length - 1"
                                    class="w-1 h-16 mt-2"
                                    :class="status.active ? 'bg-blue-600' : 'bg-gray-300'"
                                ></div>
                            </div>
                            
                            <div class="flex-1 pb-8">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 
                                        class="font-semibold text-lg"
                                        :class="status.active ? 'text-gray-900' : 'text-gray-400'"
                                    >
                                        {{ status.label }}
                                    </h5>
                                    <span 
                                        v-if="status.timestamp"
                                        class="text-sm"
                                        :class="status.active ? 'text-gray-600' : 'text-gray-400'"
                                    >
                                        {{ formatDate(status.timestamp) }}
                                    </span>
                                </div>
                                <p 
                                    class="text-sm"
                                    :class="status.active ? 'text-gray-600' : 'text-gray-400'"
                                >
                                    {{ status.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Agents -->
                <div v-if="ticket.agents && ticket.agents.length > 0" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Assigned Team
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div 
                            v-for="agent in ticket.agents" 
                            :key="agent.id"
                            class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl"
                        >
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ getInitials(agent.name) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ agent.name }}</p>
                                <p class="text-sm text-gray-500">{{ agent.pivot?.role || 'Agent' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div v-if="ticket.notes && ticket.notes.length > 0" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Updates & Notes
                    </h4>
                    
                    <div class="space-y-4">
                        <div 
                            v-for="note in ticket.notes" 
                            :key="note.id"
                            class="border-l-4 border-blue-500 pl-4 py-2"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900">{{ note.user?.name || 'Support Team' }}</p>
                                <span class="text-sm text-gray-500">{{ formatDate(note.created_at) }}</span>
                            </div>
                            <p class="text-gray-700">{{ note.note }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h4 class="text-2xl font-bold mb-2">Need More Help?</h4>
                    <p class="text-blue-100 mb-6">Our support team is here to assist you</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/login" class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                            Sign In to Reply
                        </a>
                        <a href="mailto:support@example.com" class="px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors">
                            Email Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Empty State / How to use -->
            <div v-if="!ticket && !loading && !error" class="mt-12">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">How to Track Your Ticket</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-blue-600">1</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Enter Ticket ID</h4>
                            <p class="text-sm text-gray-600">Type your ticket ID in the search box above</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-purple-600">2</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">View Status</h4>
                            <p class="text-sm text-gray-600">See real-time updates and progress</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-green-600">3</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Stay Updated</h4>
                            <p class="text-sm text-gray-600">Get notified when status changes</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400">&copy; 2024 Support System. All rights reserved.</p>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'TrackTicket',
    data() {
        return {
            searchQuery: '',
            ticket: null,
            loading: false,
            error: null,
        };
    },
    computed: {
        statusTimeline() {
            if (!this.ticket) return [];
            
            const allStatuses = [
                { value: 'Open', label: 'Ticket Created', description: 'Your ticket has been received and is in queue' },
                { value: 'In Progress', label: 'In Progress', description: 'Our team is actively working on your issue' },
                { value: 'Waiting', label: 'Awaiting Response', description: 'Waiting for additional information' },
                { value: 'Resolved', label: 'Resolved', description: 'Issue has been resolved' },
                { value: 'Closed', label: 'Closed', description: 'Ticket has been closed' },
            ];
            
            const currentStatusIndex = allStatuses.findIndex(s => s.value === this.ticket.status);
            
            return allStatuses.map((status, index) => ({
                ...status,
                active: index <= currentStatusIndex,
                timestamp: index <= currentStatusIndex ? this.ticket.updated_at : null,
            }));
        },
    },
    mounted() {
        // Check if ticket_id is in URL params
        const urlParams = new URLSearchParams(window.location.search);
        const ticketId = urlParams.get('ticket_id');
        if (ticketId) {
            this.searchQuery = ticketId;
            this.searchTicket();
        }
    },
    methods: {
        async searchTicket() {
            if (!this.searchQuery.trim()) {
                this.error = 'Please enter a ticket ID';
                return;
            }
            
            this.loading = true;
            this.error = null;
            this.ticket = null;
            
            try {
                const response = await fetch(`/api/public/tickets/${this.searchQuery}`);
                
                if (!response.ok) {
                    if (response.status === 404) {
                        throw new Error('Ticket not found. Please check your ticket ID and try again.');
                    }
                    throw new Error('Failed to fetch ticket. Please try again.');
                }
                
                const data = await response.json();
                this.ticket = data.data;
                
                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('ticket_id', this.searchQuery);
                window.history.pushState({}, '', url);
                
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'Open': 'bg-blue-100 text-blue-800',
                'In Progress': 'bg-yellow-100 text-yellow-800',
                'Waiting': 'bg-orange-100 text-orange-800',
                'Resolved': 'bg-green-100 text-green-800',
                'Closed': 'bg-gray-100 text-gray-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getPriorityClass(priority) {
            const classes = {
                'Low': 'bg-blue-100 text-blue-800',
                'Medium': 'bg-yellow-100 text-yellow-800',
                'High': 'bg-orange-100 text-orange-800',
                'Critical': 'bg-red-100 text-red-800',
            };
            return classes[priority] || 'bg-gray-100 text-gray-800';
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        getInitials(name) {
            if (!name) return '?';
            return name
                .split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        },
    },
};
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
