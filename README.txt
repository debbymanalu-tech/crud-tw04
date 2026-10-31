TW04 - CRUD SEDERHANA USER DAN PRODUK

CARA MEMASANG:
1. Ekstrak folder tw04_crud_manajemen_gudang ke:
   C:\xampp\htdocs\
2. Pastikan Apache dan MySQL aktif di XAMPP.
3. Pastikan database db_manajemen_gudang sudah ada.
4. Buka config.php. Default XAMPP:
   host=localhost, user=root, password kosong.
   Ubah jika konfigurasi MySQL kamu berbeda.
5. Buka:
   http://localhost/tw04_crud_manajemen_gudang/

FITUR:
- CRUD User: tambah, tampil, edit, hapus.
- Password user disimpan dengan password_hash().
- CRUD Produk pada tabel barang.
- CSS responsif dan JavaScript konfirmasi hapus.

PENTING:
- Kode mengikuti struktur tabel users (id, username, password, created_at)
  dan barang (id, kode, nama, kategori_id, supplier_id, satuan_id, stok,
  stok_minimum, harga_beli, harga_jual, created_at, updated_at).
- Saat menambah produk, masukkan ID kategori dan satuan yang benar-benar ada
  di tabel kategori dan satuan. Supplier boleh dikosongkan atau isi ID yang ada.
- Kode produk harus unik.
- Jika muncul error foreign key, periksa ID kategori/satuan/supplier yang diisi.
- Sebaiknya uji pada data percobaan terlebih dahulu.
