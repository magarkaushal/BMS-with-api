@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-journal-plus me-2"></i> Create New Post</h3>
        <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Posts
        </a>
    </div>

    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <div id="loading" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="card shadow rounded">
        <div class="card-body">
            <form id="postForm">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" id="title" class="form-control" placeholder="Enter post title">
                    <div class="text-danger small" id="error-title"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Category</label>
                    <select id="category_id" class="form-select"></select>
                    <div class="text-danger small" id="error-category_id"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Body</label>
                    <textarea id="body" class="form-control" rows="5" placeholder="Write post content here..."></textarea>
                    <div class="text-danger small" id="error-body"></div>
                </div>

            

                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-save me-1"></i>
                Create
                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"
                    id="btnSpinner"></span>
            </button>
            </form>
        </div>
    </div>
</div>

<script>
const token = localStorage.getItem('token');
const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
};

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
    ['title', 'category_id', 'body'].forEach(id => {
        document.getElementById(`error-${id}`).innerText = '';
    });
}

function showErrors(errors) {
    for (const [key, msg] of Object.entries(errors)) {
        const errorDiv = document.getElementById(`error-${key}`);
        if (errorDiv) errorDiv.innerText = msg[0];
    }
}


showLoading(true);
fetch('/api/categories', { headers })
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById('category_id');
        select.innerHTML = '<option value="">Select category</option>';
        data.data.forEach(cat => {
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
    })
    .catch(err => {
        console.error(err);
        showToast('<i class="bi bi-exclamation-triangle-fill"></i> Failed to load categories', 'danger');
    })
    .finally(() => showLoading(false));


document.getElementById('postForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();
    showLoading(true);

    fetch('/api/posts', {
        method: 'POST',
        headers,
        body: JSON.stringify({
            title: document.getElementById('title').value,
            body: document.getElementById('body').value,
            category_id: document.getElementById('category_id').value,
        })
    })
    .then(res => {
        showLoading(false);
        if (!res.ok) return res.json().then(err => Promise.reject(err));
        return res.json();
    })
    .then(() => {
        showToast('<i class="bi bi-check-circle-fill"></i> Post created successfully');
        setTimeout(() => window.location.href = '/posts', 1500);
    })
    .catch(err => {
        showLoading(false);
        if (err.errors) {
            showErrors(err.errors);
        } else {
            showToast(err.message || 'Post creation failed', 'danger');
        }
    });
});
</script>
@endsection
