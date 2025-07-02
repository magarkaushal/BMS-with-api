@extends('layouts.app')

@section('content')
    <div class="container py-4" style="max-width: 700px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-pencil-square me-2"></i> Edit Post</h3>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Posts
            </a>
        </div>
        <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

        <div id="loading" class="text-center my-4" style="display: none;">
            <div class="spinner-border text-primary" role="status" aria-label="Loading">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <form id="postForm" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Title</label>
                <input type="text" id="title" class="form-control form-control-lg" placeholder="Enter post title" required>
                <div class="invalid-feedback" id="error-title"></div>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label fw-semibold">Category</label>
                <select id="category_id" class="form-select form-select-lg" required>
                    <option value="" disabled selected>Select a category</option>
                </select>
                <div class="invalid-feedback" id="error-category_id"></div>
            </div>

            <div class="mb-4">
                <label for="body" class="form-label fw-semibold">Body</label>
                <textarea id="body" class="form-control" rows="7" placeholder="Write your post content here..."
                    required></textarea>
                <div class="invalid-feedback" id="error-body"></div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-save me-1"></i>
                Update
                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"
                    id="btnSpinner"></span>
            </button>
        </form>
    </div>

    <script>
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const postId = window.location.pathname.split('/')[2];

        const loadingIndicator = document.getElementById('loading');
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');

        function showLoading(show = true) {
            loadingIndicator.style.display = show ? 'block' : 'none';
            submitBtn.disabled = show;
            btnSpinner.classList.toggle('d-none', !show);
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
            ['title', 'category_id', 'body'].forEach(id => {
                const errorDiv = document.getElementById(`error-${id}`);
                errorDiv.innerText = '';
                document.getElementById(id).classList.remove('is-invalid');
            });
        }

        function showErrors(errors) {
            for (const [key, msgs] of Object.entries(errors)) {
                const errorDiv = document.getElementById(`error-${key}`);
                if (errorDiv) {
                    errorDiv.innerText = msgs[0];
                    document.getElementById(key).classList.add('is-invalid');
                }
            }
        }

        async function fetchCategories() {
            try {
                const res = await fetch('/api/categories', { headers });
                const data = await res.json();
                const select = document.getElementById('category_id');
                data.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error(error);
                showToast('Failed to load categories', 'danger');
            }
        }

        async function fetchPost() {
            try {
                const res = await fetch(`/api/posts/${postId}`, { headers });
                if (!res.ok) throw new Error('Post not found or unauthorized');
                const post = await res.json();
                document.getElementById('title').value = post.data.title;
                document.getElementById('body').value = post.data.body;
                document.getElementById('category_id').value = post.data.category.id;
            } catch (error) {
                console.error(error);
                showToast(error.message || 'Failed to load post', 'danger');
            }
        }

        document.getElementById('postForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();
            showLoading(true);

            try {
                const res = await fetch(`/api/posts/${postId}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify({
                        title: document.getElementById('title').value.trim(),
                        body: document.getElementById('body').value.trim(),
                        category_id: document.getElementById('category_id').value,
                    })
                });

                if (!res.ok) {
                    const err = await res.json();
                    if (err.errors) {
                        showErrors(err.errors);
                    } else {
                        throw new Error(err.message || 'Update failed');
                    }
                    showLoading(false);
                    return;
                }

                showToast(' Post updated successfully!');
                setTimeout(() => window.location.href = '/posts', 1500);

            } catch (error) {
                showToast(error.message || 'Update failed', 'danger');
            } finally {
                showLoading(false);
            }
        });


        showLoading(true);
        fetchCategories().then(() => fetchPost()).finally(() => showLoading(false));
    </script>
@endsection