<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Blog Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #08182e !important;
        }

        .navbar-brand,
        .navbar-nav .nav-link,
        .navbar-toggler-icon {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffc107 !important;
        }

        .navbar-toggler {
            border-color: #ffffff66;
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1055;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .content-wrapper {
            padding-top: 2rem;
        }

        #logoutBtn {
            cursor: pointer;
        }

        .toast {
            opacity: 0.95;
        }
    </style>
</head>

<body>
   
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/posts"><i class="bi bi-journal-text me-2"></i>Blog Management System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a href="/posts" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Posts</a>
                    </li>
                    <li class="nav-item" id="navCategories" style="display:none;">
                        <a href="/categories" class="nav-link"><i class="bi bi-folder2 me-1"></i>Categories</a>
                    </li>
                    <li class="nav-item" id="navUsers" style="display:none;">
                        <a href="/users" class="nav-link"><i class="bi bi-people me-1"></i>Users</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button id="logoutBtn" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content-wrapper">
        @yield('content')
    </div>

    
    <div class="toast-container" id="toastContainer"></div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const token = localStorage.getItem('token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0 show shadow-sm`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        document.addEventListener('DOMContentLoaded', async () => {
            if (!token && !['/login', '/register'].includes(window.location.pathname)) {
                return window.location.href = '/login';
            }

            if (token) {
                try {
                    const { data } = await axios.get('/api/me');
                    const roles = data.roles;
                    if (roles.includes('admin')) {
                        document.getElementById('navCategories').style.display = 'block';
                        document.getElementById('navUsers').style.display = 'block';
                    }
                } catch (err) {
                    console.error(err);
                    localStorage.removeItem('token');
                    return window.location.href = '/login';
                }
            }

            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async () => {
                    try {
                        await axios.post('/api/logout');
                    } catch {}
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
