<?php
$judul_halaman = "Riwayat Pemakaian";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$filter = isset($_GET['jenis']) ? $_GET['jenis'] : "semua";
if (!in_array($filter, array("semua", "listrik", "air"))) {
    $filter = "semua";
}

$user_id = (int) $_SESSION['user_id'];
$where = "WHERE user_id='" . $user_id . "'";
if ($filter != "semua") {
    $where .= " AND jenis='" . $filter . "'";
}

$riwayat = mysqli_query($koneksi, "SELECT * FROM pemakaian " . $where . " ORDER BY tanggal DESC, id DESC");
$ringkas = totalPemakaian($koneksi, $user_id);
?>
<?php if (isset($_GET['aksi'])) { ?>
    <p class="notifikasi sukses">Catatan berhasil <?php echo htmlspecialchars($_GET['aksi']); ?>.</p>
<?php } ?>

<section class="kartu">
    <div class="baris-atas">
        <div class="filter">
            <a href="riwayat.php?jenis=semua" class="chip <?php echo $filter == "semua" ? "aktif" : ""; ?>">Semua</a>
            <a href="riwayat.php?jenis=listrik" class="chip <?php echo $filter == "listrik" ? "aktif" : ""; ?>">Listrik</a>
            <a href="riwayat.php?jenis=air" class="chip <?php echo $filter == "air" ? "aktif" : ""; ?>">Air</a>
        </div>
        <p class="ringkas-kecil">Total: <?php echo formatAngka($ringkas['listrik_kwh'], 2); ?> kWh &bull; <?php echo formatAngka($ringkas['air_liter'], 0); ?> Liter &bull; <?php echo formatRupiah($ringkas['listrik_rupiah'] + $ringkas['air_rupiah']); ?></p>
    </div>

    <?php if (mysqli_num_rows($riwayat) == 0) { ?>
        <p class="kosong">Tidak ada catatan pada filter ini.</p>
    <?php } else { ?>
        <table class="tabel tumpuk riwayat">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Daya / Tarif</th>
                    <th>Volume</th>
                    <th>Biaya</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($baris = mysqli_fetch_array($riwayat)) { ?>
                    <tr>
                        <td class="c-tgl" data-label="Tanggal"><?php echo formatTanggal($baris['tanggal']); ?></td>
                        <td class="c-jenis" data-label="Jenis"><span class="badge <?php echo $baris['jenis']; ?>"><?php echo $baris['jenis'] == "listrik" ? "Listrik" : "Air"; ?></span></td>
                        <td class="c-daya" data-label="Daya / Tarif"><?php echo $baris['jenis'] == "listrik" ? formatAngka($baris['daya_va']) . " VA &bull; " . formatRupiah($baris['tarif_per_kwh']) . "/kWh" : "Konversi rupiah"; ?></td>
                        <td class="c-vol" data-label="Volume"><?php echo $baris['jenis'] == "listrik" ? formatAngka($baris['kwh'], 2) . " kWh" : formatAngka($baris['liter'], 0) . " Liter"; ?></td>
                        <td class="c-biaya" data-label="Biaya"><?php echo formatRupiah($baris['total_rupiah']); ?></td>
                        <td class="c-ket" data-label="Keterangan"><?php echo htmlspecialchars($baris['keterangan']); ?></td>
                        <td class="aksi c-aksi">
                            <a class="aksi-edit" href="edit_catatan.php?id=<?php echo (int) $baris['id']; ?>">Edit</a>
                            <form method="POST" action="hapus_catatan.php" onsubmit="return confirm('Hapus catatan ini?');">
                                <input type="hidden" name="id" value="<?php echo (int) $baris['id']; ?>">
                                <button type="submit" class="hapus">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</section>
<?php include("includes/footer.php"); ?>
