<?php
session_start();
include("includes/fungsi.php");
include("includes/koneksi.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

if (isset($_GET['registrasi'])) {
    $pesan = "Registrasi berhasil! Silakan login.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = nilaiPost('username');
    $password = nilaiPost('password');

    if ($username == "" || $password == "") {
        $pesan = "Username dan password wajib diisi!";
    } else {
        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='" . mysqli_real_escape_string($koneksi, $username) . "'");
        if (mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_array($query);
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                header("Location: dashboard.php");
                exit;
            } else {
                $pesan = "Username atau password salah!";
            }
        } else {
            $pesan = "Username atau password salah!";
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
    <title>Login | HemaTa</title>
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
        <p class="auth-kaki">Clean Water and Sanitation | Affordable and Clean Energy</p>
    </section>
    <div class="kartu-auth">
        <div class="brand-auth">
            <span class="logo">HemaTa</span>
            <p>Sistem Pemantauan Pemakaian Air dan Listrik</p>
        </div>
        <h2>Login</h2>
        <?php if ($pesan != "") { ?>
            <p class="notifikasi <?php echo isset($_GET['registrasi']) && $_SERVER['REQUEST_METHOD'] != 'POST' ? "sukses" : "gagal"; ?>"><?php echo htmlspecialchars($pesan); ?></p>
        <?php } ?>
        <form method="POST" action="login.php" id="form-login">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Masukkan username" value="<?php echo isset($username) ? htmlspecialchars($username) : ""; ?>">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password">
            <button type="submit" class="btn utama">Masuk</button>
        </form>
        <p class="tautan-auth">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
    <script src="js/app.js"></script>
</body>

</html>
