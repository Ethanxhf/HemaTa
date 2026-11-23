<?php
$judul_halaman = "Catat Pemakaian Air";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$pesan = "";
$hari_ini = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nominal = (float) str_replace(array(".", ","), array("", "."), nilaiPost('nominal_rupiah'));
    $tanggal = nilaiPost('tanggal');
    $keterangan = nilaiPost('keterangan');

    if ($nominal <= 0) {
        $pesan = "Nominal rupiah harus lebih dari 0!";
    } elseif ($nominal > 100000000) {
        $pesan = "Nominal terlalu besar, maksimal Rp 100.000.000!";
    } elseif (!strtotime($tanggal)) {
        $pesan = "Tanggal tidak valid!";
    } else {
        $liter = konversiLiter($nominal);
        $user_id = (int) $_SESSION['user_id'];
        $tanggal_aman = date("Y-m-d", strtotime($tanggal));
        $sql = "INSERT INTO pemakaian (user_id, jenis, daya_va, kwh, tarif_per_kwh, liter, total_rupiah, keterangan, tanggal) VALUES ('" . $user_id . "', 'air', '0', '0', '0', '" . $liter . "', '" . $nominal . "', '" . mysqli_real_escape_string($koneksi, $keterangan) . "', '" . $tanggal_aman . "')";
        mysqli_query($koneksi, $sql);
        if (mysqli_affected_rows($koneksi) > 0) {
            header("Location: dashboard.php?sukses=air");
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
        <h2>Formulir Pencatatan Air</h2>
        <form method="POST" action="catat_air.php" id="form-air" data-tipe="air" data-tarif-air="<?php echo TARIF_AIR_PER_M3; ?>">
            <label for="tanggal">Tanggal Pencatatan</label>
            <input type="date" name="tanggal" id="tanggal" value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : $hari_ini; ?>">

            <label for="nominal_rupiah">Nominal Pembayaran Air (Rp)</label>
            <input type="number" inputmode="numeric" name="nominal_rupiah" id="nominal_rupiah" min="1000" step="1000" placeholder="Contoh: 100000" required value="<?php echo isset($_POST['nominal_rupiah']) ? htmlspecialchars($_POST['nominal_rupiah']) : ""; ?>">

            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" maxlength="255" placeholder="Contoh: Tagihan air November" value="<?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ""; ?>">

            <button type="submit" class="btn utama">Simpan Catatan</button>
        </form>
    </div>

    <div class="kartu">
        <h2>Perhitungan Otomatis</h2>
        <div class="pratinjau" id="pratinjau-air">
            <div class="pratinjau-item">
                <span class="label">Estimasi Volume Air</span>
                <strong id="pratinjau-liter" class="besar">0 Liter</strong>
            </div>
            <div class="pratinjau-item">
                <span class="label">Biaya Air</span>
                <strong id="pratinjau-nominal">Rp 0</strong>
            </div>
        </div>
        <table class="tabel kecil">
            <thead>
                <tr>
                    <th>Nominal</th>
                    <th>Volume Air</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo formatRupiah(50000); ?></td>
                    <td><?php echo formatAngka(konversiLiter(50000), 0); ?> Liter</td>
                </tr>
                <tr>
                    <td><?php echo formatRupiah(100000); ?></td>
                    <td><?php echo formatAngka(konversiLiter(100000), 0); ?> Liter</td>
                </tr>
                <tr>
                    <td><?php echo formatRupiah(200000); ?></td>
                    <td><?php echo formatAngka(konversiLiter(200000), 0); ?> Liter</td>
                </tr>
            </tbody>
        </table>
        <p class="catatan">Volume air diperkirakan dari nominal pembayaran: Liter = Nominal Rp &divide; tarif air per m&sup3; &times; 1000. Tarif yang dipakai <?php echo formatRupiah(TARIF_AIR_PER_M3); ?> per m&sup3; (Rp 100.000 = <?php echo formatAngka(konversiLiter(100000), 0); ?> Liter).</p>
    </div>
</section>
<?php include("includes/footer.php"); ?>
