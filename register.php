<?php
session_start();
include("includes/fungsi.php");
include("includes/koneksi.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";
$sukses = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = nilaiPost('nama');
    $username = nilaiPost('username');
    $password = nilaiPost('password');
    $ulangi = nilaiPost('ulangi_password');

    if ($nama == "" || $username == "" || $password == "" || $ulangi == "") {
        $pesan = "Semua field wajib diisi!";
    } elseif (strlen($username) < 4) {
        $pesan = "Username minimal 4 karakter!";
    } elseif (strlen($password) < 6) {
        $pesan = "Password minimal 6 karakter!";
    } elseif ($password !== $ulangi) {
        $pesan = "Konfirmasi password tidak sama!";
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($koneksi, $username) . "'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($koneksi, "INSERT INTO users (nama, username, password) VALUES ('" . mysqli_real_escape_string($koneksi, $nama) . "', '" . mysqli_real_escape_string($koneksi, $username) . "', '" . mysqli_real_escape_string($koneksi, $hash) . "')");
            if (mysqli_affected_rows($koneksi) > 0) {
                $sukses = "Registrasi berhasil! Silakan login.";
            } else {
                $pesan = "Registrasi gagal, coba lagi!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a2e3a">
    <title>Registrasi | HemaTa</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="halaman-auth">
    <section class="auth-sisi">
        <div>
            <span class="logo">HemaTa</span>
            <p class="slogan">Pantau air dan listrik</p>
        </div>
        <div class="auth-uraian">
            <div class="meter-hias" aria-hidden="true">
                <span class="digit">0</span><span class="digit">0</span><span class="digit">4</span><span class="digit">5</span><span class="pemisah koma">,</span><span class="digit">2</span><span class="digit">0</span>
                <span class="satuan">kWh</span>
            </div>
            <h2>Tahu pemakaian listrik dan air sebelum tagihan datang.</h2>
            <ul>
                <li class="titik-listrik">Catat tagihan listrik sesuai daya terpasang, dari 450 sampai 2200 VA.</li>
                <li class="titik-air">Air dihitung dari nominal yang dibayar, tanpa perlu membaca meteran.</li>
                <li class="titik-netral">Bandingkan dengan bulan lalu dan lihat perkiraan tagihan bulan depan.</li>
            </ul>
        </div>
        <p class="auth-kaki">Mendukung SDG 6 dan SDG 7</p>
    </section>
    <div class="kartu-auth">
        <div class="brand-auth">
            <span class="logo">HemaTa</span>
            <p>Sistem Pemantauan Pemakaian Air dan Listrik</p>
        </div>
        <h2>Registrasi Akun</h2>
        <?php if ($pesan != "") { ?>
            <p class="notifikasi gagal"><?php echo htmlspecialchars($pesan); ?></p>
        <?php } ?>
        <?php if ($sukses != "") { ?>
            <p class="notifikasi sukses"><?php echo htmlspecialchars($sukses); ?></p>
        <?php } ?>
        <form method="POST" action="register.php" id="form-register">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" placeholder="Nama lengkap" value="<?php echo isset($nama) ? htmlspecialchars($nama) : ""; ?>">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Minimal 4 karakter" value="<?php echo isset($username) ? htmlspecialchars($username) : ""; ?>">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Minimal 6 karakter">
            <label for="ulangi_password">Ulangi Password</label>
            <input type="password" name="ulangi_password" id="ulangi_password" placeholder="Ulangi password">
            <button type="submit" class="btn utama">Daftar</button>
        </form>
        <p class="tautan-auth">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
    <script src="js/app.js"></script>
</body>

</html>
