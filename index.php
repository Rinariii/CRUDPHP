<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Check if user is admin
$username = $_SESSION['username'];
$isAdmin = ($username === 'admin');
define('ENVIRONMENT', 'development');
if (ENVIRONMENT === 'development') {
    $host = 'localhost:3307';
    $user = 'root';
    $pass = '';
    $name = 'dbinputpelanggan';
} else {
    $host = 'your_host';
    $user = 'your_username';
    $pass = 'your_password';
    $name = 'your_database';
}
$koneksi = new mysqli($host, $user, $pass, $name);

if ($koneksi->connect_error) {
    die('Koneksi gagal: ' . $koneksi->connect_error);
}

if (isset($_POST["bsimpan"])) {
  if (!$isAdmin) {
      die("<script>alert('Akses ditolak! Hanya admin yang dapat melakukan operasi ini.');document.location='index.php';</script>");
  }
  
  $data_pelanggan = [
      'Nama' => $_POST['tnama'],
      'Email' => $_POST['temail'],
      'Nomor Telepon' => $_POST['tnotelp']
  ];
  
  if (isset($_GET['hal']) && $_GET['hal'] == "edit") {
      $stmt = $koneksi->prepare("UPDATE tbpelanggan SET Nama=?, Email=?, `Nomor Telepon`=? WHERE id_pelanggan=?");
      $stmt->bind_param("sssi", $data_pelanggan['Nama'], $data_pelanggan['Email'], $data_pelanggan['Nomor Telepon'], $_GET['id']);
      
      if ($stmt->execute()) {
          echo "<script>alert('Edit Data Sukses Dilakukan!');document.location='index.php';</script>";
      } else {
          echo "<script>alert('Edit Data Gagal Dilakukan!');document.location='index.php';</script>";
      }
      $stmt->close();
  } else {
      $stmt = $koneksi->prepare("INSERT INTO tbpelanggan (Nama, Email, `Nomor Telepon`) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $data_pelanggan['Nama'], $data_pelanggan['Email'], $data_pelanggan['Nomor Telepon']);
      
      if ($stmt->execute()) {
          echo "<script>alert('Simpan Data Sukses Dilakukan!');document.location='index.php';</script>";
      } else {
          echo "<script>alert('Simpan Data Gagal Dilakukan!');document.location='index.php';</script>";
      }
      $stmt->close();
  }
}

$vnama = $vemail = $vnotelp = "";

if (isset($_GET['hal'])) {
  if (!$isAdmin) {
      die("<script>alert('Akses ditolak! Hanya admin yang dapat melakukan operasi ini.');document.location='index.php';</script>");
  }
  
  if ($_GET['hal'] == "edit") {
      $stmt = $koneksi->prepare("SELECT * FROM tbpelanggan WHERE id_pelanggan = ?");
      $stmt->bind_param("i", $_GET['id']);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($data = $result->fetch_assoc()) {
          $vnama = $data['Nama'];
          $vemail = $data['Email'];
          $vnotelp = $data['Nomor Telepon'];
      }
      $stmt->close();
  } elseif ($_GET['hal'] == "hapus") {
      $stmt = $koneksi->prepare("DELETE FROM tbpelanggan WHERE id_pelanggan = ?");
      $stmt->bind_param("i", $_GET['id']);
      if ($stmt->execute()) {
          echo "<script>alert('Hapus Data Sukses Dilakukan!');document.location='index.php';</script>";
      } else {
          echo "<script>alert('Hapus Data Gagal Dilakukan!');document.location='index.php';</script>";
      }
      $stmt->close();
  }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD dengan PHP dan My SQL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h3 class="text-center">DATABASE PELANGGAN</h3>
    <div class="row">
      <div class="col-md-8 mx-auto">
        <div class="card">
          <div class="card-header bg-info text-light">INPUT DATABASE PELANGGAN</div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="tnama" value ="<?= $vnama?>" class="form-control" placeholder="Masukan Nama Pelanggan">
              </div>
              <div class="mb-3">
                <label class="form-label">Email Pelanggan</label>
                <input type="email" name="temail" value ="<?= $vemail?>" class="form-control" placeholder="Masukan Email Anda">
              </div>
              <div class="mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="tnotelp" value ="<?= $vnotelp?>" class="form-control" placeholder="Masukkan Nomor Telepon Anda" minlength="11" maxlength="13" pattern="\d{11,13}" required>
                <div class="form-text">Nomor harus 11-13 digit angka.</div>
              </div>
              <script>
                document.querySelector('input[name="tnotelp"]').addEventListener('input', function (e) {
                  this.value = this.value.replace(/\D/g, '');
                });
              </script>
              <div class="text-center">
                <hr>
                <button class="btn btn-primary" name="bsimpan" type="submit">Simpan</button>
                <button class="btn btn-danger" name="bkosongkan" type="reset">Kosongkan</button>
                <a href="backup.php" class="btn btn-secondary">Backup Database</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>

                <hr>
              </div>
            </form>
          </div>
          <div class="card-footer bg-info"></div>
        </div>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header bg-info text-light">DATABASE PELANGGAN</div>
      <div class="card-body">
        <div class="col-md-8 mx-auto">
          <form method="POST">
            <div class="input-group mb-3">
              <input type="text" name="tcari" class="form-control" placeholder="Masukan Kata Kunci">
              <button class="btn btn-primary" name="bcari" type="submit">Cari</button>
              <button class="btn btn-danger" name="breset" type="submit">Reset</button>
            </div>
          </form>
        </div>
        <table class="table table-striped table-hover table-bordered">
          <tr>
            <th>ID</th>
            <th>Nama Pelanggan</th>
            <th>Email Pelanggan</th>
            <th>Nomor Telepon</th>
            <th>Tanggal Registrasi</th>
            <th>Aksi</th>
          </tr>

          <?php
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

$keyword = "%" . ($_POST['tcari'] ?? '') . "%";
$stmt = $koneksi->prepare("SELECT id_pelanggan, Nama, Email, `Nomor Telepon`, `Tanggal Registrasi` 
                           FROM tbpelanggan 
                           WHERE Nama LIKE ? OR Email LIKE ? OR `Nomor Telepon` LIKE ? 
                           ORDER BY `Tanggal Registrasi` DESC 
                           LIMIT ? OFFSET ?");
$stmt->bind_param("ssiii", $keyword, $keyword, $keyword, $limit, $offset);
$stmt->execute();
$tampil = $stmt->get_result();

$no = $offset + 1;
while ($data = $tampil->fetch_assoc()) {
?>
<tr>
    <th><?= $no++ ?></th>
    <th><?= $data["Nama"] ?></th>
    <th><?= $data["Email"] ?></th>
    <th><?= $data["Nomor Telepon"] ?></th>
    <th><?= $data["Tanggal Registrasi"] ?></th>
    <td>
        <a href="index.php?hal=edit&id=<?= $data["id_pelanggan"] ?>" class="btn btn-warning">Edit</a>
        <a href="index.php?hal=hapus&id=<?= $data["id_pelanggan"] ?>" class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">Hapus</a>
    </td>
</tr>
<?php } ?>
        </table>

<?php
        $total_result = $koneksi->query("SELECT COUNT(*) AS total FROM tbpelanggan");
        $row = $total_result->fetch_assoc();
        $total_data = $row['total'];
        $total_pages = ceil($total_data / $limit);
        ?>
        <nav>
            <ul class="pagination">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
      </div>
    </div>
  </div>
</body>
</html>
