<?php
require "config.php";
csrf_check();
$pesan = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST["aksi"] ?? "";
    $id = (int)($_POST["id"] ?? 0);
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $kategori_id = (int)($_POST["kategori_id"] ?? 0);
    $supplier_raw = trim($_POST["supplier_id"] ?? "");
    $supplier_id = ($supplier_raw === "") ? null : (int)$supplier_raw;
    $satuan_id = (int)($_POST["satuan_id"] ?? 0);
    $stok = (int)($_POST["stok"] ?? 0);
    $stok_minimum = (int)($_POST["stok_minimum"] ?? 0);
    $harga_beli = (float)($_POST["harga_beli"] ?? 0);
    $harga_jual = (float)($_POST["harga_jual"] ?? 0);

    if ($kode === "" || $nama === "" || $kategori_id < 1 || $satuan_id < 1 || $stok < 0 || $stok_minimum < 0 || $harga_beli < 0 || $harga_jual < 0) {
        $pesan = "Lengkapi kode, nama, ID kategori, ID satuan, dan pastikan angka tidak negatif.";
    } else {
        if ($aksi === "tambah") {
            $stmt = $conn->prepare("INSERT INTO barang (kode,nama,kategori_id,supplier_id,satuan_id,stok,stok_minimum,harga_beli,harga_jual) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssiiiiidd", $kode,$nama,$kategori_id,$supplier_id,$satuan_id,$stok,$stok_minimum,$harga_beli,$harga_jual);
            $pesan = $stmt->execute() ? "Produk berhasil ditambahkan." : "Gagal menambah produk. Periksa kode unik dan ID kategori/satuan. " . $stmt->error;
            $stmt->close();
        } elseif ($aksi === "ubah" && $id > 0) {
            $stmt = $conn->prepare("UPDATE barang SET kode=?,nama=?,kategori_id=?,supplier_id=?,satuan_id=?,stok=?,stok_minimum=?,harga_beli=?,harga_jual=? WHERE id=?");
            $stmt->bind_param("ssiiiiiddi", $kode,$nama,$kategori_id,$supplier_id,$satuan_id,$stok,$stok_minimum,$harga_beli,$harga_jual,$id);
            $pesan = $stmt->execute() ? "Produk berhasil diubah." : "Gagal mengubah produk. Periksa kode unik dan ID kategori/satuan. " . $stmt->error;
            $stmt->close();
        }
    }
    if ($aksi === "hapus" && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM barang WHERE id=?");
        $stmt->bind_param("i", $id);
        $pesan = $stmt->execute() ? "Produk berhasil dihapus." : "Produk tidak dapat dihapus karena mungkin sudah dipakai pada transaksi. " . $stmt->error;
        $stmt->close();
    }
}
$edit = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM barang WHERE id=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $edit = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT id,kode,nama,kategori_id,supplier_id,satuan_id,stok,stok_minimum,harga_beli,harga_jual FROM barang ORDER BY id DESC");
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>CRUD Produk</title><link rel="stylesheet" href="style.css"></head><body>
<header class="top"><div class="wrap"><h1>CRUD Produk</h1><p>Kelola data barang gudang</p></div></header><main class="wrap">
<a class="back" href="index.php">← Dashboard</a>
<?php if ($pesan): ?><div class="notice"><?= e($pesan) ?></div><?php endif; ?>
<section class="panel"><h2><?= $edit ? "Ubah Produk" : "Tambah Produk" ?></h2>
<form method="post" class="form-grid product-form">
<input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>"><input type="hidden" name="aksi" value="<?= $edit ? "ubah" : "tambah" ?>">
<?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit["id"] ?>"><?php endif; ?>
<label>Kode Produk<input name="kode" maxlength="50" required value="<?= e($edit["kode"] ?? "") ?>"></label>
<label>Nama Produk<input name="nama" maxlength="150" required value="<?= e($edit["nama"] ?? "") ?>"></label>
<label>ID Kategori<input type="number" name="kategori_id" min="1" required value="<?= e($edit["kategori_id"] ?? "") ?>"><small>Isi dengan ID yang tersedia pada tabel kategori.</small></label>
<label>ID Supplier <small>(boleh kosong)</small><input type="number" name="supplier_id" min="1" value="<?= e($edit["supplier_id"] ?? "") ?>"><small>Isi ID dari tabel supplier jika ada.</small></label>
<label>ID Satuan<input type="number" name="satuan_id" min="1" required value="<?= e($edit["satuan_id"] ?? "") ?>"><small>Isi dengan ID yang tersedia pada tabel satuan.</small></label>
<label>Stok<input type="number" name="stok" min="0" required value="<?= e($edit["stok"] ?? "0") ?>"></label>
<label>Stok Minimum<input type="number" name="stok_minimum" min="0" required value="<?= e($edit["stok_minimum"] ?? "0") ?>"></label>
<label>Harga Beli<input type="number" name="harga_beli" min="0" step="0.01" required value="<?= e($edit["harga_beli"] ?? "0") ?>"></label>
<label>Harga Jual<input type="number" name="harga_jual" min="0" step="0.01" required value="<?= e($edit["harga_jual"] ?? "0") ?>"></label>
<div class="actions"><button class="btn" type="submit"><?= $edit ? "Simpan Perubahan" : "Tambah Produk" ?></button><?php if ($edit): ?><a class="btn secondary" href="barang.php">Batal</a><?php endif; ?></div>
</form></section>
<section class="panel"><h2>Daftar Produk</h2><div class="table-scroll"><table><thead><tr><th>ID</th><th>Kode</th><th>Nama</th><th>Kategori ID</th><th>Supplier ID</th><th>Satuan ID</th><th>Stok</th><th>Harga Beli</th><th>Harga Jual</th><th>Aksi</th></tr></thead><tbody>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int)$row["id"] ?></td><td><?= e($row["kode"]) ?></td><td><?= e($row["nama"]) ?></td><td><?= (int)$row["kategori_id"] ?></td><td><?= e($row["supplier_id"] ?? "-") ?></td><td><?= (int)$row["satuan_id"] ?></td><td><?= (int)$row["stok"] ?></td><td><?= number_format((float)$row["harga_beli"],2,",",".") ?></td><td><?= number_format((float)$row["harga_jual"],2,",",".") ?></td><td class="actions-cell"><a class="btn small secondary" href="barang.php?edit=<?= (int)$row["id"] ?>">Edit</a><form method="post" class="inline-form" data-confirm="Hapus produk ini?"><input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$row["id"] ?>"><button class="btn small danger">Hapus</button></form></td></tr><?php endwhile; ?>
<?php if ($result->num_rows === 0): ?><tr><td colspan="10" class="empty">Belum ada data produk.</td></tr><?php endif; ?>
</tbody></table></div></section></main><script src="app.js"></script></body></html>
