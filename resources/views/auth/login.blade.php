<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Paolo Paolo D.A Matting & Accessories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #090d16;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(14, 165, 233, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 30%, rgba(99, 102, 241, 0.10) 0%, transparent 45%);
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 189, 248, 0.15);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px -10px rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-slate-100">

    <div class="w-full max-w-md login-card rounded-2xl p-8 sm:p-10 relative overflow-hidden">
        <!-- Accent Top Bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500"></div>

        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-cyan-400 p-0.5 shadow-xl shadow-cyan-500/20 mb-4">
                <div class="w-full h-full bg-[#090d16] rounded-[14px] flex items-center justify-center">
                    <span class="font-extrabold text-2xl font-display text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">PP</span>
                </div>
            </div>
            <h1 class="text-2xl font-bold font-display tracking-tight text-white">Paolo Paolo</h1>
            <p class="text-xs font-semibold tracking-wider uppercase text-cyan-400 mt-0.5">D.A Matting &amp; Accessories</p>
            <p class="text-xs text-slate-400 mt-1">Management &amp; Point of Sale System</p>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-rose-950/70 border border-rose-500/30 text-rose-300 text-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i class="fas fa-circle-exclamation"></i>
                <span>Authentication Failed</span>
            </div>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Username or Email -->
            <div>
                <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Username or Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-xl text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all"
                        placeholder="e.g. admin or cashier">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fas fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-xl text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-slate-400 hover:text-slate-300 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0 mr-2">
                    <span>Keep me logged in</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold text-sm tracking-wide shadow-lg shadow-cyan-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                <i class="fas fa-arrow-right-to-bracket mr-2"></i> Log In to System
            </button>
        </form>

        <!-- Demo Credentials Quick Chips -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5">
                Quick Demo Logins (Click to autofill)
            </div>
            <div class="flex items-center justify-center gap-2">
                <button type="button" onclick="fillLogin('admin', 'password123')" class="px-3 py-1.5 rounded-lg bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 text-xs text-cyan-400 font-medium transition-colors">
                    <i class="fas fa-crown text-[10px] mr-1"></i> Admin
                </button>
                <button type="button" onclick="fillLogin('cashier', 'password123')" class="px-3 py-1.5 rounded-lg bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 text-xs text-indigo-300 font-medium transition-colors">
                    <i class="fas fa-cash-register text-[10px] mr-1"></i> Cashier
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
