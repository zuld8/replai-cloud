<template>
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-0">
                                <i class="bx bx-ticket me-2 text-primary"></i>{{ $t('ticket_management') }}
                            </h5>
                            <small class="text-muted">{{ $t('total') }}: <strong>{{ totalTickets }}</strong> {{
                                $t('tickets') }}</small>
                        </div>
                        <div class="col-md-9 d-flex justify-content-end align-items-center gap-2">
                            <!-- Filter Button with Badge -->
                            <button type="button" class="btn btn-outline-secondary position-relative"
                                @click="showFilterModal" style="min-width: 140px;">
                                <i class="bx bx-filter-alt me-1"></i>{{ $t('filters') }}
                                <span v-if="activeFiltersCount > 0"
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ activeFiltersCount }}
                                </span>
                            </button>

                            <!-- Create Ticket Button -->
                            <button type="button" class="btn btn-success" @click="showAddTicketModal"
                                style="min-width: 180px;">
                                <i class="bx bx-plus-circle me-1"></i>{{ $t('add_ticket') }}
                            </button>

                            <!-- Manage Dropdown -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary dropdown-toggle"
                                    data-bs-toggle="dropdown" style="min-width: 140px;">
                                    <i class="bx bx-cog me-1"></i>{{ $t('manage') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-high-z"
                                    style="width: auto; min-width: 0; white-space: nowrap;z-index: 100;">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#"
                                            @click.prevent="showAddLabelModal">
                                            <i class="bx bx-label me-2"></i>
                                            <span class="ms-1">{{ $t('add_label') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#"
                                            @click.prevent="showCategoryManagementModal">
                                            <i class="bx bx-list-ul me-2"></i>
                                            <span class="ms-1">{{ $t('manage_categories') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    <div v-if="activeFiltersCount > 0" class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <small class="text-muted">{{ $t('active_filters') }}:</small>

                                <span v-if="filterStartDate" class="badge bg-info text-dark">
                                    {{ $t('start_date') }}: {{ filterStartDate }}
                                    <i class="bx bx-x cursor-pointer" @click="clearFilter('filterStartDate')"></i>
                                </span>

                                <span v-if="filterEndDate" class="badge bg-info text-dark">
                                    {{ $t('end_date') }}: {{ filterEndDate }}
                                    <i class="bx bx-x cursor-pointer" @click="clearFilter('filterEndDate')"></i>
                                </span>

                                <span v-if="filterTicketLevel" class="badge bg-info text-dark">
                                    {{ $t('level') }}: {{ $t('level_' + filterTicketLevel) }}
                                    <i class="bx bx-x cursor-pointer" @click="clearFilter('filterTicketLevel')"></i>
                                </span>

                                <span v-if="filterCategory" class="badge bg-info text-dark">
                                    {{ $t('category') }}: {{ getCategoryName(filterCategory) }}
                                    <i class="bx bx-x cursor-pointer" @click="clearFilter('filterCategory')"></i>
                                </span>

                                <span v-if="filterHumanAgent" class="badge bg-info text-dark">
                                    {{ $t('agent') }}: {{ getAgentName(filterHumanAgent) }}
                                    <i class="bx bx-x cursor-pointer" @click="clearFilter('filterHumanAgent')"></i>
                                </span>

                                <button type="button" class="btn btn-sm btn-outline-danger" @click="clearAllFilters">
                                    <i class="bx bx-x"></i> {{ $t('clear_all') }}
                                </button>
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
                            <div class="d-flex align-items-center gap-2">
                                <!-- Counter with detail -->
                                <span class="badge rounded-pill" :class="getBadgeClass(label)">
                                    {{ label.loaded_count || 0 }} / {{ label.total_stores || 0 }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                @click.prevent="showEditLabelModal(label)">
                                                <i class="bx bx-edit-alt me-2"></i>{{ $t('edit_label') }}
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                                @click.prevent="confirmDeleteLabel(label.id)">
                                                <i class="bx bx-trash me-2"></i>{{ $t('delete_label') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cards Container -->
                    <div :ref="'column-' + label.id" class="kanban-cards" :data-label-id="label.id">
                        <!-- Empty State -->
                        <div v-if="!label.stores || label.stores.length === 0" class="empty-column-state">
                            <p class="text-muted text-center py-4 mb-0">
                                <i class="bx bx-inbox display-4 d-block mb-2"></i>
                                {{ $t('no_ticket_in_column') }}
                            </p>
                        </div>

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
                                                <a class="dropdown-item" href="#"
                                                    @click.prevent="showEditTicketModal(store)">
                                                    <i class="bx bx-edit-alt me-2"></i>{{ $t('edit') }}
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#"
                                                    @click.prevent="confirmDeleteTicket(store.id, label.id)">
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
                                <!-- Title -->
                                <div class="mb-2">
                                    <strong class="text-primary">{{ store.title }}</strong>
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
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer-custom">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div v-if="store.agents && store.agents.length > 0" class="small text-muted">
                                        <i class="bx bx-user-circle"></i>
                                        <span class="ms-1">
                                            <template v-for="(agent, idx) in store.agents" :key="agent.id">
                                                {{ agent.name }}<span v-if="idx < store.agents.length - 1">, </span>
                                            </template>
                                        </span>
                                    </div>
                                    <div v-else-if="store.handled_by" class="small text-muted">
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

                        <!-- Load More Button - Improved -->
                        <div v-if="hasMoreData(label)" class="load-more-container">
                            <button @click="loadMore(label.id)" class="btn btn-outline-primary btn-sm w-100"
                                :disabled="loadingMore[label.id]">
                                <template v-if="loadingMore[label.id]">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    {{ $t('loading_more') }}
                                </template>
                                <template v-else>
                                    <i class="bx bx-down-arrow-circle me-1"></i>
                                    {{ $t('load_more') }}
                                    <span class="badge bg-primary ms-1">
                                        +{{ getRemainingCount(label) }}
                                    </span>
                                </template>
                            </button>

                            <!-- Progress bar -->
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar" role="progressbar"
                                    :style="{ width: getLoadProgress(label) + '%' }"
                                    :aria-valuenow="getLoadProgress(label)" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <!-- Info text -->
                            <small class="text-muted d-block text-center mt-1">
                                {{ $t('showing') }} {{ label.loaded_count || 0 }} {{ $t('of') }} {{ label.total_stores
                                    || 0 }}
                            </small>
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

        <!-- Add Label Modal -->
        <div class="modal fade" id="addLabelModal" tabindex="-1" aria-labelledby="addLabelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLabelModalLabel">
                            <i class="bx bx-plus-circle me-2"></i>{{ $t('add_label') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="submitLabel">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ $t('label_name') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-label"></i></span>
                                        <input type="text" class="form-control" v-model="newLabel.name"
                                            :placeholder="$t('label_name_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('label_tag') }} <span
                                            class="text-danger">*</span></label>
                                    <small class="d-block text-muted mb-2">{{ $t('label_tag_desc') }}</small>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                        <input type="text" class="form-control" v-model="newLabel.tag"
                                            :placeholder="$t('label_tag_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('label_position') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-sort"></i></span>
                                        <input type="number" class="form-control" v-model="newLabel.position" min="1"
                                            placeholder="1" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('label_color') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" v-model="newLabel.color"
                                            @input="updateColorHex" style="height: 38px; cursor: pointer;" required>
                                        <span class="input-group-text" id="colorHex">{{ newLabel.color }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $t('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"
                                    role="status"></span>
                                <i v-else class="bx bx-save me-1"></i>
                                {{ $t('save_label') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Label Modal -->
        <div class="modal fade" id="editLabelModal" tabindex="-1" aria-labelledby="editLabelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLabelModalLabel">
                            <i class="bx bx-edit-alt me-2"></i>{{ $t('edit_label') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="submitEditLabel">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ $t('label_name') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-label"></i></span>
                                        <input type="text" class="form-control" v-model="editingLabel.name"
                                            :placeholder="$t('label_name_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('label_tag') }} <span
                                            class="text-danger">*</span></label>
                                    <small class="d-block text-muted mb-2">{{ $t('label_tag_desc') }}</small>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                        <input type="text" class="form-control" v-model="editingLabel.tag"
                                            :placeholder="$t('label_tag_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('label_position') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-sort"></i></span>
                                        <input type="number" class="form-control" v-model="editingLabel.position"
                                            min="1" placeholder="1" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('label_color') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" v-model="editingLabel.color"
                                            @input="updateEditColorHex" style="height: 38px; cursor: pointer;" required>
                                        <span class="input-group-text" id="editColorHex">{{ editingLabel.color }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $t('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isEditingLabel">
                                <span v-if="isEditingLabel" class="spinner-border spinner-border-sm me-2"
                                    role="status"></span>
                                <i v-else class="bx bx-save me-1"></i>
                                {{ $t('update_label') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" v-if="selectedStore">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">
                            <i class="bx bx-info-circle me-2"></i>{{ $t('ticket_detail') }}
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
                                            <span class="badge"
                                                :class="getTicketLevelClass(selectedStore.ticket_level)">
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

                            <!-- Status & Assignment -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2 mb-3">{{ $t('status_assignment') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('label') }}</label>
                                        <p class="mb-0">{{ selectedStore.label || '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ $t('handled_by') }}</label>
                                        <p class="mb-0">
                                            <span v-if="selectedStore.agents && selectedStore.agents.length > 0">
                                                <div v-for="agent in selectedStore.agents" :key="agent.id" class="mb-1">
                                                    <i class="bx bx-user-circle"></i> {{ agent.name }}
                                                    <span v-if="agent.pivot && agent.pivot.role"
                                                        class="badge bg-info text-white ms-1"
                                                        style="font-size: 0.7rem;">
                                                        {{ agent.pivot.role }}
                                                    </span>
                                                    <small class="text-muted d-block ms-3">
                                                        {{ agent.pivot?.assigned_at || '-' }}
                                                    </small>
                                                </div>
                                            </span>
                                            <span v-else-if="selectedStore.handled_by">
                                                <i class="bx bx-user-circle"></i> {{ selectedStore.handled_by.name }}
                                                <small class="text-muted d-block">
                                                    {{ selectedStore.handled_by.assigned_at || '-' }}
                                                </small>
                                            </span>
                                            <span v-else>-</span>
                                        </p>
                                    </div>

                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="col-md-12 mb-4">
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

                            <!-- Notes Section -->
                            <div class="col-md-12 mt-4">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-note me-2"></i>{{ $t('notes') || 'Notes' }}
                                </h6>

                                <!-- Add Note Form -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <textarea class="form-control" v-model="newNote"
                                            :placeholder="$t('add_note_placeholder') || 'Add a note...'" rows="2"
                                            @keydown.ctrl.enter="addNote"></textarea>
                                        <button class="btn btn-primary" type="button" @click="addNote"
                                            :disabled="!newNote.trim() || isAddingNote">
                                            <i class="bx bx-send"></i> {{ $t('add') || 'Add' }}
                                        </button>
                                    </div>
                                    <small class="text-muted">{{ $t('ctrl_enter_to_send') || 'Press Ctrl+Enter to send'
                                        }}</small>
                                </div>

                                <!-- Notes Timeline -->
                                <div v-if="ticketNotes && ticketNotes.length > 0" class="notes-timeline">
                                    <div v-for="note in ticketNotes" :key="note.id" class="note-item">
                                        <div class="note-header">
                                            <div class="note-user">
                                                <i class="bx bx-user-circle"></i>
                                                <strong>{{ note.user.name }}</strong>
                                            </div>
                                            <small class="note-time text-muted">
                                                {{ formatDateTime(note.created_at) }}
                                            </small>
                                        </div>
                                        <div class="note-content">
                                            {{ note.note }}
                                        </div>
                                    </div>
                                </div>

                                <!-- No Notes Message -->
                                <div v-else class="text-center py-3 text-muted">
                                    <i class="bx bx-note" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">{{ $t('no_notes_yet') || 'No notes yet. Be the first to add one!' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Ticket Logs / Activity History -->
                            <div class="col-md-12 mt-4"
                                v-if="selectedStore.TicketLogs && selectedStore.TicketLogs.length > 0">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-history me-2"></i>{{ $t('activity_history') || 'Activity History' }}
                                </h6>
                                <div class="timeline">
                                    <div v-for="(log, index) in selectedStore.TicketLogs" :key="log.id || index"
                                        class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="bx bx-check-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="bx bx-label me-1 text-primary"></i>
                                                        <strong>{{ log.label ? log.label.name : $t('label_changed')
                                                            }}</strong>
                                                    </h6>
                                                    <p class="mb-1 text-muted">
                                                        <i class="bx bx-user me-1"></i>
                                                        <span v-if="log.agent">{{ $t('assigned_to') || 'Assigned to' }}:
                                                            <strong>{{ log.agent.name }}</strong></span>
                                                        <span v-else>{{ $t('no_agent_assigned') || 'No agent assigned'
                                                            }}</span>
                                                    </p>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bx bx-time-five me-1"></i>
                                                    {{ formatDate(log.log_time || log.created_at) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- No Logs Message -->
                            <div class="col-md-12" v-else>
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-history me-2"></i>{{ $t('activity_history') || 'Activity History' }}
                                </h6>
                                <div class="text-center py-3 text-muted">
                                    <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">{{ $t('no_activity_logs') || 'No activity logs available' }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $t('close') }}
                        </button>
                        <button class="btn btn-primary" @click="showEditTicketModal(selectedStore)">
                            <i class="bx bx-edit-alt me-1"></i>{{ $t('edit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Ticket Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalLabel">
                            <i class="bx bx-ticket me-2"></i>{{ isEditingTicket ? $t('edit_ticket') : $t('add_ticket')
                            }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="submitTicket" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Contact Selection -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ $t('contact') }} <span
                                            class="text-danger">*</span></label>
                                    <Multiselect v-model="ticketForm.contacts" :options="contacts"
                                        @search-change="loadContacts" :multiple="false" :close-on-select="true"
                                        :clear-on-select="true" :preserve-search="true" :searchable="true"
                                        :internal-search="true" :options-limit="50" :placeholder="'Pilih Kontak'"
                                        open-direction="bottom" label="name" id="id" track-by="name">
                                        <template #tag="{ option, remove }">
                                            <span class="custom__tag"><span>{{ option.name }} - {{ option.phone
                                                    }}</span></span>
                                        </template>

                                    </Multiselect>
                                </div>

                                <!-- Agent Assignment -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ $t('assign_agent') }}</label>
                                    <Multiselect v-model="ticketForm.agents" :options="humanAgents" :multiple="true"
                                        :close-on-select="true" :clear-on-select="true" :preserve-search="true"
                                        :searchable="true" :internal-search="true" :options-limit="50"
                                        :placeholder="'Pilih Agent'" open-direction="bottom" label="name" id="id"
                                        track-by="name">
                                    </Multiselect>
                                </div>

                                <!-- Ticket Name -->
                                <div class="col-12">
                                    <label class="form-label">{{ $t('ticket_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="ticketForm.ticket_name"
                                        :placeholder="$t('ticket_name_placeholder')" required>
                                </div>

                                <!-- Title -->
                                <div class="col-12">
                                    <label class="form-label">{{ $t('title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="ticketForm.title"
                                        :placeholder="$t('title_placeholder')" required>
                                </div>


                                <!-- Category Selection -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('category') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" v-model="ticketForm.category_id" required>
                                        <option value="">{{ $t('select_category') }}</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" v-if="categories.length === 0">
                                        <i class="bx bx-info-circle"></i> No categories available.
                                        <a href="#" @click.prevent="showCategoryModal" class="text-primary">Create
                                            one</a>
                                    </small>
                                    <small class="form-text text-success" v-else>
                                        <i class="bx bx-check-circle"></i> {{ categories.length }} {{ categories.length
                                            === 1 ?
                                            'category' : 'categories' }} available
                                    </small>
                                </div>

                                <!-- Level and Status -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('ticket_level') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" v-model="ticketForm.ticket_level" required>
                                        <option value="low">{{ $t('level_low') }}</option>
                                        <option value="medium">{{ $t('level_medium') }}</option>
                                        <option value="high">{{ $t('level_high') }}</option>
                                        <option value="urgent">{{ $t('level_urgent') }}</option>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <label class="form-label">{{ $t('notes') }}</label>
                                    <textarea class="form-control" v-model="ticketForm.notes" rows="3"
                                        :placeholder="$t('notes_placeholder')"></textarea>
                                </div>

                                <!-- File Upload -->
                                <div class="col-12">
                                    <label class="form-label">{{ $t('attachment') }}</label>
                                    <input type="file" class="form-control" ref="fileInput" @change="handleFileUpload"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                    <small class="form-text text-muted">
                                        {{ $t('file_upload_hint') }} (Max: 10MB)
                                    </small>
                                </div>

                                <!-- select label required -->
                                <div class="col-12">
                                    <label class="form-label">{{ $t('label') }}</label>
                                    <select class="form-select" v-model="ticketForm.label_id">
                                        <option value="">{{ $t('select_label') }}</option>
                                        <option v-for="label in labels" :key="label.id" :value="label.id">
                                            {{ label.name }} ({{ label.tag }})
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="bx bx-info-circle"></i> {{ $t('label_selection_hint') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $t('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isSubmittingTicket">
                                <span v-if="isSubmittingTicket" class="spinner-border spinner-border-sm me-2"
                                    role="status"></span>
                                <i v-else class="bx bx-save me-1"></i>
                                {{ isEditingTicket ? $t('update_ticket') : $t('create_ticket') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">
                            <i class="bx bx-category me-2"></i>{{ $t('add_category') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="submitCategory">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_name') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                                        <input type="text" class="form-control" v-model="newCategory.name"
                                            :placeholder="$t('category_name_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_slug') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-link"></i></span>
                                        <input type="text" class="form-control" v-model="newCategory.slug"
                                            :placeholder="$t('category_slug_placeholder')">
                                    </div>
                                    <small class="form-text text-muted">{{ $t('category_slug_hint') }}</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_description') }}</label>
                                    <textarea class="form-control" v-model="newCategory.description" rows="3"
                                        :placeholder="$t('category_description_placeholder')"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="newCategory.is_active"
                                            id="categoryActive">
                                        <label class="form-check-label" for="categoryActive">
                                            {{ $t('category_active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $t('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isSubmittingCategory">
                                <span v-if="isSubmittingCategory" class="spinner-border spinner-border-sm me-2"
                                    role="status"></span>
                                <i v-else class="bx bx-save me-1"></i>
                                {{ $t('create_category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">
                            <i class="bx bx-edit-alt me-2"></i>{{ $t('edit_category') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="submitEditCategory">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_name') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                                        <input type="text" class="form-control" v-model="editingCategory.name"
                                            :placeholder="$t('category_name_placeholder')" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_slug') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-link"></i></span>
                                        <input type="text" class="form-control" v-model="editingCategory.slug"
                                            :placeholder="$t('category_slug_placeholder')">
                                    </div>
                                    <small class="form-text text-muted">{{ $t('category_slug_hint') }}</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('category_description') }}</label>
                                    <textarea class="form-control" v-model="editingCategory.description" rows="3"
                                        :placeholder="$t('category_description_placeholder')"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="editingCategory.is_active" id="editCategoryActive">
                                        <label class="form-check-label" for="editCategoryActive">
                                            {{ $t('category_active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cancelEditCategory">
                                {{ $t('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isEditingCategory">
                                <span v-if="isEditingCategory" class="spinner-border spinner-border-sm me-2"
                                    role="status"></span>
                                <i v-else class="bx bx-save me-1"></i>
                                {{ $t('update_category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Management Modal -->
        <div class="modal fade" id="categoryManagementModal" tabindex="-1"
            aria-labelledby="categoryManagementModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryManagementModalLabel">
                            <i class="bx bx-category me-2"></i>{{ $t('category_management') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Search and Actions Bar -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control" v-model="categorySearchQuery"
                                        @input="debounceCategorySearch" :placeholder="$t('search_category')">
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-success" @click="showAddCategoryFromManagement">
                                    <i class="bx bx-plus-circle me-1"></i>{{ $t('add_category') }}
                                </button>
                            </div>
                        </div>

                        <!-- Categories Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="form-check-input"
                                                @change="toggleAllCategories"
                                                :checked="selectedCategories.length === categories.length && categories.length > 0">
                                        </th>
                                        <th>{{ $t('category_name') }}</th>
                                        <th>{{ $t('category_slug') }}</th>
                                        <th>{{ $t('status') }}</th>
                                        <th>{{ $t('created_at') }}</th>
                                        <th style="width: 120px;" class="text-center">{{ $t('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="isLoadingCategories">
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else-if="categories.length === 0">
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bx bx-folder-open" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">{{ $t('no_categories') }}</p>
                                        </td>
                                    </tr>
                                    <tr v-else v-for="category in categoryDataTable" :key="category.id">
                                        <td>
                                            <input type="checkbox" class="form-check-input" :value="category.id"
                                                v-model="selectedCategories">
                                        </td>
                                        <td>
                                            <strong>{{ category.name }}</strong>
                                            <br>
                                            <small class="text-muted" v-if="category.description">
                                                {{ category.description.substring(0, 50) }}{{
                                                    category.description.length > 50 ?
                                                        '...' : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            <code v-if="category.slug">{{ category.slug }}</code>
                                            <span v-else class="text-muted">-</span>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                :class="category.is_active ? 'bg-success' : 'bg-secondary'">
                                                {{ category.is_active ? $t('active') : $t('inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ formatDate(category.created_at) }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary"
                                                    @click="showEditCategoryModal(category)" :title="$t('edit')">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"
                                                    @click="confirmDeleteCategory(category.id)" :title="$t('delete')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-3"
                            v-if="selectedCategories.length > 0">
                            <div>
                                <span class="text-muted">{{ selectedCategories.length }} {{ $t('selected') }}</span>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" @click="confirmBulkDeleteCategories">
                                <i class="bx bx-trash me-1"></i>{{ $t('delete_selected') }}
                            </button>
                        </div>

                        <!-- Pagination (if needed) -->
                        <div class="d-flex justify-content-center mt-3" v-if="categoryPagination.last_page > 1">
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item" :class="{ disabled: categoryPagination.current_page === 1 }">
                                        <a class="page-link" href="#"
                                            @click.prevent="loadCategoryPage(categoryPagination.current_page - 1)">
                                            <i class="bx bx-chevron-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item" v-for="page in visiblePages" :key="page"
                                        :class="{ active: page === categoryPagination.current_page }">
                                        <a class="page-link" href="#" @click.prevent="loadCategoryPage(page)">{{ page
                                            }}</a>
                                    </li>
                                    <li class="page-item"
                                        :class="{ disabled: categoryPagination.current_page === categoryPagination.last_page }">
                                        <a class="page-link" href="#"
                                            @click.prevent="loadCategoryPage(categoryPagination.current_page + 1)">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $t('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Modal -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">
                            <i class="bx bx-filter-alt me-2"></i>{{ $t('filter_tickets') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Date Range -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-calendar me-2"></i>{{ $t('date_range') }}
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('start_date') }}</label>
                                <input type="date" class="form-control" v-model="filterStartDate">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('end_date') }}</label>
                                <input type="date" class="form-control" v-model="filterEndDate">
                            </div>

                            <!-- Ticket Properties -->
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-detail me-2"></i>{{ $t('ticket_properties') }}
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('ticket_level') }}</label>
                                <select class="form-select" v-model="filterTicketLevel">
                                    <option value="">{{ $t('all_levels') }}</option>
                                    <option value="low">{{ $t('level_low') }}</option>
                                    <option value="medium">{{ $t('level_medium') }}</option>
                                    <option value="high">{{ $t('level_high') }}</option>
                                    <option value="urgent">{{ $t('level_urgent') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('category') }}</label>
                                <select class="form-select" v-model="filterCategory">
                                    <option value="">{{ $t('all_categories') }}</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('agent') }}</label>
                                <select class="form-select" v-model="filterHumanAgent">
                                    <option value="">{{ $t('all_agents') }}</option>
                                    <option v-for="agent in humanAgents" :key="agent.id" :value="agent.id">
                                        {{ agent.name }}
                                    </option>
                                </select>
                            </div>
 
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" @click="clearAllFilters">
                            <i class="bx bx-x"></i> {{ $t('clear_all') }}
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $t('cancel') }}
                            </button>
                            <button type="button" class="btn btn-primary" @click="applyFilters">
                                <i class="bx bx-check"></i> {{ $t('apply_filters') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sortable from 'sortablejs';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
export default {
    components: {
        Multiselect
    },
    data() {
        return {
            filterStatus: '',
            labels: [],
            categories: [],
            categoryDataTable: [],
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
            isSubmitting: false,
            isEditingLabel: false,
            // Notes
            ticketNotes: [],
            newNote: '',
            isAddingNote: false,
            newLabel: {
                name: '',
                tag: '',
                position: 1,
                color: '#3b82f6'
            },
            editingLabel: {
                id: null,
                name: '',
                tag: '',
                position: 1,
                color: '#3b82f6'
            },
            // Category management properties
            isLoadingCategories: false,
            categorySearchQuery: '',
            categorySearchTimeout: null,
            newCategory: {
                name: '',
                slug: '',
                description: '',
                is_active: true
            },
            editingCategory: {
                id: null,
                name: '',
                slug: '',
                description: '',
                is_active: true
            },
            isSubmittingCategory: false,
            isEditingCategory: false,
            selectedCategories: [],
            categoryPagination: {
                current_page: 1,
                total: 0,
                per_page: 15,
                last_page: 1
            },
            // Ticket management properties
            contacts: [],
            contactSearchQuery: '',
            filterStatus: '',
            isSubmittingTicket: false,
            isEditingTicket: false,
            ticketForm: {
                id: null,
                contacts: '',
                label_id: '',
                category_id: '',
                agent_id: '',
                agents: [], // Multiple agents
                ticket_name: '',
                title: '',
                ticket_level: 'low',
                priority: 'low',
                status: 'open',
                notes: '',
                file: null
            },
            selectedTicket: null,
            // Sortable instances
            sortableInstances: []
        };
    },
    computed: {

        activeFiltersCount() {
            let count = 0;
            if (this.filterStartDate) count++;
            if (this.filterEndDate) count++;
            if (this.filterTicketLevel) count++;
            if (this.filterCategory) count++;
            if (this.filterHumanAgent) count++;
            if (this.filterStatus) count++;
            return count;
        },

        totalStores() {
            return this.labels.reduce((sum, label) => sum + label.total_stores, 0);
        },
        totalTickets() {
            return this.labels.reduce((sum, label) => sum + label.total_stores, 0);
        },

        visiblePages() {
            const pages = [];
            const current = this.categoryPagination.current_page;
            const last = this.categoryPagination.last_page;
            const delta = 2; // Number of pages to show on each side of current page

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
                    pages.push(i);
                }
            }

            return pages;
        }
    },
    methods: {

        showFilterModal() {
            const modal = new bootstrap.Modal(document.getElementById('filterModal'));
            modal.show();
        },

        applyFilters() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
            if (modal) {
                modal.hide();
            }

            this.loadData();

            // Show toast
            this.$showToast(this.$t('filters_applied'), 'success', 2000);
        },

        /**
         * Clear single filter
         */
        clearFilter(filterName) {
            this[filterName] = '';
            this.loadData();
            this.$showToast(this.$t('filter_cleared'), 'info', 2000);
        },

        /**
         * Clear all filters
         */
        clearAllFilters() {
            this.filterStartDate = '';
            this.filterEndDate = '';
            this.filterTicketLevel = '';
            this.filterCategory = '';
            this.filterHumanAgent = '';
            this.filterStatus = '';

            this.loadData();
            this.$showToast(this.$t('all_filters_cleared'), 'info', 2000);

            // Close filter modal if open
            const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
            if (modal) {
                modal.hide();
            }
        },

        /**
         * Get category name by ID
         */
        getCategoryName(categoryId) {
            const category = this.categories.find(c => c.id === categoryId);
            return category ? category.name : categoryId;
        },

        /**
         * Get agent name by ID
         */
        getAgentName(agentId) {
            const agent = this.humanAgents.find(a => a.id === agentId);
            return agent ? agent.name : agentId;
        },


        hasMoreData(label) {
            const loaded = label.loaded_count || 0;
            const total = label.total_stores || 0;
            return loaded < total && !this.loadingMore[label.id];
        },

        getRemainingCount(label) {
            const loaded = label.loaded_count || 0;
            const total = label.total_stores || 0;
            const remaining = total - loaded;
            return remaining > 0 ? remaining : 0;
        },

        getLoadProgress(label) {
            const loaded = label.loaded_count || 0;
            const total = label.total_stores || 0;
            if (total === 0) return 0;
            return Math.round((loaded / total) * 100);
        },

        getBadgeClass(label) {
            const progress = this.getLoadProgress(label);
            if (progress === 100) return 'bg-success';
            if (progress >= 50) return 'bg-primary';
            if (progress >= 25) return 'bg-info';
            return 'bg-warning';
        },


        /**
         * Translation helper
         */
        $t(key) {
            // Gunakan window.i18n jika tersedia (dari blade), jika tidak gunakan fallback
            if (window.i18n && window.i18n.translations && window.i18n.translations[key]) {
                return window.i18n.translations[key];
            }

            // Fallback translations (Bahasa Indonesia)
            const translations = {
                'ticket_management': 'Manajemen Tiket',
                'contact_list': 'Daftar Tiket',
                'total': 'Total',
                'contact': 'Kontak',
                'search_contact': 'Cari Kontak',
                'start_date': 'Tanggal Mulai',
                'end_date': 'Tanggal Selesai',
                'level': 'Level',
                'agent': 'Agen',
                'tickets': 'tiket',
                'all_categories': 'Semua Kategori',
                'all_levels': 'Semua Level',
                'all_agents': 'Semua Agen',
                'all_status': 'Semua Status',
                'no_ticket_in_column': 'Tidak ada tiket di kolom ini',
                'level_low': 'Rendah',
                'level_medium': 'Sedang',
                'level_high': 'Tinggi',
                'level_urgent': 'Mendesak',
                'ticket_id': 'ID Tiket',
                'ticket_level': 'Level Tiket',
                'contact_per_column': 'Kontak per Kolom',
                'loading_data': 'Memuat Data...',
                'load_more': 'Muat Lebih Banyak',
                'remaining': 'Tersisa',
                'loading_more': 'Memuat Lebih Banyak...',
                'view_detail': 'Lihat Detail',
                'edit': 'Edit',
                'delete': 'Hapus',
                'not_handled': 'Belum Ditangani',
                'ticket_detail': 'Detail Tiket',
                'basic_info': 'Informasi Dasar',
                'channel_info': 'Informasi Channel',
                'status_assignment': 'Status & Penugasan',
                'timestamps': 'Waktu',
                'name': 'Nama',
                'email': 'Email',
                'phone': 'Telepon',
                'category': 'Kategori',
                'source_channel': 'Channel Sumber',
                'status': 'Status',
                'label': 'Label',
                'handled_by': 'Ditangani Oleh',
                'resolved_by': 'Diselesaikan Oleh',
                'created_at': 'Dibuat Pada',
                'updated_at': 'Diupdate Pada',
                'close': 'Tutup',
                'status_open': 'Terbuka',
                'status_resolved': 'Terselesaikan',
                'status_pending': 'Tertunda',
                'status_block': 'Diblokir',
                'status_in_progress': 'Sedang Dikerjakan',
                'status_closed': 'Ditutup',
                'confirm_delete_title': 'Apakah Anda yakin?',
                'confirm_delete_text': 'Anda tidak akan dapat mengembalikan ini!',
                'yes_delete': 'Ya, hapus!',
                'cancel': 'Batal',
                'contact_deleted': 'Kontak berhasil dihapus.',
                'contact_loaded': 'kontak dimuat',
                'label_updated': 'Label berhasil diupdate',
                'failed_update_label': 'Gagal mengupdate label',
                'failed_delete_contact': 'Gagal menghapus kontak',
                'failed_load_data': 'Gagal memuat data',
                'error_load_data': 'Error memuat data',
                'add_label': 'Tambah Label',
                'edit_label': 'Edit Label',
                'delete_label': 'Hapus Label',
                'update_label': 'Update Label',
                'label_name': 'Nama Label',
                'label_name_placeholder': 'Masukkan nama label',
                'label_tag': 'Tag/Kata Kunci Label',
                'label_tag_desc': 'Kata kunci yang akan memicu penugasan label ini',
                'label_tag_placeholder': 'contoh: mendesak, keluhan, tagihan',
                'label_position': 'Indeks Posisi',
                'label_color': 'Warna Label',
                'save_label': 'Simpan Label',
                'label_created': 'Label berhasil dibuat',
                'label_updated': 'Label berhasil diupdate',
                'label_deleted': 'Label berhasil dihapus',
                'failed_create_label': 'Gagal membuat label',
                'failed_update_label': 'Gagal mengupdate label',
                'failed_delete_label': 'Gagal menghapus label',
                'confirm_delete_label_title': 'Hapus Label?',
                'confirm_delete_label_text': 'Apakah Anda yakin ingin menghapus label ini? Semua tiket di label ini akan dipindahkan ke "Tiket Baru".',
                'yes_delete_label': 'Ya, Hapus Label!',
                'add_ticket': 'Buat Tiket',
                'edit_ticket': 'Edit Tiket',
                'create_ticket': 'Buat Tiket',
                'update_ticket': 'Update Tiket',
                'select_contact': 'Pilih Kontak',
                'select_label': 'Pilih Label',
                'select_category': 'Pilih Kategori',
                'assign_agent': 'Tugaskan Agen',
                'no_agent': 'Tanpa Agen',
                'ticket_name': 'Nama Tiket',
                'ticket_name_placeholder': 'Masukkan nama tiket',
                'title': 'Judul',
                'title_placeholder': 'Masukkan judul tiket',
                'priority': 'Prioritas',
                'notes': 'Catatan',
                'quick_actions': 'Aksi Cepat',
                'move_to_label': 'Pindah ke Label',
                'ticket_moved': 'Tiket berhasil dipindahkan',
                'failed_move_ticket': 'Gagal memindahkan tiket',
                'activity_history': 'Riwayat Aktivitas',
                'label_changed': 'Label Diubah',
                'assigned_to': 'Ditugaskan ke',
                'no_agent_assigned': 'Belum ada agen yang ditugaskan',
                'no_activity_logs': 'Tidak ada log aktivitas tersedia',
                'notes_placeholder': 'Masukkan catatan atau deskripsi',
                'attachment': 'Lampiran',
                'file_upload_hint': 'Format yang didukung: JPG, PNG, PDF, DOC, DOCX, TXT',
                'manage': 'Kelola',
                'add_category': 'Tambah Kategori',
                'edit_category': 'Edit Kategori',
                'category_management': 'Manajemen Kategori',
                'manage_categories': 'Kelola Kategori',
                'category_name': 'Nama Kategori',
                'category_name_placeholder': 'Masukkan nama kategori',
                'category_slug': 'Slug Kategori',
                'category_slug_placeholder': 'slug-otomatis',
                'category_slug_hint': 'Kosongkan untuk membuat otomatis dari nama',
                'category_description': 'Deskripsi',
                'category_description_placeholder': 'Masukkan deskripsi kategori',
                'category_active': 'Aktif',
                'create_category': 'Buat Kategori',
                'update_category': 'Update Kategori',
                'search_category': 'Cari kategori...',
                'no_categories': 'Tidak ada kategori ditemukan',
                'actions': 'Aksi',
                'active': 'Aktif',
                'inactive': 'Tidak Aktif',
                'selected': 'terpilih',
                'delete_selected': 'Hapus Terpilih',
                'confirm_delete_category_title': 'Hapus Kategori?',
                'confirm_delete_category_text': 'Apakah Anda yakin ingin menghapus kategori ini?',
                'confirm_bulk_delete_categories_title': 'Hapus Beberapa Kategori?',
                'confirm_bulk_delete_categories_text': 'Apakah Anda yakin ingin menghapus {count} kategori?',
                'category_deleted': 'Kategori berhasil dihapus',
                'categories_deleted': 'Kategori berhasil dihapus',
                'failed_delete_category': 'Gagal menghapus kategori',
                'category_updated': 'Kategori berhasil diupdate',
                'failed_update_category': 'Gagal mengupdate kategori',
                'add': 'Tambah',
                'add_note_placeholder': 'Tambahkan catatan...',
                'no_notes_yet': 'Belum ada catatan. Jadilah yang pertama menambahkan!',
                'ctrl_enter_to_send': 'Tekan Ctrl+Enter untuk mengirim',
                'hold_ctrl_multiple': 'Tahan Ctrl/Cmd untuk memilih beberapa agen',
                'label_selection_hint': 'Label akan ditentukan otomatis berdasarkan kata kunci jika tidak dipilih',
                'showing': 'Menampilkan',
                'of': 'dari',
                'all_loaded': 'Semua data telah dimuat',
                'load_more_tickets': 'Muat lebih banyak tiket',
                'filters': 'Filter',
                'filter_tickets': 'Filter Tiket',
                'date_range': 'Rentang Tanggal',
                'ticket_properties': 'Properti Tiket',
                'active_filters': 'Filter Aktif',
                'clear_all': 'Hapus Semua',
                'apply_filters': 'Terapkan Filter',
                'filters_applied': 'Filter diterapkan',
                'filter_cleared': 'Filter dihapus',
                'all_filters_cleared': 'Semua filter dihapus',
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

            // Load notes for this ticket
            this.loadNotes(store.id);

            // Close ticket modal if open
            const ticketModal = bootstrap.Modal.getInstance(document.getElementById('ticketModal'));
            if (ticketModal) {
                ticketModal.hide();
            }

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();
            });
        },

        /**
         * Confirm delete ticket
         */
        confirmDeleteTicket(ticketId, labelId) {
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
                        this.deleteTicket(ticketId, labelId);
                    }
                });
            } else {
                // Fallback confirm
                if (confirm(this.$t('confirm_delete_text'))) {
                    this.deleteTicket(ticketId, labelId);
                }
            }
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
         * Load data from API
         */
        async loadData() {
            this.loading = true;

            try {
                const routeUrl = window.route ? window.route('tickets.data') : '/app/tickets/data';

                const params = new URLSearchParams();

                if (this.filterStartDate) {
                    params.append('start_date', this.filterStartDate);
                }

                if (this.filterEndDate) {
                    params.append('end_date', this.filterEndDate);
                }

                if (this.filterTicketLevel) {
                    params.append('ticket_level', this.filterTicketLevel);
                }

                if (this.filterHumanAgent) {
                    params.append('human_agent_id', this.filterHumanAgent);
                }

                if (this.filterCategory) {
                    params.append('category_id', this.filterCategory);
                }

                if (this.filterStatus) {
                    params.append('status', this.filterStatus);
                }

                params.append('per_page', this.perPage);

                const response = await fetch(`${routeUrl}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    this.labels = result.data.map(label => ({
                        id: label.id,
                        name: label.name,
                        color: label.color || '#3b82f6',
                        tag: label.tag || '',
                        position: label.position || 1,
                        total_stores: label.total_stores || 0,
                        loaded_count: label.loaded_count || (label.stores ? label.stores.length : 0),
                        has_more: label.has_more || (label.loaded_count < label.total_stores),
                        stores: label.stores || [],
                        logs: label.logs || []
                    }));

                    // Log untuk debugging
                    this.labels.forEach(label => {
                        console.log(`Label: ${label.name}, Total: ${label.total_stores}, Loaded: ${label.loaded_count}, Has More: ${label.has_more}`);
                    });
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }

                this.$showToast('Data loaded successfully', 'success', 2000);
            } catch (error) {
                console.error('Error loading data:', error);
                this.$showToast(this.$t('error_load_data'), 'error', 3000);
                this.labels = [];
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load human agents from API
         */
        async loadHumanAgents() {
            try {
                // Get human agents - you may need to create this route
                const response = await fetch('/app/tickets/agents', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    this.humanAgents = data.agents || data.data || [];
                } else {
                    console.error('Failed to load human agents - endpoint not available');
                    this.humanAgents = [];
                }

            } catch (error) {
                console.error('Error loading human agents:', error);
                this.humanAgents = [];
            }
        },

        /**
         * Load more stores untuk kolom tertentu
         */
        async loadMore(labelId) {
            // Prevent multiple simultaneous loads
            if (this.loadingMore[labelId]) {
                return;
            }
            this.loadingMore[labelId] = true;


            try {
                const label = this.labels.find(l => l.id === labelId);
                if (!label) {
                    throw new Error('Label not found');
                }

                // Check if there's more data to load
                if (label.loaded_count >= label.total_stores) {
                    this.$showToast('All tickets loaded', 'info', 2000);
                    return;
                }

                const currentCount = label.stores.length;

                // Build query parameters for pagination
                const params = new URLSearchParams();
                params.append('label_id', labelId);
                params.append('offset', currentCount);
                params.append('per_page', this.perPage);

                // Include filters
                if (this.filterStartDate) {
                    params.append('start_date', this.filterStartDate);
                }

                if (this.filterEndDate) {
                    params.append('end_date', this.filterEndDate);
                }

                if (this.filterTicketLevel) {
                    params.append('ticket_level', this.filterTicketLevel);
                }

                if (this.filterHumanAgent) {
                    params.append('human_agent_id', this.filterHumanAgent);
                }

                if (this.filterCategory) {
                    params.append('category_id', this.filterCategory);
                }

                // Make API request for more data - FIX: gunakan endpoint yang benar
                const response = await fetch(`/app/tickets/load-more?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                console.log('Load More Response:', data); // Debugging

                // FIX: gunakan data.stores (sesuai dengan backend response)
                if (data.success && data.stores && data.stores.length > 0) {
                    // Append new stores
                    label.stores.push(...data.stores);

                    // Update loaded count
                    label.loaded_count = label.stores.length;

                    // Update has_more flag from backend
                    label.has_more = data.has_more;

                    // Show success message with details
                    const remaining = label.total_stores - label.loaded_count;
                    const message = `Loaded ${data.stores.length} more tickets. ${remaining > 0 ? remaining + ' remaining' : 'All loaded!'}`;
                    this.$showToast(message, 'success', 3000);

                    // Reinitialize drag & drop for new cards
                    this.$nextTick(() => {
                        this.initializeDragAndDrop();
                    });
                } else {
                    // No more data
                    label.has_more = false;
                    label.loaded_count = label.total_stores; // Sync the counts
                    this.$showToast('No more tickets to load', 'info', 2000);
                }

            } catch (error) {
                console.error('Error loading more:', error);
                this.$showToast(this.$t('failed_load_data') + ': ' + error.message, 'error', 3000);
            } finally {
                this.loadingMore[labelId] = false;
            }
        },

        /**
         * Load categories from API using TicketCategoryController
         */
        async loadCategories() {
            try {
                // Try primary endpoint first
                let response = await fetch('/app/tickets/categories/options', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                // If options endpoint fails, try the main categories endpoint
                if (!response.ok) {
                    response = await fetch('/app/tickets/categories', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    });
                }

                if (response.ok) {
                    const result = await response.json();

                    if (result.success && result.data && result.data.length > 0) {
                        this.categories = result.data.map(category => ({
                            id: category.id, // Keep original ID (could be UUID or integer)
                            name: category.name,
                            slug: category.slug || '',
                            description: category.description || '',
                            is_active: category.is_active !== false,
                            created_at: category.created_at || null
                        }));

                        return;
                    }
                }

                console.warn('No categories available from API');

            } catch (error) {
                console.error('Error loading categories:', error.message);

                if (!this.categories || this.categories.length === 0) {
                    this.categories = [];
                    console.warn('Failed to load categories - please ensure categories are created in the system');
                }
            }
        },

        /**
         * Handle drop event - Move ticket between labels realtime
         */
        async handleDrop(evt) {
            const ticketId = evt.item.dataset.storeId;
            const newLabelId = evt.to.dataset.labelId;
            const oldLabelId = evt.from.dataset.labelId;

            // No change if dropped in same column
            if (newLabelId === oldLabelId) {
                return;
            }

            // Find the ticket object
            const oldLabel = this.labels.find(l => l.id == oldLabelId);
            const newLabel = this.labels.find(l => l.id == newLabelId);

            if (!oldLabel || !newLabel) {
                this.$showToast('Label not found', 'error', 3000);
                return;
            }

            const ticketIndex = oldLabel.stores.findIndex(s => s.id == ticketId);
            if (ticketIndex === -1) {
                this.$showToast('Ticket not found', 'error', 3000);
                return;
            }

            const ticket = oldLabel.stores[ticketIndex];

            try {
                // Update UI immediately (optimistic update)
                oldLabel.stores.splice(ticketIndex, 1);
                oldLabel.total_stores--;
                oldLabel.loaded_count--;

                newLabel.stores.push(ticket);
                newLabel.total_stores++;
                newLabel.loaded_count++;

                // Update ticket's label reference
                ticket.label_id = newLabelId;
                ticket.label = newLabel.name;

                // Make API call to persist the change
                const response = await fetch(`/app/tickets/move`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        label_id: newLabelId,
                        old_label_id: oldLabelId,
                        ticket_id: ticketId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.$showToast(this.$t('label_updated') || 'Ticket moved successfully', 'success', 2000);
                } else {
                    throw new Error(result.message || 'Failed to move ticket');
                }

            } catch (error) {
                console.error('Error moving ticket:', error);

                // Rollback UI changes on error
                newLabel.stores = newLabel.stores.filter(s => s.id != ticketId);
                newLabel.total_stores--;
                newLabel.loaded_count--;

                oldLabel.stores.splice(ticketIndex, 0, ticket);
                oldLabel.total_stores++;
                oldLabel.loaded_count++;

                // Restore original label reference
                ticket.label_id = oldLabelId;
                ticket.label = {
                    id: oldLabel.id,
                    name: oldLabel.name,
                    tag: oldLabel.tag,
                    color: oldLabel.color
                };

                this.$showToast(error.message || this.$t('failed_update_label'), 'error', 3000);
            }
        },

        /**
         * Move ticket to another label programmatically
         */
        async moveTicketToLabel(ticketId, newLabelId) {
            // Find current label
            let currentLabel = null;
            let ticket = null;
            let ticketIndex = -1;

            for (const label of this.labels) {
                ticketIndex = label.stores.findIndex(s => s.id == ticketId);
                if (ticketIndex !== -1) {
                    currentLabel = label;
                    ticket = label.stores[ticketIndex];
                    break;
                }
            }

            if (!currentLabel || !ticket) {
                this.$showToast('Ticket not found', 'error', 3000);
                return false;
            }

            if (currentLabel.id == newLabelId) {
                this.$showToast('Ticket is already in this label', 'info', 2000);
                return false;
            }

            const newLabel = this.labels.find(l => l.id == newLabelId);
            if (!newLabel) {
                this.$showToast('Target label not found', 'error', 3000);
                return false;
            }

            try {
                // Update UI immediately
                currentLabel.stores.splice(ticketIndex, 1);
                currentLabel.total_stores--;
                currentLabel.loaded_count--;

                newLabel.stores.unshift(ticket); // Add to top
                newLabel.total_stores++;
                newLabel.loaded_count++;

                ticket.label_id = newLabelId;
                ticket.label = {
                    id: newLabel.id,
                    name: newLabel.name,
                    tag: newLabel.tag,
                    color: newLabel.color
                };

                // Persist to backend
                const response = await fetch(`/app/tickets/move/${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        label_id: newLabelId,
                        old_label_id: currentLabel.id
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.$showToast('Ticket moved successfully', 'success', 2000);
                    return true;
                } else {
                    throw new Error(result.message || 'Failed to move ticket');
                }

            } catch (error) {
                console.error('Error moving ticket:', error);

                // Rollback on error
                newLabel.stores = newLabel.stores.filter(s => s.id != ticketId);
                newLabel.total_stores--;
                newLabel.loaded_count--;

                currentLabel.stores.splice(ticketIndex, 0, ticket);
                currentLabel.total_stores++;
                currentLabel.loaded_count++;

                ticket.label_id = currentLabel.id;
                ticket.label = {
                    id: currentLabel.id,
                    name: currentLabel.name,
                    tag: currentLabel.tag,
                    color: currentLabel.color
                };

                this.$showToast(error.message || 'Failed to move ticket', 'error', 3000);
                return false;
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
         * Confirm delete with SweetAlert
         */
        confirmDelete(storeId, labelId) {
            // This method is deprecated, use confirmDeleteTicket instead
            this.confirmDeleteTicket(storeId, labelId);
        },

        /**
         * Delete store (deprecated - use deleteTicket instead)
         */
        async deleteStore(storeId, labelId) {
            return this.deleteTicket(storeId, labelId);
        },

        /**
         * Show Add Label Modal
         */
        showAddLabelModal() {
            // Reset form
            this.newLabel = {
                name: '',
                tag: '',
                position: this.labels.length + 1,
                color: '#3b82f6'
            };

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('addLabelModal'));
                modal.show();
            });
        },

        /**
         * Update color hex display
         */
        updateColorHex() {
            // This method is called when color input changes
            // The color hex is automatically updated via v-model
        },

        /**
         * Submit new label form
         */
        async submitLabel() {
            this.isSubmitting = true;

            try {
                // Prepare data for API
                const formData = {
                    name: this.newLabel.name,
                    tag: this.newLabel.tag,
                    position: this.newLabel.position,
                    color: this.newLabel.color,
                    type: 'ticket' // Assuming this is for ticket labels
                };

                // Make API request to create label using the correct route
                const response = await fetch('/app/master/ticket-labels/store', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload data to get updated labels
                    await this.loadData();

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addLabelModal'));
                    modal.hide();

                    this.$showToast(this.$t('label_created'), 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to create label');
                }

            } catch (error) {
                console.error('Error creating label:', error);
                this.$showToast(this.$t('failed_create_label'), 'error', 3000);
            } finally {
                this.isSubmitting = false;
            }
        },

        /**
         * Show Edit Label Modal
         */
        showEditLabelModal(label) {
            // Set editing label data
            this.editingLabel = {
                id: label.id,
                name: label.name,
                tag: label.tag || '',
                position: label.position || 1,
                color: label.color
            };

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('editLabelModal'));
                modal.show();
            });
        },

        /**
         * Update edit color hex display
         */
        updateEditColorHex() {
            // This method is called when color input changes in edit modal
            // The color hex is automatically updated via v-model
        },

        /**
         * Submit edit label form
         */
        async submitEditLabel() {
            this.isEditingLabel = true;

            try {
                // Prepare data for API
                const formData = {
                    name: this.editingLabel.name,
                    tag: this.editingLabel.tag,
                    position: this.editingLabel.position,
                    color: this.editingLabel.color,
                    type: 'ticket'
                };

                // Make API request to update label using the correct route
                const response = await fetch(`/app/master/ticket-labels/edit/${this.editingLabel.id}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload data to get updated labels
                    await this.loadData();

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editLabelModal'));
                    modal.hide();

                    this.$showToast(this.$t('label_updated'), 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to update label');
                }

            } catch (error) {
                console.error('Error updating label:', error);
                this.$showToast(this.$t('failed_update_label'), 'error', 3000);
            } finally {
                this.isEditingLabel = false;
            }
        },

        /**
         * Confirm delete label
         */
        confirmDeleteLabel(labelId) {
            if (window.Swal) {
                Swal.fire({
                    title: this.$t('confirm_delete_label_title'),
                    text: this.$t('confirm_delete_label_text'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: this.$t('yes_delete_label'),
                    cancelButtonText: this.$t('cancel')
                }).then((result) => {
                    if (result.value) {
                        this.deleteLabel(labelId);
                    }
                });
            } else {
                // Fallback confirm
                if (confirm(this.$t('confirm_delete_label_text'))) {
                    this.deleteLabel(labelId);
                }
            }
        },

        /**
         * Delete label
         */
        async deleteLabel(labelId) {
            try {
                // Make API request to delete label using the correct route
                const response = await fetch(`/app/master/ticket-labels/delete/${labelId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload data to get updated labels list
                    await this.loadData();

                    this.$showToast(this.$t('label_deleted'), 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to delete label');
                }

            } catch (error) {
                console.error('Error deleting label:', error);
                this.$showToast(this.$t('failed_delete_label'), 'error', 3000);
            }
        },

        /**
         * Search categories from API
         */
        async searchCategories(query = '') {
            try {
                const params = new URLSearchParams();
                if (query) {
                    params.append('search', query);
                }

                const response = await fetch(`/app/tickets/categories/search?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data) {
                        return result.data;
                    }
                }
                return [];

            } catch (error) {
                console.error('Error searching categories:', error);
                return [];
            }
        },

        /**
         * Create new category
         */
        async createCategory(categoryData) {
            try {
                const response = await fetch('/app/tickets/categories/store', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(categoryData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories to get updated list
                    await this.loadCategories();
                    this.$showToast('Category created successfully', 'success', 3000);
                    return result.data;
                } else {
                    throw new Error(result.message || 'Failed to create category');
                }

            } catch (error) {
                console.error('Error creating category:', error);
                this.$showToast('Failed to create category', 'error', 3000);
                throw error;
            }
        },

        /**
         * Update existing category
         */
        async updateCategory(categoryId, categoryData) {
            try {
                const response = await fetch(`/app/tickets/categories/edit/${categoryId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(categoryData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories to get updated list
                    await this.loadCategories();
                    this.$showToast('Category updated successfully', 'success', 3000);
                    return result.data;
                } else {
                    throw new Error(result.message || 'Failed to update category');
                }

            } catch (error) {
                console.error('Error updating category:', error);
                this.$showToast('Failed to update category', 'error', 3000);
                throw error;
            }
        },

        /**
         * Delete category
         */
        async deleteCategory(categoryId) {
            try {
                const response = await fetch(`/app/tickets/categories/delete/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories to get updated list
                    await this.loadCategories();
                    this.$showToast('Category deleted successfully', 'success', 3000);
                    return true;
                } else {
                    throw new Error(result.message || 'Failed to delete category');
                }

            } catch (error) {
                console.error('Error deleting category:', error);
                this.$showToast('Failed to delete category', 'error', 3000);
                throw error;
            }
        },

        /**
         * Bulk delete categories
         */
        async bulkDeleteCategories(categoryIds) {
            try {
                const response = await fetch('/app/tickets/categories/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ids: categoryIds })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories to get updated list
                    await this.loadCategories();
                    this.$showToast(`${result.deleted_count} categories deleted successfully`, 'success', 3000);
                    return result.deleted_count;
                } else {
                    throw new Error(result.message || 'Failed to delete categories');
                }

            } catch (error) {
                console.error('Error bulk deleting categories:', error);
                this.$showToast('Failed to delete categories', 'error', 3000);
                throw error;
            }
        },

        /**
         * Get category details by ID
         */
        async getCategoryDetails(categoryId) {
            try {
                const response = await fetch(`/app/tickets/categories/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    return result.data;
                } else {
                    throw new Error(result.message || 'Failed to get category details');
                }

            } catch (error) {
                console.error('Error getting category details:', error);
                this.$showToast('Failed to get category details', 'error', 3000);
                throw error;
            }
        },

        /**
         * Get categories with pagination
         */
        async getCategoriesWithPagination(page = 1, perPage = 15) {
            try {
                const params = new URLSearchParams();
                params.append('page', page);
                params.append('per_page', perPage);

                const response = await fetch(`/app/tickets/categories/options?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    return {
                        data: result.data,
                        pagination: result.pagination || {
                            current_page: 1,
                            total: result.data.length,
                            per_page: perPage,
                            last_page: 1
                        }
                    };
                } else {
                    throw new Error(result.message || 'Failed to get categories');
                }

            } catch (error) {
                console.error('Error getting categories with pagination:', error);
                this.$showToast('Failed to get categories', 'error', 3000);
                throw error;
            }
        },

        /**
         * Helper: Show category management modal
         */
        showCategoryManagementModal() {
            // This method can be called to open a category management modal
            // You can implement this based on your UI requirements
            this.$showToast('Category management feature available', 'info', 2000);
        },

        cancelEditCategory() {
            this.editingCategory = null;

            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
            if (modal) {
                modal.hide();
            }

            this.$showToast('Category edit cancelled', 'info', 2000);

            const managementModal = bootstrap.Modal.getInstance(document.getElementById('categoryManagementModal'));
            if (managementModal) {
                managementModal.show();
            }
        },

        /**
         * Helper: Format category for select options
         */
        formatCategoriesForSelect() {
            return this.categories.map(category => ({
                value: category.id,
                text: category.name,
                disabled: !category.is_active
            }));
        },

        /**
         * Helper: Get category name by ID
         */
        getCategoryNameById(categoryId) {
            const category = this.categories.find(cat => cat.id === categoryId);
            return category ? category.name : 'Unknown Category';
        },

        /**
         * Helper: Get active categories only
         */
        getActiveCategories() {
            return this.categories.filter(category => category.is_active !== false);
        },

        /**
         * Helper: Validate category data
         */
        validateCategoryData(categoryData) {
            const errors = [];

            if (!categoryData.name || categoryData.name.trim() === '') {
                errors.push('Category name is required');
            }

            if (categoryData.name && categoryData.name.length > 100) {
                errors.push('Category name must not exceed 100 characters');
            }

            if (categoryData.slug && categoryData.slug.length > 100) {
                errors.push('Category slug must not exceed 100 characters');
            }

            if (categoryData.description && categoryData.description.length > 500) {
                errors.push('Category description must not exceed 500 characters');
            }

            return {
                isValid: errors.length === 0,
                errors: errors
            };
        },

        /**
         * Helper: Generate slug from name
         */
        generateSlugFromName(name) {
            return name
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
        },

        /**
         * Load contacts for dropdown
         */
        async loadContacts(search = '') {
            try {
                const params = new URLSearchParams();
                if (search) {
                    params.append('search', search);
                }

                const response = await fetch(`/app/tickets/contacts?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data) {
                        this.contacts = result.data;
                    }
                } else {
                    console.error('Failed to load contacts');
                }

            } catch (error) {
                console.error('Error loading contacts:', error);
            }
        },

        /**
         * Show Add Ticket Modal
         */
        async showAddTicketModal() {
            this.isEditingTicket = false;
            this.resetTicketForm();

            // Ensure categories are loaded before opening modal
            if (!this.categories || this.categories.length === 0) {
                await this.loadCategories();
            }

            // Load required data
            this.loadContacts();

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
                modal.show();
            });
        },

        /**
         * Show Edit Ticket Modal
         */
        async showEditTicketModal(ticket) {
            this.isEditingTicket = true;
            this.selectedTicket = ticket;

            // Close detail modal if open
            const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
            if (detailModal) {
                detailModal.hide();
            }

            // Populate form with ticket data
            this.ticketForm = ticket;

            // Ensure categories are loaded before opening modal
            if (!this.categories || this.categories.length === 0) {
                await this.loadCategories();
            }

            // Load required data
            this.loadContacts();

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
                modal.show();

            });
        },

        /**
         * Reset ticket form
         */
        resetTicketForm() {
            this.ticketForm = {
                id: null,
                contacts: '',
                label_id: '',
                category_id: '',
                agent_id: '',
                agents: [],
                ticket_name: '',
                title: '',
                ticket_level: 'low',
                priority: 'low',
                status: 'open',
                notes: '',
                file: null
            };
            this.contactSearchQuery = '';
        },

        /**
         * Handle file upload
         */
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    this.$showToast('File size must be less than 10MB', 'error', 3000);
                    event.target.value = '';
                    return;
                }

                // Convert file to base64
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.ticketForm.file = {
                        name: file.name,
                        type: file.type,
                        size: file.size,
                        data: e.target.result // base64 string with data:image/png;base64,... prefix
                    };
                };
                reader.onerror = (error) => {
                    console.error('Error reading file:', error);
                    this.$showToast('Failed to read file', 'error', 3000);
                    event.target.value = '';
                };
                reader.readAsDataURL(file);
            }
        },

        /**
         * Submit ticket form (create or update)
         */
        async submitTicket() {
            this.isSubmittingTicket = true;

            try {
                // Build JSON payload
                const payload = {};

                // Add all form fields
                Object.keys(this.ticketForm).forEach(key => {
                    if (this.ticketForm[key] !== null && this.ticketForm[key] !== '' && key !== 'id') {
                        if (key === 'file' && this.ticketForm[key]) {
                            payload.file = this.ticketForm[key];
                        } else if (key === 'agents' && Array.isArray(this.ticketForm[key])) {
                            payload.agents = this.ticketForm[key].map(agent => {
                                if (typeof agent === 'object' && agent.id) {
                                    return { id: agent.id };
                                }
                                return { id: agent };
                            });
                        } else if (key !== 'file' && key !== 'agents') {
                            payload[key] = this.ticketForm[key];
                        }
                    }
                });

                const url = this.isEditingTicket
                    ? `/app/tickets/edit/${this.ticketForm.id}`
                    : '/app/tickets/store';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    if (this.isEditingTicket) {
                        // UPDATE: Update ticket yang sudah ada
                        this.updateTicketInLabel(result.data, result.old_label_id, result.label_changed);
                    } else {
                        // CREATE: Tambahkan ticket baru
                        this.addTicketToLabel(result.data);
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('ticketModal'));
                    modal.hide();

                    const message = this.isEditingTicket ? 'Ticket updated successfully' : 'Ticket created successfully';
                    this.$showToast(message, 'success', 3000);

                    // Reinitialize drag & drop
                    this.$nextTick(() => {
                        this.initializeDragAndDrop();
                    });
                } else {
                    Swal.fire({
                        title: 'Warning',
                        html: result.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }

            } catch (error) {
                console.error('Error saving ticket:', error);
                this.$showToast('Failed to save ticket: ' + error.message, 'error', 3000);
            } finally {
                this.isSubmittingTicket = false;
            }
        },

        addTicketToLabel(ticketData) {
            const labelId = ticketData.label_id;
            const label = this.labels.find(l => l.id === labelId);

            if (label) {
                // Add ticket to beginning of array (newest first)
                label.stores.unshift(ticketData);

                // Update counters
                label.total_stores++;
                label.loaded_count++;

                // Update has_more flag
                label.has_more = label.loaded_count < label.total_stores;

                console.log(`Added ticket to label: ${label.name}`);
            } else {
                console.warn(`Label ${labelId} not found, reloading all data`);
                this.loadData();
            }
        },

        updateTicketInLabel(ticketData, oldLabelId, labelChanged) {
            if (labelChanged) {
                // Ticket moved to different label
                this.moveTicketBetweenLabels(ticketData, oldLabelId, ticketData.label_id);
            } else {
                // Ticket stayed in same label, just update data
                const label = this.labels.find(l => l.id === ticketData.label_id);

                if (label) {
                    const ticketIndex = label.stores.findIndex(t => t.id === ticketData.id);

                    if (ticketIndex !== -1) {
                        // Update ticket data using Vue.set for reactivity
                        label.stores[ticketIndex] = ticketData;
                        console.log(`Updated ticket in label: ${label.name}`);
                    } else {
                        console.warn(`Ticket not found in label, adding it`);
                        label.stores.unshift(ticketData);
                        label.loaded_count++;
                    }
                } else {
                    console.warn(`Label ${ticketData.label_id} not found, reloading all data`);
                    this.loadData();
                }
            }
        },

        moveTicketBetweenLabels(ticketData, oldLabelId, newLabelId) {
            const oldLabel = this.labels.find(l => l.id === oldLabelId);
            const newLabel = this.labels.find(l => l.id === newLabelId);

            if (!oldLabel || !newLabel) {
                console.warn('Old or new label not found, reloading all data');
                this.loadData();
                return;
            }

            // Remove from old label
            const ticketIndex = oldLabel.stores.findIndex(t => t.id === ticketData.id);
            if (ticketIndex !== -1) {
                oldLabel.stores.splice(ticketIndex, 1);
                oldLabel.total_stores--;
                oldLabel.loaded_count--;
                oldLabel.has_more = oldLabel.loaded_count < oldLabel.total_stores;
            }

            // Add to new label (at the beginning)
            newLabel.stores.unshift(ticketData);
            newLabel.total_stores++;
            newLabel.loaded_count++;
            newLabel.has_more = newLabel.loaded_count < newLabel.total_stores;

            console.log(`Moved ticket from ${oldLabel.name} to ${newLabel.name}`);
        },

        /**
         * Delete ticket
         */
        async deleteTicket(ticketId, labelId = null) {
            try {
                const response = await fetch(`/app/tickets/delete/${ticketId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Update UI by removing the ticket from the label
                    if (labelId) {
                        const label = this.labels.find(l => l.id === labelId);
                        if (label) {
                            label.total_stores--;
                            label.loaded_count--;
                            label.stores = label.stores.filter(s => s.id !== ticketId);
                        }
                    } else {
                        // Reload data to get updated tickets
                        await this.loadData();
                    }

                    this.$showToast('Ticket deleted successfully', 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to delete ticket');
                }

            } catch (error) {
                console.error('Error deleting ticket:', error);
                this.$showToast('Failed to delete ticket: ' + error.message, 'error', 3000);
            }
        },

        /**
         * Show Category Modal
         */
        showCategoryModal() {
            // Reset form
            this.newCategory = {
                name: '',
                slug: '',
                description: '',
                is_active: true
            };

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            });
        },

        /**
         * Submit category form
         */
        async submitCategory() {
            this.isSubmittingCategory = true;

            try {
                // Auto-generate slug if not provided
                if (!this.newCategory.slug && this.newCategory.name) {
                    this.newCategory.slug = this.generateSlugFromName(this.newCategory.name);
                }

                const response = await fetch('/app/tickets/categories/store', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.newCategory)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories for both dropdown and management
                    await this.loadCategories();

                    // If management modal is open, reload that too
                    if (document.getElementById('categoryManagementModal').classList.contains('show')) {
                        await this.loadCategoriesWithPagination();
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                    modal.hide();

                    this.$showToast('Category created successfully', 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to create category');
                }

            } catch (error) {
                console.error('Error creating category:', error);
                this.$showToast('Failed to create category: ' + error.message, 'error', 3000);
            } finally {
                this.isSubmittingCategory = false;
            }
        },

        /**
         * Show Category Management Modal
         */
        showCategoryManagementModal() {
            this.selectedCategories = [];
            this.categorySearchQuery = '';
            this.isLoadingCategories = true;

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('categoryManagementModal'));
                modal.show();

                // Load categories with pagination
                this.loadCategoriesWithPagination();
            });
        },

        showAddCategoryFromManagement() {
            const managementModal = bootstrap.Modal.getInstance(document.getElementById('categoryManagementModal'));
            if (managementModal) {
                managementModal.hide();
            }

            this.showCategoryModal();

            const addModal = document.getElementById('categoryModal');

            addModal.addEventListener('hidden.bs.modal', () => {
                this.$nextTick(() => {
                    const modal = new bootstrap.Modal(document.getElementById('categoryManagementModal'));
                    modal.show();
                });
            }, { once: true });
        },

        /**
         * Show Edit Category Modal
         */
        showEditCategoryModal(category) {
            // hide management modal temporarily
            const managementModal = bootstrap.Modal.getInstance(document.getElementById('categoryManagementModal'));
            if (managementModal) {
                managementModal.hide();
            }

            this.editingCategory = {
                id: category.id,
                name: category.name,
                slug: category.slug || '',
                description: category.description || '',
                is_active: category.is_active !== false
            };

            this.$nextTick(() => {
                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
            });
        },

        /**
         * Submit Edit Category
         */
        async submitEditCategory() {
            this.isEditingCategory = true;

            try {
                // Auto-generate slug if not provided
                if (!this.editingCategory.slug && this.editingCategory.name) {
                    this.editingCategory.slug = this.generateSlugFromName(this.editingCategory.name);
                }

                const response = await fetch(`/app/tickets/categories/edit/${this.editingCategory.id}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        name: this.editingCategory.name,
                        slug: this.editingCategory.slug,
                        description: this.editingCategory.description,
                        is_active: this.editingCategory.is_active
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories for both dropdown and management
                    await this.loadCategories();
                    await this.loadCategoriesWithPagination();

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    modal.hide();

                    this.$showToast(this.$t('category_updated'), 'success', 3000);

                    // show management modal again
                    const managementModal = new bootstrap.Modal(document.getElementById('categoryManagementModal'));
                    managementModal.show();

                } else {
                    throw new Error(result.message || 'Failed to update category');
                }

            } catch (error) {
                console.error('Error updating category:', error);
                this.$showToast(this.$t('failed_update_category'), 'error', 3000);
            } finally {
                this.isEditingCategory = false;
            }
        },

        /**
         * Confirm delete single category
         */
        confirmDeleteCategory(categoryId) {
            if (window.Swal) {
                Swal.fire({
                    title: this.$t('confirm_delete_category_title'),
                    text: this.$t('confirm_delete_category_text'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: this.$t('yes_delete'),
                    cancelButtonText: this.$t('cancel')
                }).then((result) => {
                    if (result.value) {
                        this.deleteSingleCategory(categoryId);
                    }
                });
            } else {
                if (confirm(this.$t('confirm_delete_category_text'))) {
                    this.deleteSingleCategory(categoryId);
                }
            }
        },

        /**
         * Delete single category
         */
        async deleteSingleCategory(categoryId) {
            try {
                const response = await fetch(`/app/tickets/categories/delete/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload categories
                    await this.loadCategoriesWithPagination();

                    // Also reload main categories for filter dropdown
                    await this.loadCategories();

                    this.$showToast(this.$t('category_deleted'), 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to delete category');
                }

            } catch (error) {
                console.error('Error deleting category:', error);
                this.$showToast(this.$t('failed_delete_category'), 'error', 3000);
            }
        },

        /**
         * Confirm bulk delete categories
         */
        confirmBulkDeleteCategories() {
            const count = this.selectedCategories.length;
            const text = this.$t('confirm_bulk_delete_categories_text').replace('{count}', count);

            if (window.Swal) {
                Swal.fire({
                    title: this.$t('confirm_bulk_delete_categories_title'),
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: this.$t('yes_delete'),
                    cancelButtonText: this.$t('cancel')
                }).then((result) => {
                    if (result.value) {
                        this.bulkDeleteCategories();
                    }
                });
            } else {
                if (confirm(text)) {
                    this.bulkDeleteCategories();
                }
            }
        },

        /**
         * Bulk delete categories
         */
        async bulkDeleteCategories() {
            try {
                const response = await fetch('/app/tickets/categories/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ids: this.selectedCategories })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Clear selection
                    this.selectedCategories = [];

                    // Reload categories
                    await this.loadCategoriesWithPagination();

                    // Also reload main categories for filter dropdown
                    await this.loadCategories();

                    this.$showToast(this.$t('categories_deleted'), 'success', 3000);
                } else {
                    throw new Error(result.message || 'Failed to delete categories');
                }

            } catch (error) {
                console.error('Error bulk deleting categories:', error);
                this.$showToast(this.$t('failed_delete_category'), 'error', 3000);
            }
        },

        /**
         * Load categories with pagination
         */
        async loadCategoriesWithPagination(page = 1) {
            this.isLoadingCategories = true;

            try {
                const params = new URLSearchParams();
                params.append('page', page);
                params.append('per_page', this.categoryPagination.per_page);

                if (this.categorySearchQuery) {
                    params.append('query', this.categorySearchQuery);
                }

                const response = await fetch(`/app/tickets/categories/search?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    const data = result.data || {};

                    this.categoryDataTable = data.categories || [];

                    // Update pagination info
                    if (result.pagination) {
                        this.categoryPagination = {
                            current_page: data.pagination.current_page || 1,
                            total: data.pagination.total || 0,
                            per_page: data.pagination.per_page || 15,
                            last_page: data.pagination.last_page || 1
                        };
                    }
                } else {
                    throw new Error(result.message || 'Failed to load categories');
                }

            } catch (error) {
                console.error('Error loading categories:', error);
                this.$showToast('Failed to load categories', 'error', 3000);
                this.categories = [];
            } finally {
                this.isLoadingCategories = false;
            }
        },

        /**
         * Load category page
         */
        loadCategoryPage(page) {
            if (page >= 1 && page <= this.categoryPagination.last_page) {
                this.loadCategoriesWithPagination(page);
            }
        },

        /**
         * Debounce category search
         */
        debounceCategorySearch() {
            clearTimeout(this.categorySearchTimeout);
            this.categorySearchTimeout = setTimeout(() => {
                this.loadCategoriesWithPagination(1);
            }, 500);
        },

        /**
         * Toggle all categories selection
         */
        toggleAllCategories(event) {
            if (event.target.checked) {
                this.selectedCategories = this.categories.map(cat => cat.id);
            } else {
                this.selectedCategories = [];
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
            }
        },

        /**
         * Load categories for dropdown
         */
        async loadCategories() {
            try {
                const response = await fetch('/app/tickets/categories/options', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data) {
                        this.categories = result.data;
                    }
                } else {
                    console.error('Failed to load categories');
                }

            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        /**
         * Generate slug from category name
         */
        generateSlugFromName(name) {
            return name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
        },

        /**
         * Update category slug when name changes
         */
        updateCategorySlug() {
            if (this.newCategory.name) {
                this.newCategory.slug = this.generateSlugFromName(this.newCategory.name);
            }
        },

        /**
         * Move ticket from detail modal dropdown
         */
        async moveTicketFromDetail(event, ticketId) {
            const newLabelId = event.target.value;

            if (!newLabelId) {
                return;
            }

            // Reset dropdown to original value first
            event.target.value = '';

            const result = await this.moveTicketToLabel(ticketId, newLabelId);

            if (result) {
                // Update selected store if it's still the same ticket
                if (this.selectedStore && this.selectedStore.id == ticketId) {
                    const newLabel = this.labels.find(l => l.id == newLabelId);
                    if (newLabel) {
                        this.selectedStore.label_id = newLabelId;
                        this.selectedStore.label = newLabel.name;
                    }
                }
            }
        },

        /**
         * Bulk move tickets to a label
         */
        async bulkMoveTickets(ticketIds, targetLabelId) {
            if (!Array.isArray(ticketIds) || ticketIds.length === 0) {
                this.$showToast('No tickets selected', 'warning', 2000);
                return false;
            }

            const targetLabel = this.labels.find(l => l.id == targetLabelId);
            if (!targetLabel) {
                this.$showToast('Target label not found', 'error', 3000);
                return false;
            }

            let successCount = 0;
            let failCount = 0;

            for (const ticketId of ticketIds) {
                const result = await this.moveTicketToLabel(ticketId, targetLabelId);
                if (result) {
                    successCount++;
                } else {
                    failCount++;
                }
            }

            if (successCount > 0) {
                this.$showToast(`${successCount} ticket(s) moved successfully`, 'success', 3000);
            }

            if (failCount > 0) {
                this.$showToast(`${failCount} ticket(s) failed to move`, 'error', 3000);
            }

            return successCount > 0;
        },

        /**
         * Get ticket by ID across all labels
         */
        getTicketById(ticketId) {
            for (const label of this.labels) {
                const ticket = label.stores.find(s => s.id == ticketId);
                if (ticket) {
                    return {
                        ticket: ticket,
                        label: label
                    };
                }
            }
            return null;
        },

        /**
         * Refresh single ticket data from server
         */
        async refreshTicket(ticketId) {
            try {
                const response = await fetch(`/app/tickets/${ticketId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    const ticketData = this.getTicketById(ticketId);
                    if (ticketData) {
                        // Update ticket in place
                        Object.assign(ticketData.ticket, result.data);
                    }
                    return result.data;
                }

                return null;

            } catch (error) {
                console.error('Error refreshing ticket:', error);
                return null;
            }
        },

        /**
         * Format date to readable format
         */
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },

        /**
         * Get priority badge class
         */
        getPriorityBadge(priority) {
            const badges = {
                high: 'badge-danger',
                medium: 'badge-warning',
                low: 'badge-success'
            };
            return badges[priority] || 'badge-secondary';
        },

        /**
         * Get status badge class
         */
        getStatusBadge(status) {
            const badges = {
                open: 'badge-primary',
                in_progress: 'badge-warning',
                pending: 'badge-info',
                resolved: 'badge-success',
                closed: 'badge-secondary'
            };
            return badges[status] || 'badge-secondary';
        },

        /**
         * Initialize Drag & Drop for all kanban columns
         */
        initializeDragAndDrop() {
            // Destroy existing instances first
            this.destroyDragAndDrop();

            // Initialize Sortable for each label column
            this.labels.forEach(label => {
                const columnElement = this.$refs['column-' + label.id];

                if (columnElement && columnElement[0]) {
                    const sortable = new Sortable(columnElement[0], {
                        group: 'kanban-tickets', // Allow dragging between columns
                        animation: 150,
                        handle: '.kanban-card', // The element to drag
                        draggable: '.kanban-card', // Which items can be dragged
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',

                        // Events
                        onEnd: (evt) => {
                            this.handleDrop(evt);
                        },

                        // Optional: disable if loading
                        disabled: this.loading,

                        // Force fallback for better compatibility
                        forceFallback: false,

                        // Scroll options
                        scroll: true,
                        scrollSensitivity: 60,
                        scrollSpeed: 10,

                        // Prevent dragging to itself
                        filter: '.no-drag'
                    });

                    this.sortableInstances.push(sortable);
                }
            });

        },

        /**
         * Destroy all Sortable instances
         */
        destroyDragAndDrop() {
            this.sortableInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            this.sortableInstances = [];
        },

        /**
         * Enable/Disable drag and drop
         */
        setDragEnabled(enabled) {
            this.sortableInstances.forEach(instance => {
                if (instance) {
                    instance.option('disabled', !enabled);
                }
            });
        },

        /**
         * Load ticket notes
         */
        async loadNotes(ticketId) {
            if (!ticketId) return;

            try {
                const response = await fetch(`/app/tickets/${ticketId}/notes`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    this.ticketNotes = data.data;
                } else {
                    console.error('Failed to load notes:', data.message);
                }
            } catch (error) {
                console.error('Error loading notes:', error);
            }
        },

        /**
         * Add new note
         */
        async addNote() {
            if (!this.newNote.trim() || this.isAddingNote) return;
            if (!this.selectedStore || !this.selectedStore.id) return;

            this.isAddingNote = true;

            try {
                const response = await fetch(`/app/tickets/${this.selectedStore.id}/notes`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        note: this.newNote
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Add note to beginning of array
                    this.ticketNotes.unshift(data.data);
                    this.newNote = '';

                } else {
                    alert('Failed to add note: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error adding note:', error);
                alert('Error adding note. Please try again.');
            } finally {
                this.isAddingNote = false;
            }
        },

        /**
         * Format date time for display
         */
        formatDateTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    },

    mounted() {

        // Load real data from API
        this.loadData();
        this.loadCategories();
        this.loadHumanAgents();

        // Initialize drag & drop after data loaded
        this.$nextTick(() => {
            this.initializeDragAndDrop();
        });
    },

    updated() {
        // Reinitialize drag & drop when labels change
        this.$nextTick(() => {
            this.initializeDragAndDrop();
        });
    },

    beforeUnmount() {
        // Destroy all Sortable instances
        this.destroyDragAndDrop();
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
    max-width: 360px;
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

.kanban-column-header .btn-icon {
    padding: 0.2rem 0.4rem;
    font-size: 0.9rem;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.kanban-column-header .btn-icon:hover {
    opacity: 1;
    background: #f8f9fa;
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

/* Sortable.js Drag & Drop Styles */
.sortable-ghost {
    opacity: 0.4;
    background: #e3f2fd;
    border: 2px dashed #2196f3;
}

.sortable-chosen {
    cursor: grabbing !important;
    transform: rotate(2deg);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.sortable-drag {
    opacity: 0.8;
    cursor: grabbing !important;
}

.kanban-card {
    cursor: grab;
    transition: all 0.2s ease;
    user-select: none;
}

.kanban-card:active {
    cursor: grabbing;
}

.kanban-cards {
    min-height: 100px;
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

/* Add Label Modal Styling */
.modal-dialog {
    max-width: 600px;
}

.form-label .text-danger {
    font-size: 0.8rem;
}

.input-group-text {
    min-width: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Create Ticket Button Highlight */
.btn-success {
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
}

.btn-success:hover {
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    transform: translateY(-1px);
}

/* Custom Card Styles */
.custom-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.custom-card .card-body {
    background: #ffffff;
}

/* Badge Styles */
.badge {
    font-weight: 500;
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
}

/* Form Control Focus */
.form-control:focus,
.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: none;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: background-color 0.15s ease-in-out;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
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

    .col-md-8 .row {
        gap: 0.5rem;
    }

    .col-md-8 .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Loading State */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Empty State */
.empty-column-state {
    padding: 1rem;
}

.empty-column-state i {
    opacity: 0.3;
}

.empty-column-state p {
    color: #6c757d !important;
    font-style: italic;
    font-size: 0.9rem;
}

/* Category Management Modal */
.modal-xl {
    max-width: 1140px;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.75rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.form-check-input {
    cursor: pointer;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #007bff;
    border-color: #dee2e6;
}

.page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Search Input in Modal */
.modal-body .input-group {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.modal-body .input-group:focus-within {
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.25);
}

/* Table Responsive */
.table-responsive {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Code tag styling */
code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-size: 0.875rem;
    color: #e83e8c;
}

/* Action buttons spacing */
.modal-body .row.mb-3 {
    align-items: center;
}

/* Timeline Styles for Activity History */
.timeline {
    position: relative;
    padding-left: 30px;
    margin-top: 1rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #e9ecef);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding-left: 0;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.timeline-marker i {
    color: #fff;
    font-size: 10px;
}

.timeline-content {
    background: #f8f9fa;
    border-left: 3px solid #007bff;
    padding: 0.75rem 1rem;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.timeline-content:hover {
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    transform: translateX(2px);
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: #495057;
}

.timeline-content p {
    font-size: 0.85rem;
    margin-bottom: 0;
}

.timeline-content small {
    font-size: 0.75rem;
}

/* Notes Timeline Styles */
.notes-timeline {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.note-item {
    background: #f8f9fa;
    border-left: 3px solid #28a745;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.note-item:hover {
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    transform: translateX(2px);
}

.note-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.note-user {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.note-user i {
    font-size: 1.2rem;
    color: #28a745;
}

.note-time {
    font-size: 0.75rem;
}

.note-content {
    font-size: 0.9rem;
    color: #495057;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Scrollbar styling for notes */
.notes-timeline::-webkit-scrollbar {
    width: 6px;
}

.notes-timeline::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.notes-timeline::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.notes-timeline::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Load More Container Styling */
.load-more-container {
    margin: 0.75rem 0;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
}

.load-more-container .btn {
    position: relative;
    transition: all 0.3s ease;
}

.load-more-container .btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.load-more-container .btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.load-more-container .progress {
    border-radius: 2px;
    background-color: #e9ecef;
}

.load-more-container .progress-bar {
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.6s ease;
}

/* Badge in load more button */
.load-more-container .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Counter badge colors */
.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-primary {
    background-color: #007bff !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/* Active Filter Badges */
.badge .bx-x {
    margin-left: 4px;
    font-size: 14px;
    vertical-align: middle;
}

.badge .bx-x:hover {
    opacity: 0.7;
}

.cursor-pointer {
    cursor: pointer;
}

/* Filter Button Badge */
.btn .badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.4rem;
}

/* Filter Modal Sections */
.modal-body h6 {
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
}

.modal-body h6 i {
    color: #007bff;
}

/* Filter Modal Form Controls */
#filterModal .form-label {
    font-weight: 500;
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

#filterModal .form-select,
#filterModal .form-control {
    font-size: 0.9rem;
}

/* Active Filters Display */
.badge.bg-info {
    font-size: 0.85rem;
    font-weight: 500;
    padding: 0.4rem 0.6rem;
}

.badge.bg-info .bx-x {
    cursor: pointer;
    transition: all 0.2s ease;
}

.badge.bg-info .bx-x:hover {
    transform: scale(1.2);
}
</style>
