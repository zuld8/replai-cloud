<template>
    <div class="row">
        <!-- Improved Header Section -->
        <div class="col-12">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Top Row: Title & Actions -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="mb-1 d-flex align-items-center">
                                <i class="bx bx-sitemap me-2 text-primary"></i>
                                {{ $t('crm_pipeline') }}
                            </h5>
                            <small class="text-muted">
                                {{ $t('total') }}: <strong>{{ totalStores }}</strong> {{
                                    $t('total_contacts_stages') }} <strong>{{ labels.length }}</strong> {{
                                $t('stages') }}
                            </small>
                        </div>

                        <!-- Pipeline Actions Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown"
                                aria-label="Pipeline actions">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="openPipelineModal('create')">
                                        <i class="bx bx-plus-circle me-2 text-primary"></i>{{ $t('add_pipeline')
                                        }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        @click.prevent="openPipelineModal('edit', getCurrentPipeline())">
                                        <i class="bx bx-edit-alt me-2 text-warning"></i>{{ $t('edit_pipeline') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="openAddLabelModal(null)">
                                        <i class="bx bx-purchase-tag me-2 text-success"></i>{{ $t('add_label') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="openReorderModal()">
                                        <i class="bx bx-sort me-2 text-info"></i>{{ $t('arrange_labels') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#"
                                        @click.prevent="confirmDeletePipeline()">
                                        <i class="bx bx-trash me-2"></i>{{ $t('delete_pipeline') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bottom Row: Filters -->
                    <div class="row g-2 align-items-end">
                        <!-- Pipeline Selector -->
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small text-muted mb-1">
                                <i class="bx bx-sitemap me-1"></i>{{ $t('pipeline') }}
                            </label>
                            <select class="form-select form-select-sm" v-model="selectedPipeline"
                                @change="switchPipeline">
                                <option v-for="pipeline in pipelines" :key="pipeline.id" :value="pipeline.id">
                                    {{ pipeline.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label small text-muted mb-1">
                                <i class="bx bx-search me-1"></i>{{ $t('search_contact') }}
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bx bx-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    :placeholder="$t('search_contact_placeholder')" v-model="searchQuery"
                                    @input="debounceSearch">
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small text-muted mb-1">
                                <i class="bx bx-category me-1"></i>{{ $t('category') }}
                            </label>
                            <select class="form-select form-select-sm" v-model="filterCategory" @change="loadData">
                                <option value="">{{ $t('all_categories') }}</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Per Page -->
                        <div class="col-lg-2 col-md-2">
                            <label class="form-label small text-muted mb-1">
                                <i class="bx bx-list-ul me-1"></i>{{ $t('show') }}
                            </label>
                            <select class="form-select form-select-sm" v-model="perPage" @change="loadData">
                                <option :value="10">10</option>
                                <option :value="20">20</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>
                        </div>

                        <!-- Add Data Button -->
                        <div class="col-lg-3 col-md-12">
                            <button class="btn btn-primary btn-sm w-100" @click="openQuickAddModal()">
                                <i class="bx bx-plus-circle me-1"></i>{{ $t('add_contact') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="col-12 kanban-container mt-3">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ $t('loading_data') }}</span>
                </div>
                <p class="mt-3 text-muted">{{ $t('loading_pipeline') }}</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="labels.length === 0" class="text-center py-5">
                <i class="bx bx-folder-open display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">{{ $t('no_pipeline_yet') }}</h5>
                <p class="text-muted">{{ $t('no_pipeline_desc') }}</p>
                <button class="btn btn-primary" @click="openPipelineModal('create')">
                    <i class="bx bx-plus-circle me-1"></i>{{ $t('create_pipeline') }}
                </button>
            </div>

            <!-- Kanban Board -->
            <div v-else class="kanban-board">
                <!-- Each Column -->
                <div v-for="label in labels" :key="label.id" class="kanban-column">
                    <!-- Column Header -->
                    <div class="kanban-column-header" :style="{ borderTopColor: label.color }">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <span class="label-indicator" :style="{ backgroundColor: label.color }"></span>
                                <h6 class="mb-0 ms-2 flex-grow-1">{{ label.name }}</h6>

                                <!-- Virtual Badge -->
                                <span v-if="label.is_virtual" class="badge bg-info-subtle text-info ms-2"
                                    :title="$t('virtual_stage')">
                                    <i class="bx bx-ghost"></i>
                                </span>

                                <!-- Closeable Badge -->
                                <span v-if="label.is_closeable === 'yes'"
                                    class="badge bg-success-subtle text-success ms-2"
                                    :title="$t('closing_stage')">
                                    <i class="bx bx-flag"></i>
                                </span>
                            </div>

                            <!-- Column Actions Dropdown -->
                            <div class="dropdown" v-if="!label.is_virtual">
                                <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown"
                                    aria-label="Label actions">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            @click.prevent="openContactModalInLabel(label.id)">
                                            <i class="bx bx-plus me-2 text-primary"></i>{{ $t('add_contact') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" @click.prevent="editLabel(label)">
                                            <i class="bx bx-edit-alt me-2 text-warning"></i>{{ $t('edit_stage') }}
                                        </a>
                                    </li>
                                    <li v-if="label.is_closeable !== 'yes'">
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li v-if="label.is_closeable !== 'yes'">
                                        <a class="dropdown-item text-danger" href="#"
                                            @click.prevent="confirmDeleteLabel(label)">
                                            <i class="bx bx-trash me-2"></i>{{ $t('delete') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Counter -->
                        <div class="mt-2 d-flex align-items-center justify-content-between">
                            <span class="badge bg-primary rounded-pill">
                                {{ label.loaded_count }} / {{ label.total_stores }}
                            </span>
                            <small class="text-muted">
                                {{ label.is_virtual ? $t('virtual') : `${$t('position')} ${label.order}` }}
                            </small>
                        </div>
                    </div>

                    <!-- Cards Container -->
                    <div :ref="'column-' + label.id" class="kanban-cards" :data-label-id="label.id"
                        :data-is-virtual="label.is_virtual">

                        <!-- Contact Cards -->
                        <div v-for="store in label.stores" :key="store.id" class="kanban-card"
                            :data-store-id="store.id">
                            <!-- Card Header -->
                            <div class="card-header-custom">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="flex-grow-1">
                                        <h6 class="store-name mb-1">{{ store.name }}</h6>
                                        <small class="text-muted d-block">{{ store.email || '-' }}</small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown"
                                            aria-label="Contact actions">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="#" @click.prevent="showDetail(store)">
                                                    <i class="bx bx-show me-2 text-info"></i>{{ $t('view_detail')
                                                    }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" :href="'/app/stores/update/' + store.id">
                                                    <i class="bx bx-edit-alt me-2 text-warning"></i>{{ $t('edit')
                                                    }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    @click.prevent="startChatWithContact(store)">
                                                    <i class="bx bx-chat me-2 text-success"></i>{{
                                                    $t('start_chat') }}
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
                                <!-- Channel Badge -->
                                <div class="mb-2" v-if="store.from_channel">
                                    <span class="badge badge-sm" :class="getChannelClass(store.from_channel)">
                                        <i :class="getChannelIcon(store.from_channel)"></i>
                                        {{ getChannelName(store.from_channel) }}
                                    </span>
                                </div>

                                <!-- Phone -->
                                <div class="info-item" v-if="store.phone">
                                    <i class="bx bxl-whatsapp text-success"></i>
                                    <a :href="'https://wa.me/' + store.phone.replace(/\D/g, '')" target="_blank"
                                        class="ms-2 text-decoration-none text-success small">
                                        {{ store.phone }}
                                    </a>
                                </div>

                                <!-- Category -->
                                <div class="info-item mt-2" v-if="store.category">
                                    <i class="bx bx-category"></i>
                                    <span class="ms-2 small">{{ store.category.name }}</span>
                                </div>

                                <!-- Status Badge -->
                                <div class="mt-3">
                                    <span class="badge badge-sm" :class="getStatusClass(store.status)">
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
                                        <i class="bx bx-user-x"></i>
                                        <span class="ms-1">{{ $t('unassigned') }}</span>
                                    </div>
                                    <small class="text-muted">{{ store.updated_at }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Load More Button -->
                        <div v-if="label.has_more && !loadingMore[label.id]" class="load-more-container">
                            <button @click="loadMore(label.id)" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-down-arrow-circle me-1"></i>
                                {{ $t('load_more') }} ({{ label.total_stores - label.loaded_count }})
                            </button>
                        </div>

                        <!-- Loading More -->
                        <div v-if="loadingMore[label.id]" class="text-center my-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <p class="small text-muted mt-2">{{ $t('loading') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Add First Label (if only virtual label exists) -->
                <div v-if="labels.length === 1 && labels[0].is_virtual" class="kanban-column-add">
                    <button @click="openAddLabelModal(null)" class="btn-add-column">
                        <i class="bx bx-plus-circle"></i>
                        <span>{{ $t('add_first_stage') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Pipeline Management -->
        <div class="modal fade" id="pipelineModal" ref="pipelineModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ pipelineModalMode === 'create' ? $t('create_new_pipeline') :
                                $t('edit_pipeline') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ $t('pipeline_name') }}</label>
                            <input type="text" class="form-control" v-model="pipelineForm.name"
                                :placeholder="$t('pipeline_name_placeholder')">
                        </div>

                        <div class="mb-3" v-if="pipelineModalMode === 'create'">
                            <label class="form-label">{{ $t('template') }}</label>
                            <select class="form-select" v-model="pipelineForm.template">
                                <option value="default">{{ $t('template_default') }}</option>
                                <option value="sales">{{ $t('template_sales') }}</option>
                                <option value="service">{{ $t('template_service') }}</option>
                                <option value="custom">{{ $t('template_custom') }}</option>
                            </select>
                            <small class="text-muted">{{ $t('template_info') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ $t('pipeline_color') }}</label>
                            <div class="color-picker-grid">
                                <div v-for="color in colorOptions" :key="color" class="color-option"
                                    :class="{ active: pipelineForm.color === color }"
                                    :style="{ backgroundColor: color }" @click="pipelineForm.color = color">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel')
                            }}</button>
                        <button type="button" class="btn btn-primary" @click="savePipeline"
                            :disabled="!pipelineForm.name || pipelineSaving">
                            <span v-if="pipelineSaving">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ $t('saving') }}
                            </span>
                            <span v-else>
                                <i class="bx bx-save me-1"></i>
                                {{ pipelineModalMode === 'create' ? $t('create_pipeline') : $t('save') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Add/Edit Label -->
        <div class="modal fade" id="labelModal" ref="labelModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-purchase-tag me-2"></i>
                            {{ labelModalMode === 'create' ? $t('add_stage') : $t('edit_stage') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ $t('stage_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="labelForm.name"
                                :placeholder="$t('stage_name_placeholder')">
                        </div>

                        <!-- Position Field -->
                        <div class="mb-3">
                            <label class="form-label">{{ $t('position') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model.number="labelForm.position" min="1"
                                :placeholder="$t('position_placeholder')">
                            <small class="text-muted">
                                {{ $t('position_info') }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ $t('color') }}</label>
                            <div class="color-picker-grid">
                                <div v-for="color in colorOptions" :key="color" class="color-option"
                                    :class="{ active: labelForm.color === color }" :style="{ backgroundColor: color }"
                                    @click="labelForm.color = color" :title="color">
                                </div>
                            </div>
                        </div>

                        <div v-if="labelForm.is_closeable === 'yes'" class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            {{ $t('closing_stage_info') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel')
                            }}</button>
                        <button type="button" class="btn btn-primary" @click="saveLabel"
                            :disabled="!labelForm.name || !labelForm.position || labelSaving">
                            <span v-if="labelSaving">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ $t('saving') }}
                            </span>
                            <span v-else>
                                <i class="bx bx-save me-1"></i>{{ $t('save') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Add Contact in Label -->
        <div class="modal fade" id="quickAddContactModal" ref="quickAddContactModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('quick_add_contact') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ $t('name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="quickContactForm.name"
                                :placeholder="$t('contact_name_quick')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ $t('phone_number') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="quickContactForm.phone"
                                placeholder="628123456789">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ $t('email') }}</label>
                            <input type="email" class="form-control" v-model="quickContactForm.email"
                                :placeholder="$t('email_placeholder')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ $t('category') }}</label>
                            <select class="form-select" v-model="quickContactForm.category_id">
                                <option value="">{{ $t('select_category') }}</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel')
                            }}</button>
                        <button type="button" class="btn btn-primary" @click="saveQuickContact"
                            :disabled="!quickContactForm.name || !quickContactForm.phone || contactSaving">
                            <span v-if="contactSaving">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ $t('saving') }}
                            </span>
                            <span v-else>
                                <i class="bx bx-save me-1"></i>{{ $t('save') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Reorder Labels -->
        <div class="modal fade" id="reorderModal" ref="reorderModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-sort me-2"></i>{{ $t('reorder_labels') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>{{ $t('how_to_use') }}</strong>
                            <ul class="mb-0 mt-2">
                                <li>{{ $t('drag_drop_info') }}</li>
                                <li>{{ $t('locked_stages_info') }}</li>
                            </ul>
                        </div>

                        <div ref="sortableLabels" class="label-reorder-list">
                            <div v-for="(label, index) in sortableLabels" :key="label.id" class="label-reorder-item"
                                :class="{
                                    'disabled': label.is_virtual || label.is_closeable === 'yes',
                                    'closeable': label.is_closeable === 'yes'
                                }" :data-label-id="label.id">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-menu handle me-3"
                                        v-if="!label.is_virtual && label.is_closeable !== 'yes'"></i>
                                    <i class="bx bx-lock me-3 text-muted" v-else></i>
                                    <span class="label-indicator me-2" :style="{ backgroundColor: label.color }"></span>
                                    <span class="flex-grow-1">{{ label.name }}</span>
                                    <span v-if="label.is_virtual" class="badge bg-info-subtle text-info me-2">
                                        <i class="bx bx-ghost"></i>
                                    </span>
                                    <span v-if="label.is_closeable === 'yes'"
                                        class="badge bg-success-subtle text-success me-2">
                                        <i class="bx bx-flag"></i>
                                    </span>
                                    <span class="badge bg-secondary">{{ index + 1 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel')
                            }}</button>
                        <button type="button" class="btn btn-primary" @click="saveReorder" :disabled="reorderSaving">
                            <span v-if="reorderSaving">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ $t('saving') }}
                            </span>
                            <span v-else>
                                <i class="bx bx-save me-1"></i>{{ $t('save_order') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Start Chat with Device Selector -->
        <div class="modal fade" id="startChatModal" ref="startChatModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-chat me-2"></i>{{ $t('start_conversation') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ $t('contact') }}</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bx bx-user-circle fs-3 text-primary me-3"></i>
                                <div>
                                    <div class="fw-semibold">{{ chatContact.name }}</div>
                                    <small class="text-muted">{{ chatContact.phone }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ $t('select_device') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" v-model="chatContact.deviceId">
                                <option value="" disabled>{{ $t('select_device_placeholder') }}</option>
                                <option v-for="device in deviceList" :key="device.id" :value="device.id">
                                    {{ device.phone }} ({{ device.name }})
                                </option>
                            </select>
                            <small class="text-muted">{{ $t('device_info') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel')
                            }}</button>
                        <button type="button" class="btn btn-primary" @click="startChatSession"
                            :disabled="!chatContact.deviceId || startingChat">
                            <span v-if="startingChat">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ $t('processing') }}
                            </span>
                            <span v-else>
                                <i class="bx bx-chat me-1"></i>{{ $t('start_chat') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal (existing) -->
        <div class="modal fade" id="detailModal" tabindex="-1" ref="detailModal">
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
                        <a :href="'/app/stores/update/' + selectedStore.id" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i>{{ $t('edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sortable from 'sortablejs';

export default {
    data() {
        return {
            // Pipelines
            pipelines: [],
            selectedPipeline: null,

            // Labels & Stores
            labels: [],
            categories: [],
            loading: true,
            loadingMore: {},

            // Filters
            searchQuery: '',
            filterCategory: '',
            perPage: 20,
            searchTimeout: null,

            // Sortable
            sortableInstances: [],

            // Modals
            selectedStore: null,
            pipelineModalMode: 'create',
            labelModalMode: 'create',

            // Forms
            pipelineForm: {
                id: null,
                name: '',
                color: '#3b82f6',
                template: 'default'
            },
            labelForm: {
                id: null,
                name: '',
                color: '#3b82f6',
                position: null, // NEW
                pipeline_segment_id: null,
                is_closeable: 'no'
            },
            quickContactForm: {
                name: '',
                phone: '',
                email: '',
                category_id: '',
                label_id: null
            },
            chatContact: {
                id: null,
                name: '',
                phone: '',
                store_id: null,
                deviceId: ''
            },

            // Saving states
            pipelineSaving: false,
            labelSaving: false,
            contactSaving: false,
            startingChat: false,
            reorderSaving: false,

            // Reorder
            sortableLabels: [],
            sortableInstance: null,

            // Device list
            deviceList: [],

            // Color options
            colorOptions: [
                '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
                '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
                '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
                '#ec4899', '#f43f5e', '#64748b', '#6b7280', '#94a3b8'
            ]
        };
    },

    computed: {
        totalStores() {
            return this.labels.reduce((sum, label) => sum + label.total_stores, 0);
        }
    },

    methods: {

        getCurrentPipeline() {
            return this.pipelines.find(p => p.id === this.selectedPipeline);
        },

        async confirmDeletePipeline() {
            const pipeline = this.getCurrentPipeline();
            if (!pipeline) return;

            if (confirm(`Hapus pipeline "${pipeline.name}"? Semua label akan ikut terhapus.`)) {
                try {
                    const response = await fetch(`/app/kanban/pipelines/${pipeline.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.$showToast('Pipeline berhasil dihapus', 'success', 3000);
                        await this.loadPipelines();
                        await this.loadData();
                    } else {
                        this.$showToast(result.message, 'error', 3000);
                    }
                } catch (error) {
                    console.error('Error deleting pipeline:', error);
                    this.$showToast('Gagal menghapus pipeline', 'error', 3000);
                }
            }
        },


        openReorderModal() {
            this.sortableLabels = [...this.labels];

            const modal = new bootstrap.Modal(this.$refs.reorderModal);
            modal.show();

            this.$nextTick(() => {
                if (this.sortableInstance) {
                    this.sortableInstance.destroy();
                }

                const el = this.$refs.sortableLabels;
                if (el) {
                    this.sortableInstance = new Sortable(el, {
                        animation: 150,
                        handle: '.handle',
                        ghostClass: 'sortable-ghost-reorder',
                        filter: '.disabled',
                        onEnd: (evt) => {
                            const movedItem = this.sortableLabels.splice(evt.oldIndex, 1)[0];
                            this.sortableLabels.splice(evt.newIndex, 0, movedItem);
                        }
                    });
                }
            });
        },


        async saveReorder() {
            this.reorderSaving = true;

            try {
                const labelsToUpdate = this.sortableLabels
                    .filter(l => !l.is_virtual)
                    .map((label, index) => ({
                        id: label.id,
                        position: index + 1
                    }));

                const response = await fetch('/app/kanban/labels/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ labels: labelsToUpdate })
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast('Urutan label berhasil diupdate', 'success', 3000);

                    const modal = bootstrap.Modal.getInstance(this.$refs.reorderModal);
                    modal.hide();

                    await this.loadData();
                } else {
                    this.$showToast(result.message, 'error', 3000);
                }
            } catch (error) {
                console.error('Error saving reorder:', error);
                this.$showToast('Gagal menyimpan urutan', 'error', 3000);
            } finally {
                this.reorderSaving = false;
            }
        },


        openQuickAddModal() {
            this.quickContactForm = {
                name: '',
                phone: '',
                email: '',
                category_id: '',
                label_id: null
            };

            const modal = new bootstrap.Modal(this.$refs.quickAddContactModal);
            modal.show();
        },


        async startChatWithContact(store) {
            if (this.deviceList.length === 0) {
                await this.loadDeviceList();
            }

            this.chatContact = {
                id: store.id,
                name: store.name,
                phone: store.phone,
                store_id: store.id,
                deviceId: ''
            };

            // Open modal
            const modal = new bootstrap.Modal(this.$refs.startChatModal);
            modal.show();
        },

        /**
         * Load device list
         */
        async loadDeviceList() {
            try {
                const response = await fetch('/app/master/components/devices');
                const data = await response.json();
                this.deviceList = data || [];
            } catch (error) {
                console.error('Error loading devices:', error);
                this.deviceList = [];
            }
        },

        /**
         * Start chat session
         */
        async startChatSession() {
            if (!this.chatContact.deviceId || !this.chatContact.store_id) {
                this.$showToast('Pilih device terlebih dahulu', 'error', 3000);
                return;
            }

            this.startingChat = true;

            try {
                // Determine device type
                const device = this.deviceList.find(d => d.id === this.chatContact.deviceId);
                const type = device?.from === 'unofficial' ? 'whatsapp' : 'waba';

                // Create/get history chat
                const response = await fetch('/app/crm/contacts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: this.chatContact.name,
                        phone: this.chatContact.phone,
                        device_id: this.chatContact.deviceId,
                        type: type,
                        store_id: this.chatContact.store_id
                    })
                });

                const result = await response.json();

                if (result.success || result.contact) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(this.$refs.startChatModal);
                    modal.hide();

                    this.$showToast('Membuka chat...', 'success', 2000);

                    // Redirect to chat room - CORRECT URL
                    setTimeout(() => {
                        window.location.href = `/app/crm/chat/${result.contact.id}`;
                    }, 500);
                } else {
                    this.$showToast(result.message || 'Gagal membuat sesi chat', 'error', 3000);
                }
            } catch (error) {
                console.error('Error starting chat:', error);
                this.$showToast('Gagal memulai chat', 'error', 3000);
            } finally {
                this.startingChat = false;
            }
        },


        /**
         * Load pipelines
         */
        async loadPipelines() {
            try {
                const response = await fetch('/app/kanban/pipelines');
                const result = await response.json();

                if (result.success) {
                    this.pipelines = result.pipelines;

                    // Auto-select first pipeline
                    if (this.pipelines.length > 0 && !this.selectedPipeline) {
                        this.selectedPipeline = this.pipelines[0].id;
                        // Auto-load data for first pipeline
                        await this.loadData();
                    }
                }
            } catch (error) {
                console.error('Error loading pipelines:', error);
                this.$showToast('Gagal memuat pipeline', 'error', 3000);
            }
        },

        /**
         * Switch pipeline
         */
        switchPipeline() {
            this.loadData();
        },

        /**
         * Load kanban data
         */
        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    category: this.filterCategory,
                    per_page: this.perPage,
                    pipeline_id: this.selectedPipeline || ''
                });

                const response = await fetch(`/app/kanban/data?${params}`);
                const result = await response.json();

                if (result.success) {
                    this.labels = result.data;

                    this.$nextTick(() => {
                        this.initializeSortable();
                    });
                } else {
                    this.$showToast('Gagal memuat data', 'error', 3000);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.$showToast('Error saat memuat data', 'error', 3000);
            } finally {
                this.loading = false;
            }
        },


        /**
         * Load more stores
         */
        async loadMore(labelId) {
            this.loadingMore[labelId] = true;

            try {
                const label = this.labels.find(l => l.id === labelId);
                const currentOffset = label.loaded_count;

                const params = new URLSearchParams({
                    search: this.searchQuery,
                    category: this.filterCategory,
                    per_page: this.perPage,
                    offset: currentOffset
                });

                const response = await fetch(`/app/kanban/load-more/${labelId}?${params}`);
                const result = await response.json();

                if (result.success) {
                    label.stores.push(...result.data);
                    label.loaded_count += result.data.length;
                    label.has_more = result.pagination.has_more;

                    this.$nextTick(() => {
                        this.initializeSortable();
                    });
                }
            } catch (error) {
                console.error('Error loading more:', error);
            } finally {
                this.loadingMore[labelId] = false;
            }
        },

        /**
         * Load categories
         */
        async loadCategories() {
            try {
                const response = await fetch('/app/crm/categories');
                const result = await response.json();
                this.categories = result.categories || [];
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        /**
         * Initialize Sortable
         */
        initializeSortable() {
            this.sortableInstances.forEach(instance => instance.destroy());
            this.sortableInstances = [];

            this.labels.forEach(label => {
                const columnRef = this.$refs['column-' + label.id];
                if (columnRef && columnRef[0]) {
                    const sortable = new Sortable(columnRef[0], {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        handle: '.kanban-card',
                        filter: '.btn, .load-more-container, .add-label-container, .text-center',
                        preventOnFilter: true,
                        onEnd: (evt) => {
                            this.handleDrop(evt);
                        }
                    });
                    this.sortableInstances.push(sortable);
                }
            });
        },

        /**
         * Handle card drop
         */
        async handleDrop(evt) {
            const storeId = evt.item.dataset.storeId;
            const newLabelId = evt.to.dataset.labelId;
            const oldLabelId = evt.from.dataset.labelId;
            const isTargetVirtual = evt.to.dataset.isVirtual === 'true';

            // Same column - just reorder
            if (newLabelId === oldLabelId) {
                await this.updateOrder(oldLabelId);
                return;
            }

            try {
                const response = await fetch('/app/kanban/update-label', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        store_id: storeId,
                        label_id: isTargetVirtual ? null : newLabelId, // null for virtual column
                        position: evt.newIndex
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast('Label berhasil diupdate', 'success', 2000);

                    // Update counters
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
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error updating label:', error);
                this.$showToast('Gagal update label', 'error', 3000);
                await this.loadData();
            }
        },

        /**
         * Update order
         */
        async updateOrder(labelId) {
            const column = this.$refs['column-' + labelId][0];
            const cards = column.querySelectorAll('.kanban-card');

            const stores = Array.from(cards).map((card, index) => ({
                id: card.dataset.storeId,
                position: index
            }));

            try {
                await fetch('/app/kanban/update-position', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ stores })
                });
            } catch (error) {
                console.error('Error updating order:', error);
            }
        },

        /**
         * Pipeline Management
         */
        openPipelineModal(mode, pipeline = null) {
            this.pipelineModalMode = mode;

            if (mode === 'edit' && pipeline) {
                this.pipelineForm = {
                    id: pipeline.id,
                    name: pipeline.name,
                    color: pipeline.color || '#3b82f6',
                    template: 'default'
                };
            } else {
                this.pipelineForm = {
                    id: null,
                    name: '',
                    color: '#3b82f6',
                    template: 'default'
                };
            }

            const modal = new bootstrap.Modal(this.$refs.pipelineModal);
            modal.show();
        },

        async savePipeline() {
            if (!this.pipelineForm.name) return;

            this.pipelineSaving = true;

            try {
                const url = this.pipelineModalMode === 'create'
                    ? '/app/kanban/pipelines'
                    : `/app/kanban/pipelines/${this.pipelineForm.id}`;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.pipelineForm)
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast(result.message, 'success', 3000);

                    const modal = bootstrap.Modal.getInstance(this.$refs.pipelineModal);
                    modal.hide();

                    await this.loadPipelines();

                    if (this.pipelineModalMode === 'create') {
                        this.selectedPipeline = result.pipeline.id;
                    }

                    await this.loadData();
                } else {
                    this.$showToast(result.message, 'error', 3000);
                }
            } catch (error) {
                console.error('Error saving pipeline:', error);
                this.$showToast('Gagal menyimpan pipeline', 'error', 3000);
            } finally {
                this.pipelineSaving = false;
            }
        },

        /**
         * Label Management
         */
        openAddLabelModal(afterLabel) {
            this.labelModalMode = 'create';

            // Calculate next position
            const nonCloseableLabels = this.labels.filter(l => !l.is_virtual && l.is_closeable !== 'yes');
            const maxPosition = nonCloseableLabels.length > 0
                ? Math.max(...nonCloseableLabels.map(l => l.order))
                : 0;

            this.labelForm = {
                id: null,
                name: '',
                color: this.colorOptions[Math.floor(Math.random() * this.colorOptions.length)],
                position: maxPosition + 1, // Auto-set to next position
                pipeline_segment_id: this.selectedPipeline,
                is_closeable: 'no'
            };

            const modal = new bootstrap.Modal(this.$refs.labelModal);
            modal.show();
        },

        editLabel(label) {
            this.labelModalMode = 'edit';
            this.labelForm = {
                id: label.id,
                name: label.name,
                color: label.color || '#3b82f6',
                position: label.order, // Set current position
                pipeline_segment_id: label.pipeline_segment_id,
                is_closeable: label.is_closeable
            };

            const modal = new bootstrap.Modal(this.$refs.labelModal);
            modal.show();
        },

        async saveLabel() {
            if (!this.labelForm.name || !this.labelForm.position) {
                this.$showToast('Nama dan posisi wajib diisi', 'error', 3000);
                return;
            }

            this.labelSaving = true;

            try {
                const url = this.labelModalMode === 'create'
                    ? '/app/kanban/labels'
                    : `/app/kanban/labels/${this.labelForm.id}`;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.labelForm)
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast(result.message, 'success', 3000);

                    const modal = bootstrap.Modal.getInstance(this.$refs.labelModal);
                    modal.hide();

                    await this.loadData();
                } else {
                    this.$showToast(result.message, 'error', 3000);
                }
            } catch (error) {
                console.error('Error saving label:', error);
                this.$showToast('Gagal menyimpan label', 'error', 3000);
            } finally {
                this.labelSaving = false;
            }
        },

        confirmDeleteLabel(label) {
            if (label.total_stores > 0) {
                this.$showToast(`Label masih digunakan oleh ${label.total_stores} kontak`, 'error', 3000);
                return;
            }

            if (confirm(`Hapus label "${label.name}"?`)) {
                this.deleteLabel(label.id);
            }
        },

        async deleteLabel(labelId) {
            try {
                const response = await fetch(`/app/kanban/labels/${labelId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast('Label berhasil dihapus', 'success', 3000);
                    await this.loadData();
                } else {
                    this.$showToast(result.message, 'error', 3000);
                }
            } catch (error) {
                console.error('Error deleting label:', error);
                this.$showToast('Gagal menghapus label', 'error', 3000);
            }
        },

        /**
         * Quick Add Contact
         */
        openContactModalInLabel(labelId) {
            this.quickContactForm = {
                name: '',
                phone: '',
                email: '',
                category_id: '',
                label_id: labelId
            };

            const modal = new bootstrap.Modal(this.$refs.quickAddContactModal);
            modal.show();
        },

        async saveQuickContact() {
            if (!this.quickContactForm.name || !this.quickContactForm.phone) return;

            this.contactSaving = true;

            try {
                const response = await fetch('/app/kanban/stores/create-contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.quickContactForm)
                });

                const result = await response.json();

                if (result.success) {
                    this.$showToast('Kontak berhasil ditambahkan', 'success', 3000);

                    const modal = bootstrap.Modal.getInstance(this.$refs.quickAddContactModal);
                    modal.hide();

                    await this.loadData();
                } else {
                    this.$showToast(result.message, 'error', 3000);
                }
            } catch (error) {
                console.error('Error saving contact:', error);
                this.$showToast('Gagal menyimpan kontak', 'error', 3000);
            } finally {
                this.contactSaving = false;
            }
        },

        /**
         * Start chat with contact
         */
        async startChatWithContact(store) {
            if (this.deviceList.length === 0) {
                await this.loadDeviceList();
            }

            this.chatContact = {
                id: store.id,
                name: store.name,
                phone: store.phone,
                store_id: store.id,
                deviceId: ''
            };

            // Open modal
            const modal = new bootstrap.Modal(this.$refs.startChatModal);
            modal.show();
        },

        /**
         * Utilities
         */
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadData();
            }, 500);
        },

        getChannelClass(channel) {
            const classes = {
                'whatsapp': 'bg-success-subtle text-success',
                'waba': 'bg-success',
                'telegram': 'bg-info',
                'livechat': 'bg-warning',
            };
            return classes[channel?.toLowerCase()] || 'bg-secondary';
        },

        getChannelIcon(channel) {
            const icons = {
                'whatsapp': 'bx bxl-whatsapp',
                'waba': 'bx bxl-whatsapp',
                'telegram': 'bx bxl-telegram',
                'livechat': 'bx bx-message-square-dots',
            };
            return icons[channel?.toLowerCase()] || 'bx bx-message';
        },

        getChannelName(channel) {
            const names = {
                'whatsapp': 'WhatsApp',
                'waba': 'WA Business',
                'telegram': 'Telegram',
                'livechat': 'Live Chat',
            };
            return names[channel?.toLowerCase()] || channel;
        },

        getStatusClass(status) {
            const classes = {
                'open': 'bg-primary',
                'resolved': 'bg-success',
                'pending': 'bg-warning',
                'block': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        },

        getStatusText(status) {
            const texts = {
                'open': 'Open',
                'resolved': 'Resolved',
                'pending': 'Pending',
                'block': 'Block'
            };
            return texts[status] || status;
        },

        showDetail(store) {
            this.selectedStore = store;
            const modal = new bootstrap.Modal(this.$refs.detailModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        confirmDelete(storeId, labelId) {
            if (confirm('Hapus kontak ini?')) {
                this.deleteStore(storeId, labelId);
            }
        },

        async deleteStore(storeId, labelId) {
            try {
                await fetch(`/app/stores/delete/${storeId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                this.$showToast('Kontak berhasil dihapus', 'success', 3000);

                const label = this.labels.find(l => l.id === labelId);
                if (label) {
                    label.total_stores--;
                    label.loaded_count--;
                    label.stores = label.stores.filter(s => s.id !== storeId);
                }
            } catch (error) {
                console.error('Error deleting store:', error);
                this.$showToast('Gagal menghapus kontak', 'error', 3000);
            }
        },

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

        $t(key) {
            if (window.i18n && window.i18n.translations && window.i18n.translations[key]) {
                return window.i18n.translations[key];
            }
            return key;
        },
    },

    mounted() {
        this.loadPipelines();
        this.loadCategories();
        this.loadData();
        this.loadDeviceList();
    },

    beforeUnmount() {
        this.sortableInstances.forEach(instance => instance.destroy());
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }
    }
};
</script>

<style scoped>
.custom-card {
    border-radius: 12px;
    transition: box-shadow 0.3s;
}

.form-label.small {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.form-label.small i {
    font-size: 0.875rem;
}

.input-group-text.bg-light {
    background-color: #f8f9fa !important;
}

/* Label reorder list */
.label-reorder-list {
    max-height: 400px;
    overflow-y: auto;
}

.label-reorder-item {
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: move;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.label-reorder-item:not(.disabled):hover {
    background: #e9ecef;
    border-color: #3b82f6;
}

.label-reorder-item.disabled {
    cursor: not-allowed;
    opacity: 0.6;
    background: #fff;
}

.label-reorder-item.closeable {
    background: #d1fae5;
}

.label-reorder-item .handle {
    cursor: grab;
    color: #6b7280;
    font-size: 1.25rem;
}

.label-reorder-item .handle:active {
    cursor: grabbing;
}

.sortable-ghost-reorder {
    opacity: 0.4;
    background: #dbeafe;
}

.label-indicator {
    width: 4px;
    height: 20px;
    border-radius: 2px;
    display: inline-block;
}

.color-picker-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 0.5rem;
}

.color-option {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.color-option:hover {
    transform: scale(1.1);
}

.color-option.active {
    border-color: #1f2937;
    box-shadow: 0 0 0 2px white, 0 0 0 4px #1f2937;
}

/* Kanban improvements */
.kanban-column-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
    background: white;
    border-radius: 12px 12px 0 0;
    border-top: 3px solid transparent;
}

.btn-add-column {
    width: 100%;
    min-height: 120px;
    border: 2px dashed #cbd5e1;
    background: white;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add-column:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
}

.btn-add-column i {
    font-size: 2rem;
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
    border-top: 3px solid transparent;
}

.label-indicator {
    width: 4px;
    height: 24px;
    border-radius: 2px;
    display: inline-block;
}

.kanban-cards {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    max-height: calc(100vh - 350px);
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

.card-footer-custom {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    border-radius: 0 0 8px 8px;
}

.add-label-container {
    margin: 0.75rem 0;
    padding: 0 0.25rem;
}

.kanban-column-add {
    min-width: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-add-column {
    width: 100%;
    min-height: 120px;
    border: 2px dashed #cbd5e1;
    background: white;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add-column:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
}

.btn-add-column i {
    font-size: 2rem;
}

.color-picker-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 0.5rem;
}

.color-option {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.color-option:hover {
    transform: scale(1.1);
}

.color-option.active {
    border-color: #1f2937;
    box-shadow: 0 0 0 2px white, 0 0 0 4px #1f2937;
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

.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
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