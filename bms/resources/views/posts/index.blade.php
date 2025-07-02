@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-journal-text me-2"></i>All Posts
        </h3>

        <div class="d-flex gap-2">
            <a href="{{ route('posts.create') }}" class="btn btn-success d-flex align-items-center shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Create Post
            </a>

            <button id="exportPostsBtn" class="btn btn-outline-primary d-flex align-items-center shadow-sm">
                <i class="bi bi-download me-1"></i> Export 
            </button>
        </div>
    </div>


    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

  
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0" id="postsTable">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 30%;">Title</th>
                    <th style="width: 15%;">Author</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 15%;">Created</th>
                    <th style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showToast(message, type = 'success') {
        const toastId = 'toast' + Date.now();
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 shadow show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.getElementById('toastContainer').appendChild(toast);

        const bsToast = new bootstrap.Toast(toast.querySelector('.toast'));
        bsToast.show();

        toast.querySelector('.toast').addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    async function loadPosts() {
        try {
            const res = await axios.get('/api/posts');
            const posts = res.data.data ?? res.data;

            const tbody = document.querySelector('#postsTable tbody');
            tbody.innerHTML = '';

            posts.forEach(post => {
                tbody.innerHTML += `
                    <tr>
                        <td>${post.id}</td>
                        <td>${escapeHtml(post.title)}</td>
                        <td>${escapeHtml(post.author_name ?? 'N/A')}</td>
                        <td>${escapeHtml(post.category?.name ?? 'Uncategorized')}</td>
                        <td>${new Date(post.created_at).toLocaleDateString()}</td>
                        <td>
                            <a href="/posts/${post.id}/edit" class="btn btn-sm btn-outline-primary me-1" title="Edit Post">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button onclick="deletePost(${post.id})" class="btn btn-sm btn-outline-danger" title="Delete Post">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } catch (err) {
            console.error(err);
            showToast('Failed to load posts', 'danger');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async function deletePost(id) {
        if (!confirm("Delete this post?")) return;
        try {
            await axios.delete(`/api/posts/${id}`);
            showToast(' Post deleted successfully');
            loadPosts();
        } catch (err) {
            console.error(err);
            showToast('Delete failed', 'danger');
        }
    }

    document.getElementById('exportPostsBtn').addEventListener('click', async () => {
        const btn = document.getElementById('exportPostsBtn');
        const token = localStorage.getItem('token');

        if (!token) {
            showToast('You must be logged in to export posts.', 'danger');
            return;
        }

        try {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Exporting...`;

            const response = await axios.get('/api/posts/export', {
                responseType: 'blob',
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            });

            const blob = new Blob([response.data], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'posts.xlsx');
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);

            showToast(' Posts exported successfully!');
        } catch (error) {
            console.error('Export failed:', error);
            showToast('Export failed. Please try again.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-download me-1"></i> Export to Excel`;
        }
    });

    loadPosts();
</script>
@endsection
