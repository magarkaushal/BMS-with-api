@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container">
    <h3>Create User</h3>
    <form id="createUserForm" class="w-50">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="roles" class="form-label">Roles</label>
            <select id="roles" class="form-select" multiple required>
                <option value="admin">admin</option>
                <option value="editor">editor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create User</button>
        <a href="/users" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('createUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
            roles: Array.from(document.getElementById('roles').selectedOptions).map(o => o.value),
        };

        if (payload.password !== payload.password_confirmation) {
            alert('Passwords do not match');
            return;
        }

        try {
            await axios.post('/api/users', payload);
            alert('User created successfully');
            window.location.href = '/users';
        } catch (err) {
            alert(err.response?.data?.message || 'Failed to create user');
        }
    });
</script>
@endsection
