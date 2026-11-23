<?php
session_start();
include("includes/koneksi.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) ? (int) $_POST['id'] : 0;
$user_id = (int) $_SESSION['user_id'];

if ($id <= 0) {
    header("Location: riwayat.php");
    exit;
}

mysqli_query($koneksi, "DELETE FROM pemakaian WHERE id='" . $id . "' AND user_id='" . $user_id . "'");
header("Location: riwayat.php?aksi=dihapus");
exit;
