<?php

// Tarif air per meter kubik (m3) untuk memperkirakan volume dari nominal pembayaran.
const TARIF_AIR_PER_M3 = 5000;

// Pemakaian bulan ini dianggap boros jika sudah melewati total bulan lalu sebesar persen ini.
const BATAS_BOROS_PERSEN = 20;

// Golongan tarif listrik
function daftarGolongan()
{
    return array(
        "450" => array("daya" => 450, "tarif" => 415, "label" => "450 VA (subsidi)"),
        "900s" => array("daya" => 900, "tarif" => 605, "label" => "900 VA (subsidi)"),
        "900" => array("daya" => 900, "tarif" => 1352, "label" => "900 VA (non-subsidi)"),
        "1300" => array("daya" => 1300, "tarif" => 1444.7, "label" => "1300 VA"),
        "2200" => array("daya" => 2200, "tarif" => 1444.7, "label" => "2200 VA")
    );
}

// Mencari golongan yang cocok dengan catatan lama (daya dan tarif yang tersimpan).
function kodeGolongan($daya_va, $tarif)
{
    foreach (daftarGolongan() as $kode => $golongan) {
        if ($golongan['daya'] == (int) $daya_va && abs($golongan['tarif'] - (float) $tarif) < 0.01) {
            return (string) $kode;
        }
    }
    return "";
}

function konversiLiter($rupiah)
{
    return $rupiah / TARIF_AIR_PER_M3 * 1000;
}

function formatRupiah($angka)
{
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

function formatAngka($angka, $desimal = 0)
{
    return number_format((float) $angka, $desimal, ",", ".");
}

function namaBulan()
{
    return array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
}

function formatTanggal($tanggal)
{
    $bulan = namaBulan();
    $waktu = strtotime($tanggal);
    if (!$waktu) {
        return "-";
    }
    return date("d", $waktu) . " " . $bulan[(int) date("n", $waktu)] . " " . date("Y", $waktu);
}

function bersihkan($teks)
{
    $teks = trim($teks);
    $teks = stripslashes($teks);
    $teks = strip_tags($teks);
    return $teks;
}

function nilaiPost($kunci)
{
    if (!isset($_POST[$kunci])) {
        return "";
    }
    return bersihkan($_POST[$kunci]);
}

function totalPemakaian($koneksi, $id_user = 0)
{
    $batas = $id_user > 0 ? "WHERE user_id='" . (int) $id_user . "'" : "";
    $hasil = array("listrik_kwh" => 0, "listrik_rupiah" => 0, "air_liter" => 0, "air_rupiah" => 0, "jumlah_catatan" => 0);
    $query = mysqli_query($koneksi, "SELECT jenis, SUM(kwh) AS total_kwh, SUM(liter) AS total_liter, SUM(total_rupiah) AS total_biaya FROM pemakaian " . $batas . " GROUP BY jenis");
    while ($baris = mysqli_fetch_array($query)) {
        if ($baris['jenis'] == "listrik") {
            $hasil['listrik_kwh'] = (float) $baris['total_kwh'];
            $hasil['listrik_rupiah'] = (float) $baris['total_biaya'];
        } else {
            $hasil['air_liter'] = (float) $baris['total_liter'];
            $hasil['air_rupiah'] = (float) $baris['total_biaya'];
        }
    }
    $hitung = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM pemakaian " . $batas);
    $baris_hitung = mysqli_fetch_array($hitung);
    $hasil['jumlah_catatan'] = (int) $baris_hitung['jumlah'];
    return $hasil;
}

function pemakaianBulan($koneksi, $id_user, $bulan)
{
    $hasil = array("kwh" => 0, "listrik_rupiah" => 0, "liter" => 0, "air_rupiah" => 0);
    $query = mysqli_query($koneksi, "SELECT jenis, SUM(kwh) AS total_kwh, SUM(liter) AS total_liter, SUM(total_rupiah) AS total_biaya FROM pemakaian WHERE user_id='" . (int) $id_user . "' AND DATE_FORMAT(tanggal, '%Y-%m')='" . mysqli_real_escape_string($koneksi, $bulan) . "' GROUP BY jenis");
    while ($baris = mysqli_fetch_array($query)) {
        if ($baris['jenis'] == "listrik") {
            $hasil['kwh'] = (float) $baris['total_kwh'];
            $hasil['listrik_rupiah'] = (float) $baris['total_biaya'];
        } else {
            $hasil['liter'] = (float) $baris['total_liter'];
            $hasil['air_rupiah'] = (float) $baris['total_biaya'];
        }
    }
    return $hasil;
}

function persenPerubahan($sekarang, $sebelumnya)
{
    if ($sebelumnya <= 0) {
        return null;
    }
    return ($sekarang - $sebelumnya) / $sebelumnya * 100;
}

function teksPerubahan($persen)
{
    if ($persen === null) {
        return "-";
    }
    return ($persen > 0 ? "+" : "") . formatAngka($persen, 0) . "%";
}

// Rekap pemakaian per bulan milik satu akun, diurutkan dari bulan terbaru.
function rekapBulanan($koneksi, $id_user)
{
    $hasil = array();
    $query = mysqli_query($koneksi, "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, SUM(CASE WHEN jenis='listrik' THEN kwh ELSE 0 END) AS kwh, SUM(CASE WHEN jenis='listrik' THEN total_rupiah ELSE 0 END) AS listrik, SUM(CASE WHEN jenis='air' THEN liter ELSE 0 END) AS liter, SUM(CASE WHEN jenis='air' THEN total_rupiah ELSE 0 END) AS air FROM pemakaian WHERE user_id='" . (int) $id_user . "' GROUP BY DATE_FORMAT(tanggal, '%Y-%m') ORDER BY bulan DESC");
    if ($query) {
        while ($baris = mysqli_fetch_array($query)) {
            $hasil[] = array(
                "bulan" => $baris['bulan'],
                "kwh" => (float) $baris['kwh'],
                "listrik" => (float) $baris['listrik'],
                "liter" => (float) $baris['liter'],
                "air" => (float) $baris['air']
            );
        }
    }
    return $hasil;
}

// Mengubah kode bulan "2026-11" menjadi "November 2026" (atau "Nov 2026" bila $singkat true).
function labelBulan($kode, $singkat = false)
{
    $bagian = explode("-", $kode);
    $nama = namaBulan();
    $teks = $nama[(int) $bagian[1]];
    if ($singkat) {
        $teks = substr($teks, 0, 3);
    }
    return $teks . " " . $bagian[0];
}

// Perkiraan tagihan bulan depan = rata-rata biaya dari paling banyak $jumlah bulan terakhir yang tercatat.
// Mengembalikan null bila belum ada catatan sama sekali.
function perkiraanBulanDepan($rekap, $jumlah = 3)
{
    $data = array_slice($rekap, 0, $jumlah);
    $banyak = count($data);
    if ($banyak == 0) {
        return null;
    }
    $listrik = 0;
    $air = 0;
    foreach ($data as $baris) {
        $listrik += $baris['listrik'];
        $air += $baris['air'];
    }
    return array(
        "jumlah_bulan" => $banyak,
        "listrik" => $listrik / $banyak,
        "air" => $air / $banyak,
        "total" => ($listrik + $air) / $banyak
    );
}

// Angka untuk berkas CSV: koma sebagai desimal, tanpa pemisah ribuan
function angkaCsv($angka, $desimal = 2)
{
    return number_format((float) $angka, $desimal, ",", "");
}

// Mencegah teks yang diawali = + - @ dibaca sebagai rumus saat CSV dibuka di Excel.
function teksCsv($teks)
{
    if ($teks !== "" && strpos("=+-@", $teks[0]) !== false) {
        return "'" . $teks;
    }
    return $teks;
}

// Memecah angka yang sudah diformat (contoh "1.234,50") menjadi kotak-kotak digit bergaya meteran.
// Digit setelah koma desimal diberi tanda khusus lewat CSS (merah, seperti roda desimal meteran kWh).
function digitMeter($teks)
{
    $hasil = '<span class="meter" role="img" aria-label="' . htmlspecialchars($teks) . '">';
    foreach (str_split($teks) as $karakter) {
        if (ctype_digit($karakter)) {
            $hasil .= '<span class="digit" aria-hidden="true">' . $karakter . '</span>';
        } elseif ($karakter == ",") {
            $hasil .= '<span class="pemisah koma" aria-hidden="true">,</span>';
        } else {
            $hasil .= '<span class="pemisah" aria-hidden="true">' . htmlspecialchars($karakter) . '</span>';
        }
    }
    return $hasil . '</span>';
}
