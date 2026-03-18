<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nuevo Contacto</title>
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;padding:20px}
.box{max-width:580px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border-top:4px solid #00AC97}
h2{color:#051E1C}table{width:100%;border-collapse:collapse;margin-top:16px}
td{padding:8px 12px;border-bottom:1px solid #f0f0f0;font-size:.9rem}
td:first-child{color:#888;width:140px}
.msg{background:#F2FBFA;border-radius:8px;padding:14px;margin-top:16px;font-size:.88rem;line-height:1.7;color:#1A6B67}</style>
</head>
<body>
<div class="box">
<h2>✉️ Nuevo Mensaje de Contacto</h2>
<table>
  <tr><td>Nombre</td><td>{{ $contact->full_name }}</td></tr>
  <tr><td>Email</td><td>{{ $contact->email }}</td></tr>
  <tr><td>Teléfono</td><td>{{ $contact->phone ?? '—' }}</td></tr>
  <tr><td>Servicio</td><td>{{ $contact->service ?? '—' }}</td></tr>
</table>
<div class="msg">{{ $contact->message }}</div>
<p style="margin-top:20px;font-size:.8rem;color:#888">Enviado el {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
</body>
</html>
