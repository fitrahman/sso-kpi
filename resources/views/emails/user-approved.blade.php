<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Halo, {{ $user->name }}!</h2>
    <p>Kabar baik! Pendaftaran akun Anda untuk sistem SSO KPI telah disetujui oleh Administrator.</p>
    <p>Sekarang Anda sudah dapat masuk ke dalam sistem menggunakan alamat email ({{ $user->email }}) dan kata sandi yang telah Anda daftarkan sebelumnya.</p>
    
    <p style="margin-top: 30px;">
        <a href="{{ route('login') }}" style="background-color: #E0070B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Masuk ke SSO KPI</a>
    </p>

    <p style="margin-top: 40px; font-size: 12px; color: #777;">
        Terima kasih,<br>
        Tim Administrator SSO KPI
    </p>
</body>
</html>
