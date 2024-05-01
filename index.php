<?php
require_once 'koneksi.php';

// Definisikan kelas Produkcetak sebagai kelas singleton untuk menyimpan produk
class Produkcetak {
    private static $instance = null;
    private $dbConnection;
    private $table = 'produk';

    // Constructor private untuk mencegah instansiasi langsung dari luar kelas
    private function __construct() {
        // Panggil getInstance dari KoneksiDatabase untuk mendapatkan koneksi
        $koneksiDatabase = KoneksiDatabase::getInstance();
        $this->dbConnection = $koneksiDatabase->getConnection();
    }

    // Metode untuk mendapatkan instance tunggal
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Produkcetak();
        }
        return self::$instance;
    }

    // Metode untuk mendapatkan produk dari database
    public function getProduk() {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metode untuk menambahkan produk baru ke database
    public function tambahProduk($nama_produk, $harga, $deskripsi, $kategori) {
        $query = "INSERT INTO {$this->table} (nama_produk, harga, deskripsi, kategori) VALUES (?, ?, ?, ?)";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute([$nama_produk, $harga, $deskripsi, $kategori]);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }

    // Metode untuk menghapus produk dari database berdasarkan ID
    public function hapusProduk($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute([$id]);
    }

}

// Deklarasi objek Produkcetak untuk penggunaan
$produkCetak = Produkcetak::getInstance();

// Proses form submission untuk menambah produk
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];

    // Memanggil metode tambahProduk untuk menambahkan produk baru ke database
    $produkCetak->tambahProduk($nama_produk, $harga, $deskripsi, $kategori);
    echo "Produk berhasil ditambahkan ke dalam database!";
}

// Mendapatkan daftar produk dari database
$dataProduk = $produkCetak->getProduk();

// Proses penghapusan produk
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $produkCetak->hapusProduk($id);
    // Refresh halaman setelah menghapus produk
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Produk Cetak</title>
</head>
<body>
    <div class="container">
        <h2>Produk Cetak</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="nama">Nama produk:</label>
            <input type="text" id="nama" name="nama" required><br>

            <label for="harga">Harga:</label>
            <input type="number" id="harga" name="harga" required><br>

            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" required></textarea><br>

            <label for="kategori">Kategori:</label>
            <select id="kategori" name="kategori" required>
                <option value="Banner">Banner</option>
                <option value="Stempel">Stempel</option>
            </select><br>

            <input type="submit" value="Tambahkan Produk">
        </form>

        <h2>Daftar Produk cetak</h2>
        <table>
            <tr>
                <th>Nama produk</th>
                <th>Harga</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Hapus</th>
            </tr>
            <?php foreach ($dataProduk as $produk) : ?>
                <tr>
                    <td><?php echo $produk['nama_produk']; ?></td>
                    <td><?php echo $produk['harga']; ?></td>
                    <td><?php echo $produk['deskripsi']; ?></td>
                    <td><?php echo $produk['kategori']; ?></td>
                    <td><a href="?hapus=<?php echo $produk['id']; ?>"><i class="bi bi-trash3-fill"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
