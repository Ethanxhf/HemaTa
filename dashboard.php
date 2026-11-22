<?php
$judul_halaman = "Dashboard";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$ringkas = totalPemakaian($koneksi, $_SESSION['user_id']);
$total_listrik = $ringkas['listrik_rupiah'];
$total_air = $ringkas['air_rupiah'];
$total_semua = $total_listrik + $total_air;

$bulan_ini_kode = date("Y-m");
$bulan_lalu_kode = date("Y-m", strtotime("first day of last month"));
$bulan_ini = pemakaianBulan($koneksi, $_SESSION['user_id'], $bulan_ini_kode);
$bulan_lalu = pemakaianBulan($koneksi, $_SESSION['user_id'], $bulan_lalu_kode);
$naik_listrik = persenPerubahan($bulan_ini['kwh'], $bulan_lalu['kwh']);
$naik_air = persenPerubahan($bulan_ini['liter'], $bulan_lalu['liter']);
$pengeluaran_bulan_ini = $bulan_ini['listrik_rupiah'] + $bulan_ini['air_rupiah'];

$peringatan = array();
if ($naik_listrik !== null && $naik_listrik > BATAS_BOROS_PERSEN) {
    $peringatan[] = "Listrik bulan ini sudah " . formatAngka($naik_listrik, 0) . "% di atas total bulan lalu. Cek peralatan yang menyala terus-menerus.";
}
if ($naik_air !== null && $naik_air > BATAS_BOROS_PERSEN) {
    $peringatan[] = "Air bulan ini sudah " . formatAngka($naik_air, 0) . "% di atas total bulan lalu. Cek kemungkinan kebocoran atau pemakaian berlebih.";
}

$perkiraan = perkiraanBulanDepan(rekapBulanan($koneksi, $_SESSION['user_id']));

$terakhir = mysqli_query($koneksi, "SELECT jenis, tanggal, kwh, liter, total_rupiah FROM pemakaian WHERE user_id='" . (int) $_SESSION['user_id'] . "' ORDER BY id DESC LIMIT 5");
?>
<?php if (isset($_GET['sukses'])) { ?>
    <p class="notifikasi sukses">Data <?php echo htmlspecialchars($_GET['sukses']); ?> berhasil disimpan.</p>
<?php } ?>
<?php foreach ($peringatan as $teks) { ?>
    <p class="notifikasi gagal"><?php echo htmlspecialchars($teks); ?></p>
<?php } ?>
<section class="kartu-grid">
    <article class="kartu metrik kwh">
        <span class="label">Total Pemakaian Listrik</span>
        <strong class="angka"><?php echo digitMeter(formatAngka($ringkas['listrik_kwh'], 2)); ?> <small>kWh</small></strong>
        <span class="sub"><?php echo $ringkas['jumlah_catatan']; ?> catatan tersimpan</span>
    </article>
    <article class="kartu metrik liter">
        <span class="label">Total Pemakaian Air</span>
        <strong class="angka"><?php echo digitMeter(formatAngka($ringkas['air_liter'], 0)); ?> <small>Liter</small></strong>
        <span class="sub">konversi dari nominal rupiah</span>
    </article>
    <article class="kartu metrik biaya-listrik">
        <span class="label">Total Biaya Listrik</span>
        <strong class="angka"><?php echo formatRupiah($total_listrik); ?></strong>
        <span class="sub">berdasarkan daya terpasang</span>
    </article>
    <article class="kartu metrik biaya-air">
        <span class="label">Total Biaya Air</span>
        <strong class="angka"><?php echo formatRupiah($total_air); ?></strong>
        <span class="sub">berdasarkan nominal pembayaran</span>
    </article>
</section>

<section class="kartu ringkas-tambahan">
    <div class="ringkas-item">
        <span class="label">Total Pengeluaran</span>
        <strong><?php echo formatRupiah($total_semua); ?></strong>
    </div>
    <div class="ringkas-item">
        <span class="label">Pengeluaran Bulan Ini</span>
        <strong><?php echo formatRupiah($pengeluaran_bulan_ini); ?></strong>
    </div>
    <div class="ringkas-item">
        <span class="label">Rata-rata Biaya per kWh</span>
        <strong><?php echo $ringkas['listrik_kwh'] > 0 ? formatRupiah($total_listrik / $ringkas['listrik_kwh']) : "-"; ?></strong>
    </div>
    <div class="ringkas-item">
        <a class="btn utama" href="catat_listrik.php">+ Catat Listrik</a>
        <a class="btn sekunder" href="catat_air.php">+ Catat Air</a>
    </div>
</section>

<section class="kartu">
    <h2>Dibanding Bulan Lalu</h2>
    <table class="tabel kecil tumpuk">
        <thead>
            <tr>
                <th>Pemakaian</th>
                <th>Bulan Lalu</th>
                <th>Bulan Ini</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="c-judul">Listrik</td>
                <td data-label="Bulan Lalu"><?php echo formatAngka($bulan_lalu['kwh'], 2); ?> kWh</td>
                <td data-label="Bulan Ini"><?php echo formatAngka($bulan_ini['kwh'], 2); ?> kWh</td>
                <td data-label="Perubahan"><strong><?php echo teksPerubahan($naik_listrik); ?></strong></td>
            </tr>
            <tr>
                <td class="c-judul">Air</td>
                <td data-label="Bulan Lalu"><?php echo formatAngka($bulan_lalu['liter'], 0); ?> Liter</td>
                <td data-label="Bulan Ini"><?php echo formatAngka($bulan_ini['liter'], 0); ?> Liter</td>
                <td data-label="Perubahan"><strong><?php echo teksPerubahan($naik_air); ?></strong></td>
            </tr>
        </tbody>
    </table>
    <p class="catatan">Peringatan muncul jika pemakaian sudah melewati total bulan lalu lebih dari <?php echo BATAS_BOROS_PERSEN; ?>%.</p>
</section>

<section class="kartu">
    <h2>Perkiraan Tagihan Bulan Depan</h2>
    <?php if ($perkiraan === null) { ?>
        <p class="kosong">Perkiraan muncul setelah ada catatan pemakaian.</p>
    <?php } else { ?>
        <div class="pratinjau">
            <div class="pratinjau-item">
                <span class="label">Perkiraan Listrik</span>
                <strong><?php echo formatRupiah($perkiraan['listrik']); ?></strong>
            </div>
            <div class="pratinjau-item">
                <span class="label">Perkiraan Air</span>
                <strong><?php echo formatRupiah($perkiraan['air']); ?></strong>
            </div>
            <div class="pratinjau-item">
                <span class="label">Perkiraan Total</span>
                <strong class="besar"><?php echo formatRupiah($perkiraan['total']); ?></strong>
            </div>
        </div>
        <p class="catatan">Perkiraan dihitung dari rata-rata biaya <?php echo $perkiraan['jumlah_bulan']; ?> bulan terakhir yang tercatat, sehingga hanya gambaran kasar dan bisa berbeda dari tagihan sebenarnya.</p>
    <?php } ?>
</section>

<section class="kartu">
    <h2>5 Catatan Terakhir</h2>
    <?php if (mysqli_num_rows($terakhir) == 0) { ?>
        <p class="kosong">Belum ada catatan pemakaian. Mulai catat pemakaian listrik atau air Anda.</p>
    <?php } else { ?>
        <table class="tabel tumpuk riwayat">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Volume</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($baris = mysqli_fetch_array($terakhir)) { ?>
                    <tr>
                        <td class="c-tgl" data-label="Tanggal"><?php echo formatTanggal($baris['tanggal']); ?></td>
                        <td class="c-jenis" data-label="Jenis"><span class="badge <?php echo $baris['jenis']; ?>"><?php echo $baris['jenis'] == "listrik" ? "Listrik" : "Air"; ?></span></td>
                        <td class="c-vol" data-label="Volume"><?php echo $baris['jenis'] == "listrik" ? formatAngka($baris['kwh'], 2) . " kWh" : formatAngka($baris['liter'], 0) . " Liter"; ?></td>
                        <td class="c-biaya" data-label="Biaya"><?php echo formatRupiah($baris['total_rupiah']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</section>
<?php include("includes/footer.php"); ?>
