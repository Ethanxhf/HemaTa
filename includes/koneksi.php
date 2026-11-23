<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_hemata") or die("Gagal koneksi ke database");
mysqli_set_charset($koneksi, "utf8");
