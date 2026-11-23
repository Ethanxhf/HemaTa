<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$judul_halaman = isset($judul_halaman) ? $judul_halaman : "Dashboard";

$menu_aktif = basename($_SERVER['PHP_SELF']);
if ($menu_aktif == "edit_catatan.php") {
    $menu_aktif = "riwayat.php";
}

$inisial = function_exists("mb_substr") ? mb_substr($_SESSION['nama'], 0, 1) : substr($_SESSION['nama'], 0, 1);

$ikon = array(
    "dashboard.php" => '<rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>',
    "catat_listrik.php" => '<path d="M13 2L4 14h7l-1 8 9-12h-7z"/>',
    "catat_air.php" => '<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/>',
    "riwayat.php" => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>',
    "laporan.php" => '<path d="M5 20V11M11 20V4M17 20v-7M2 20h20"/>'
);
$menu = array(
    "dashboard.php" => 'Dashboard',
    "catat_listrik.php" => '<span class="menu-awal">Catat </span>Listrik',
    "catat_air.php" => '<span class="menu-awal">Catat </span>Air',
    "riwayat.php" => 'Riwayat',
    "laporan.php" => 'Laporan'
);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a2e3a">
    <title><?php echo htmlspecialchars($judul_halaman); ?> | HemaTa</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <a class="lompat" href="#isi">Lewati ke konten</a>
    <div class="app">
        <aside class="sisi">
            <div class="sisi-merek">
                <span class="logo">HemaTa</span>
                <span class="slogan">Pantau air dan listrik</span>
            </div>
            <nav class="sisi-menu" aria-label="Menu utama">
                <?php foreach ($menu as $berkas => $label) { ?>
                    <a href="<?php echo $berkas; ?>" class="<?php echo $menu_aktif == $berkas ? "aktif" : ""; ?>" <?php echo $menu_aktif == $berkas ? 'aria-current="page"' : ""; ?>>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $ikon[$berkas]; ?></svg>
                        <span class="menu-teks"><?php echo $label; ?></span>
                    </a>
                <?php } ?>
            </nav>
            <div class="sisi-bawah">
                <a href="logout.php" class="keluar">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    <span>Keluar</span>
                </a>
            </div>
        </aside>
        <div class="isi">
            <header class="topbar">
                <div>
                    <span class="logo-mini">HemaTa</span>
                    <h1><?php echo htmlspecialchars($judul_halaman); ?></h1>
                </div>
                <div class="pengguna">
                    <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars(strtoupper($inisial)); ?></span>
                    <span class="pengguna-nama"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                    <a href="logout.php" class="keluar-mobile">Keluar</a>
                </div>
            </header>
            <main class="kontainer" id="isi">
