<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Golden Cabo Transportation</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Helvetica Neue',Arial,sans-serif;background:linear-gradient(135deg,#051E1C,#007B6D);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}
.box{background:#fff;border-radius:24px;padding:48px 40px;max-width:400px;width:100%;box-shadow:0 40px 80px rgba(0,0,0,.3)}
.brand{text-align:center;margin-bottom:36px}
.logo-mark{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#00AC97,#007B6D);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:14px}
h1{font-size:1.5rem;color:#051E1C;margin-bottom:4px}
p{font-size:.82rem;color:#8CCBC6;letter-spacing:1px;text-transform:uppercase}
.fg{margin-bottom:16px}
label{display:block;font-size:.62rem;letter-spacing:2px;text-transform:uppercase;color:#1A6B67;margin-bottom:7px;font-weight:700}
input{width:100%;padding:12px 16px;border-radius:12px;border:1.5px solid #D4EDEB;background:#F2FBFA;font-size:.9rem;color:#051E1C;outline:none;transition:border-color .2s}
input:focus{border-color:#00AC97}
.cbx{display:flex;align-items:center;gap:8px;font-size:.82rem;color:#1A6B67;cursor:pointer}
.cbx input{width:auto;margin:0}
.btn{width:100%;padding:14px;background:linear-gradient(135deg,#00AC97,#009988);color:#fff;border:none;border-radius:100px;font-size:.76rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;margin-top:22px;transition:opacity .2s}
.btn:hover{opacity:.9}
.err{background:#FEE2E2;color:#991B1B;border-radius:10px;padding:10px 14px;font-size:.82rem;margin-bottom:16px}
</style>
</head>
<body>
<div class="box">
  <div class="brand">
    <div class="logo-mark">✦</div>
    <h1>Golden Cabo</h1>
    <p>Panel de Administración</p>
  </div>

  @if($errors->any())
    <div class="err">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="fg">
      <label>Correo Electrónico</label>
      <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@goldencabo.com">
    </div>
    <div class="fg">
      <label>Contraseña</label>
      <input type="password" name="password" required placeholder="••••••••">
    </div>
    <label class="cbx">
      <input type="checkbox" name="remember"> Recordar sesión
    </label>
    <button type="submit" class="btn">Iniciar Sesión</button>
  </form>
</div>
</body>
</html>
