<template>
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0">
                                <i class="bx bx-group me-2 text-primary"></i>Ticket List
                            </h5>
                            <small class="text-muted">{{ $t('total') }}: <strong>{{ totalStores }}</strong> tickets</small>
                        </div>
                        <div class="col-md-8 row">
                            <div class="col-md-3">
                                <label class="form-label small">Start Date</label>
                                <input type="date" class="form-control" v-model="filterStartDate" @change="loadData">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">End Date</label>
                                <input type="date" class="form-control" v-model="filterEndDate" @change="loadData">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Ticket Level</label>
                                <select class="form-select" v-model="filterTicketLevel" @change="loadData">
                                    <option value="">{{ $t('all_levels') }}</option>
                                    <option value="low">{{ $t('level_low') }}</option>
                                    <option value="medium">{{ $t('level_medium') }}</option>
                                    <option value="high">{{ $t('level_high') }}</option>
                                    <option value="urgent">{{ $t('level_urgent') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Human Agent</label>
                                <select class="form-select" v-model="filterHumanAgent" @change="loadData">
                                    <option value="">{{ $t('all_agents') }}</option>
                                    <option v-for="agent in humanAgents" :key="agent.id" :value="agent.id">
                                        {{ agent.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 kanban-container">

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">{{ $t('loading_data') }}</p>
            </div>

            <!-- Kanban Board -->
            <div v-else class="kanban-board">
                <div v-for="label in labels" :key="label.id" class="kanban-column">
                    <!-- Column Header dengan Counter -->
                    <div class="kanban-column-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="label-indicator" :style="{ backgroundColor: label.color }"></span>
                                <h6 class="mb-0 ms-2">{{ label.name }}</h6>
                            </div>
                            <div>
                                <span class="badge bg-primary rounded-pill">
                                    {{ label.loaded_count }} / {{ label.total_stores }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cards Container -->
                    <div :ref="'column-' + label.id" class="kanban-cards" :data-label-id="label.id">
                        <!-- Kontak Cards -->
                        <div v-for="store in label.stores" :key="store.id" class="kanban-card"
                            :data-store-id="store.id">
                            <!-- Card Header -->
                            <div class="card-header-custom">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="ticket-id me-2">#{{ store.ticket_id }}</span>
                                            <span class="badge" :class="getTicketLevelClass(store.ticket_level)">
                                                {{ getTicketLevelText(store.ticket_level) }}
                                            </span>
                                        </div>
                                        <h6 class="store-name mb-1">{{ store.name }}</h6>
                                        <small class="text-muted d-block">{{ store.email }}</small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" @click.prevent="showDetail(store)">
                                                    <i class="bx bx-show me-2"></i>{{ $t('view_detail') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" @click.prevent="editStore(store)">
                                                    <i class="bx bx-edit-alt me-2"></i>{{ $t('edit') }}
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#"
                                                    @click.prevent="confirmDelete(store.id, label.id)">
                                                    <i class="bx bx-trash me-2"></i>{{ $t('delete') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body-custom">
                                <!-- Channel Platform Badge -->
                                <div class="mb-2" v-if="store.from_channel">
                                    <span class="badge" :class="getChannelClass(store.from_channel)">
                                        <i :class="getChannelIcon(store.from_channel)"></i>
                                        {{ getChannelName(store.from_channel) }}
                                    </span>
                                </div>

                                <!-- Phone dengan WhatsApp Link -->
                                <div class="info-item" v-if="store.phone">
                                    <i class="bx bxl-whatsapp text-success"></i>
                                    <a :href="'https://wa.me/' + store.phone.replace(/\D/g, '')" target="_blank"
                                        class="ms-2 text-decoration-none text-success">
                                        {{ store.phone }}
                                    </a>
                                </div>

                                <!-- Category -->
                                <div class="info-item mt-2" v-if="store.category">
                                    <i class="bx bx-category"></i>
                                    <span class="ms-2">{{ store.category.name }}</span>
                                </div>

                                <!-- Status Badge -->
                                <div class="mt-3">
                                    <span class="badge" :class="getStatusClass(store.status)">
                                        {{ getStatusText(store.status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer-custom">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div v-if="store.handled_by" class="small text-muted">
                                        <i class="bx bx-user-circle"></i>
                                        <span class="ms-1">{{ store.handled_by.name }}</span>
                                    </div>
                                    <div v-else class="small text-muted">
                                        <i class="bx bx-user-circle"></i>
                                        <span class="ms-1">{{ $t('not_handled') }}</span>
                                    </div>
                                    <small class="text-muted">{{ store.created_at }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Load More Button -->
                        <div v-if="label.has_more && !loadingMore[label.id]" class="load-more-container">
                            <button @click="loadMore(label.id)" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-down-arrow-circle me-1"></i>
                                {{ $t('load_more') }} ({{ label.total_stores - label.loaded_count }} {{ $t('remaining')
                                }})
                            </button>
                        </div>

                        <!-- Loading More State -->
                        <div v-if="loadingMore[label.id]" class="text-center my-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="small text-muted mt-2">{{ $t('loading_more') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" v-if="selectedStore">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">
                            <i class="bx bx-info-circle me-2"></i>{{ $t('contact_detail') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Basic Info -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2 mb-3">{{ $t('basic_info') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('ticket_id') }}</label>
                                        <p class="mb-0 fw-semibold">#{{ selectedStore.ticket_id }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('ticket_level') }}</label>
                                        <p class="mb-0">
                                            <span class="badge" :class="getTicketLevelClass(selectedStore.ticket_level)">
                                                {{ getTicketLevelText(selectedStore.ticket_level) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('name') }}</label>
                                        <p class="mb-0 fw-semibold">{{ selectedStore.name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('email') }}</label>
                                        <p class="mb-0">{{ selectedStore.email || '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('phone') }}</label>
                                        <p class="mb-0">
                                            <a v-if="selectedStore.phone"
                                                :href="'https://wa.me/' + selectedStore.phone.replace(/\D/g, '')"
                                                target="_blank" class="text-decoration-none text-success">
                                                <i class="bx bxl-whatsapp"></i> {{ selectedStore.phone }}
                                            </a>
                                            <span v-else>-</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('category') }}</label>
                                        <p class="mb-0">
                                            <span v-if="selectedStore.category" class="badge bg-info">
                                                {{ selectedStore.category.name }}
                                            </span>
                                            <span v-else>-</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Channel Info -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2 mb-3">{{ $t('channel_info') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="text-muted small">{{ $t('source_channel') }}</label>
                                        <p class="mb-0">
                                            <span v-if="selectedStore.from_channel" class="badge"
                                                :class="getChannelClass(selectedStore.from_channel)">
                                                <i :class="getChannelIcon(selectedStore.from_channel)"></i>
                                                {{ getChannelName(selectedStore.from_channel) }}
                                            </span>
                                            <span v-else>-</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Assignment -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2 mb-3">{{ $t('status_assignment') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('status') }}</label>
                                        <p class="mb-0">
                                            <span class="badge" :class="getStatusClass(selectedStore.status)">
                                                {{ getStatusText(selectedStore.status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('label') }}</label>
                                        <p class="mb-0">{{ selectedStore.label || '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('handled_by') }}</label>
                                        <p class="mb-0">
                                            <span v-if="selectedStore.handled_by">
                                                <i class="bx bx-user-circle"></i> {{ selectedStore.handled_by.name }}
                                                <small class="text-muted d-block">
                                                    {{ selectedStore.handled_by.assigned_at || '-' }}
                                                </small>
                                            </span>
                                            <span v-else>-</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('resolved_by') }}</label>
                                        <p class="mb-0">
                                            <span v-if="selectedStore.resolved_by">
                                                <i class="bx bx-user-check"></i> {{ selectedStore.resolved_by.name }}
                                                <small class="text-muted d-block">
                                                    {{ selectedStore.resolved_by.resolved_at || '-' }}
                                                </small>
                                            </span>
                                            <span v-else>-</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">{{ $t('timestamps') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('created_at') }}</label>
                                        <p class="mb-0">{{ selectedStore.created_at }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('updated_at') }}</label>
                                        <p class="mb-0">{{ selectedStore.updated_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $t('close') }}
                        </button>
                        <button class="btn btn-primary" @click="editStore(selectedStore)">
                            <i class="bx bx-edit-alt me-1"></i>{{ $t('edit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            labels: [],
            categories: [],
            humanAgents: [],
            loading: true,
            loadingMore: {},
            searchQuery: '',
            filterCategory: '',
            filterStartDate: '',
            filterEndDate: '',
            filterTicketLevel: '',
            filterHumanAgent: '',
            perPage: 20,
            searchTimeout: null,
            selectedStore: null,
            // Mock data
            mockLabels: [
                {
                    id: 1,
                    name: 'New Tickets',
                    color: '#007bff',
                    total_stores: 12,
                    loaded_count: 8,
                    has_more: true,
                    stores: []
                },
                {
                    id: 2,
                    name: 'In Progress',
                    color: '#ffc107',
                    total_stores: 8,
                    loaded_count: 6,
                    has_more: true,
                    stores: []
                },
                {
                    id: 3,
                    name: 'Pending',
                    color: '#6f42c1',
                    total_stores: 5,
                    loaded_count: 4,
                    has_more: true,
                    stores: []
                },
                {
                    id: 4,
                    name: 'Resolved',
                    color: '#28a745',
                    total_stores: 15,
                    loaded_count: 10,
                    has_more: true,
                    stores: []
                }
            ],
            mockCategories: [
                { id: 1, name: 'General Support' },
                { id: 2, name: 'Technical Issue' },
                { id: 3, name: 'Billing' },
                { id: 4, name: 'Sales Inquiry' }
            ],
            mockHumanAgents: [
                { id: 1, name: 'Admin User' },
                { id: 2, name: 'Support Agent' },
                { id: 3, name: 'Senior Agent' },
                { id: 4, name: 'Manager' }
            ],
            mockStores: [
                {
                    id: 1,
                    ticket_id: 'TICKET-20241110-A3F5',
                    name: 'John Doe',
                    email: 'john.doe@example.com',
                    phone: '+6281234567890',
                    status: 'open',
                    ticket_level: 'high',
                    from_channel: 'whatsapp',
                    category: { id: 1, name: 'General Support' },
                    handled_by: { id: 1, name: 'Admin User', assigned_at: '2024-01-15 10:30:00' },
                    resolved_by: null,
                    created_at: '2024-01-15 09:00:00',
                    updated_at: '2024-01-15 10:30:00',
                    label: 'New Tickets'
                },
                {
                    id: 2,
                    ticket_id: 'TICKET-20241110-B7X9',
                    name: 'Jane Smith',
                    email: 'jane.smith@example.com',
                    phone: '+6281234567891',
                    status: 'pending',
                    ticket_level: 'medium',
                    from_channel: 'telegram',
                    category: { id: 2, name: 'Technical Issue' },
                    handled_by: { id: 2, name: 'Support Agent', assigned_at: '2024-01-14 14:20:00' },
                    resolved_by: null,
                    created_at: '2024-01-14 13:00:00',
                    updated_at: '2024-01-14 14:20:00',
                    label: 'In Progress'
                },
                {
                    id: 3,
                    ticket_id: 'TICKET-20241110-C2M1',
                    name: 'Bob Johnson',
                    email: 'bob.johnson@example.com',
                    phone: '+6281234567892',
                    status: 'resolved',
                    ticket_level: 'low',
                    from_channel: 'email',
                    category: { id: 3, name: 'Billing' },
                    handled_by: { id: 1, name: 'Admin User', assigned_at: '2024-01-13 11:00:00' },
                    resolved_by: { id: 1, name: 'Admin User', resolved_at: '2024-01-13 16:30:00' },
                    created_at: '2024-01-13 10:00:00',
                    updated_at: '2024-01-13 16:30:00',
                    label: 'Resolved'
                },
                {
                    id: 4,
                    ticket_id: 'TICKET-20241110-D8K4',
                    name: 'Alice Brown',
                    email: 'alice.brown@example.com',
                    phone: '+6281234567893',
                    status: 'open',
                    ticket_level: 'urgent',
                    from_channel: 'livechat',
                    category: { id: 4, name: 'Sales Inquiry' },
                    handled_by: null,
                    resolved_by: null,
                    created_at: '2024-01-16 08:30:00',
                    updated_at: '2024-01-16 08:30:00',
                    label: 'New Tickets'
                },
                {
                    id: 5,
                    ticket_id: 'TICKET-20241110-E5P7',
                    name: 'Charlie Wilson',
                    email: 'charlie.wilson@example.com',
                    phone: '+6281234567894',
                    status: 'pending',
                    ticket_level: 'medium',
                    from_channel: 'instagram',
                    category: { id: 1, name: 'General Support' },
                    handled_by: { id: 3, name: 'Senior Agent', assigned_at: '2024-01-15 15:00:00' },
                    resolved_by: null,
                    created_at: '2024-01-15 14:00:00',
                    updated_at: '2024-01-15 15:00:00',
                    label: 'Pending'
                }
            ]
        };
    },
    computed: {
        totalStores() {
            return this.labels.reduce((sum, label) => sum + label.total_stores, 0);
        }
    },
    methods: {
        /**
         * Translation helper
         */
        $t(key) {
            // Gunakan window.i18n jika tersedia (dari blade), jika tidak gunakan fallback
            if (window.i18n && window.i18n.translations && window.i18n.translations[key]) {
                return window.i18n.translations[key];
            }
            
            // Fallback translations
            const translations = {
                'contact_list': 'Ticket List',
                'total': 'Total',
                'contact': 'Contact',
                'search_contact': 'Search Contact',
                'all_categories': 'All Categories',
                'all_levels': 'All Levels',
                'all_agents': 'All Agents',
                'level_low': 'Low',
                'level_medium': 'Medium',
                'level_high': 'High',
                'level_urgent': 'Urgent',
                'ticket_id': 'Ticket ID',
                'ticket_level': 'Ticket Level',
                'contact_per_column': 'Contact per Column',
                'loading_data': 'Loading Data...',
                'load_more': 'Load More',
                'remaining': 'Remaining',
                'loading_more': 'Loading More...',
                'view_detail': 'View Detail',
                'edit': 'Edit',
                'delete': 'Delete',
                'not_handled': 'Not Handled',
                'contact_detail': 'Contact Detail',
                'basic_info': 'Basic Information',
                'channel_info': 'Channel Information',
                'status_assignment': 'Status & Assignment',
                'timestamps': 'Timestamps',
                'name': 'Name',
                'email': 'Email',
                'phone': 'Phone',
                'category': 'Category',
                'source_channel': 'Source Channel',
                'status': 'Status',
                'label': 'Label',
                'handled_by': 'Handled By',
                'resolved_by': 'Resolved By',
                'created_at': 'Created At',
                'updated_at': 'Updated At',
                'close': 'Close',
                'status_open': 'Open',
                'status_resolved': 'Resolved',
                'status_pending': 'Pending',
                'status_block': 'Blocked',
                'confirm_delete_title': 'Are you sure?',
                'confirm_delete_text': 'You won\'t be able to revert this!',
                'yes_delete': 'Yes, delete it!',
                'cancel': 'Cancel',
                'contact_deleted': 'Contact has been deleted.',
                'contact_loaded': 'contacts loaded',
                'label_updated': 'Label updated successfully',
                'failed_update_label': 'Failed to update label',
                'failed_delete_contact': 'Failed to delete contact',
                'failed_load_data': 'Failed to load data',
                'error_load_data': 'Error loading data'
            };
            return translations[key] || key;
        },

        /**
         * Get ticket level badge class
         */
        getTicketLevelClass(level) {
            const classes = {
                'low': 'bg-success',
                'medium': 'bg-warning text-dark',
                'high': 'bg-danger',
                'urgent': 'bg-dark'
            };
            return classes[level] || 'bg-secondary';
        },

        /**
         * Get ticket level text
         */
        getTicketLevelText(level) {
            return this.$t('level_' + level) || level;
        },

        /**
         * Show detail modal
         */
        showDetail(store) {
            this.selectedStore = store;
            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();
            });
        },

        /**
         * Edit store (mock function)
         */
        editStore(store) {
            this.$showToast(`Editing ${store.name}`, 'info', 3000);
        },

        /**
         * Get channel badge class
         */
        getChannelClass(channel) {
            const classes = {
                'whatsapp': 'bg-success',
                'wa': 'bg-success',
                'waba': 'bg-success-subtle text-success',
                'telegram': 'bg-info',
                'livechat': 'bg-warning text-dark',
                'instagram': 'bg-danger',
                'facebook': 'bg-primary',
                'email': 'bg-secondary'
            };
            return classes[channel?.toLowerCase()] || 'bg-secondary';
        },

        /**
         * Get channel icon
         */
        getChannelIcon(channel) {
            const icons = {
                'whatsapp': 'bx bxl-whatsapp',
                'wa': 'bx bxl-whatsapp',
                'waba': 'bx bxl-whatsapp',
                'telegram': 'bx bxl-telegram',
                'livechat': 'bx bx-message-square-dots',
                'instagram': 'bx bxl-instagram',
                'facebook': 'bx bxl-facebook',
                'email': 'bx bx-envelope'
            };
            return icons[channel?.toLowerCase()] || 'bx bx-message';
        },

        /**
         * Get channel name
         */
        getChannelName(channel) {
            const names = {
                'whatsapp': 'WhatsApp',
                'wa': 'WhatsApp',
                'waba': 'WhatsApp Business',
                'telegram': 'Telegram',
                'livechat': 'Live Chat',
                'instagram': 'Instagram',
                'facebook': 'Facebook',
                'email': 'Email'
            };
            return names[channel?.toLowerCase()] || channel;
        },

        /**
         * Load initial mock data
         */
        async loadData() {
            this.loading = true;
            
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 1000));

            try {
                // Filter stores based on filters
                let filteredStores = [...this.mockStores];
                
                // Filter by date range
                if (this.filterStartDate) {
                    filteredStores = filteredStores.filter(store => 
                        new Date(store.created_at) >= new Date(this.filterStartDate)
                    );
                }
                
                if (this.filterEndDate) {
                    filteredStores = filteredStores.filter(store => 
                        new Date(store.created_at) <= new Date(this.filterEndDate)
                    );
                }

                // Filter by ticket level
                if (this.filterTicketLevel) {
                    filteredStores = filteredStores.filter(store => 
                        store.ticket_level === this.filterTicketLevel
                    );
                }

                // Filter by human agent
                if (this.filterHumanAgent) {
                    filteredStores = filteredStores.filter(store => 
                        store.handled_by && store.handled_by.id == this.filterHumanAgent
                    );
                }

                // Reset labels
                this.labels = this.mockLabels.map(label => ({
                    ...label,
                    stores: []
                }));

                // Distribute stores to labels
                filteredStores.forEach(store => {
                    const labelName = store.label;
                    const label = this.labels.find(l => l.name === labelName);
                    if (label && label.stores.length < this.perPage) {
                        label.stores.push(store);
                    }
                });

                // Update counts
                this.labels.forEach(label => {
                    const totalInLabel = filteredStores.filter(s => s.label === label.name).length;
                    label.total_stores = totalInLabel;
                    label.loaded_count = label.stores.length;
                    label.has_more = label.loaded_count < label.total_stores;
                });

                this.$showToast('Data loaded successfully', 'success', 2000);
            } catch (error) {
                console.error('Error loading data:', error);
                this.$showToast(this.$t('error_load_data'), 'error', 3000);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load human agents
         */
        async loadHumanAgents() {
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 300));
            this.humanAgents = this.mockHumanAgents;
        },

        /**
         * Load more stores untuk kolom tertentu
         */
        async loadMore(labelId) {
            this.loadingMore[labelId] = true;

            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 500));

            try {
                const label = this.labels.find(l => l.id === labelId);
                const currentCount = label.stores.length;
                const remaining = label.total_stores - currentCount;
                const toLoad = Math.min(remaining, this.perPage);

                // Simulate loading more data
                for (let i = 0; i < toLoad; i++) {
                    const newStore = {
                        ...this.mockStores[currentCount % this.mockStores.length],
                        id: currentCount + i + 100,
                        name: `Additional Contact ${currentCount + i + 1}`,
                        email: `additional${currentCount + i + 1}@example.com`
                    };
                    label.stores.push(newStore);
                }

                label.loaded_count = label.stores.length;
                label.has_more = label.loaded_count < label.total_stores;

                this.$showToast(`${toLoad} ${this.$t('contact_loaded')}`, 'success', 2000);
            } catch (error) {
                console.error('Error loading more:', error);
                this.$showToast(this.$t('failed_load_data'), 'error', 3000);
            } finally {
                this.loadingMore[labelId] = false;
            }
        },

        /**
         * Load categories
         */
        async loadCategories() {
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 300));
            this.categories = this.mockCategories;
        },

        /**
         * Handle drop event (mock)
         */
        async handleDrop(evt) {
            const storeId = evt.item.dataset.storeId;
            const newLabelId = evt.to.dataset.labelId;
            const oldLabelId = evt.from.dataset.labelId;

            if (newLabelId === oldLabelId) {
                return;
            }

            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 300));

                const oldLabel = this.labels.find(l => l.id == oldLabelId);
                const newLabel = this.labels.find(l => l.id == newLabelId);

                if (oldLabel) {
                    oldLabel.total_stores--;
                    oldLabel.loaded_count--;
                }
                if (newLabel) {
                    newLabel.total_stores++;
                    newLabel.loaded_count++;
                }

                this.$showToast(this.$t('label_updated'), 'success', 2000);
            } catch (error) {
                console.error('Error updating label:', error);
                this.$showToast(this.$t('failed_update_label'), 'error', 3000);
            }
        },

        /**
         * Debounce search
         */
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadData();
            }, 500);
        },

        /**
         * Get status badge class
         */
        getStatusClass(status) {
            const classes = {
                'open': 'bg-primary',
                'resolved': 'bg-success',
                'pending': 'bg-warning',
                'block': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        },

        /**
         * Get status text
         */
        getStatusText(status) {
            return this.$t('status_' + status) || status;
        },

        /**
         * Confirm delete with mock SweetAlert
         */
        confirmDelete(storeId, labelId) {
            if (window.Swal) {
                Swal.fire({
                    title: this.$t('confirm_delete_title'),
                    text: this.$t('confirm_delete_text'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: this.$t('yes_delete'),
                    cancelButtonText: this.$t('cancel')
                }).then((result) => {
                    if (result.value) {
                        this.deleteStore(storeId, labelId);
                    }
                });
            } else {
                // Fallback confirm
                if (confirm(this.$t('confirm_delete_text'))) {
                    this.deleteStore(storeId, labelId);
                }
            }
        },

        /**
         * Delete store (mock)
         */
        async deleteStore(storeId, labelId) {
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 300));

                const label = this.labels.find(l => l.id === labelId);
                if (label) {
                    label.total_stores--;
                    label.loaded_count--;
                    label.stores = label.stores.filter(s => s.id !== storeId);
                }

                this.$showToast(this.$t('contact_deleted'), 'success', 2000);
            } catch (error) {
                console.error('Error deleting store:', error);
                this.$showToast(this.$t('failed_delete_contact'), 'error', 3000);
            }
        },

        /**
         * Show toast notification
         */
        $showToast(message, type = 'info', duration = 3000) {
            if (window.toastr) {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    timeOut: duration
                };
                toastr[type](message);
            } else {
                console.log(`[${type.toUpperCase()}] ${message}`);
            }
        }
    },

    mounted() {
        this.loadData();
        this.loadCategories();
        this.loadHumanAgents();
    }
};
</script>

<style scoped>
/* Ticket ID Styling */
.ticket-id {
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.kanban-board {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.kanban-column {
    min-width: 320px;
    max-width: 320px;
    background: #f8f9fa;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
}

.kanban-column-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
    background: white;
    border-radius: 12px 12px 0 0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.label-indicator {
    width: 4px;
    height: 20px;
    border-radius: 2px;
    display: inline-block;
}

.kanban-cards {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    max-height: calc(100vh - 300px);
}

.kanban-card {
    background: white;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
    cursor: move;
}

.kanban-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-header-custom {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.store-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
}

.card-body-custom {
    padding: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6c757d;
}

.info-item i {
    font-size: 1.1rem;
}

.card-footer-custom {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    border-radius: 0 0 8px 8px;
}

.load-more-container {
    margin: 0.75rem 0;
}

.sortable-ghost {
    opacity: 0.4;
    background: #e9ecef;
}

.btn-icon {
    padding: 0.25rem 0.5rem;
    background: transparent;
    border: none;
    color: #6c757d;
}

.btn-icon:hover {
    background: #f8f9fa;
    color: #495057;
}

/* Modal Detail Styling */
.modal-body label {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.modal-body h6 {
    font-weight: 600;
    color: #495057;
}

/* Responsive */
@media (max-width: 768px) {
    .kanban-board {
        flex-direction: column;
    }

    .kanban-column {
        max-width: 100%;
        min-width: 100%;
    }
}
</style>