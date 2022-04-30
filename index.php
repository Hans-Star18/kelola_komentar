<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "kelola_komentar");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

$komentar = query('SELECT
t_k_b.id AS comment_id,
t_k_b.isi_komentar,
t_k_b.id_halaman,
t_k_b.komentar_dibuat,
t_k_b.komentar_diperbarui,
t_p.id AS user_id,
t_p.nama_pengguna,
t_p.username,
t_p.email,
t_p.password,
t_p.pengguna_dibuat,
t_p.pengguna_diperbarui
FROM tabel_komentar_baru AS t_k_b JOIN tabel_pengguna AS t_p ON (t_p.id = t_k_b.id_pengguna);');

$balasan = query('SELECT
t_b_k.id AS reason_id,
t_b_k.id_komentar,
t_b_k.isi_balasan,
t_b_k.balasan_ke,
t_b_k.balasan_dibuat,
t_b_k.balasan_diperbarui,
t_p.id AS user_id,
t_p.nama_pengguna,
t_p.username,
t_p.email,
t_p.password,
t_p.pengguna_dibuat,
t_p.pengguna_diperbarui
FROM tabel_balasan_ke_komentar AS t_b_k JOIN tabel_pengguna AS t_p ON (t_p.id = t_b_k.id_pengguna);');

$balasan2 = query('SELECT
t_b_b.id AS balasan_id,
t_b_b.isi_balasan,
t_b_b.id_balasan_ke_komentar,
t_b_b.id_pengguna,
t_b_b.pengguna_dibalas,
t_b_b.balasan_ke,
t_b_b.balasan_dibuat,
t_b_b.balasan_diperbarui,
t_b_k.id AS reason_id,
t_b_k.id_komentar,
t_b_k.balasan_dibuat,
t_b_k.balasan_diperbarui,
t_p.nama_pengguna
FROM
tabel_balasan_ke_balasan AS t_b_b
    JOIN
tabel_balasan_ke_komentar AS t_b_k ON (t_b_b.id_balasan_ke_komentar = t_b_k.id)
    JOIN
tabel_pengguna AS t_p ON(t_b_b.id_pengguna = t_p.id)');

$jumlah_komentar = count($komentar) + count($balasan) + count($balasan2);

function selisihWaktuUntukKomentar($id)
{
    // date_default_timezone_set('Asia/Makassar');
    $komentar_dibuat = query("SELECT komentar_dibuat FROM tabel_komentar_baru WHERE id = '$id'")[0];
    $komentar_dibuat = date_create($komentar_dibuat['komentar_dibuat']);
    $hari_ini = date_create();
    $diff = date_diff($komentar_dibuat, $hari_ini);

    if ($diff->y > 0) {
        $selisih_waktu = $diff->y . " tahun yang lalu.";
    } else if ($diff->m > 0) {
        $selisih_waktu = $diff->m . " bulan yang lalu.";
    } else if ($diff->d > 0) {
        $selisih_waktu = $diff->d . " hari yang lalu.";
    } else if ($diff->h > 0) {
        $selisih_waktu = $diff->h . " jam yang lalu.";
    } else if ($diff->i > 0) {
        $selisih_waktu = $diff->i . " menit yang lalu.";
    } else {
        $selisih_waktu = $diff->s . " detik yang lalu.";
    }

    return $selisih_waktu;
}

function selisihWaktuUntukBalasan($id)
{
    // date_default_timezone_set('Asia/Makassar');
    $balasan_dibuat = query("SELECT balasan_dibuat FROM tabel_balasan_ke_komentar WHERE id = '$id'")['0'];
    $balasan_dibuat = date_create($balasan_dibuat['balasan_dibuat']);
    $hari_ini = date_create();
    $diff = date_diff($hari_ini, $balasan_dibuat);

    if ($diff->y > 0) {
        $selisih_waktu = $diff->y . " tahun yang lalu.";
    } else if ($diff->m > 0) {
        $selisih_waktu = $diff->m . " bulan yang lalu.";
    } else if ($diff->d > 0) {
        $selisih_waktu = $diff->d . " hari yang lalu.";
    } else if ($diff->h > 0) {
        $selisih_waktu = $diff->h . " jam yang lalu.";
    } else if ($diff->i > 0) {
        $selisih_waktu = $diff->i . " menit yang lalu.";
    } else {
        $selisih_waktu = $diff->s . " detik yang lalu.";
    }

    return $selisih_waktu;
}

function selisihWaktuUntukBalasan2($id)
{
    // date_default_timezone_set('Asia/Makassar');
    $balasan_dibuat = query("SELECT balasan_dibuat FROM tabel_balasan_ke_balasan WHERE id = '$id'")['0'];
    $balasan_dibuat = date_create($balasan_dibuat['balasan_dibuat']);
    $hari_ini = date_create();
    $diff = date_diff($hari_ini, $balasan_dibuat);

    if ($diff->y > 0) {
        $selisih_waktu = $diff->y . " tahun yang lalu.";
    } else if ($diff->m > 0) {
        $selisih_waktu = $diff->m . " bulan yang lalu.";
    } else if ($diff->d > 0) {
        $selisih_waktu = $diff->d . " hari yang lalu.";
    } else if ($diff->h > 0) {
        $selisih_waktu = $diff->h . " jam yang lalu.";
    } else if ($diff->i > 0) {
        $selisih_waktu = $diff->i . " menit yang lalu.";
    } else {
        $selisih_waktu = $diff->s . " detik yang lalu.";
    }

    return $selisih_waktu;
}

if (isset($_POST['kirim'])) {
    global $conn;
    if (!isset($_SESSION['masuk'])) {
        $_SESSION['user_salah'] = 'Masuk dulu atau daftar kalau belum punya akun';
        return header("Location: index.php");
    }

    $komentar_baru = htmlspecialchars($_POST['komentar_baru']);
    $id_pengguna = $_SESSION['masuk']['id'];

    if ($komentar_baru == "") {
        $_SESSION["user_salah"] = 'Komentarnya diisi dulu dong!';
        header("Location: index.php");
        exit;
    }

    $query = "INSERT INTO tabel_komentar_baru (isi_komentar, id_halaman, id_pengguna) VALUES ('$komentar_baru', 1, '$id_pengguna')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        header("Refresh:0");
    }
}

if (isset($_POST['tombol_masuk'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM tabel_pengguna WHERE email = '$email'");
    $hasil = mysqli_query($conn, "SELECT * FROM tabel_komentar_baru");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $komentar = mysqli_fetch_assoc($hasil);

        if (password_verify($password, $row['password'])) {
            $_SESSION["masuk"] = $row;
            $_SESSION["komentar"] = $komentar;

            header("Location: index.php");
            exit;
        } else {
            $_SESSION["user_salah"] = 'Akun belum terdaftar!';
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION["user_salah"] = 'Akun belum terdaftar!';
        header("Location: index.php");
        exit;
    }
}

if (isset($_POST['tombol_daftar'])) {
    $nama_pengguna = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM tabel_pengguna WHERE email = '$email'");

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['user_salah'] = 'Pengguna sudah terdaftar';
        return header("Location: index.php");
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($conn, "INSERT INTO tabel_pengguna (nama_pengguna, username, email, password) VALUES ('$nama_pengguna', '$username', '$email', '$password')");

    $_SESSION['sukses'] = 'Pengguna berhasil daftar, silahkan login!';
    return header("Location: index.php");
}

if (isset($_GET['keluar'])) {
    session_destroy();
    session_unset();

    return header("Location: index.php");
}

// komentar 1
if (isset($_POST['tombol_balas'])) {
    $balasan_ke = 1;

    $isi_balasan = htmlspecialchars($_POST['isi_balasan']);
    $id_komentar = $_POST['id'];
    $id_pengguna = $_SESSION['masuk']['id'];
    $balasan_ke = $balasan_ke;

    $query = "INSERT INTO tabel_balasan_ke_komentar (isi_balasan, id_komentar, id_pengguna, balasan_ke) VALUES ('$isi_balasan', '$id_komentar', '$id_pengguna', '$balasan_ke')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        header("Refresh:0");
    }
}

// Komentar 2
if (isset($_POST['tombol_balas_2'])) {
    $balasan_ke = $_POST['balasan_ke'];

    $isi_balasan = htmlspecialchars($_POST['isi_balasan']);
    $id_balasan_ke_komentar = $_POST['id'];
    $id_pengguna = $_SESSION['masuk']['id'];
    $balasan_ke = $balasan_ke;
    $penggunaDibalas = $_POST['penggunaDibalas'];

    $query = "INSERT INTO tabel_balasan_ke_balasan (isi_balasan, id_balasan_ke_komentar, id_pengguna, balasan_ke, pengguna_dibalas) VALUES ('$isi_balasan', '$id_balasan_ke_komentar', '$id_pengguna', '$balasan_ke', '$penggunaDibalas')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        header("Refresh:0");
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Coba Komentar</title>
</head>

<body>
    <div class="container my-5 col-lg-6 border pt-3">
        <?php if (isset($_SESSION['user_salah'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?=$_SESSION['user_salah'];?>
            <?php unset($_SESSION['user_salah']);?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif;?>
        <?php if (isset($_SESSION['sukses'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?=$_SESSION['sukses'];?>
            <?php unset($_SESSION['sukses']);?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif;?>

        <div class="row">
            <div class="d-flex justify-content-between">
                <span class="mb-1 ms-2">
                    <strong><?=$jumlah_komentar;?> Komentar <i class="bi bi-chat"></i></strong>
                </span>

                <span class="mb-0 me-3">
                    <?php if (isset($_SESSION['masuk'])): ?>
                    <li class="list-unstyled dropdown">
                        <a class="text-decoration-none text-muted dropdown-toggle" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <strong><?=$_SESSION['masuk']['nama_pengguna'];?></strong>
                        </a>
                        <ul class="dropdown-menu mx-0 shadow dropdown-menu-end">
                            <li>
                                <form action="" method="GET">
                                    <a class="dropdown-item d-flex gap-2 align-items-center"
                                        href="/kelola_komentar/index.php?keluar">
                                        Keluar
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <a href="" class="text-decoration-none text-muted" data-bs-toggle="modal"
                        data-bs-target="#modalSignin">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </a>
                    <?php endif;?>
                    </a>
                </span>

            </div>
        </div>
        <hr class="mt-0">

        <form action="" class="mt-3" method="POST">
            <div class="input-group mb-1">
                <input type="text" class="form-control" placeholder="Masukan komentar..." name="komentar_baru"
                    autocomplete="off">
                <button type="submit" class="btn btn-outline-primary" name="kirim">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </form>
        <?php if (!isset($_SESSION['masuk'])): ?>
        <div class="row ">
            <div class="col-4">
                <p class="my-0"><small class="text-muted">Masuk</small></p>
                <a href="" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSignin"><i
                        class="bi bi-box-arrow-in-right"></i> Masuk</a>
            </div>
            <div class="col-8 flex-row-reverse ">
                <p class="my-0 "><small class="text-muted ">Atau daftar akun.</small></p>
                <form action="" method="POST">
                    <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseExample" role="button"
                        aria-expanded="false" aria-controls="collapseExample">
                        <input class="form-control form-control-sm mb-1" type="text" placeholder="Nama Lengkap"
                            name="nama_pengguna" autocomplete="off" required>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <input class="form-control form-control-sm mb-1" type="text" placeholder="Username"
                            name="username" autocomplete="off">
                        <input class="form-control form-control-sm mb-1" type="text" placeholder="Email" name="email"
                            autocomplete="off" required>
                        <input class="form-control form-control-sm mb-2" type="text" placeholder="Password"
                            name="password" autocomplete="off" required>
                        <button class="btn btn-outline-primary btn-sm mb-3" name="tombol_daftar">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif;?>

        <ul class="list-group mt-4">
            <?php foreach ($komentar as $k): ?>
            <li class="list-unstyled mb-3">
                <div class="ms-2 me-auto">
                    <div class="fw-normal text-primary d-inline"><?=$k['nama_pengguna'];?></div>
                    <small class="text-muted ms-3">
                        <?=selisihWaktuUntukKomentar($k['comment_id']);?>
                    </small>
                    <p class="mb-0">
                        <?=$k['isi_komentar'];?>
                    </p>
                    <small class="mt-0">
                        <?php if (isset($_SESSION['masuk'])): ?>
                        <a class="text-decoration-none text-muted balas-komentar" data-bs-toggle="modal"
                            data-bs-target="#modalSheet" data-id="<?=$k['comment_id'];?>">
                            Balas <i class="bi bi-reply"></i>
                        </a>
                        <?php else: ?>
                        <a tabindex="0" class="text-decoration-none text-muted popover-dismiss" role="button"
                            data-bs-toggle="popover" data-bs-trigger="focus" title="Penting"
                            data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                            Balas <i class="bi bi-reply"></i>
                        </a>
                        <?php endif;?>
                    </small>

                    <!-- balasan ke 1 -->
                    <?php foreach ($balasan as $b): ?>
                    <?php if ($b['id_komentar'] == $k['comment_id'] && $b['balasan_ke'] == 1): ?>
                    <div class="fw-normal ms-5 text-primary ">
                        <?=$b['nama_pengguna'];?> <i class="bi bi-arrow-return-right ms-2 me-2"></i>
                        <span class="text-muted"><?=$k['nama_pengguna'];?></span>
                        <small class="text-muted ms-3">
                            <?=selisihWaktuUntukBalasan($b['reason_id']);?>
                        </small>
                    </div>
                    <p class="mb-0 ms-5">
                        <?=$b['isi_balasan'];?>
                    </p>
                    <small class="mt-0 ms-5">
                        <?php if (isset($_SESSION['masuk'])): ?>
                        <a class="text-decoration-none text-muted balas-komentar-2" data-bs-toggle="modal"
                            data-bs-target="#modalSheet" data-id="<?=$b['reason_id'];?>" data-balasan_ke="2">
                            Balas <i class="bi bi-reply"></i>
                        </a>
                        <?php else: ?>
                        <a tabindex="0" class="text-decoration-none text-muted popover-dismiss" role="button"
                            data-bs-toggle="popover" data-bs-trigger="focus" title="Penting"
                            data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                            Balas <i class="bi bi-reply"></i>
                        </a>
                        <?php endif;?>
                    </small>

                    <!-- balasan ke 2 -->
                    <?php foreach ($balasan2 as $b2): ?>
                    <?php if ($b2['id_balasan_ke_komentar'] == $b['reason_id']): ?>
                    <?php if ($b2['balasan_ke'] == 2): ?>
                    <div class="ms-5">
                        <div class="fw-normal ms-5 text-primary ">
                            <?=$b2['nama_pengguna'];?> <i class="bi bi-arrow-return-right ms-2 me-2"></i>
                            <span class="text-muted"><?=$b['nama_pengguna'];?></span>
                            <small class="text-muted ms-3">
                                <?=selisihWaktuUntukBalasan2($b2['balasan_id']);?>
                            </small>
                        </div>
                        <p class="mb-0 ms-5">
                            <?=$b2['isi_balasan'];?>
                        </p>
                        <small class="mt-0 ms-5">
                            <?php if (isset($_SESSION['masuk'])): ?>
                            <a class="text-decoration-none text-muted balas-komentar-2" data-bs-toggle="modal"
                                data-bs-target="#modalSheet" data-id="<?=$b['reason_id'];?>"
                                data-pengguna="<?=$b2['nama_pengguna'];?>" data-balasan_ke="3">
                                Balas <i class="bi bi-reply"></i>
                            </a>
                            <?php else: ?>
                            <a tabindex="0" class="text-decoration-none text-muted popover-dismiss" role="button"
                                data-bs-toggle="popover" data-bs-trigger="focus" title="Penting"
                                data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                                Balas <i class="bi bi-reply"></i>
                            </a>
                            <?php endif;?>
                        </small>
                    </div>
                    <!-- Balasan 3 -->
                    <?php elseif ($b2['balasan_ke'] == 3): ?>
                    <div class="ms-5">
                        <div class="ms-5">
                            <div class="fw-normal ms-5 text-primary ">
                                <?=$b2['nama_pengguna'];?> <i class="bi bi-arrow-return-right ms-2 me-2"></i>
                                <span class="text-muted"><?=$b2['pengguna_dibalas'];?></span>
                                <small class="text-muted ms-3">
                                    <?=selisihWaktuUntukBalasan2($b2['balasan_id']);?>
                                </small>
                            </div>
                            <p class="mb-0 ms-5">
                                <?=$b2['isi_balasan'];?>
                            </p>
                            <small class="mt-0 ms-5">
                                <?php if (isset($_SESSION['masuk'])): ?>
                                <a class="text-decoration-none text-muted balas-komentar-2" data-bs-toggle="modal"
                                    data-bs-target="#modalSheet" data-id="<?=$b['reason_id'];?>"
                                    data-pengguna="<?=$b2['nama_pengguna'];?>" data-balasan_ke="4">
                                    Balas <i class="bi bi-reply"></i>
                                </a>
                                <?php else: ?>
                                <a tabindex="0" class="text-decoration-none text-muted popover-dismiss" role="button"
                                    data-bs-toggle="popover" data-bs-trigger="focus" title="Penting"
                                    data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                                    Balas <i class="bi bi-reply"></i>
                                </a>
                                <?php endif;?>
                            </small>
                        </div>
                    </div>
                    <!-- Balasan 4 -->
                    <?php elseif ($b2['balasan_ke'] == 4): ?>
                    <div class="ms-5">
                        <div class="ms-5">
                            <div class="ms-5">
                                <div class="fw-normal ms-5 text-primary ">
                                    <?=$b2['nama_pengguna'];?> <i class="bi bi-arrow-return-right ms-2 me-2"></i>
                                    <span class="text-muted"><?=$b2['pengguna_dibalas'];?></span>
                                    <small class="text-muted ms-3">
                                        <?=selisihWaktuUntukBalasan2($b2['balasan_id']);?>
                                    </small>
                                </div>
                                <p class="mb-0 ms-5">
                                    <?=$b2['isi_balasan'];?>
                                </p>
                                <small class="mt-0 ms-5">
                                    <?php if (isset($_SESSION['masuk'])): ?>
                                    <a class="text-decoration-none text-muted balas-komentar-2" data-bs-toggle="modal"
                                        data-bs-target="#modalSheet" data-id="<?=$b['reason_id'];?>"
                                        data-pengguna="<?=$b2['nama_pengguna'];?>" data-balasan_ke="5">
                                        Balas <i class="bi bi-reply"></i>
                                    </a>
                                    <?php else: ?>
                                    <a tabindex="0" class="text-decoration-none text-muted popover-dismiss"
                                        role="button" data-bs-toggle="popover" data-bs-trigger="focus" title="Penting"
                                        data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                                        Balas <i class="bi bi-reply"></i>
                                    </a>
                                    <?php endif;?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <!-- Balasan 5 -->
                    <?php elseif ($b2['balasan_ke'] == 5): ?>
                    <div class="ms-5">
                        <div class="ms-5">
                            <div class="ms-5">
                                <div class="ms-5">
                                    <div class="fw-normal ms-5 text-primary ">
                                        <?=$b2['nama_pengguna'];?> <i class="bi bi-arrow-return-right ms-2 me-2"></i>
                                        <span class="text-muted"><?=$b2['pengguna_dibalas'];?></span>
                                        <small class="text-muted ms-3">
                                            <?=selisihWaktuUntukBalasan2($b2['balasan_id']);?>
                                        </small>
                                    </div>
                                    <p class="mb-0 ms-5">
                                        <?=$b2['isi_balasan'];?>
                                    </p>
                                    <small class="mt-0 ms-5">
                                        <?php if (isset($_SESSION['masuk'])): ?>
                                        <a class="text-decoration-none text-muted balas-komentar-2"
                                            data-bs-toggle="modal" data-bs-target="#modalSheet"
                                            data-id="<?=$b['reason_id'];?>" data-pengguna="<?=$b2['nama_pengguna'];?>"
                                            data-balasan_ke="6">
                                            Balas <i class="bi bi-reply"></i>
                                        </a>
                                        <?php else: ?>
                                        <a tabindex="0" class="text-decoration-none text-muted popover-dismiss"
                                            role="button" data-bs-toggle="popover" data-bs-trigger="focus"
                                            title="Penting"
                                            data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                                            Balas <i class="bi bi-reply"></i>
                                        </a>
                                        <?php endif;?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Balasan 6 -->
                    <?php elseif ($b2['balasan_ke'] == 6): ?>
                    <div class="ms-5">
                        <div class="ms-5">
                            <div class="ms-5">
                                <div class="ms-5">
                                    <div class="ms-5">
                                        <div class="fw-normal ms-5 text-primary ">
                                            <?=$b2['nama_pengguna'];?> <i
                                                class="bi bi-arrow-return-right ms-2 me-2"></i>
                                            <span class="text-muted"><?=$b2['pengguna_dibalas'];?></span>
                                            <small class="text-muted ms-3">
                                                <?=selisihWaktuUntukBalasan2($b2['balasan_id']);?>
                                            </small>
                                        </div>
                                        <p class="mb-0 ms-5">
                                            <?=$b2['isi_balasan'];?>
                                        </p>
                                        <small class="mt-0 ms-5">
                                            <?php if (isset($_SESSION['masuk'])): ?>
                                            <a class="text-decoration-none text-muted balas-komentar-2"
                                                data-bs-toggle="modal" data-bs-target="#modalSheet"
                                                data-id="<?=$b['reason_id'];?>"
                                                data-pengguna="<?=$b2['nama_pengguna'];?>" data-balasan_ke="7">
                                                Balas <i class="bi bi-reply"></i>
                                            </a>
                                            <?php else: ?>
                                            <a tabindex="0" class="text-decoration-none text-muted popover-dismiss"
                                                role="button" data-bs-toggle="popover" data-bs-trigger="focus"
                                                title="Penting"
                                                data-bs-content="Login dulu jika ingin membalas pesan, atau daftar terlebih dahulu.">
                                                Balas <i class="bi bi-reply"></i>
                                            </a>
                                            <?php endif;?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>
                    <?php endif;?>
                    <?php endforeach;?>
                    <?php endif;?>
                    <?php endforeach;?>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>

    <!-- Modals Login -->
    <div class="modal fade modal-signin  " tabindex="-1" role="dialog" id="modalSignin">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-5 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h2 class="fw-bold mb-0">Silahkan Login</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-5 pt-0">
                    <form action="" method="post">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control rounded-4" id="floatingInput"
                                placeholder="name@example.com" name="email" required>
                            <label for="floatingInput">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control rounded-4" id="floatingPassword"
                                placeholder="Password" name="password" required>
                            <label for="floatingPassword">Password</label>
                        </div>
                        <button class="w-100 mb-2 btn btn-lg rounded-4 btn-primary" type="submit"
                            name="tombol_masuk">Masuk</button>
                    </form>
                    <small class="text-muted text-center">
                        Belum punya akun? <a href="" class="text-decoration-none" data-bs-toggle="modal"
                            data-bs-target="#modalRegistrasi">Registrasi</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registrasi -->
    <div class="modal fade modal-registrasi  " tabindex="-1" role="dialog" id="modalRegistrasi">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-5 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h2 class="fw-bold mb-0">Silahkan Daftar Dulu!</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-5 pt-0">
                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4" id="floatingInput"
                                placeholder="Masukan nama ..." name="nama_pengguna" required>
                            <label for="floatingInput">Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4" id="floatingInput" placeholder="username"
                                name="username">
                            <label for="floatingInput">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control rounded-4" id="floatingInput"
                                placeholder="name@example.com" name="email" required>
                            <label for="floatingInput">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control rounded-4" id="floatingPassword"
                                placeholder="Password" name="password" required>
                            <label for="floatingPassword">Password</label>
                        </div>
                        <button class="w-100 mb-2 btn btn-lg rounded-4 btn-primary" type="submit"
                            name="tombol_daftar">Daftar</button>
                    </form>
                    <small class="text-muted text-center">
                        Sudah punya akun? <a href="" class="text-decoration-none" data-bs-toggle="modal"
                            data-bs-target="#modalSignin">Masuk</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reason -->
    <div class="modal fade modal-sheet" tabindex="-1" role="dialog" id="modalSheet">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-6 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Balas Komentar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body py-0">
                        <div class="form-floating">
                            <input type="hidden" name="id" id="idUntukBalasan">
                            <input type="hidden" name="penggunaDibalas" id="penggunaDibalas">
                            <input type="hidden" name="balasan_ke" id="balasanKe">
                            <textarea class="form-control" placeholder="Tulis komentar di sini" id="floatingTextarea2"
                                style="height: 100px" name="isi_balasan" required></textarea>
                            <label for="floatingTextarea2">Komentar</label>
                        </div>
                    </div>
                    <div class="modal-footer flex-column border-top-0">
                        <button type="submit" class="btn btn-lg btn-primary w-100 mx-0 mb-2"
                            name="tombol_balas">Balas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $('.balas-komentar').on('click', function() {
        const id = $(this).data("id");
        $('#idUntukBalasan').val(id);
        const balasan_ke = $(this).data('balasan_ke');
        $('#balasanKe').val(balasan_ke);

        $('.modal-sheet form .modal-footer button').attr('name', 'tombol_balas');
    });

    $('.balas-komentar-2').on('click', function() {
        const nama_pengguna = $(this).data('pengguna');
        $('#penggunaDibalas').val(nama_pengguna);
        const id = $(this).data('id');
        $('#idUntukBalasan').val(id);
        const balasan_ke = $(this).data('balasan_ke');
        $('#balasanKe').val(balasan_ke);
        console.info(balasan_ke);

        $('.modal-sheet form .modal-footer button').attr('name', 'tombol_balas_2');
    });

    let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    let popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })

    let popover = new bootstrap.Popover(document.querySelector('.popover-dismiss'), {
        trigger: 'focus'
    })
    </script>
</body>

</html>