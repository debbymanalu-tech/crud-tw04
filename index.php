<?php require "config.php"; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manajemen Gudang</title><link rel="stylesheet" href="style.css">
</head>
<body>
<header class="top"><div class="wrap"><h1>Manajemen Gudang</h1><p>CRUD User dan Produk</p></div></header>
<main class="wrap">
  <section class="hero"><h2>Dashboard</h2><p>Kelola data pengguna dan barang dalam satu tempat.</p></section>
  <section class="cards">
    <a class="card" href="users.php"><span class="emoji">👥</span><h2>Data User</h2><p>Tambah, lihat, ubah, dan hapus pengguna.</p><b>Buka CRUD User →</b></a>
    <a class="card" href="barang.php"><span class="emoji">📦</span><h2>Data Produk</h2><p>Kelola kode, nama, stok, dan harga barang.</p><b>Buka CRUD Produk →</b></a>
  </section>
</main>
<footer class="wrap foot">Aplikasi CRUD sederhana · PHP, MySQL, CSS, JavaScript</footer>
<script src="app.js"></script>
</body></html>
