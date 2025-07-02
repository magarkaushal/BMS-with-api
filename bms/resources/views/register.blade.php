<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Register | BMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
          
            background: linear-gradient(to right, #08182e, #102c4a, #1a3f5f);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 1rem;
        }

        .card {
            background-color: rgba(255 255 255 / 0.1);
            border-radius: 12px;
            box-shadow: 0 8px 32px 0 rgba(0 0 0 / 0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255 255 255 / 0.18);
        }

        .form-label {
            font-weight: 600;
            color: #cfd8dc;
        }

        input.form-control {
            background-color: rgba(255 255 255 / 0.15);
            border: none;
            color: #e0e0e0;
            transition: background-color 0.3s ease;
        }

        input.form-control:focus {
            background-color: rgba(255 255 255 / 0.25);
            color: #fff;
            box-shadow: none;
            border: 1px solid #4dabf7;
        }

        .btn-primary {
            background-color: #4dabf7;
            border: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background-color: #1971c2;
        }

        .btn-link {
            color: #90caf9;
            font-weight: 500;
        }

        .btn-link:hover {
            text-decoration: underline;
            color: #bbdefb;
        }

        #toastContainer {
            z-index: 9999;
        }

        .text-danger {
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4">
                    <h4 class="mb-4 text-center"style="color: white;">
                        <i class="bi bi-person-plus-fill me-2"></i> Register Your Account
                    </h4>
                    <form id="registerForm" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="bi bi-person-circle me-1"></i> Full Name</label>
                            <input type="text" id="name" class="form-control" required />
                            <div class="text-danger small" id="error-name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope-fill me-1"></i> Email Address</label>
                            <input type="email" id="email" class="form-control" required />
                            <div class="text-danger small" id="error-email"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="bi bi-lock-fill me-1"></i> Password</label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" required />
                                <button class="btn btn-outline-light" type="button" id="togglePassword" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="text-danger small" id="error-password"></div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label"><i class="bi bi-lock-fill me-1"></i> Confirm Password</label>
                            <input type="password" id="password_confirmation" class="form-control" required />
                            <div class="text-danger small" id="error-password_confirmation"></div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary" id="registerBtn">
                                <span id="registerText"><i class="bi bi-check-circle me-1"></i> Register</span>
                                <span id="registerSpinner" class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="text-center">
                            <a href="/login" class="btn btn-link">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3"></div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        document.getElementById('togglePassword').addEventListener('click', function () {
            const pwd = document.getElementById('password');
            const icon = this.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        function showToast(message, type = 'danger') {
            const toastId = 'toast' + Date.now();
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 show mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => document.getElementById(toastId)?.remove(), 5000);
        }

        function clearErrors() {
            ['name', 'email', 'password', 'password_confirmation'].forEach(field => {
                document.getElementById(`error-${field}`).innerText = '';
            });
        }

        function showErrors(errors) {
            for (const [key, messages] of Object.entries(errors)) {
                const errorDiv = document.getElementById(`error-${key}`);
                if (errorDiv) errorDiv.innerText = messages[0];
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const registerBtn = document.getElementById('registerBtn');
            const registerText = document.getElementById('registerText');
            const registerSpinner = document.getElementById('registerSpinner');

            registerSpinner.classList.remove('d-none');
            registerText.classList.add('d-none');
            clearErrors();

            fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    password_confirmation: document.getElementById('password_confirmation').value
                })
            })
                .then(res => {
                    if (!res.ok) return res.json().then(err => Promise.reject(err));
                    return res.json();
                })
                .then(data => {
                    localStorage.setItem('token', data.token);
                    showToast(' Registration successful!', 'success');
                    setTimeout(() => {
                        window.location.href = '/posts';
                    }, 1000);
                })
                .catch(err => {
                    if (err.errors) {
                        showErrors(err.errors);
                    } else {
                        showToast(err.message || 'Registration failed');
                    }
                })
                .finally(() => {
                    registerSpinner.classList.add('d-none');
                    registerText.classList.remove('d-none');
                });
        });
    </script>
</body>

</html>
