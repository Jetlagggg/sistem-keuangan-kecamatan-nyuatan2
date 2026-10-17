<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuangan Kecamatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">

    <style>
        body {
            background-color: #0f172a; /* Warna latar belakang gelap (Dark Blue) */
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            padding: 40px 30px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-img {
            width: 100px; 
            height: auto;
            margin-bottom: 15px;
        }
        .app-title {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .app-subtitle {
            font-size: 0.85rem;
            color: #64748b;
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }
        .btn-login {
            background-color: #0f172a;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background-color: #1e293b;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('img/kubar.png') }}" alt="Logo Kubar" class="logo-img">
            
            <div class="app-title">Sistem Keuangan</div>
            <div class="app-subtitle">Kecamatan Nyuatan</div>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email Pegawai</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="nama@nyuatan.go.id" required autofocus>
                </div>
                @error('email')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
                @error('password')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-login">
                MASUK SISTEM <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted" style="font-size: 10px;">&copy; {{ date('Y') }} Kecamatan Nyuatan</small>
        </div>
    </div>

</body>
</html>