@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h3>Edit User</h3>
    <form id="editUserForm" class="w-50">
        <input type="hidden" id="userId" />
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password <small>(leave blank to keep current)</small></label>
            <input type="password" id="password" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="roles" class="form-label">Roles</label>
            <select id="roles" class="form-select" multiple required>
                <option value="admin">admin</option>
                <option value="editor">editor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="/users" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const rolesSelect = document.getElementById('roles');
    const userIdInput = document.getElementById('userId');

    async function loadUser(id) {
        try {
            const res = await axios.get(`/api/users/${id}`);
            const user = res.data;
            userIdInput.value = user.id;
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;

       
            Array.from(rolesSelect.options).forEach(opt => {
                opt.selected = user.roles.some(r => r.name === opt.value);
            });
        } catch {
            alert('Failed to load user data');
            window.location.href = '/users';
        }
    }

    document.getElementById('editUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = userIdInput.value;

        const payload = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            roles: Array.from(rolesSelect.selectedOptions).map(opt => opt.value),
        };
        const password = document.getElementById('password').value;
        if (password) {
            payload.password = password;
            payload.password_confirmation = password; 
        }

        try {
            await axios.put(`/api/users/${id}`, payload);
            alert('User updated successfully');
            window.location.href = '/users';
        } catch (err) {
            alert(err.response?.data?.message || 'Failed to update user');
        }
    });

    (async () => {
        const urlParts = window.location.pathname.split('/');
        const userId = urlParts[urlParts.length - 1];
        await loadUser(userId);
    })();
</script>
@endsection
