<!DOCTYPE html>
<html>
<head>
    <title>Akses Aplikasi Ditolak</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Halo, {{ $user->name }}!</h2>
    <p>Mohon maaf, permohonan akses Anda untuk aplikasi <strong>{{ $client->name }}</strong> telah ditolak oleh Administrator.</p>
    <p>Anda tidak dapat mengakses aplikasi ini. Jika Anda merasa ini adalah sebuah kesalahan, silakan hubungi Administrator kami.</p>
    
    <p style="margin-top: 30px;">
        <a href="{{ route('dashboard') }}" style="background-color: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Kembali ke Portal</a>
    </p>

    <p style="margin-top: 40px; font-size: 12px; color: #777;">
        Terima kasih,<br>
        Tim Administrator SSO KPI
    </p>
</body>
</html>
