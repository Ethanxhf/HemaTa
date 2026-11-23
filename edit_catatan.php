<?php
$judul_halaman = "Ubah Catatan";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$pesan = "";
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = (int) $_SESSION['user_id'];
$daftar = daftarGolongan();

$cari = mysqli_query($koneksi, "SELECT * FROM pemakaian WHERE id='" . $id . "' AND user_id='" . $user_id . "'");
if (mysqli_num_rows($cari) == 0) {
    header("Location: riwayat.php");
    exit;
}
$catatan = mysqli_fetch_array($cari);
$kode_sekarang = kodeGolongan($catatan['daya_va'], $catatan['tarif_per_kwh']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = nilaiPost('tanggal');
    $keterangan = nilaiPost('keterangan');

    if (!strtotime($tanggal)) {
        $pesan = "Tanggal tidak valid!";
    } elseif ($catatan['jenis'] == "listrik") {
        $kode = nilaiPost('daya_va');
        $kwh = (float) str_replace(",", ".", nilaiPost('kwh'));

        if (!isset($daftar[$kode])) {
            $pesan = "Daya harus dipilih dari daftar yang tersedia!";
        } elseif ($kwh <= 0 || $kwh > 10000) {
            $pesan = "Pemakaian kWh tidak valid!";
        } else {
            $daya = $daftar[$kode]['daya'];
            $tarif = $daftar[$kode]['tarif'];
            $total = $kwh * $tarif;
            mysqli_query($koneksi, "UPDATE pemakaian SET daya_va='" . $daya . "', kwh='" . $kwh . "', tarif_per_kwh='" . $tarif . "', total_rupiah='" . $total . "', tanggal='" . date("Y-m-d", strtotime($tanggal)) . "', keterangan='" . mysqli_real_escape_string($koneksi, $keterangan) . "' WHERE id='" . $id . "' AND user_id='" . $user_id . "'");
            if (mysqli_affected_rows($koneksi) >= 0) {
                header("Location: riwayat.php?aksi=diperbarui");
                exit;
            }
            $pesan = "Gagal memperbarui data!";
        }
    } else {
        $nominal = (float) str_replace(array(".", ","), array("", "."), nilaiPost('nominal_rupiah'));

        if ($nominal <= 0 || $nominal > 100000000) {
            $pesan = "Nominal rupiah tidak valid!";
        } else {
            $liter = konversiLiter($nominal);
            mysqli_query($koneksi, "UPDATE pemakaian SET liter='" . $liter . "', total_rupiah='" . $nominal . "', tanggal='" . date("Y-m-d", strtotime($tanggal)) . "', keterangan='" . mysqli_real_escape_string($koneksi, $keterangan) . "' WHERE id='" . $id . "' AND user_id='" . $user_id . "'");
            if (mysqli_affected_rows($koneksi) >= 0) {
                header("Location: riwayat.php?aksi=diperbarui");
                exit;
            }
            $pesan = "Gagal memperbarui data!";
        }
    }
}
?>
<?php if ($pesan != "") { ?>
    <p class="notifikasi gagal"><?php echo htmlspecialchars($pesan); ?></p>
<?php } ?>

<section class="dua-kolom">
    <div class="kartu">
        <h2>Ubah Catatan <?php echo $catatan['jenis'] == "listrik" ? "Listrik" : "Air"; ?></h2>
        <form method="POST" action="edit_catatan.php?id=<?php echo $id; ?>" id="<?php echo $catatan['jenis'] == "listrik" ? "form-listrik" : "form-air"; ?>" data-tipe="<?php echo $catatan['jenis']; ?>" data-tarif-air="<?php echo TARIF_AIR_PER_M3; ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="tanggal">Tanggal Pencatatan</label>
            <input type="date" name="tanggal" id="tanggal" value="<?php echo htmlspecialchars($catatan['tanggal']); ?>">

            <?php if ($catatan['jenis'] == "listrik") { ?>
                <label for="daya_va">Daya Terpasang (VA)</label>
                <select name="daya_va" id="daya_va" required>
                    <option value="">-- Pilih Daya --</option>
                    <?php foreach ($daftar as $kode_opsi => $golongan) { ?>
                        <option value="<?php echo $kode_opsi; ?>" data-tarif="<?php echo $golongan['tarif']; ?>" <?php echo (string) $kode_sekarang === (string) $kode_opsi ? "selected" : ""; ?>><?php echo $golongan['label']; ?></option>
                    <?php } ?>
                </select>

                <label for="kwh">Pemakaian kWh</label>
                <input type="number" inputmode="decimal" name="kwh" id="kwh" min="0.01" step="0.01" required value="<?php echo htmlspecialchars($catatan['kwh']); ?>">
            <?php } else { ?>
                <label for="nominal_rupiah">Nominal Pembayaran Air (Rp)</label>
                <input type="number" inputmode="numeric" name="nominal_rupiah" id="nominal_rupiah" min="1000" step="1000" required value="<?php echo htmlspecialchars($catatan['total_rupiah']); ?>">
            <?php } ?>

            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" maxlength="255" value="<?php echo htmlspecialchars($catatan['keterangan']); ?>">

            <button type="submit" class="btn utama">Simpan Perubahan</button>
            <a href="riwayat.php" class="btn sekunder">Batal</a>
        </form>
    </div>

    <div class="kartu">
        <h2>Perhitungan Otomatis</h2>
        <div class="pratinjau" <?php echo $catatan['jenis'] == "listrik" ? 'id="pratinjau-listrik"' : 'id="pratinjau-air"'; ?>>
            <?php if ($catatan['jenis'] == "listrik") { ?>
                <div class="pratinjau-item">
                    <span class="label">Tarif yang Dipakai</span>
                    <strong id="pratinjau-tarif"><?php echo formatRupiah($catatan['tarif_per_kwh']); ?> / kWh</strong>
                </div>
                <div class="pratinjau-item">
                    <span class="label">Estimasi Biaya Listrik</span>
                    <strong id="pratinjau-total" class="besar"><?php echo formatRupiah($catatan['total_rupiah']); ?></strong>
                </div>
            <?php } else { ?>
                <div class="pratinjau-item">
                    <span class="label">Estimasi Volume Air</span>
                    <strong id="pratinjau-liter" class="besar"><?php echo formatAngka($catatan['liter'], 0); ?> Liter</strong>
                </div>
                <div class="pratinjau-item">
                    <span class="label">Biaya Air</span>
                    <strong id="pratinjau-nominal"><?php echo formatRupiah($catatan['total_rupiah']); ?></strong>
                </div>
            <?php } ?>
        </div>
        <p class="catatan">Setiap perubahan input langsung menghitung ulang biaya listrik (kWh &times; tarif) dan volume air (nominal &divide; tarif air per m&sup3; &times; 1000).</p>
    </div>
</section>
<?php include("includes/footer.php"); ?>
