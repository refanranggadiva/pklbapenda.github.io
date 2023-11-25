<?php
    //cek session
    if(empty($_SESSION['admin'])){
        $_SESSION['err'] = '<center>Anda harus login terlebih dahulu!</center>';
        header("Location: ./");
        die();
    } else {

        if(isset($_REQUEST['submit'])){

            //validasi form kosong
            if($_REQUEST['no_agenda'] == "" || $_REQUEST['nop'] == "" || $_REQUEST['uraian'] == "" || $_REQUEST['nama_wp'] == ""
                || $_REQUEST['kode'] == "" || $_REQUEST['tgl_cetak'] == ""  || $_REQUEST['keterangan'] == ""){
                $_SESSION['errEmpty'] = 'ERROR! Semua form wajib diisi';
                echo '<script language="javascript">window.history.back();</script>';
            } else {

                $no_agenda = $_REQUEST['no_agenda'];
                $nop = $_REQUEST['nop'];
                $uraian = $_REQUEST['uraian'];
                $nama_wp = $_REQUEST['nama_wp'];
                $kode = substr($_REQUEST['kode'],0,30);
                $nkode = trim($kode);
                $tgl_cetak = $_REQUEST['tgl_cetak'];
                $keterangan = $_REQUEST['keterangan'];
                $id_user = $_SESSION['id_user'];

                //validasi input data
                if(!preg_match("/^[0-9]*$/", $no_agenda)){
                    $_SESSION['no_agenda'] = 'Form Nomor Agenda harus diisi angka!';
                    echo '<script language="javascript">window.history.back();</script>';
                } else {

                    if(!preg_match("/^[a-zA-Z0-9.\/ -]*$/", $nop)){
                        $_SESSION['nop'] = 'Form NOP hanya boleh mengandung karakter huruf, angka, spasi, titik(.), minus(-) dan garis miring(/)';
                        echo '<script language="javascript">window.history.back();</script>';
                    } else {

                        if(!preg_match("/^[a-zA-Z0-9.,() \/ -]*$/", $uraian)){
                            $_SESSION['uraian'] = 'Form Uraian hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-),kurung() dan garis miring(/)';
                            echo '<script language="javascript">window.history.back();</script>';
                        } else {

                            if(!preg_match("/^[a-zA-Z0-9.,_()%&@\/\r\n -]*$/", $nama_wp)){
                                $_SESSION['nama_wp'] = 'Form Nama WP hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-), garis miring(/), kurung(), underscore(_), dan(&) persen(%) dan at(@)';
                                echo '<script language="javascript">window.history.back();</script>';
                            } else {

                                if(!preg_match("/^[a-zA-Z0-9., ]*$/", $nkode)){
                                    $_SESSION['kode'] = 'Form Kode Klasifikasi hanya boleh mengandung karakter huruf, angka, spasi, titik(.) dan koma(,)';
                                    echo '<script language="javascript">window.history.back();</script>';
                                } else {
                                        if(!preg_match("/^[0-9.-]*$/", $tgl_cetak)){
                                            $_SESSION['tgl_cetak'] = 'Form Tanggal Cetak hanya boleh mengandung angka dan minus(-)';
                                            echo '<script language="javascript">window.history.back();</script>';
                                        } else {

                                            if(!preg_match("/^[a-zA-Z0-9.,()\/ -]*$/", $keterangan)){
                                                $_SESSION['keterangan'] = 'Form Keterangan hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-), garis miring(/), dan kurung()';
                                                echo '<script language="javascript">window.history.back();</script>';
                                            } else {

                                                $cek = mysqli_query($config, "SELECT * FROM tbl_surat_masuk WHERE nop='$nop'");
                                                $result = mysqli_num_rows($cek);

                                                if($result > 0){
                                                    $_SESSION['errDup'] = 'NOP sudah terpakai, gunakan yang lain!';
                                                    echo '<script language="javascript">window.history.back();</script>';
                                                } else {

                                                    $ekstensi = array('jpg','png','jpeg','doc','docx','pdf');
                                                    $file = $_FILES['file']['name'];
                                                    $x = explode('.', $file);
                                                    $eks = strtolower(end($x));
                                                    $ukuran = $_FILES['file']['size'];
                                                    $target_dir = "upload/surat_masuk/";

                                                    if (! is_dir($target_dir)) {
                                                        mkdir($target_dir, 0755, true);
                                                    }

                                                    //jika form file tidak kosong akan mengeksekusi script dibawah ini
                                                    if($file != ""){

                                                        $rand = rand(1,10000);
                                                        $nfile = $rand."-".$file;

                                                        //validasi file
                                                        if(in_array($eks, $ekstensi) == true){
                                                            if($ukuran < 2500000){

                                                                move_uploaded_file($_FILES['file']['tmp_name'], $target_dir.$nfile);

                                                                $query = mysqli_query($config, "INSERT INTO tbl_surat_masuk(no_agenda,nop,uraian,nama_wp,kode,tgl_cetak,
                                                                    tgl_diterima,file,keterangan,id_user)
                                                                        VALUES('$no_agenda','$nop','$uraian','$nama_wp','$nkode','$tgl_cetak',NOW(),'$nfile','$keterangan','$id_user')");

                                                                if($query == true){
                                                                    $_SESSION['succAdd'] = 'SUKSES! Data berhasil ditambahkan';
                                                                    header("Location: ./admin.php?page=tsm");
                                                                    die();
                                                                } else {
                                                                    $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                                    echo '<script language="javascript">window.history.back();</script>';
                                                                }
                                                            } else {
                                                                $_SESSION['errSize'] = 'Ukuran file yang diupload terlalu besar!';
                                                                echo '<script language="javascript">window.history.back();</script>';
                                                            }
                                                        } else {
                                                            $_SESSION['errFormat'] = 'Format file yang diperbolehkan hanya *.JPG, *.PNG, *.DOC, *.DOCX atau *.PDF!';
                                                            echo '<script language="javascript">window.history.back();</script>';
                                                        }
                                                    } else {

                                                        //jika form file kosong akan mengeksekusi script dibawah ini
                                                        $query = mysqli_query($config, "INSERT INTO tbl_surat_masuk(no_agenda,nop,uraian,nama_wp,kode,tgl_cetak, tgl_diterima,file,keterangan,id_user)
                                                            VALUES('$no_agenda','$nop','$uraian','$nama_wp','$nkode','$tgl_cetak',NOW(),'','$keterangan','$id_user')");

                                                        if($query == true){
                                                            $_SESSION['succAdd'] = 'SUKSES! Data berhasil ditambahkan';
                                                            header("Location: ./admin.php?page=tsm");
                                                            die();
                                                        } else {
                                                            $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                            echo '<script language="javascript">window.history.back();</script>';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
            ?>
            <!-- Row Start -->
            <div class="row">
                <!-- Secondary Nav START -->
                <div class="col s12">
                    <nav class="secondary-nav">
                        <div class="nav-wrapper blue-grey darken-1">
                            <ul class="left">
                                <li class="waves-effect waves-light"><a href="?page=tsm&act=add" class="judul"><i class="material-icons">mail</i> Tambah Data SPPT Masuk</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <!-- Secondary Nav END -->
            </div>
            <!-- Row END -->

            <?php
                if(isset($_SESSION['errQ'])){
                    $errQ = $_SESSION['errQ'];
                    echo '<div id="alert-message" class="row">
                            <div class="col m12">
                                <div class="card red lighten-5">
                                    <div class="card-content notif">
                                        <span class="card-title red-text"><i class="material-icons md-36">clear</i> '.$errQ.'</span>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    unset($_SESSION['errQ']);
                }
                if(isset($_SESSION['errEmpty'])){
                    $errEmpty = $_SESSION['errEmpty'];
                    echo '<div id="alert-message" class="row">
                            <div class="col m12">
                                <div class="card red lighten-5">
                                    <div class="card-content notif">
                                        <span class="card-title red-text"><i class="material-icons md-36">clear</i> '.$errEmpty.'</span>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    unset($_SESSION['errEmpty']);
                }
            ?>

            <!-- Row form Start -->
            <div class="row jarak-form">

                <!-- Form START -->
                <form class="col s12" method="POST" action="?page=tsm&act=add" enctype="multipart/form-data">

                    <!-- Row in form START -->
                    <div class="row">
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">looks_one</i>
                            <?php
                            echo '<input id="no_agenda" type="number" class="validate" name="no_agenda" value="';
                                $sql = mysqli_query($config, "SELECT no_agenda FROM tbl_surat_masuk");
                                $no_agenda = "1";
                                if (mysqli_num_rows($sql) == 0){
                                    echo $no_agenda;
                                }

                                $result = mysqli_num_rows($sql);
                                $counter = 0;
                                while(list($no_agenda) = mysqli_fetch_array($sql)){
                                    if (++$counter == $result) {
                                        $no_agenda++;
                                        echo $no_agenda;
                                    }
                                }
                                echo '" required>';

                                if(isset($_SESSION['no_agenda'])){
                                    $no_agenda = $_SESSION['no_agenda'];
                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$no_agenda.'</div>';
                                    unset($_SESSION['no_agenda']);
                                }
                            ?>
                            <label for="no_agenda">Nomor Agenda</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">bookmark</i>
                            <input id="kode" type="text" class="validate" name="kode" required>
                                <?php
                                    if(isset($_SESSION['kode'])){
                                        $kode = $_SESSION['kode'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$kode.'</div>';
                                        unset($_SESSION['kode']);
                                    }
                                ?>
                            <label for="kode">Kode Klasifikasi</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">bookmark</i>
                            <input id="uraian" type="text" class="validate" name="uraian" required>
                                <?php
                                    if(isset($_SESSION['uraian'])){
                                        $uraian = $_SESSION['uraian'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$uraian.'</div>';
                                        unset($_SESSION['uraian']);
                                    }
                                ?>
                            <label for="uraian">Uraian</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">looks_two</i>
                            <input id="nop" type="text" class="validate" name="nop" required>
                                <?php
                                    if(isset($_SESSION['nop'])){
                                        $nop = $_SESSION['nop'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nop.'</div>';
                                        unset($_SESSION['nop']);
                                    }
                                ?>
                            <label for="indeks">NOP</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">date_range</i>
                            <input id="tgl_cetak" type="text" name="tgl_cetak" class="datepicker" required>
                                <?php
                                    if(isset($_SESSION['tgl_cetak'])){
                                        $tgl_cetak = $_SESSION['tgl_cetak'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$tgl_cetak.'</div>';
                                        unset($_SESSION['tgl_cetak']);
                                    }
                                ?>
                            <label for="tgl_cetak">Tanggal Cetak</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">description</i>
                            <textarea id="nama_wp" class="materialize-textarea validate" name="nama_wp" required></textarea>
                                <?php
                                    if(isset($_SESSION['nama_wp'])){
                                        $nama_wp = $_SESSION['nama_wp'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nama_wp.'</div>';
                                        unset($_SESSION['nama_wp']);
                                    }
                                ?>
                            <label for="isi">Nama WP</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">featured_play_list</i>
                            <input id="keterangan" type="text" class="validate" name="keterangan" required>
                                <?php
                                    if(isset($_SESSION['keterangan'])){
                                        $keterangan = $_SESSION['keterangan'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$keterangan.'</div>';
                                        unset($_SESSION['keterangan']);
                                    }
                                ?>
                            <label for="keterangan">Keterangan</label>
                        </div>
                        <div class="input-field col s6">
                            <div class="file-field input-field">
                                <div class="btn light-green darken-1">
                                    <span>File</span>
                                    <input type="file" id="file" name="file">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload file/scan gambar surat masuk">
                                        <?php
                                            if(isset($_SESSION['errSize'])){
                                                $errSize = $_SESSION['errSize'];
                                                echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$errSize.'</div>';
                                                unset($_SESSION['errSize']);
                                            }
                                            if(isset($_SESSION['errFormat'])){
                                                $errFormat = $_SESSION['errFormat'];
                                                echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$errFormat.'</div>';
                                                unset($_SESSION['errFormat']);
                                            }
                                        ?>
                                    <small class="red-text">*Format file yang diperbolehkan *.JPG, *.PNG, *.DOC, *.DOCX, *.PDF dan ukuran maksimal file 2 MB!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row in form END -->

                    <div class="row">
                        <div class="col 6">
                            <button type="submit" name="submit" class="btn-large blue waves-effect waves-light">SIMPAN <i class="material-icons">done</i></button>
                        </div>
                        <div class="col 6">
                            <a href="?page=tsm" class="btn-large deep-orange waves-effect waves-light">BATAL <i class="material-icons">clear</i></a>
                        </div>
                    </div>

                </form>
                <!-- Form END -->

            </div>
            <!-- Row form END -->

<?php
        }
    }
?>
