@extends('layouts.app')

@section('styles')
<style>
    .permission-module {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .permission-module:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border-color: #d1d5db;
    }

    .permission-module h6 {
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.625rem;
    }

    .permission-item {
        background: #fafafa;
        border-radius: 6px;
        padding: 0.625rem 0.75rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid #f0f0f0;
    }

    .permission-item:hover {
        background: #f5f5f5;
        border-color: #e0e0e0;
        transform: translateY(-1px);
    }

    .permission-item.selected {
        background: #f0f9ff;
        border-color: #3b82f6;
    }

    .permission-checkbox {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    .permission-label {
        cursor: pointer;
        margin-left: 0.5rem;
        font-size: 0.8125rem;
        color: #4b5563;
        font-weight: 500;
    }

    .permission-item.selected .permission-label {
        color: #1e40af;
    }

    .badge-role {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .module-icon {
        width: 22px;
        height: 22px;
        background: #f3f4f6;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .select-all-module {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 0.5rem 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.8125rem;
    }

    .select-all-module:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
</style>
@endsection

@section('button')
<button type="button" class="btn btn-primary" onclick="openCreateModal()">
    <i class='bx bx-plus'></i> Tambah Role Baru
</button>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Daftar Role & Permission
                </div>
                <div class="d-flex gap-2">
                    <input type="text" id="searchRole" class="form-control form-control-sm" placeholder="Cari role..." style="width: 200px;">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap" id="rolesTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Role</th>
                                <th width="15%">Total Permission</th>
                                <th width="15%">Total User</th>
                                <th width="20%">Dibuat</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rolesTableBody">
                            @forelse($roles as $index => $role)
                            <tr data-role-id="{{ $role->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm avatar-rounded">
                                            <span class="badge bg-primary-transparent">
                                                <i class='bx bx-shield'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold">{{ $role->name }}</span>
                                            @if($role->name === 'Administrator')
                                            <span class="badge bg-danger-transparent ms-2">Super Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-transparent">
                                        <i class='bx bx-check-shield me-1'></i>
                                        {{ $role->permissions_count ?? 0 }} Permissions
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-transparent">
                                        <i class='bx bx-user me-1'></i>
                                        {{ $role->users_count ?? 0 }} Users
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class='bx bx-calendar me-1'></i>
                                        {{ $role->created_at->format('d M Y, H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary-light"
                                            onclick="openPermissionModal('{{ $role->id }}')"
                                            title="Kelola Permission">
                                            <i class='bx bx-lock-alt'></i>
                                        </button>
                                        @if($role->name !== 'Administrator')
                                        <button type="button" class="btn btn-sm btn-warning-light"
                                            onclick="editRole('{{ $role->id }}', '{{ $role->name }}')"
                                            title="Edit Role">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger-light"
                                            onclick="deleteRole('{{ $role->id }}', '{{ $role->name }}')"
                                            title="Hapus Role">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="{{ asset('images/empty.svg') }}" alt="Empty" style="width: 200px; opacity: 0.5;">
                                    <p class="text-muted mt-3">Belum ada role yang dibuat</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit Role -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="roleModalTitle">Tambah Role Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <input type="hidden" id="roleId">
                    <div class="mb-3">
                        <label class="form-label">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roleName"
                            placeholder="Contoh: Manager, Operator, Customer Service" required>
                        <small class="text-muted">Nama role yang akan digunakan dalam sistem</small>
                    </div>

                    <div class="alert alert-primary-transparent d-flex align-items-start">
                        <i class='bx bx-info-circle fs-18 me-2'></i>
                        <div>
                            <strong>Info:</strong> Setelah membuat role, Anda dapat mengatur permission dengan klik icon <i class='bx bx-lock-alt'></i> di tabel.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRole">
                        <i class='bx bx-save me-1'></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Permission Management -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h6 class="modal-title mb-1">
                        <i class='bx bx-lock-alt me-2'></i>Kelola Permission
                    </h6>
                    <small id="permissionRoleName">Loading...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search & Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input type="text" class="form-control" id="searchPermission"
                                placeholder="Cari permission atau module...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-success-light btn-sm" onclick="selectAllPermissions()">
                            <i class='bx bx-check-double'></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-danger-light btn-sm" onclick="deselectAllPermissions()">
                            <i class='bx bx-x'></i> Hapus Semua
                        </button>
                    </div>
                </div>

                <!-- Permission Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border border-primary">
                            <div class="card-body text-center">
                                <h3 class="text-primary mb-1" id="selectedCount">0</h3>
                                <small class="text-muted">Permission Terpilih</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border border-success">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-1" id="totalModules">0</h3>
                                <small class="text-muted">Total Module</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border border-info">
                            <div class="card-body text-center">
                                <h3 class="text-info mb-1" id="totalPermissions">0</h3>
                                <small class="text-muted">Total Permission</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="permissionLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Memuat permission...</p>
                </div>

                <!-- Permission List -->
                <div id="permissionList" style="display: none;">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="savePermissions()">
                    <i class='bx bx-save me-1'></i> Simpan Permission
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentRoleId = null;
    let currentPermissions = [];
    let allModules = [];

    // Open Create Modal
    function openCreateModal() {
        document.getElementById('roleModalTitle').textContent = 'Tambah Role Baru';
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';
        new bootstrap.Modal(document.getElementById('roleModal')).show();
    }

    // Edit Role
    function editRole(roleId, roleName) {
        document.getElementById('roleModalTitle').textContent = 'Edit Role';
        document.getElementById('roleId').value = roleId;
        document.getElementById('roleName').value = roleName;
        new bootstrap.Modal(document.getElementById('roleModal')).show();
    }

    // Save Role (Create/Update)
    document.getElementById('roleForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const roleId = document.getElementById('roleId').value;
        const roleName = document.getElementById('roleName').value;
        const btnSave = document.getElementById('btnSaveRole');

        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Menyimpan...';

        try {
            const url = roleId ? `/app/master/roles/${roleId}` : '/app/master/roles';
            const method = roleId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: roleName
                })
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
                setTimeout(() => location.reload(), 2000);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message
            });
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bx bx-save me-1"></i> Simpan Role';
        }
    });

    // Delete Role
    function deleteRole(roleId, roleName) {
        Swal.fire({
            title: 'Hapus Role?',
            html: `Apakah Anda yakin ingin menghapus role <strong>${roleName}</strong>?<br><small class="text-muted">Aksi ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.value) {
                try {
                    const response = await fetch(`/app/master/roles/${roleId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        document.querySelector(`tr[data-role-id="${roleId}"]`).remove();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message
                    });
                }
            }
        });
    }

    // Open Permission Modal
    async function openPermissionModal(roleId) {
        currentRoleId = roleId;
        const modal = new bootstrap.Modal(document.getElementById('permissionModal'));
        modal.show();

        document.getElementById('permissionLoading').style.display = 'block';
        document.getElementById('permissionList').style.display = 'none';

        try {
            const response = await fetch(`/app/master/roles/${roleId}`);
            const html = await response.text();

            // Parse HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Extract data (you might need to adjust based on actual response)
            const roleNameElement = doc.querySelector('[data-role-name]');
            const roleName = roleNameElement ? roleNameElement.dataset.roleName : 'Unknown';

            document.getElementById('permissionRoleName').textContent = `Role: ${roleName}`;

            // Load modules and permissions
            await loadPermissions(roleId);

        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal memuat data permission'
            });
            modal.hide();
        }
    }

    // Load Permissions
    async function loadPermissions(roleId) {
        try {
            // Fetch role with permissions
            const response = await fetch(`/app/master/roles/${roleId}/permissions`);
            const data = await response.json();

            allModules = data.modules;
            currentPermissions = data.role_permissions || [];

            renderPermissions();
            updateStats();

            document.getElementById('permissionLoading').style.display = 'none';
            document.getElementById('permissionList').style.display = 'block';

        } catch (error) {
            console.error('Error loading permissions:', error);
        }
    }

    // Render Permissions
    function renderPermissions() {
        const container = document.getElementById('permissionList');
        container.innerHTML = '';

        allModules.forEach(module => {
            const moduleDiv = document.createElement('div');
            moduleDiv.className = 'permission-module';
            moduleDiv.dataset.module = module.slug;

            // Module Header
            const header = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <span class="module-icon">
                        <i class='bx bx-${module.icon || 'cube'}'></i>
                    </span>
                    ${module.name}
                    <span class="badge bg-white text-primary ms-2">${module.permissions.length} permissions</span>
                </h6>
                <div class="select-all-module" onclick="toggleModulePermissions('${module.slug}')">
                    <input type="checkbox" class="permission-checkbox module-checkbox" 
                           id="module-${module.slug}" 
                           onchange="toggleModulePermissions('${module.slug}')">
                    <label for="module-${module.slug}" class="permission-label mb-0">
                        Pilih Semua
                    </label>
                </div>
            </div>
        `;

            // Permissions Grid
            let permissionsHTML = '<div class="permission-grid">';
            module.permissions.forEach(permission => {
                const isChecked = currentPermissions.includes(permission.id);
                permissionsHTML += `
                <div class="permission-item ${isChecked ? 'selected' : ''}"  >
                    <input type="checkbox" 
                           class="permission-checkbox" 
                           id="perm-${permission.id}" 
                           data-module="${module.slug}"
                           ${isChecked ? 'checked' : ''}
                           onchange="togglePermission('${permission.id}', '${module.slug}')">
                    <label for="perm-${permission.id}" class="permission-label">
                        ${permission.name.split('.').pop()}
                    </label>
                </div>
            `;
            });
            permissionsHTML += '</div>';

            moduleDiv.innerHTML = header + permissionsHTML;
            container.appendChild(moduleDiv);

            // Update module checkbox state
            updateModuleCheckbox(module.slug);
        });
    }

    // Toggle Permission
    function togglePermission(permissionId, moduleSlug) {
        console.log("hallo")
        const checkbox = document.getElementById(`perm-${permissionId}`);
        const permItem = checkbox.closest('.permission-item');

        if (currentPermissions.includes(permissionId)) {
            currentPermissions = currentPermissions.filter(id => id !== permissionId);
            permItem.classList.remove('selected');
        } else {
            currentPermissions.push(permissionId);
            permItem.classList.add('selected');
        }

        updateModuleCheckbox(moduleSlug);
        updateStats();
    }

    // Toggle Module Permissions
    function toggleModulePermissions(moduleSlug) {
        console.log("hai");
        const moduleCheckbox = document.getElementById(`module-${moduleSlug}`);
        const isChecked = moduleCheckbox.checked;

        const module = allModules.find(m => m.slug === moduleSlug);
        if (!module) return;

        module.permissions.forEach(permission => {
            const checkbox = document.getElementById(`perm-${permission.id}`);
            const permItem = checkbox.closest('.permission-item');

            if (isChecked) {
                if (!currentPermissions.includes(permission.id)) {
                    currentPermissions.push(permission.id);
                }
                checkbox.checked = true;
                permItem.classList.add('selected');
            } else {
                currentPermissions = currentPermissions.filter(id => id !== permission.id);
                checkbox.checked = false;
                permItem.classList.remove('selected');
            }
        });

        updateStats();
    }

    // Update Module Checkbox
    function updateModuleCheckbox(moduleSlug) {
        const module = allModules.find(m => m.slug === moduleSlug);
        if (!module) return;

        const moduleCheckbox = document.getElementById(`module-${moduleSlug}`);
        const permissionIds = module.permissions.map(p => p.id);
        const checkedCount = permissionIds.filter(id => currentPermissions.includes(id)).length;

        if (checkedCount === 0) {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = false;
        } else if (checkedCount === permissionIds.length) {
            moduleCheckbox.checked = true;
            moduleCheckbox.indeterminate = false;
        } else {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = true;
        }
    }

    // Select All Permissions
    function selectAllPermissions() {
        allModules.forEach(module => {
            module.permissions.forEach(permission => {
                if (!currentPermissions.includes(permission.id)) {
                    currentPermissions.push(permission.id);
                }
            });
        });

        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
            const permItem = checkbox.closest('.permission-item');
            if (permItem) permItem.classList.add('selected');
        });

        updateStats();
    }

    // Deselect All Permissions
    function deselectAllPermissions() {
        currentPermissions = [];

        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            const permItem = checkbox.closest('.permission-item');
            if (permItem) permItem.classList.remove('selected');
        });

        updateStats();
    }

    // Update Stats
    function updateStats() {
        document.getElementById('selectedCount').textContent = currentPermissions.length;
        document.getElementById('totalModules').textContent = allModules.length;
        const totalPerms = allModules.reduce((sum, m) => sum + m.permissions.length, 0);
        document.getElementById('totalPermissions').textContent = totalPerms;
    }

    // Save Permissions
    async function savePermissions() {
        if (currentPermissions.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Pilih minimal 1 permission'
            });
            return;
        }

        try {
            const response = await fetch(`/app/master/roles/${currentRoleId}/permissions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    permission_ids: currentPermissions
                })
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                bootstrap.Modal.getInstance(document.getElementById('permissionModal')).hide();
                setTimeout(() => location.reload(), 2000);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message
            });
        }
    }

    // Search Functionality
    document.getElementById('searchRole')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#rolesTableBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('searchPermission')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const modules = document.querySelectorAll('.permission-module');

        modules.forEach(module => {
            const moduleText = module.textContent.toLowerCase();
            module.style.display = moduleText.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endsection