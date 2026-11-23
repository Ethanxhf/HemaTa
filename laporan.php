<?php
$judul_halaman = "Laporan Pemakaian Listrik dan Air";
include("includes/header.php");
include("includes/fungsi.php");
include("includes/koneksi.php");

$user_id = (int) $_SESSION['user_id'];
$ringkas = totalPemakaian($koneksi, $user_id);
$total_semua = $ringkas['listrik_rupiah'] + $ringkas['air_rupiah'];

$rekap = rekapBulanan($koneksi, $user_id);
$jumlah_bulan = count($rekap);

$grafik = array_reverse(array_slice($rekap, 0, 6));
$maks_grafik = 0;
foreach ($grafik as $titik) {
    $maks_grafik = max($maks_grafik, $titik['listrik'], $titik['air']);
}
?>
<section class="kartu-grid">
    <article class="kartu metrik kwh">
        <span class="label">Total Listrik</span>
        <strong class="angka"><?php echo digitMeter(formatAngka($ringkas['listrik_kwh'], 2)); ?> <small>kWh</small></strong>
        <span class="sub"><?php echo formatRupiah($ringkas['listrik_rupiah']); ?></span>
    </article>
    <article class="kartu metrik liter">
        <span class="label">Total Air</span>
        <strong class="angka"><?php echo digitMeter(formatAngka($ringkas['air_liter'], 0)); ?> <small>Liter</small></strong>
        <span class="sub"><?php echo formatRupiah($ringkas['air_rupiah']); ?></span>
    </article>
    <article class="kartu metrik netral">
        <span class="label">Total Pengeluaran</span>
        <strong class="angka"><?php echo formatRupiah($total_semua); ?></strong>
        <span class="sub">listrik + air</span>
    </article>
    <article class="kartu metrik netral">
        <span class="label">Jumlah Catatan</span>
        <strong class="angka"><?php echo $ringkas['jumlah_catatan']; ?></strong>
        <span class="sub">dari <?php echo $jumlah_bulan; ?> bulan</span>
    </article>
</section>

<section class="kartu">
    <h2>Tren Biaya 6 Bulan Terakhir</h2>
    <?php if ($jumlah_bulan == 0) { ?>
        <p class="kosong">Belum ada data untuk digambarkan.</p>
    <?php } else { ?>
        <div class="grafik">
            <?php foreach ($grafik as $titik) { ?>
                <div class="grafik-kolom">
                    <div class="grafik-batang">
                        <span class="batang listrik" style="height: <?php echo $maks_grafik > 0 ? round($titik['listrik'] / $maks_grafik * 100) : 0; ?>%" title="Listrik: <?php echo formatRupiah($titik['listrik']); ?>"></span>
                        <span class="batang air" style="height: <?php echo $maks_grafik > 0 ? round($titik['air'] / $maks_grafik * 100) : 0; ?>%" title="Air: <?php echo formatRupiah($titik['air']); ?>"></span>
                    </div>
                    <span class="grafik-label"><?php echo labelBulan($titik['bulan'], true); ?></span>
                </div>
            <?php } ?>
        </div>
        <p class="grafik-legenda"><span class="badge listrik">Biaya Listrik</span> <span class="badge air">Biaya Air</span></p>
    <?php } ?>
</section>

<section class="kartu">
    <div class="baris-atas">
        <h2>Rekap Per Bulan</h2>
        <div class="aksi-laporan">
            <a class="btn sekunder" href="export_csv.php?data=rekap">Unduh Rekap (CSV)</a>
            <a class="btn sekunder" href="export_csv.php?data=riwayat">Unduh Semua Catatan (CSV)</a>
            <button type="button" class="btn sekunder" onclick="window.print()">Cetak Laporan</button>
        </div>
    </div>
    <?php if ($jumlah_bulan == 0) { ?>
        <p class="kosong">Belum ada catatan pemakaian. Mulai dari menu Catat Listrik atau Catat Air.</p>
    <?php } else { ?>
        <table class="tabel tumpuk rekap">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Listrik</th>
                    <th>Biaya Listrik</th>
                    <th>Air</th>
                    <th>Biaya Air</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekap as $baris) { ?>
                    <tr>
                        <td class="c-judul"><?php echo labelBulan($baris['bulan']); ?></td>
                        <td data-label="Listrik"><?php echo formatAngka($baris['kwh'], 2); ?> kWh</td>
                        <td data-label="Biaya Listrik"><?php echo formatRupiah($baris['listrik']); ?></td>
                        <td data-label="Air"><?php echo formatAngka($baris['liter'], 0); ?> Liter</td>
                        <td data-label="Biaya Air"><?php echo formatRupiah($baris['air']); ?></td>
                        <td data-label="Total"><strong><?php echo formatRupiah($baris['listrik'] + $baris['air']); ?></strong></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="c-judul"><strong>Total Keseluruhan</strong></td>
                    <td data-label="Listrik"><strong><?php echo formatAngka($ringkas['listrik_kwh'], 2); ?> kWh</strong></td>
                    <td data-label="Biaya Listrik"><strong><?php echo formatRupiah($ringkas['listrik_rupiah']); ?></strong></td>
                    <td data-label="Air"><strong><?php echo formatAngka($ringkas['air_liter'], 0); ?> Liter</strong></td>
                    <td data-label="Biaya Air"><strong><?php echo formatRupiah($ringkas['air_rupiah']); ?></strong></td>
                    <td data-label="Total"><strong><?php echo formatRupiah($total_semua); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php } ?>
    <p class="catatan">Laporan ini dihitung dari seluruh catatan pemakaian milik akun <?php echo htmlspecialchars($_SESSION['nama']); ?>.</p>
</section>
<?php include("includes/footer.php"); ?>
