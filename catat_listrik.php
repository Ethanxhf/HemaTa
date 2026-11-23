<?php
$judul_halaman = "Catat Pemakaian Listrik";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$pesan = "";
$daftar = daftarGolongan();
$hari_ini = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = nilaiPost('daya_va');
    $kwh = (float) str_replace(",", ".", nilaiPost('kwh'));
    $tanggal = nilaiPost('tanggal');
    $keterangan = nilaiPost('keterangan');

    if (!isset($daftar[$kode])) {
        $pesan = "Daya harus dipilih dari daftar yang tersedia!";
    } elseif ($kwh <= 0) {
        $pesan = "Pemakaian kWh harus lebih dari 0!";
    } elseif ($kwh > 10000) {
        $pesan = "Pemakaian kWh tidak wajar (maksimal 10.000 kWh)!";
    } elseif (!strtotime($tanggal)) {
        $pesan = "Tanggal tidak valid!";
    } else {
        $daya = $daftar[$kode]['daya'];
        $tarif = $daftar[$kode]['tarif'];
        $total = $kwh * $tarif;
        $user_id = (int) $_SESSION['user_id'];
        $tanggal_aman = date("Y-m-d", strtotime($tanggal));
        $sql = "INSERT INTO pemakaian (user_id, jenis, daya_va, kwh, tarif_per_kwh, liter, total_rupiah, keterangan, tanggal) VALUES ('" . $user_id . "', 'listrik', '" . $daya . "', '" . $kwh . "', '" . $tarif . "', '0', '" . $total . "', '" . mysqli_real_escape_string($koneksi, $keterangan) . "', '" . $tanggal_aman . "')";
        mysqli_query($koneksi, $sql);
        if (mysqli_affected_rows($koneksi) > 0) {
            header("Location: dashboard.php?sukses=listrik");
            exit;
        }
        $pesan = "Gagal menyimpan data, coba lagi!";
    }
}
?>
<?php if ($pesan != "") { ?>
    <p class="notifikasi gagal"><?php echo htmlspecialchars($pesan); ?></p>
<?php } ?>

<section class="dua-kolom">
    <div class="kartu">
        <h2>Formulir Pencatatan Listrik</h2>
        <form method="POST" action="catat_listrik.php" id="form-listrik" data-tipe="listrik">
            <label for="tanggal">Tanggal Pencatatan</label>
            <input type="date" name="tanggal" id="tanggal" value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : $hari_ini; ?>">

            <label for="daya_va">Daya Terpasang (VA)</label>
            <select name="daya_va" id="daya_va" required>
                <option value="">-- Pilih Daya --</option>
                <?php foreach ($daftar as $kode_opsi => $golongan) { ?>
                    <option value="<?php echo $kode_opsi; ?>" data-tarif="<?php echo $golongan['tarif']; ?>" <?php echo isset($_POST['daya_va']) && (string) $_POST['daya_va'] === (string) $kode_opsi ? "selected" : ""; ?>><?php echo $golongan['label']; ?></option>
                <?php } ?>
            </select>

            <label for="kwh">Pemakaian kWh</label>
            <input type="number" inputmode="decimal" name="kwh" id="kwh" min="0.01" step="0.01" placeholder="Contoh: 45.5" required value="<?php echo isset($_POST['kwh']) ? htmlspecialchars($_POST['kwh']) : ""; ?>">

            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" maxlength="255" placeholder="Contoh: Tagihan bulan November" value="<?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ""; ?>">

            <button type="submit" class="btn utama">Simpan Catatan</button>
        </form>
    </div>

    <div class="kartu">
        <h2>Perhitungan Otomatis</h2>
        <div class="pratinjau" id="pratinjau-listrik">
            <div class="pratinjau-item">
                <span class="label">Tarif yang Dipakai</span>
                <strong id="pratinjau-tarif">Rp 0 / kWh</strong>
            </div>
            <div class="pratinjau-item">
                <span class="label">Estimasi Biaya Listrik</span>
                <strong id="pratinjau-total" class="besar">Rp 0</strong>
            </div>
        </div>
        <table class="tabel kecil">
            <thead>
                <tr>
                    <th>Golongan Daya</th>
                    <th>Tarif per kWh</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar as $golongan) { ?>
                    <tr>
                        <td><?php echo $golongan['label']; ?></td>
                        <td><?php echo formatRupiah($golongan['tarif']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p class="catatan">Biaya listrik = pemakaian kWh &times; tarif golongan daya. Tarif mengikuti ketetapan PLN periode Oktober-Desember 2026 dan dapat berubah.</p>
    </div>
</section>
<?php include("includes/footer.php"); ?>
