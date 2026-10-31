<?php
require "config.php";
csrf_check();
$pesan = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST["aksi"] ?? "";
    if ($aksi === "tambah") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        if ($username === "" || $password === "") {
            $pesan = "Username dan password wajib diisi.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) $pesan = "User berhasil ditambahkan.";
            else $pesan = ($stmt->errno === 1062) ? "Username sudah digunakan." : "Gagal menambah user: " . $stmt->error;
            $stmt->close();
        }
    } elseif ($aksi === "ubah") {
        $id = (int)($_POST["id"] ?? 0);
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        if ($id < 1 || $username === "") $pesan = "Data user tidak valid.";
        elseif ($password !== "") {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $hash, $id);
            $pesan = $stmt->execute() ? "User berhasil diubah." : "Gagal mengubah user: " . $stmt->error;
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
            $stmt->bind_param("si", $username, $id);
            $pesan = $stmt->execute() ? "User berhasil diubah." : "Gagal mengubah user: " . $stmt->error;
            $stmt->close();
        }
    } elseif ($aksi === "hapus") {
        $id = (int)($_POST["id"] ?? 0);
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $pesan = $stmt->execute() ? "User berhasil dihapus." : "Gagal menghapus user: " . $stmt->error;
        $stmt->close();
    }
}
$edit = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT id, username, created_at FROM users ORDER BY id DESC");
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>CRUD User</title><link rel="stylesheet" href="style.css"></head><body>
<header class="top"><div class="wrap"><h1>CRUD User</h1><p>Kelola data pengguna</p></div></header>
<main class="wrap">
<a class="back" href="index.php">← Dashboard</a>
<?php if ($pesan): ?><div class="notice"><?= e($pesan) ?></div><?php endif; ?>
<section class="panel"><h2><?= $edit ? "Ubah User" : "Tambah User" ?></h2>
<form method="post" class="form-grid">
<input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
<input type="hidden" name="aksi" value="<?= $edit ? "ubah" : "tambah" ?>">
<?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit["id"] ?>"><?php endif; ?>
<label>Username<input name="username" maxlength="80" required value="<?= e($edit["username"] ?? "") ?>"></label>
<label>Password <small><?= $edit ? "(kosongkan jika tidak ingin mengganti)" : "" ?></small><input type="password" name="password" <?= $edit ? "" : "required" ?> minlength="6" autocomplete="new-password"></label>
<div class="actions"><button class="btn" type="submit"><?= $edit ? "Simpan Perubahan" : "Tambah User" ?></button><?php if ($edit): ?><a class="btn secondary" href="users.php">Batal</a><?php endif; ?></div>
</form></section>
<section class="panel"><h2>Daftar User</h2><div class="table-scroll"><table><thead><tr><th>ID</th><th>Username</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int)$row["id"] ?></td><td><?= e($row["username"]) ?></td><td><?= e($row["created_at"]) ?></td><td class="actions-cell"><a class="btn small secondary" href="users.php?edit=<?= (int)$row["id"] ?>">Edit</a><form method="post" class="inline-form" data-confirm="Hapus user ini?"><input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$row["id"] ?>"><button class="btn small danger">Hapus</button></form></td></tr><?php endwhile; ?>
<?php if ($result->num_rows === 0): ?><tr><td colspan="4" class="empty">Belum ada data user.</td></tr><?php endif; ?>
</tbody></table></div></section></main><script src="app.js"></script></body></html>
