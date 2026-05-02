<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= !empty($pengaturan->nama_sekolah) ? $pengaturan->nama_sekolah : 'Sistem Informasi Kelulusan' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        .login-card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .login-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .input-field:focus {
            border-color: #4f5bd5;
            box-shadow: 0 0 0 3px rgba(79, 91, 213, 0.2);
        }
        
        .btn-login {
            transition: all 0.3s ease;
            background-color: #4f5bd5;
        }
        
        .btn-login:hover {
            background-color: #3e4bc1;
            transform: translateY(-2px);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert-error {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="login-card w-full max-w-md p-8 bg-white rounded-xl">
        <!-- Logo -->
        <div class="flex flex-col items-center justify-center mb-8">
            <?php if (!empty($pengaturan->logo_sekolah) && file_exists(FCPATH . $pengaturan->logo_sekolah)): ?>
                <img src="<?= base_url($pengaturan->logo_sekolah) ?>" alt="Logo" class="h-16 w-auto mb-3">
            <?php elseif (file_exists(FCPATH . 'uploads/logo_sekolah.png')): ?>
                <img src="<?= base_url('uploads/logo_sekolah.png') ?>" alt="Logo" class="h-16 w-auto mb-3">
            <?php else: ?>
                <div class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center mb-3">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
            <?php endif; ?>
            <span class="text-xl font-bold text-gray-800 text-center">
                <?= !empty($pengaturan->nama_sekolah) ? $pengaturan->nama_sekolah : 'Sistem Informasi Kelulusan' ?>
            </span>
        </div>
        
        <!-- Flash Message -->
        <div id="flashMessage" class="alert-error mb-6 px-4 py-3 rounded-lg bg-red-100 text-red-700 flex items-center <?= $this->session->flashdata('error') ? '' : 'hidden' ?>">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span><?= $this->session->flashdata('error') ?></span>
        </div>
        
        <!-- Login Form -->
        <form id="loginForm" method="post" class="space-y-6" action="<?= base_url('auth/login') ?>">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username" 
                        value="<?= set_value('username') ?>" 
                        class="input-field w-full pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-1"
                        required
                    >
                </div>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        class="input-field w-full pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-1"
                        required
                    >
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me" 
                        name="remember-me" 
                        type="checkbox" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                
                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot password?
                    </a>
                </div>
            </div>
            
            <div>
                <button 
                    id="loginBtn"
                    type="submit" 
                    class="btn-login w-full py-3 px-4 text-white font-medium rounded-lg flex items-center justify-center"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    <span id="btnText">Sign in</span>
                    <svg id="btnSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Tampilkan loading
                loginBtn.disabled = true;
                btnText.textContent = "Signing in...";
                btnSpinner.classList.remove('hidden');

                // Simulasi delay 3 detik
                setTimeout(() => {
                    // Submit form sebenarnya setelah delay
                    loginForm.submit();
                }, 2000);
            });
        });
    </script>
</body>
</html>
