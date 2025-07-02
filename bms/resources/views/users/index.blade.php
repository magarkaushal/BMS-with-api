@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people-fill me-2"></i> Manage Users</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateUserModal()">
            <i class="bi bi-plus-circle me-1"></i> Add User
        </button>
    </div>

    <table class="table table-hover table-bordered shadow-sm" id="usersTable">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th><i class="bi bi-person"></i> Name</th>
                <th><i class="bi bi-envelope"></i> Email</th>
                <th><i class="bi bi-shield-lock"></i> Roles</th>
                <th><i class="bi bi-gear"></i> Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="userForm" class="modal-content shadow-sm">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="userModalLabel"><i class="bi bi-person-plus me-2"></i>Add User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" id="userId" />
          <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" id="userName" class="form-control" placeholder="Full name" required />
          </div>
          <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" id="userEmail" class="form-control" placeholder="user@example.com" required />
          </div>
          <div class="mb-3">
              <label class="form-label">Password <small class="text-muted">(required for new users)</small></label>
              <input type="password" id="userPassword" class="form-control" />
          </div>
          <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" id="userPasswordConfirmation" class="form-control" />
          </div>
          <div class="mb-3">
              <label class="form-label">Roles</label>
              <select id="userRoles" class="form-select" multiple required>
                  <option value="admin">Admin</option>
                  <option value="editor">Editor</option>
              </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save2 me-1"></i> Save User
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
    const usersTableBody = document.querySelector('#usersTable tbody');
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const rolesSelect = document.getElementById('userRoles');

    function showToast(message, type = 'success') {
        const toastId = 'toast' + Date.now();
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 show position-fixed top-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => document.getElementById(toastId)?.remove(), 5000);
    }

    async function loadUsers() {
        try {
            const res = await axios.get('/api/users');
            usersTableBody.innerHTML = '';
            res.data.forEach(user => {
                usersTableBody.innerHTML += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.roles.map(r => r.name).join(', ')}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditUserModal(${user.id})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } catch {
            showToast('Failed to load users', 'danger');
        }
    }

    function openCreateUserModal() {
        document.getElementById('userModalLabel').textContent = 'Add User';
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('userEmail').value = '';
        document.getElementById('userPassword').value = '';
        document.getElementById('userPasswordConfirmation').value = '';
        rolesSelect.value = [];
        userModal.show();
    }

    async function openEditUserModal(id) {
        try {
            const res = await axios.get(`/api/users/${id}`);
            const user = res.data;
            document.getElementById('userModalLabel').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userPassword').value = '';
            document.getElementById('userPasswordConfirmation').value = '';
            rolesSelect.value = user.roles.map(r => r.name);
            userModal.show();
        } catch {
            showToast('Failed to load user data', 'danger');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Delete this user?')) return;
        try {
            await axios.delete(`/api/users/${id}`);
            showToast('User deleted successfully');
            loadUsers();
        } catch {
            showToast('Failed to delete user', 'danger');
        }
    }

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('userId').value;

        const payload = {
            name: document.getElementById('userName').value.trim(),
            email: document.getElementById('userEmail').value.trim(),
            roles: Array.from(rolesSelect.selectedOptions).map(o => o.value),
        };
        const password = document.getElementById('userPassword').value;
        const passwordConfirmation = document.getElementById('userPasswordConfirmation').value;

        if (!id && !password) {
            showToast('Password is required for new user', 'danger');
            return;
        }
        if (password && password !== passwordConfirmation) {
            showToast('Passwords do not match', 'danger');
            return;
        }
        if (password) {
            payload.password = password;
            payload.password_confirmation = passwordConfirmation;
        }

        try {
            if (id) {
                await axios.put(`/api/users/${id}`, payload);
                showToast('User updated successfully');
            } else {
                await axios.post('/api/users', payload);
                showToast('User created successfully');
            }
            userModal.hide();
            loadUsers();
        } catch (error) {
            showToast(error.response?.data?.message || 'Failed to save user', 'danger');
        }
    });

   (async () => {
    const token = localStorage.getItem('token');
    if (!token) return window.location.href = '/login';

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    try {
        const me = await axios.get('/api/me');
        const roles = me.data.roles;
        if (!roles.includes('admin')) {
            alert('Unauthorized: Only admins can manage users');
            return window.location.href = '/posts';
        }
        loadUsers();
    } catch (error) {
        alert('Failed to verify user role. Please login again.');
        localStorage.removeItem('token');
        window.location.href = '/login';
    }
})();
</script>
@endsection
