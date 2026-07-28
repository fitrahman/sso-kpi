<!DOCTYPE html>
<html>
<head>
    <title>Akses Aplikasi Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Halo, {{ $user->name }}!</h2>
    <p>Permintaan akses Anda untuk aplikasi <strong>{{ $client->name }}</strong> telah disetujui oleh Administrator.</p>
    <p>Sekarang Anda sudah dapat masuk ke dalam aplikasi tersebut melalui Dashboard SSO Anda.</p>
    
    <p style="margin-top: 30px;">
        <a href="{{ route('dashboard') }}" style="background-color: #E0070B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Buka Dashboard SSO</a>
    </p>

    <p style="margin-top: 40px; font-size: 12px; color: #777;">
        Terima kasih,<br>
        Tim Administrator SSO KPI
    </p>
</body>
</html>
