@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container">
    <h3>Create Category</h3>
    <form id="createCategoryForm" class="w-50">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Create Category</button>
        <a href="/categories" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('createCategoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            name: document.getElementById('name').value.trim(),
            slug: document.getElementById('slug').value.trim(),
        };

        try {
            await axios.post('/api/categories', payload);
            alert('Category created successfully');
            window.location.href = '/categories';
        } catch (err) {
            alert(err.response?.data?.message || 'Failed to create category');
        }
    });
</script>
@endsection
