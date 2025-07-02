@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="container">
        <h3>Edit Category</h3>

        <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

        <div id="loading" class="text-center my-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <form id="categoryForm" class="w-50">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" class="form-control" required />
                <div class="text-danger small" id="error-name"></div>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" id="slug" class="form-control" required />
                <div class="text-danger small" id="error-slug"></div>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="/categories" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>

    <script>
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const categoryId = window.location.pathname.split('/')[2];

        function showLoading(show = true) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function showToast(message, type = 'success') {
            const toastId = 'toast' + Date.now();
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => document.getElementById(toastId)?.remove(), 5000);
        }

        function clearErrors() {
            ['name', 'slug'].forEach(id => {
                document.getElementById(`error-${id}`).innerText = '';
            });
        }

        function showErrors(errors) {
            for (const [key, msgs] of Object.entries(errors)) {
                const errorDiv = document.getElementById(`error-${key}`);
                if (errorDiv) errorDiv.innerText = msgs[0];
            }
        }

        async function loadCategory() {
            showLoading(true);
            try {
                const res = await fetch(`/api/categories/${categoryId}`, { headers });
                console.log('Load category response:', res);
                if (!res.ok) throw new Error('Failed to load category');
                const data = await res.json();
                console.log('Category data:', data);
                const category = data.data;
                document.getElementById('name').value = category.name;
                document.getElementById('slug').value = category.slug;
            } catch (err) {
                console.error(err);
                showToast(err.message || 'Error loading category', 'danger');
                window.location.href = '/categories';
            } finally {
                showLoading(false);
            }
        }


        document.getElementById('categoryForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors();
            showLoading(true);

            const payload = {
                name: document.getElementById('name').value.trim(),
                slug: document.getElementById('slug').value.trim()
            };

            try {
                const res = await fetch(`/api/categories/${categoryId}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(payload)
                });

                showLoading(false);

                if (!res.ok) {
                    const errData = await res.json();
                    if (errData.errors) {
                        showErrors(errData.errors);
                    } else {
                        throw new Error(errData.message || 'Update failed');
                    }
                    return;
                }

                showToast('Category updated successfully');
                setTimeout(() => window.location.href = '/categories', 1500);
            } catch (err) {
                showLoading(false);
                showToast(err.message || 'Update failed', 'danger');
            }
        });

        loadCategory();
    </script>
@endsection