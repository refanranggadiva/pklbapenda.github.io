<?php
    ob_start();
    //cek session
    session_start();

    if(empty($_SESSION['admin'])){
        $_SESSION['err'] = '<center>Anda harus login terlebih dahulu!</center>';
        header("Location: ./");
        die();
    } else {
?>
<!--

Name        : Aplikasi Sederhana Manajemen SPPT
Version     : v1.0.0
Description : Aplikasi untuk mencatat data sppt masuk dan keluar secara digital.
Date        : 2023
Developer   : Anonimouse
Phone/WA    : 0838-1718-5175
Email       : Universitasmandiri@gmail.com

-->
<!doctype html>
<html lang="en">

<!-- Include Head START -->
<?php include('include/head.php'); ?>
<!-- Include Head END -->

<!-- Body START -->
<body class="bg">

<!-- Header START -->
<header>

<!-- Include Navigation START -->
<?php include('include/menu.php'); ?>
<!-- Include Navigation END -->

</header>
<!-- Header END -->

<!-- Main START -->
<main>

    <!-- container START -->
    <div class="container">

    <?php
        if(isset($_REQUEST['page'])){
            $page = $_REQUEST['page'];
            switch ($page) {
                case 'tsm':
                    include "transaksi_surat_masuk.php";
                    break;
                case 'tsk':
                    include "transaksi_surat_keluar.php";
                    break;
                case 'ref':
                    include "referensi.php";
                    break;
                case 'sett':
                    include "pengaturan.php";
                    break;
                case 'pro':
                    include "profil.php";
                    break;
                case 'gsm':
                    include "galeri_sm.php";
                    break;
                case 'gsk':
                    include "galeri_sk.php";
                    break;
            }
        } else {
    ?>
        <!-- Row START -->
        <div class="row">

            <!-- Include Header Instansi START -->
            <?php include('include/header_instansi.php'); ?>
            <!-- Include Header Instansi END -->

            <!-- Welcome Message START -->
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <h4>Selamat Datang <?php echo $_SESSION['nama']; ?></h4>
                        <p class="description">Anda login sebagai
                        <?php
                            if($_SESSION['admin'] == 1){
                                echo "<strong>Super Admin</strong>. Anda memiliki akses penuh terhadap sistem.";
                            } elseif($_SESSION['admin'] == 2){
                                echo "<strong>Administrator</strong>. Berikut adalah statistik data yang tersimpan dalam sistem.";
                            } else {
                                echo "<strong>Petugas Disposisi</strong>. Berikut adalah statistik data yang tersimpan dalam sistem.";
                            }?></p>
                    </div>
                </div>
            </div>
            <!-- Welcome Message END -->

            <?php
                //menghitung jumlah surat masuk
                $count1 = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_surat_masuk"));

                //menghitung jumlah surat masuk
                $count2 = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_surat_keluar"));

                //menghitung jumlah klasifikasi
                $count4 = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_klasifikasi"));

                //menghitung jumlah pengguna
                $count5 = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_user"));
            ?>

            <!-- Info Statistic START -->
            <div class="col s12 m4">
                <div class="card cyan">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">mail</i> Jumlah SPPT Masuk</span>
                        <?php echo '<h5 class="white-text link">'.$count1.' SPPT Masuk</h5>'; ?>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card lime darken-1">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">drafts</i> Jumlah SPPT Keluar</span>
                        <?php echo '<h5 class="white-text link">'.$count2.' SPPT Keluar</h5>'; ?>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card deep-orange">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">class</i> Jumlah Klasifikasi SPPT</span>
                        <?php echo '<h5 class="white-text link">'.$count4.' Klasifikasi SPPT</h5>'; ?>
                    </div>
                </div>
            </div>

        <?php
            if($_SESSION['id_user'] == 1 || $_SESSION['admin'] == 2){?>
            <div class="col s12 m4">
                <div class="card blue accent-2">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">people</i> Jumlah Pengguna</span>
                        <?php echo '<h5 class="white-text link">'.$count5.' Pengguna</h5>'; ?>
                    </div>
                </div>
            </div>
            <!-- Info Statistic START -->
        <?php
            }
        ?>

        </div>
        <!-- Row END -->
    <?php
        }
    ?>
    </div>
    <!-- container END -->

</main>
<!-- Main END -->

<!-- Include Footer START -->
<?php include('include/footer.php'); ?>
<!-- Include Footer END -->

</body>
<!-- Body END -->

</html>

<?php
    }
?>
