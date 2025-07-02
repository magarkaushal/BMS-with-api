<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #08182e, #102c4a, #1a3f5f);
            color: #fff;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 1rem;
            background-color: #fff;
            color: #08182e;
        }

        .form-label i {
            margin-right: 6px;
        }

        #toastContainer {
            z-index: 9999;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center text-white mb-4">
                    <h3><i class="bi bi-shield-lock-fill me-1"></i>Login</h3>
                    <p class="text-light small">Secure access to your dashboard</p>
                </div>
                <form id="loginForm" class="card p-4 shadow-lg">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill"></i>Email Address
                        </label>
                        <input type="email" id="email" class="form-control" placeholder="you@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" class="form-control" placeholder="********" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword"
                                title="Toggle password visibility">
                                <i class="bi bi-eye-fill" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                        <span id="loginText"><i class="bi bi-box-arrow-in-right"></i> Login</span>
                        <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                    <div class="text-center">
                        <a href="/register" class="btn btn-link">Don't have an account? Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="toastContainer" class="position-fixed top-0 end-0 p-3"></div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>

        document.getElementById("togglePassword").addEventListener("click", function () {
            const pwd = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");
            const isPassword = pwd.type === "password";
            pwd.type = isPassword ? "text" : "password";
            eyeIcon.className = isPassword ? "bi bi-eye-slash-fill" : "bi bi-eye-fill";
        });


        function showToast(message, type = 'danger') {
            const toastId = 'toast' + Date.now();
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 show mb-2 shadow" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => document.getElementById(toastId)?.remove(), 5000);
        }


        document.getElementById("loginForm").addEventListener("submit", function (e) {
            e.preventDefault();

            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');

            loginText.classList.add('d-none');
            loginSpinner.classList.remove('d-none');

            fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: document.getElementById("email").value,
                    password: document.getElementById("password").value,
                })
            })
                .then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                })
                .then(data => {
                    localStorage.setItem('token', data.token);
                    window.location.href = '/posts';
                })
                .catch(async (err) => {
                    let errorMsg = 'Login failed. Please check your credentials.';
                    if (err.json) {
                        const json = await err.json();
                        errorMsg = json.message || JSON.stringify(json);
                    }
                    showToast(errorMsg, 'danger');
                })
                .finally(() => {
                    loginText.classList.remove('d-none');
                    loginSpinner.classList.add('d-none');
                });
        });
    </script>
</body>

</html>