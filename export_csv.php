<?php
session_start();
include("includes/koneksi.php");
include("includes/fungsi.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$data = isset($_GET['data']) ? $_GET['data'] : "rekap";
if (!in_array($data, array("rekap", "riwayat"))) {
    $data = "rekap";
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"hemata-" . $data . "-" . date("Ymd") . ".csv\"");

$keluaran = fopen("php://output", "w");
fwrite($keluaran, "\xEF\xBB\xBF");

if ($data == "rekap") {
    fputcsv($keluaran, array("Bulan", "Listrik (kWh)", "Biaya Listrik (Rp)", "Air (Liter)", "Biaya Air (Rp)", "Total (Rp)"), ";", '"', "\\");
    foreach (rekapBulanan($koneksi, $user_id) as $baris) {
        fputcsv($keluaran, array(
            labelBulan($baris['bulan']),
            angkaCsv($baris['kwh']),
            angkaCsv($baris['listrik']),
            angkaCsv($baris['liter'], 0),
            angkaCsv($baris['air']),
            angkaCsv($baris['listrik'] + $baris['air'])
        ), ";", '"', "\\");
    }
} else {
    fputcsv($keluaran, array("Tanggal", "Jenis", "Daya (VA)", "Pemakaian (kWh)", "Tarif per kWh (Rp)", "Volume (Liter)", "Biaya (Rp)", "Keterangan"), ";", '"', "\\");
    $query = mysqli_query($koneksi, "SELECT * FROM pemakaian WHERE user_id='" . $user_id . "' ORDER BY tanggal DESC, id DESC");
    while ($baris = mysqli_fetch_array($query)) {
        fputcsv($keluaran, array(
            $baris['tanggal'],
            $baris['jenis'] == "listrik" ? "Listrik" : "Air",
            (int) $baris['daya_va'],
            angkaCsv($baris['kwh']),
            angkaCsv($baris['tarif_per_kwh']),
            angkaCsv($baris['liter']),
            angkaCsv($baris['total_rupiah']),
            teksCsv($baris['keterangan'])
        ), ";", '"', "\\");
    }
}

fclose($keluaran);
exit;
