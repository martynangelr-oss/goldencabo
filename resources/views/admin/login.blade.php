<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Golden Cabo</title>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
body{background:linear-gradient(160deg,#005F53,#00AC97);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}
.login-box{background:#fff;border-radius:24px;padding:48px 40px;max-width:400px;width:100%;box-shadow:0 40px 80px rgba(0,0,0,.25)}
.login-logo{text-align:center;margin-bottom:32px}
.login-logo .logo-mark{width:56px;height:56px;background:linear-gradient(145deg,#00AC97,#007B6D);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;margin:0 auto 12px}
.login-logo h1{font-family:'Playfair Display',serif;font-size:1.5rem;color:#051E1C}
.login-logo p{font-family:'Josefin Sans',sans-serif;font-size:.6rem;letter-spacing:3px;text-transform:uppercase;color:#3A9C97;margin-top:4px}
.form-group{margin-bottom:18px}
.form-group label{display:block;font-family:'Josefin Sans',sans-serif;font-size:.6rem;letter-spacing:2px;text-transform:uppercase;color:#1A6B67;margin-bottom:8px;font-weight:700}
.form-group input{width:100%;padding:13px 16px;border-radius:12px;border:1.5px solid #D4EDEB;background:#F2FBFA;font-size:.9rem;outline:none;transition:all .25s}
.form-group input:focus{border-color:#00AC97;background:#fff;box-shadow:0 0 0 4px rgba(0,172,151,.1)}
.err-msg{font-family:'Josefin Sans',sans-serif;font-size:.68rem;color:#E5414A;margin-top:6px}
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <div class="logo-mark">✦</div>
    <h1>Panel Administrativo</h1>
    <p>Golden Cabo Transportation</p>
  </div>
  <form method="POST" action="{{ route('admin.login.post') }}">
    @csrf
    <div class="form-group">
      <label>Contraseña</label>
      <input type="password" name="password" autofocus required placeholder="Ingrese su contraseña">
      @error('password')<div class="err-msg">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-teal" style="width:100%;justify-content:center;margin-top:8px">
      Acceder al Panel
    </button>
  </form>
</div>
</body>
</html>
