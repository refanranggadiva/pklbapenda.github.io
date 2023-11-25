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
                    $_SESSION['eno_agenda'] = 'Form Nomor Agenda harus diisi angka!';
                    echo '<script language="javascript">window.history.back();</script>';
                } else {

                    if(!preg_match("/^[a-zA-Z0-9.\/ -]*$/", $nop)){
                        $_SESSION['enop'] = 'Form NOP hanya boleh mengandung karakter huruf, angka, spasi, titik(.), minus(-) dan garis miring(/)';
                        echo '<script language="javascript">window.history.back();</script>';
                    } else {

                        if(!preg_match("/^[a-zA-Z0-9.,() \/ -]*$/", $uraian)){
                            $_SESSION['euraian'] = 'Form Uraian hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-),kurung() dan garis miring(/)';
                            echo '<script language="javascript">window.history.back();</script>';
                        } else {

                            if(!preg_match("/^[a-zA-Z0-9.,_()%&@\/\r\n -]*$/", $nama_wp)){
                                $_SESSION['enama_wp'] = 'Form nama_wp hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-), garis miring(/), kurung(), underscore(_), dan(&) persen(%) dan at(@)';
                                echo '<script language="javascript">window.history.back();</script>';
                            } else {

                                if(!preg_match("/^[a-zA-Z0-9., ]*$/", $nkode)){
                                    $_SESSION['ekode'] = 'Form Kode Klasifikasi hanya boleh mengandung karakter huruf, angka, spasi, titik(.) dan koma(,)';
                                    echo '<script language="javascript">window.history.back();</script>';
                                } else {

                                        if(!preg_match("/^[0-9.-]*$/", $tgl_cetak)){
                                            $_SESSION['etgl_cetak'] = 'Form Tanggal Cetak hanya boleh mengandung angka dan minus(-)';
                                            echo '<script language="javascript">window.history.back();</script>';
                                        } else {

                                            if(!preg_match("/^[a-zA-Z0-9.,()\/ -]*$/", $keterangan)){
                                                $_SESSION['eketerangan'] = 'Form Keterangan hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-), garis miring(/), dan kurung()';
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
                                                    if($ukuran < 2300000){

                                                        $id_surat = $_REQUEST['id_surat'];
                                                        $query = mysqli_query($config, "SELECT file FROM tbl_surat_masuk WHERE id_surat='$id_surat'");
                                                        list($file) = mysqli_fetch_array($query);

                                                        //jika file tidak kosong akan mengeksekusi script dibawah ini
                                                        if(!empty($file)){
                                                            unlink($target_dir.$file);

                                                            move_uploaded_file($_FILES['file']['tmp_name'], $target_dir.$nfile);

                                                            $query = mysqli_query($config, "UPDATE tbl_surat_masuk SET no_agenda='$no_agenda',nop='$nop',uraian='$uraian',nama_wp='$nama_wp',kode='$nkode',tgl_cetak='$tgl_cetak',file='$nfile',keterangan='$keterangan',id_user='$id_user' WHERE id_surat='$id_surat'");

                                                            if($query == true){
                                                                $_SESSION['succEdit'] = 'SUKSES! Data berhasil diupdate';
                                                                header("Location: ./admin.php?page=tsm");
                                                                die();
                                                            } else {
                                                                $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                                echo '<script language="javascript">window.history.back();</script>';
                                                            }
                                                        } else {

                                                            //jika file kosong akan mengeksekusi script dibawah ini
                                                            move_uploaded_file($_FILES['file']['tmp_name'], $target_dir.$nfile);

                                                            $query = mysqli_query($config, "UPDATE tbl_surat_masuk SET no_agenda='$no_agenda',nop='$nop',uraian='$uraian',nama_wp='$nama_wp',kode='$nkode',tgl_cetak='$tgl_cetak',file='$nfile',keterangan='$keterangan',id_user='$id_user' WHERE id_surat='$id_surat'");

                                                            if($query == true){
                                                                $_SESSION['succEdit'] = 'SUKSES! Data berhasil diupdate';
                                                                header("Location: ./admin.php?page=tsm");
                                                                die();
                                                            } else {
                                                                $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                                echo '<script language="javascript">window.history.back();</script>';
                                                            }
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
                                                $id_surat = $_REQUEST['id_surat'];

                                                $query = mysqli_query($config, "UPDATE tbl_surat_masuk SET no_agenda='$no_agenda',nop='$nop',uraian='$uraian',nama_wp='$nama_wp',kode='$nkode',tgl_cetak='$tgl_cetak',keterangan='$keterangan',id_user='$id_user' WHERE id_surat='$id_surat'");

                                                if($query == true){
                                                    $_SESSION['succEdit'] = 'SUKSES! Data berhasil diupdate';
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
        } else {

        $id_surat = mysqli_real_escape_string($config, $_REQUEST['id_surat']);
        $query = mysqli_query($config, "SELECT id_surat, no_agenda, nop, uraian, nama_wp, kode, tgl_cetak, file, keterangan, id_user FROM tbl_surat_masuk WHERE id_surat='$id_surat'");
        list($id_surat, $no_agenda, $nop, $uraian, $nama_wp, $kode, $tgl_cetak, $file, $keterangan, $id_user) = mysqli_fetch_array($query);

        if($_SESSION['id_user'] != $id_user AND $_SESSION['id_user'] != 1){
            echo '<script language="javascript">
                    window.alert("ERROR! Anda tidak memiliki hak akses untuk mengedit data ini");
                    window.location.href="./admin.php?page=tsm";
                  </script>';
        } else {?>

            <!-- Row Start -->
            <div class="row">
                <!-- Secondary Nav START -->
                <div class="col s12">
                    <nav class="secondary-nav">
                        <div class="nav-wrapper blue-grey darken-1">
                            <ul class="left">
                                <li class="waves-effect waves-light"><a href="#" class="judul"><i class="material-icons">edit</i> Edit Data SPPT Masuk</a></li>
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
                <form class="col s12" method="POST" action="?page=tsm&act=edit" enctype="multipart/form-data">

                    <!-- Row in form START -->
                    <div class="row">
                        <div class="input-field col s6">
                            <input type="hidden" name="id_surat" value="<?php echo $id_surat ;?>">
                            <i class="material-icons prefix md-prefix">looks_one</i>
                            <input id="no_agenda" type="number" class="validate" value="<?php echo $no_agenda ;?>" name="no_agenda" required>
                                <?php
                                    if(isset($_SESSION['eno_agenda'])){
                                        $eno_agenda = $_SESSION['eno_agenda'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$eno_agenda.'</div>';
                                        unset($_SESSION['eno_agenda']);
                                    }
                                ?>
                            <label for="no_agenda">Nomor Agenda</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">bookmark</i>
                            <input id="kode" type="text" class="validate" name="kode" value="<?php echo $kode ;?>" required>
                                <?php
                                    if(isset($_SESSION['ekode'])){
                                        $ekode = $_SESSION['ekode'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$ekode.'</div>';
                                        unset($_SESSION['ekode']);
                                    }
                                ?>
                            <label for="kode">Kode Klasifikasi</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">bookmark</i>
                            <input id="uraian" type="text" class="validate" name="uraian" value="<?php echo $uraian ;?>" required>
                                <?php
                                    if(isset($_SESSION['euraian'])){
                                        $euraian = $_SESSION['euraian'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$euraian.'</div>';
                                        unset($_SESSION['euraian']);
                                    }
                                ?>
                            <label for="uraian">Uraian</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">looks_two</i>
                            <input id="nop" type="text" class="validate" name="nop" value="<?php echo $nop ;?>" required>
                                <?php
                                    if(isset($_SESSION['enop'])){
                                        $enop = $_SESSION['enop'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$enop.'</div>';
                                        unset($_SESSION['enop']);
                                    }
                                ?>
                            <label for="nop">NOP</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">date_range</i>
                            <input id="tgl_cetak" type="text" name="tgl_cetak" class="datepicker" value="<?php echo $tgl_cetak ;?>" required>
                                <?php
                                    if(isset($_SESSION['etgl_cetak'])){
                                        $etgl_cetak = $_SESSION['etgl_cetak'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$etgl_cetak.'</div>';
                                        unset($_SESSION['etgl_cetak']);
                                    }
                                ?>
                            <label for="tgl_cetak">Tanggal Cetak</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">description</i>
                            <textarea id="nama_wp" class="materialize-textarea validate" name="nama_wp" required><?php echo $nama_wp ;?></textarea>
                                <?php
                                    if(isset($_SESSION['enama_wp'])){
                                        $enama_wp = $_SESSION['enama_wp'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$enama_wp.'</div>';
                                        unset($_SESSION['enama_wp']);
                                    }
                                ?>
                            <label for="isi">Nama WP</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix md-prefix">featured_play_list</i>
                            <input id="keterangan" type="text" class="validate" name="keterangan" value="<?php echo $keterangan ;?>" required>
                                <?php
                                    if(isset($_SESSION['eketerangan'])){
                                        $eketerangan = $_SESSION['eketerangan'];
                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$eketerangan.'</div>';
                                        unset($_SESSION['eketerangan']);
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
                                    <input class="file-path validate" type="text" value="<?php echo $file ;?>" placeholder="Upload file/scan gambar surat masuk">
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
    }
?>
