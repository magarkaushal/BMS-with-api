@extends('layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-folder2-open me-2"></i> Categories</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCreateCategoryModal()">
            <i class="bi bi-plus-circle me-1"></i> Add Category
        </button>
    </div>

    <div class="mb-3 d-flex align-items-center gap-3 flex-wrap">
        <button id="exportBtn" class="btn btn-outline-info">
            <i class="bi bi-cloud-arrow-down me-1"></i> Export
        </button>

        <form id="importForm" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required class="form-control" style="max-width: 250px;" />
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-cloud-arrow-up me-1"></i> Import
            </button>
        </form>
    </div>

    <table class="table table-hover table-bordered shadow-sm" id="categoriesTable">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th><i class="bi bi-card-text me-1"></i> Name</th>
                <th><i class="bi bi-link me-1"></i> Slug</th>
                <th><i class="bi bi-gear me-1"></i> Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="categoryForm" class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="categoryModalLabel"><i class="bi bi-folder-plus me-2"></i>Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="categoryId" />
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Name</label>
                    <input type="text" id="categoryName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="categorySlug" class="form-label">Slug</label>
                    <input type="text" id="categorySlug" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>
@endsection

@section('scripts')
<script>
   // const token = localStorage.getItem('token');
    let categoriesTableBody = document.querySelector('#categoriesTable tbody');
    let categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

    function showToast(message, type = 'success') {
        const toastId = 'toast' + Date.now();
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div id="${toastId}" class="toast show align-items-center text-white bg-${type} border-0 shadow-sm mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => document.getElementById(toastId)?.remove(), 4000);
    }

    async function loadCategories() {
        try {
            const res = await axios.get('/api/categories', {
                headers: { Authorization: `Bearer ${token}` }
            });
            categoriesTableBody.innerHTML = '';
            res.data.data.forEach(cat => {
                categoriesTableBody.innerHTML += `
                    <tr>
                        <td>${cat.id}</td>
                        <td>${cat.name}</td>
                        <td>${cat.slug}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditCategoryModal(${cat.id})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            console.error('Failed to load categories:', error);
            showToast('Failed to load categories', 'danger');
        }
    }

    function openCreateCategoryModal() {
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryName').value = '';
        document.getElementById('categorySlug').value = '';
        categoryModal.show();
    }

    async function openEditCategoryModal(id) {
        try {
            const res = await axios.get(`/api/categories/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const category = res.data.data;
            document.getElementById('categoryModalLabel').textContent = 'Edit Category';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categorySlug').value = category.slug;
            categoryModal.show();
        } catch (error) {
            showToast('Failed to load category data', 'danger');
        }
    }

    async function deleteCategory(id) {
        if (!confirm('Are you sure you want to delete this category?')) return;
        try {
            await axios.delete(`/api/categories/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            showToast('Category deleted successfully');
            loadCategories();
        } catch (error) {
            showToast(error.response?.data?.message || 'Failed to delete category', 'danger');
        }
    }

    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('categoryId').value;
        const payload = {
            name: document.getElementById('categoryName').value,
            slug: document.getElementById('categorySlug').value,
        };

        try {
            if (id) {
                await axios.put(`/api/categories/${id}`, payload, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                showToast('Category updated successfully');
            } else {
                await axios.post('/api/categories', payload, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                showToast('Category created successfully');
            }
            categoryModal.hide();
            loadCategories();
        } catch (error) {
            showToast(error.response?.data?.message || 'Failed to save category', 'danger');
        }
    });

    document.getElementById('importForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fileInput = document.getElementById('importFile');
        if (!fileInput.files.length) {
            return alert('Please choose a file.');
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        try {
            const response = await fetch('/api/categories/import', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData
            });

            const data = await response.json();
            if (!response.ok) return alert(data.message || 'Import failed');

            showToast('Import successful!');
            fileInput.value = '';
            loadCategories();
        } catch (err) {
            console.error('Import failed:', err);
            showToast('Import failed.', 'danger');
        }
    });

    document.getElementById('exportBtn').addEventListener('click', async () => {
        try {
            const response = await fetch('/api/categories/export', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            });

            if (!response.ok) throw new Error('Export failed');

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'categories.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (err) {
            console.error('Export failed:', err);
            showToast('Export failed.', 'danger');
        }
    });

    loadCategories();
</script>
@endsection
