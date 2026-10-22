<?php require_once('db.php');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bursa Büyükşehir Belediyespor Online Kayıt Sistemi</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/bootstrap-formhelpers.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/docs.min.css">
<link rel="stylesheet" type="text/css" href="./bootstrap-3.1.1-dist/css/style.css">
<script>
  function sayfayagit(Fm){
  var URL = Fm.options[Fm.selectedIndex].value;
  window.location.href = URL;
}
</script>
</head>

  <body role="document">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Mobil Menü</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?php if (!$bolum) {echo ' class="active"';}?>><a href="./">Anasayfa</a></li>
            <?php if ($_SESSION['giris']) {?>

            <?php if ($_SESSION['uye_admin'] == "admin") {?>
            <li class="dropdown<?php if ($bolum == "uyeler") {echo ' active';}?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Üyeler <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="./?bolum=uyeler&islem=yeni_ekle">Yeni Ekle</a></li>
                <li><a href="./?bolum=uyeler&islem=listele">Listele</a></li>
              </ul>
            </li>
            <li<?php if ($bolum == "tesisler" && $islem == "tesis_listele") {echo ' class="active"';}?>><a href="./?bolum=tesisler&islem=tesis_listele">Tesisler</a></li>

            <li class="dropdown<?php if ($bolum == "tesisler" && $islem == "brans_listele") {echo ' active';}?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Branşlar <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="./?bolum=tesisler&islem=brans_listele">Branş Ekle / Listele</a></li>
                <li><a href="./?bolum=tesisler&islem=brans_yas">Branş Yaş Ekle / Listele</a></li>
              </ul>
            </li>

            <?php }?>

            <li class="dropdown<?php if ($bolum == "basvurular") {echo ' active';}?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Başvurular <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="./?bolum=basvurular&islem=ekle">Ekle</a></li>
                <li><a href="./?bolum=basvurular&islem=listele">Listele</a></li>
                <?php if ($_SESSION['uye_admin'] == "admin") {?>
                <li><a href="./?bolum=tumunu_excele_aktar">Tümünü Excel'e Aktar</a></li>
                <?php }?>
              </ul>
            </li>

            <!--<li<?php if ($bolum == "istatistikler") {echo ' class="active"';}?>><a href="./?bolum=istatistikler">İstatistikler</a></li>-->
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=e($_SESSION['uye_adsoyad'])?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="./?bolum=profilim">Profilim</a></li>
                <li><a href="./?bolum=uye_cikis">Çıkış</a></li>
              </ul>
            </li>
            <?php }?>
          </ul>
        </div>
      </div>
    </div>

    <div class="container theme-showcase" role="main">
      <div class="jumbotron">
      <?php
        if (!$bolum) {
          if (!$_SESSION['giris']) {
      ?>
          <form class="form-signin" role="form" action="./?bolum=giris" method="post">
            <h2 class="form-signin-heading">Lütfen Giriş Yapınız</h2>
            <input type="email" name="email" class="form-control" placeholder="E-Mail Adresi" required autofocus>
            <input type="password" name="sifre" class="form-control" placeholder="Şifre" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Giriş</button>
          </form>
      <?php
          } else {
            echo "Devam etmek menüden bir bölüm seçiniz.";
          }
      } elseif ($bolum == "giris") {
        $email = sql_clear($_POST["email"] ?? '');
        $sifre = (string) ($_POST["sifre"] ?? '');

        if (!$email || $sifre === '') {
          echo 'E-Mail adresini ve Şifre alanını boş bırakmayınız.' . GERI_DON;
        } elseif (valid_email($email) == false) {
          echo 'Hatalı E-Mail adresi' . GERI_DON;
        } else {
          $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_email = '".$email."'");
            if (!$kontrol || !sifre_dogrula($sifre, $kontrol->uye_sifre)) {
              echo 'Hatalı E-Mail Adresi veya Şifre' . GERI_DON;
            } else {
              // MD5 veya eski algoritmalı şifreyi girişte password_hash'e yükselt
              if (password_needs_rehash((string) $kontrol->uye_sifre, PASSWORD_DEFAULT)) {
                $wpdb->query("UPDATE basvuru_uyeler SET uye_sifre = '".sifre_hash($sifre)."' where uye_id = ".(int) $kontrol->uye_id);
              }
              session_regenerate_id(true);
              $_SESSION['giris'] = "evet";
              $_SESSION['uye_id'] = $kontrol->uye_id;
              $_SESSION['uye_adsoyad'] = $kontrol->uye_adsoyad;
              $_SESSION['uye_yetki'] = yetki_listesi($kontrol->uye_yetki);
              $_SESSION['uye_admin'] = $kontrol->uye_admin;
              redirect("./?bolum=basvurular&islem=listele");
            }
        }
      } elseif ($bolum == "istatistikler") {
      ?>
      test
      <?php
      } elseif ($bolum == "tesisler" && $_SESSION['uye_admin'] == "admin") {
        if ($islem == "tesis_duzenle") {
        $kontrol = $wpdb->get_row("select * from basvuru_tesisler where tesis_id = ". $id); 
      ?>
      <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=tesis_guncelle&id=<?=$id?>">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="tesis_isim">Tesis İsim</label>
          <div class="controls">
            <input id="tesis_isim" name="tesis_isim" type="text" placeholder="" class="form-control" required value="<?=e($kontrol->tesis_isim ?? '')?>">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="guncelle"></label>
          <div class="controls">
            <button id="guncelle" name="guncelleguncelle" class="btn btn-primary">Güncelle</button>
          </div>
        </div>

        </fieldset>
      </form>
      <?php
        } elseif ($islem == "brans_yas") {
      ?>
              <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=brans_yas_ekle">
                <fieldset>
                <div class="control-group">
                  <label class="control-label" for="yas_isim">Branş Yaşı</label>
                  <div class="controls">
                    <input id="yas_isim" name="yas_isim" type="text" placeholder="" class="form-control" required>            
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="ekle"></label>
                  <div class="controls">
                    <button id="ekle" name="ekle" class="btn btn-primary">Ekle</button>
                  </div>
                </div>

                </fieldset>
              </form>
              <br />
              <table class="table table-hover">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="85%">Yaş</th>
                  <th width="10%">Sil</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $listele = $wpdb->get_results("select * from basvuru_yas order by yas_isim asc");

              if ($listele) {
                $listesira = 1;
                foreach ($listele as $liste) {
                ?>
                  <tr>
                    <td><?=$listesira?></td>
                    <td><a href="./?bolum=tesisler&islem=brans_yas_duzenle&id=<?=e($liste->yas_id ?? '')?>"><?=e($liste->yas_isim ?? '')?></a></td>
                    <td><a href="./?bolum=tesisler&islem=brans_yas_sil&id=<?=e($liste->yas_id ?? '')?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i> Sil</a></td>
                  </tr>
                <?php
                $listesira++;
                }
              } else {
                echo '<tr>
                        <td colspan="2">Kayıtlı branş yaşı bulunamadı</td>
                      </tr>';
              }
              ?>
              </tbody>
            </table>
      <?php
        } elseif ($islem == "brans_yas_sil") {
          $kontrol = $wpdb->get_row("select * from basvuru_branslar where yas_id = ". $id);
          if ($kontrol) {
            echo 'Bu yaşa bağlı branş mevcut. Öncelikle bu yaşa bağlı olan branşları güncellemelisiniz.' . GERI_DON;
          } else {
            $sql = "DELETE FROM basvuru_yas ";
            $sql.="where yas_id=".$id;
              if($wpdb->query($sql)){
                echo 'Yeni branş yaşı başarıyla eklenmiştir.' . GERI_DON;
              } else {
              echo 'Yeni branş yaşı eklenirken bir hata oluştu.' . GERI_DON;
              }
          }
        } elseif ($islem == "brans_yas_ekle") {
          $yas_isim       = sql_clear($_POST["yas_isim"] ?? '');
            if (!$yas_isim) {
              echo 'Branş Yaşını boş bırakmayınız.' . GERI_DON;
            } else {
                $sql = "INSERT INTO basvuru_yas SET ";
                $sql.="yas_isim = '" . $yas_isim ."'";
                  if($wpdb->query($sql)){
                    echo 'Branş yaşı başarıyla silinmiştir.' . GERI_DON;
                  } else {
                    echo 'Branş yaşı başarıyla silinirken bir hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "brans_yas_duzenle") {
          $yas = $wpdb->get_row("select * from basvuru_yas where yas_id = ".$id);
      ?>
              <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=brans_yas_guncelle&id=<?=$id?>">
                <fieldset>
                <div class="control-group">
                  <label class="control-label" for="yas_isim">Branş Yaşı</label>
                  <div class="controls">
                    <input id="yas_isim" name="yas_isim" type="text" placeholder="" class="form-control" required value="<?=e($yas->yas_isim ?? '')?>">            
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="guncelle"></label>
                  <div class="controls">
                    <button id="guncelle" name="guncelle" class="btn btn-primary">Güncelle</button>
                  </div>
                </div>

                </fieldset>
              </form>
      <?php
        } elseif ($islem == "brans_yas_guncelle") {
          $yas_isim       = sql_clear($_POST["yas_isim"] ?? '');

            if (!$yas_isim) {
              echo 'Branş Yaşını boş bırakmayınız.' . GERI_DON;
            } else {
                $sql = "UPDATE basvuru_yas SET ";
                $sql.="yas_isim = '" . $yas_isim ."'";
                $sql.=" where yas_id=".$id;

                  if($wpdb->query($sql)){
                    echo 'Branş yaşı başarıyla güncellenmiştir.' . GERI_DON;
                  } else {
                    echo 'Branş yaşı güncellenirken bir hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "brans_listele") {
      ?>
        <div class="control-group">
          <label class="control-label" for="tesis_id">Tesis Seçiniz</label>
          <div class="controls">
            <select class="form-control" onChange="sayfayagit(this)" name="tesis_id" id="tesis_id" autofocus>
                <option value="./?bolum=tesisler&islem=brans_listele">..:: Seçiniz ::..</option>
              <?php
              $tesisler = $wpdb->get_results("select * from basvuru_tesisler order by tesis_isim asc");
                foreach ($tesisler as $tesis) {
                  $toplam_brans = $wpdb->get_var("SELECT COUNT(brans_id) FROM basvuru_branslar where tesis_id=".$tesis->tesis_id);
              ?>
                <option value="/?bolum=tesisler&islem=brans_listele&id=<?=e($tesis->tesis_id ?? '')?>"<?php if ($tesis->tesis_id == $id) { echo ' selected';}?>><?=e($tesis->tesis_isim ?? '')?> (<?=$toplam_brans?>)</option>
              <?php
                }
              ?>
            </select>            
          </div>
        </div>
        <br>
      <?php
            if ($id) {
      ?>
              <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=brans_ekle&id=<?=$id?>">
                <fieldset>
                <div class="control-group">
                  <label class="control-label" for="brans_isim">Branş İsim</label>
                  <div class="controls">
                    <input id="brans_isim" name="brans_isim" type="text" placeholder="" class="form-control" required>            
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="yas_id">Branş Yaş</label>
                  <div class="controls">
                    <select class="form-control" name="yas_id" required>
                      <option value="">..:: Seçiniz ::..</option>
                      <?php
                      $yaslar = $wpdb->get_results("select * from basvuru_yas order by yas_isim asc");
                        foreach ($yaslar as $yas) {
                      ?>
                        <option value="<?=e($yas->yas_id ?? '')?>"><?=e($yas->yas_isim ?? '')?></option>
                      <?php
                        }
                      ?>
                    </select>              
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="brans_ucret">Branş Ücret</label>
                  <div class="controls">
                    <input name="brans_ucret" id="brans_ucret" type="text" class="form-control" required value="0.00">           
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="brans_kontenjan">Branş Kontenjan</label>
                  <div class="controls">
                    <input name="brans_kontenjan" id="brans_kontenjan" type="text" class="form-control bfh-number" required>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="ekle"></label>
                  <div class="controls">
                    <button id="ekle" name="ekle" class="btn btn-primary">Ekle</button>
                  </div>
                </div>

                </fieldset>
              </form>
              <br>
              <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Branş İsim</th>
                  <th>Branş Yaş</th>
                  <th>Branş Ücret</th>
                  <th>Branş Kontenjan</th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $listele = $wpdb->get_results("select * from basvuru_branslar where tesis_id=".$id." order by brans_id ASC");

              if ($listele) {
                $listesira = 1;
                foreach ($listele as $liste) {
                  $yas = $wpdb->get_row("select * from basvuru_yas where yas_id = ".(int) $liste->yas_id);
                ?>
                  <tr>
                    <td><?=$listesira?></td>
                    <td><a href="./?bolum=tesisler&islem=brans_duzenle&id=<?=e($liste->brans_id ?? '')?>"><?=e($liste->brans_isim ?? '')?></a></td>
                    <td><?=e($yas->yas_isim ?? '')?></td>
                    <td><?php if ($liste->brans_ucret == "0.00"){echo'Ücretsiz';}else{echo $liste->brans_ucret.'TL';} ?></td>
                    <td><?=e($liste->brans_kontenjan ?? '')?></td>
                    <td><a href="./?bolum=tesisler&islem=brans_sil&id=<?=e($liste->brans_id ?? '')?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i> Sil</a></td>
                  </tr>
                <?php
                $listesira++;
                }
              } else {
                echo '<tr>
                        <td colspan="5">Kayıtlı branş bulunamadı</td>
                      </tr>';
              }
              ?>
              </tbody>
            </table>
      <?php
          }
        } elseif ($islem == "brans_sil" && $_SESSION['uye_admin'] == "admin") {
          $brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = " . $id);
          if (!$brans) {
            echo 'Kayıtlı böyle bir branş bulunamadı.' . GERI_DON;
          } else {
            $basvuru = $wpdb->get_row("select * from basvuru_basvuru where brans_id = " . $brans->brans_id);
              if ($basvuru) {
                echo 'Branşı silmek için önce branşa ait başvuruları silmelisiniz.' . GERI_DON;
              } else {
                $wpdb->query("DELETE FROM basvuru_branslar where brans_id = " . $id);
                echo 'Branş başarıyla silinmiştir.' . GERI_DON;
              }
          }
        } elseif ($islem == "brans_ekle") {
          $brans_isim       = sql_clear($_POST["brans_isim"] ?? '');
          $yas_id           = (int) sql_clear($_POST["yas_id"] ?? '');
          $brans_ucret      = (float) str_replace(',', '', sql_clear($_POST["brans_ucret"] ?? ''));
          $brans_kontenjan  = (int) sql_clear($_POST["brans_kontenjan"] ?? '');

            if (!$brans_isim || !$yas_id || !$brans_kontenjan) {
              echo 'Branş İsim, yaş ve kontenjanlarını boş bırakmayınız.' . GERI_DON;
            } elseif ($brans_kontenjan<1) {
              echo 'Branş Kontenjanı yazınız.' . GERI_DON;
            } else {
                $sql = "INSERT INTO basvuru_branslar SET ";
                $sql.="brans_isim = '" . $brans_isim ."', ";
                $sql.="yas_id = " . $yas_id .", ";
                $sql.="brans_ucret = " . $brans_ucret .", ";
                $sql.="brans_kontenjan = " . $brans_kontenjan .", ";
                $sql.="tesis_id = " . $id ."";
                  if($wpdb->query($sql)){
                    echo 'Yeni branş başarıyla eklenmiştir.' . GERI_DON;
                  } else {
                    echo 'Yeni branş eklenirken bir hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "brans_duzenle") {
          $brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = ".$id);
        ?>
              <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=brans_guncelle&id=<?=$id?>">
                <fieldset>
                <div class="control-group">
                  <label class="control-label" for="brans_isim">Branş İsim</label>
                  <div class="controls">
                    <input id="brans_isim" name="brans_isim" type="text" placeholder="" class="form-control" required value="<?=e($brans->brans_isim ?? '')?>">            
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="yas_id">Branş Yaş</label>
                  <div class="controls">
                    <select class="form-control" name="yas_id" required>
                      <option value="">..:: Seçiniz ::..</option>
                      <?php
                      $yaslar = $wpdb->get_results("select * from basvuru_yas order by yas_isim asc");
                        foreach ($yaslar as $yas) {
                      ?>
                        <option value="<?=e($yas->yas_id ?? '')?>"<?php if ($brans->yas_id == $yas->yas_id) {echo ' selected';}?>><?=e($yas->yas_isim ?? '')?></option>
                      <?php
                        }
                      ?>
                    </select>              
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="brans_ucret">Branş Ücret</label>
                  <div class="controls">
                    <input name="brans_ucret" id="brans_ucret" type="text" class="form-control" required value="<?=e($brans->brans_ucret ?? '')?>">           
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="brans_kontenjan">Branş Kontenjan</label>
                  <div class="controls">
                    <input name="brans_kontenjan" id="brans_kontenjan" type="text" class="form-control bfh-number" required value="<?=e($brans->brans_kontenjan ?? '')?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="guncelle"></label>
                  <div class="controls">
                    <button id="guncelle" name="guncelle" class="btn btn-primary">Güncelle</button>
                  </div>
                </div>

                </fieldset>
              </form>
        <?php
        } elseif ($islem == "brans_guncelle") {
          $brans_isim       = sql_clear($_POST["brans_isim"] ?? '');
          $yas_id           = (int) sql_clear($_POST["yas_id"] ?? '');
          $brans_ucret      = (float) str_replace(',', '', sql_clear($_POST["brans_ucret"] ?? ''));
          $brans_kontenjan  = (int) sql_clear($_POST["brans_kontenjan"] ?? '');

            if (!$brans_isim || !$yas_id || !$brans_kontenjan) {
              echo 'Branş İsim, yaş ve kontenjanlarını boş bırakmayınız.' . GERI_DON;
            } elseif ($brans_kontenjan<1) {
              echo 'Branş Kontenjanı yazınız.' . GERI_DON;
            } else {
                $sql = "UPDATE basvuru_branslar SET ";
                $sql.="brans_isim = '" . $brans_isim ."', ";
                $sql.="yas_id = " . $yas_id .", ";
                $sql.="brans_ucret = " . $brans_ucret .", ";
                $sql.="brans_kontenjan = " . $brans_kontenjan ." ";
                $sql.="where brans_id = " . $id;
                  if($wpdb->query($sql)){
                    echo 'Branş bilgileri başarıyla güncellenmiştir.' . GERI_DON;
                  } else {
                    echo 'Branş bilgileri güncellenirken bir hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "tesis_guncelle") {
          $tesis_isim  = sql_clear($_POST["tesis_isim"] ?? '');
            if (!$tesis_isim) {
              echo 'Tesis İsmini boş bırakmayınız.' . GERI_DON;
            } else {
                $sql = "UPDATE basvuru_tesisler SET ";
                $sql.="tesis_isim = '" . $tesis_isim ."' ";
                $sql.= "where tesis_id = " . $id;
                  if($wpdb->query($sql)){
                    echo 'Tesis bilgileri başarıyla güncellenmiştir.' . GERI_DON;
                  } else {
                    echo 'Tesis bilgileri güncellenirken hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "tesis_ekle") {
          $tesis_isim  = sql_clear($_POST["tesis_isim"] ?? '');
            if (!$tesis_isim) {
              echo 'Tesis İsmini boş bırakmayınız.' . GERI_DON;
            } else {
                $sql = "INSERT INTO basvuru_tesisler SET ";
                $sql.="tesis_isim = '" . $tesis_isim ."'";
                  if($wpdb->query($sql)){
                    echo 'Yeni tesis başarıyla eklenmiştir.' . GERI_DON;
                  } else {
                    echo 'Yeni tesis eklenirken hata oluştu.' . GERI_DON;
                  }
            }
        } elseif ($islem == "tesis_listele") {
      ?>
      <form class="form-horizontal" method="post" action="./?bolum=tesisler&islem=tesis_ekle">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="tesis_isim">Tesis İsim</label>
          <div class="controls">
            <input id="tesis_isim" name="tesis_isim" type="text" placeholder="" class="form-control" required value="<?=e($kontrol->tesis_isim ?? '')?>" autofocus>            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="ekle"></label>
          <div class="controls">
            <button id="ekle" name="ekle" class="btn btn-primary">Ekle</button>
          </div>
        </div>

        </fieldset>
      </form>
      <br /><br />
      <table class="table table-hover">
      <thead>
        <tr>
          <th width="1%">#</th>
          <th width="23%">Tesis İsim</th>
          <th width="46%">Branşlar</th>
          <th width="30%">Yetkili</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $listele = $wpdb->get_results("select * from basvuru_tesisler order by tesis_isim asc");

      if ($listele) {
        $listesira = 1;
        foreach ($listele as $liste) {
          $yetkililer = yetki_listesi($liste->tesis_yetkili);
        ?>
          <tr>
            <td><?=$listesira?></td>
            <td><a href="./?bolum=tesisler&islem=tesis_duzenle&id=<?=e($liste->tesis_id ?? '')?>"><?=e($liste->tesis_isim ?? '')?></a>
            <?php
            if ($_SESSION['uye_admin'] == "admin") {
            ?>
              [<a href="/?bolum=tesisler&islem=toplu_basvuru_sil&id=<?=e($liste->tesis_id ?? '')?>" onClick="return confirm(<?=e(json_encode($liste->tesis_isim . "\nTesise Ait Tüm Kayıtları Silmek İstediğinizden Eminmisiniz?", JSON_UNESCAPED_UNICODE))?>)">SİL</a>]
            <?php
            }?></td>
            <td>
            <?php
            $branslar = $wpdb->get_results("select * from basvuru_branslar where tesis_id=".$liste->tesis_id." order by brans_isim asc");
            if ($branslar) {
              foreach ($branslar as $brans) {
                echo '<a href="./?bolum=tesisler&islem=brans_duzenle&id='.e($brans->brans_id).'">'.e($brans->brans_isim).'</a><br>';
              }
            } else {
              echo "-";
            }
            ?>
            </td>
            <td>
            <?php
            if (count($yetkililer)<1) {
              echo "-";
            } else {
              foreach ($yetkililer as $yetkili) {
                $yetki = $wpdb->get_row("select * from basvuru_uyeler where uye_id = ".(int) $yetkili);
                if ($yetki) {
                echo '<a href="./?bolum=uyeler&islem=duzenle&id='.e($yetki->uye_id).'">'.e($yetki->uye_adsoyad).'</a><br>';
                }
              }
            }
            ?>
            </td>
          </tr>
        <?php
        $listesira++;
        }
      } else {
          echo '<tr>
                  <td colspan="4">Kayıtlı tesis bulunamadı</td>
                </tr>';
      }
      ?>
      </tbody>
    </table>
      <?php
        } elseif ($islem == 'toplu_basvuru_sil') {
            $sql = "DELETE FROM basvuru_basvuru ";
            $sql.="where tesis_id = " . $id;
              if($_SESSION['uye_admin'] == "admin" && $wpdb->query($sql)){
                echo 'Başvurular başarıyla silinmiştir.' . GERI_DON;
              } else {
                echo 'Başvurular silinirken bir hata oluştu.' . GERI_DON;
              }
        }
      } elseif ($bolum == "tumunu_excele_aktar" && $_SESSION['giris'] && $_SESSION['uye_admin'] == "admin") {
        $basvurular = $wpdb->get_results("select B.basvuru_tckimlik, 
          B.basvuru_dtarih, 
          B.basvuru_adsoyad_sporcu, 
          B.basvuru_telefon_cep, 
          B.basvuru_odeme_turu,
          B.basvuru_odenen_tutar, 
          B.basvuru_aciklama, 
          B.basvuru_zamani, 
          U.uye_adsoyad, 
          T.tesis_isim,
          BR.brans_isim from basvuru_basvuru B INNER JOIN basvuru_tesisler T on B.tesis_id = T.tesis_id INNER JOIN basvuru_uyeler U on U.uye_id = B.uye_id INNER JOIN basvuru_branslar BR on BR.brans_id = B.brans_id");
                  $Excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

                  // Oluşturacağımız Excel Dosyasının ayarlarını yapıyoruz.
                  // Bu bilgiler O kadar önenli değil kafanıza göre doldurabilirsiniz.
                  $Excel->getProperties()->setCreator("Bursabbspor")
                  ->setLastModifiedBy("F.Y Web Günlüğü")
                  ->setTitle("Yaz Okulları Başvuruları")
                  ->setSubject("Yaz Okulları Başvuruları")
                  ->setDescription("Yaz Okulları Başvuruları")
                  ->setKeywords("bursa, büyükşehir, belediye, spor")
                  ->setCategory("Yaz Okulları");
                   
                  //Excel Dosyasının Sayfasını Adını Belirliyoruz.Ben vArsayılan olarak gelen Sayfa1 ' i tercih ettim.
                  $Excel->getActiveSheet()->setTitle('Belediyespor-Tum-Kayitlar');
                   
                  //Belirtilen Alanlara Veri Girişi Yapıyoruz.
                  $Excel->getActiveSheet()->setCellValue('A1', 'TC Kimlik');
                  $Excel->getActiveSheet()->setCellValue('B1', 'Doğum Tarihi');
                  $Excel->getActiveSheet()->setCellValue('C1', 'Sporcunun Adı Soyadı');
                  $Excel->getActiveSheet()->setCellValue('D1', 'Cep Telefonu');
                  $Excel->getActiveSheet()->setCellValue('E1', 'Ödeme Türü');
                  $Excel->getActiveSheet()->setCellValue('F1', 'Ödenen Tutar');
                  $Excel->getActiveSheet()->setCellValue('G1', 'Vergisiz Tutar');
                  $Excel->getActiveSheet()->setCellValue('H1', 'Açıklama');
                  $Excel->getActiveSheet()->setCellValue('I1', 'Başvuru Zamanı');
                  $Excel->getActiveSheet()->setCellValue('J1', 'Kaydı Alan Kişi');
                  $Excel->getActiveSheet()->setCellValue('K1', 'Branş');
                  $Excel->getActiveSheet()->setCellValue('L1', 'Tesis');

                  $satir = 2;

                    foreach ( $basvurular as $basvuru ) {                   
                      $Excel->getActiveSheet()->setCellValueExplicit('A'.$satir, $basvuru->basvuru_tckimlik, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                      $Excel->getActiveSheet()->setCellValue('B'.$satir, $basvuru->basvuru_dtarih);
                      $Excel->getActiveSheet()->setCellValue('C'.$satir, $basvuru->basvuru_adsoyad_sporcu);
                      $Excel->getActiveSheet()->setCellValue('D'.$satir, $basvuru->basvuru_telefon_cep);
                      $Excel->getActiveSheet()->setCellValue('E'.$satir, $basvuru->basvuru_odeme_turu);
                      $Excel->getActiveSheet()->setCellValue('F'.$satir, $basvuru->basvuru_odenen_tutar);
                      $Excel->getActiveSheet()->setCellValue('G'.$satir, ($basvuru->basvuru_odenen_tutar - ($basvuru->basvuru_odenen_tutar * 10) / 100));
                      $Excel->getActiveSheet()->setCellValue('H'.$satir, $basvuru->basvuru_aciklama);
                      $Excel->getActiveSheet()->setCellValue('I'.$satir, $basvuru->basvuru_zamani);
                      $Excel->getActiveSheet()->setCellValue('J'.$satir, $basvuru->uye_adsoyad);
                      $Excel->getActiveSheet()->setCellValue('K'.$satir, $basvuru->brans_isim);
                      $Excel->getActiveSheet()->setCellValue('L'.$satir, $basvuru->tesis_isim);
                      $satir++;
                    }
                  //Kaydet
                  $Kaydet = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($Excel);
                  excel_indir($Kaydet, "belediyespor-tum-kayitlar.xlsx");
      } elseif ($bolum == "basvurular" && $_SESSION['giris']) {
        if ($islem == "excel_aktar") {
          $brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = " . $id);
          if (!$brans) {
            echo 'Kayıtlı böyle bir branş bulunamadı.' . GERI_DON;
          } elseif (!in_array($brans->tesis_id, $_SESSION['uye_yetki'])) {
            echo 'Bu branşı excel\'e aktarmak için yetkili değilsiniz.'. GERI_DON;
          } else {
            $basvurular = $wpdb->get_results("select B.*, U.uye_adsoyad, T.tesis_isim from basvuru_basvuru B LEFT JOIN basvuru_tesisler T on B.tesis_id = T.tesis_id LEFT JOIN basvuru_uyeler U on U.uye_id = B.uye_id where B.brans_id = ".$id." order by B.basvuru_id asc");
              if (!$basvurular) {
                echo 'Branş\'a ait kayıt bulunamadığı için Excel\'e aktarılamıyor.' . GERI_DON;
              } else {
                  $Excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

                  // Oluşturacağımız Excel Dosyasının ayarlarını yapıyoruz.
                  // Bu bilgiler O kadar önenli değil kafanıza göre doldurabilirsiniz.
                  $Excel->getProperties()->setCreator("Bursabbspor")
                  ->setLastModifiedBy("F.Y Web Günlüğü")
                  ->setTitle("Yaz Okulları Başvuruları")
                  ->setSubject("Yaz Okulları Başvuruları")
                  ->setDescription("Yaz Okulları Başvuruları")
                  ->setKeywords("bursa, büyükşehir, belediye, spor")
                  ->setCategory("Yaz Okulları");
                   
                  //Excel Dosyasının Sayfasını Adını Belirliyoruz.Ben vArsayılan olarak gelen Sayfa1 ' i tercih ettim.
                  $Excel->getActiveSheet()->setTitle(substr(sef_link($brans->brans_isim),0,31));
                   
                  //Belirtilen Alanlara Veri Girişi Yapıyoruz.
                  $Excel->getActiveSheet()->setCellValue('A1', 'TC Kimlik');
                  $Excel->getActiveSheet()->setCellValue('B1', 'Doğum Tarihi');
                  $Excel->getActiveSheet()->setCellValue('C1', 'Sporcunun Adı Soyadı');
                  $Excel->getActiveSheet()->setCellValue('D1', 'Cep Telefonu');
                  $Excel->getActiveSheet()->setCellValue('E1', 'Ödeme Türü');
                  $Excel->getActiveSheet()->setCellValue('F1', 'Ödenen Tutar');
                  $Excel->getActiveSheet()->setCellValue('G1', 'Vergisiz Tutar');
                  $Excel->getActiveSheet()->setCellValue('H1', 'Açıklama');
                  $Excel->getActiveSheet()->setCellValue('I1', 'Başvuru Zamanı');
                  $Excel->getActiveSheet()->setCellValue('J1', 'Kaydı Alan Kişi');
                  $Excel->getActiveSheet()->setCellValue('K1', 'Tesis');

                  $satir = 2;

                    foreach ( $basvurular as $basvuru ) {
                      $Excel->getActiveSheet()->setCellValueExplicit('A'.$satir, $basvuru->basvuru_tckimlik, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                      $Excel->getActiveSheet()->setCellValue('B'.$satir, $basvuru->basvuru_dtarih);
                      $Excel->getActiveSheet()->setCellValue('C'.$satir, $basvuru->basvuru_adsoyad_sporcu);
                      $Excel->getActiveSheet()->setCellValue('D'.$satir, $basvuru->basvuru_telefon_cep);
                      $Excel->getActiveSheet()->setCellValue('E'.$satir, $basvuru->basvuru_odeme_turu);
                      $Excel->getActiveSheet()->setCellValue('F'.$satir, $basvuru->basvuru_odenen_tutar);
                      $Excel->getActiveSheet()->setCellValue('G'.$satir, ($basvuru->basvuru_odenen_tutar - ($basvuru->basvuru_odenen_tutar * 10) / 100));
                      $Excel->getActiveSheet()->setCellValue('H'.$satir, $basvuru->basvuru_aciklama);
                      $Excel->getActiveSheet()->setCellValue('I'.$satir, $basvuru->basvuru_zamani);
                      $Excel->getActiveSheet()->setCellValue('J'.$satir, $basvuru->uye_adsoyad);
                      $Excel->getActiveSheet()->setCellValue('K'.$satir, $basvuru->tesis_isim);
                      $satir++;
                    }

                  //Kaydet
                  $Kaydet = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($Excel);
                  excel_indir($Kaydet, sef_link($brans->brans_isim).".xlsx");
              }
          }

        } elseif ($islem == "toplu_basvuru_sil") {

            $sql = "DELETE FROM basvuru_basvuru ";
            $sql.="where brans_id = " . $id;
              if($_SESSION['uye_admin'] == "admin" && $wpdb->query($sql)){
                echo 'Başvurular başarıyla silinmiştir.' . GERI_DON;
              } else {
                echo 'Başvurular silinirken bir hata oluştu.' . GERI_DON;
              }

        } elseif ($islem == "listele") {
      ?>
        <div class="control-group">
          <label class="control-label" for="brans_tesis">Branş Seçiniz</label>
          <div class="controls">
            <select name="brans_tesis" data-style="btn-info" id="brans_tesis" required autofocus onChange="sayfayagit(this)" data-live-search="true"data-selected-text-format="count" class="selectpicker form-control">
                <option value="">..:: Seçiniz ::..</option>
              <?php
              foreach ($_SESSION['uye_yetki'] as $tesis_id) {
                $tesis = $wpdb->get_row("select tesis_isim from basvuru_tesisler where tesis_id = ". (int) $tesis_id);
                $branslar = $wpdb->get_results("select * from basvuru_branslar where tesis_id = ".(int) $tesis_id." order by brans_isim asc");
              ?>
                <optgroup label="<?=e($tesis->tesis_isim ?? '')?>">
                  <?php
                  foreach ($branslar as $brans) {
                    echo '<option value="./?bolum=basvurular&islem=listele&id='.e($brans->brans_id).'"';
                    if ($id == $brans->brans_id) { echo ' selected';}
                    echo '>'.e($brans->brans_isim).'</option>';
                  }
                  ?>                
                </optgroup>
              <?php
              }
              ?>
            </select>            
          </div>
        </div>
      <?php
      if ($id && ($brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = ". $id)) && in_array($brans->tesis_id, $_SESSION['uye_yetki'])) {
        ?>
      <div class="bs-docs-section">
        <div class="bs-callout bs-callout-info">
          <?php
          $brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = ". $id);
          $toplam_basvuru = $wpdb->get_var("SELECT COUNT(basvuru_id) FROM basvuru_basvuru where brans_id = " . $id);
          $yas = $wpdb->get_row("select * from basvuru_yas where yas_id = ". (int) ($brans->yas_id ?? 0));
          ?>
          <h4>Branş Bilgileri <?php if ($_SESSION['uye_admin'] == 'admin') {?><a href="/?bolum=basvurular&islem=toplu_basvuru_sil&id=<?=$id?>" onClick="return confirm(<?=e(json_encode($brans->brans_isim . "\nBranşına ait tüm başvuruları silmek istediğinize eminmisiniz?", JSON_UNESCAPED_UNICODE))?>)" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i> SİL</a><?php }?> <a href="./?bolum=basvurular&islem=excel_aktar&id=<?=$id?>" class="btn btn-sm btn-success"><i class="glyphicon glyphicon-zoom-in"></i> Excel'e Aktar</a></h4>
          <p>Kontenjan Sayısı : <span class="badge"><?=e($brans->brans_kontenjan ?? '')?></span><br>
          Toplam Başvuru : 
          <?php if ($toplam_basvuru>=$brans->brans_kontenjan) {
          echo '<span class="badge alert-danger">'.$toplam_basvuru.'</span>';
          } else {
          echo '<span class="badge alert-info">'.$toplam_basvuru.'</span>';
          }?><br>
          Branş Ücreti : <?=e($brans->brans_ucret ?? '')?><br>
          Branş Yaş : <?=e($yas->yas_isim ?? '')?>
          </p>
        </div>
      </div>
      <table id="basvurular" class="display" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th width="6%">TC Kimlik</th>
            <th width="93%">Ad Soyad</th>
            <th width="1%">#</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th>TC Kimlik</th>
            <th>Ad Soyad</th>
            <th>#</th>
          </tr>
        </tfoot>
      </table>
      <?php
          }
        } elseif ($islem == "basvuru_sil") {
            $sql = "DELETE FROM basvuru_basvuru ";
            $sql.="where basvuru_id = " . $id;
              if($_SESSION['uye_admin'] != "admin" || !$wpdb->query($sql)){
                echo 'Başvuru silinirken bir hata oluştu.' . GERI_DON;
              } else {
                echo 'Başvuru başarıyla silinmiştir.' . GERI_DON;
              }
        } elseif ($islem == "duzenle") {
          $kontrol = $wpdb->get_row("select * from basvuru_basvuru where basvuru_id = ". $id);
          if (!$kontrol || !in_array($kontrol->tesis_id, $_SESSION['uye_yetki'])) {
            echo 'Bu başvuruyu görüntülemek için yetkili değilsiniz.' . GERI_DON;
          } else {
      ?>
      <div class="bs-docs-section">
        <div class="bs-callout bs-callout-info">
          <?php
          $tesis  = $wpdb->get_row("select * from basvuru_tesisler where tesis_id = ". (int) $kontrol->tesis_id);
          $brans  = $wpdb->get_row("select * from basvuru_branslar where brans_id = ". (int) $kontrol->brans_id);
          $uye    = $wpdb->get_row("select * from basvuru_uyeler where uye_id = ". (int) $kontrol->uye_id);
          ?>
          <h4>Başvuru Bilgileri</h4>
          <p>Başvuru Zamanı : <?=mysql_to_extra($kontrol->basvuru_zamani)?><br>
          Kaydı Alan Kişi : <?=e($uye->uye_adsoyad ?? '')?><br>
          Tesis : <?=e($tesis->tesis_isim ?? '')?><br>
          Branş : <?=e($brans->brans_isim ?? '')?><br>
          </p>
        </div>
      </div>

      <form class="form-horizontal" method="post" action="./?bolum=basvurular&islem=guncelle&id=<?=$id?>">
        <input type="hidden" name="tesis_id" value="<?=e($kontrol->tesis_id ?? '')?>"/>
        <input type="hidden" name="brans_id" value="<?=e($kontrol->brans_id ?? '')?>"/>
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="tckimlik">TC Kimlik</label>
          <div class="controls">
            <input name="tckimlik" id="tckimlik" type="text" class="form-control bfh-phone" data-format="ddddddddddd" required value="<?=e($kontrol->basvuru_tckimlik ?? '')?>">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="adsoyad_sporcu">Sporcunun Adı Soyad</label>
          <div class="controls">
            <input id="adsoyad_sporcu" name="adsoyad_sporcu" type="text" placeholder="" class="form-control" required value="<?=e($kontrol->basvuru_adsoyad_sporcu ?? '')?>">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="sifre1">Doğum Tarihi (Gün-Ay-Yıl)</label>
            <div class="controls">
              <input name="dtarih" id="dtarih" type="text" class="form-control bfh-phone" data-format="dd-dd-dddd" required value="<?=date("d-m-Y", strtotime((string) $kontrol->basvuru_dtarih))?>">
            </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="telefon_cep">Cep Telefonu</label>
          <div class="controls">
            <input name="telefon_cep" id="telefon_cep" type="text" class="form-control bfh-phone" data-format="ddd-ddd-dd-dd" required value="<?=e($kontrol->basvuru_telefon_cep ?? '')?>">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="odenen_tutar">Ödeme Türü</label>
          <div class="controls">
            <select class="form-control" name="odeme_turu" required>
                <option value="nakit"<?php if ($kontrol->basvuru_odeme_turu == "nakit") { echo " selected"; }?>>Nakit</option>
                <option value="kredi-karti"<?php if ($kontrol->basvuru_odeme_turu == "kredi-karti") { echo " selected"; }?>>Kredi Kartı</option>
            </select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="odenen_tutar">Ödenen Tutar</label>
          <div class="controls">
            <input name="odenen_tutar" id="odenen_tutar" type="text" class="form-control bfh-phone" data-format="ddddd" required="" value="<?=e($kontrol->basvuru_odenen_tutar ?? '')?>" placeholder="0.00">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="aciklama">Açıklama</label>
          <div class="controls">
            <textarea id="aciklama" name="aciklama" class="form-control"><?=e($kontrol->basvuru_aciklama ?? '')?></textarea>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="ekle"></label>
          <div class="controls">
            <button id="ekle" name="ekle" class="btn btn-primary">Güncelle</button>
          </div>
        </div>
        </fieldset>
      </form>
      <?php
          }
        } elseif ($islem == "guncelle") {
          $tckimlik       = sql_clear($_POST["tckimlik"] ?? '');
          $adsoyad_sporcu = sql_clear($_POST["adsoyad_sporcu"] ?? '');
          $dtarih         = sql_clear($_POST["dtarih"] ?? '');
          $telefon_cep    = sql_clear($_POST["telefon_cep"] ?? '');
          $odenen_tutar   = (float) sql_clear($_POST["odenen_tutar"] ?? '');
          $aciklama       = sql_clear($_POST["aciklama"] ?? '');

          $mevcut         = $wpdb->get_row("select tesis_id, brans_id from basvuru_basvuru where basvuru_id = ". $id);
          $tesis_id       = (int) ($mevcut->tesis_id ?? 0);
          $brans_id       = (int) ($mevcut->brans_id ?? 0);

            if (!$mevcut || !in_array($tesis_id, $_SESSION['uye_yetki'])) {
              echo 'Bu Tesise kayıt almak için yetkili değilsiniz.'. GERI_DON;
            } elseif (!$tckimlik || strlen($tckimlik)<11 || !is_numeric($tckimlik)) {
              echo 'Geçerli bir TC Kimlik numarası yazınız.'. GERI_DON;
            } elseif (!$adsoyad_sporcu) {
              echo 'Sporcunun Adını ve Soyadını yazınız.' . GERI_DON;
            } elseif (tarih_kontrol($dtarih) == false ) {
              echo 'Geçerli bir doğum tarihi yazınız.' . GERI_DON;
            } elseif (!$telefon_cep) {
              echo 'Cep telefonunu yazınız.' . GERI_DON;
            } elseif (!$odenen_tutar) {
              echo 'Ödenen tutarı yazınız.' . GERI_DON;
            } else {
              $kontrol = $wpdb->get_row("select * from basvuru_basvuru where basvuru_tckimlik = '".$tckimlik."' and brans_id=".$brans_id." and tesis_id=".$tesis_id);
                if ($kontrol && $id != $kontrol->basvuru_id) {
                  echo 'Bu TC Kimlik no ile bu branşa daha önce kayıt yapılmış.' . GERI_DON;
                } else {
                  $sql = "UPDATE basvuru_basvuru SET ";
                  $sql.="basvuru_tckimlik = '" . $tckimlik . "', ";
                  $sql.="basvuru_dtarih = '" . mysql_datetime($dtarih) ."', ";
                  $sql.="basvuru_adsoyad_sporcu = '" . $adsoyad_sporcu ."', ";
                  $sql.="basvuru_telefon_cep = '" . $telefon_cep ."', ";
                  $sql.="basvuru_odenen_tutar = " . $odenen_tutar .", ";
                  $sql.="basvuru_aciklama = '" . $aciklama ."' ";
                  $sql.= "where basvuru_id = " . $id;

                  if($wpdb->query($sql)){
                    echo 'Başvuru kaydı başarıyla güncellenmiştir.' . GERI_DON_BOS;
                  } else {
                    echo 'Başvuru kaydı güncellenirken bir hata oluştu.' . GERI_DON_BOS;
                  }
                }
            }
        } elseif ($islem == "ekle") {
      ?>
      <form class="form-horizontal" method="post" action="./?bolum=basvurular&islem=ekle_post">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="brans_tesis">Branş Seçiniz</label>
          <div class="controls">
            <select class="form-control" name="brans_tesis" id="brans_tesis" required autofocus onChange="sayfayagit(this)">
                <option value="">..:: Seçiniz ::..</option>
                <?php
                foreach ($_SESSION['uye_yetki'] as $tesis_id) {
                  $tesis = $wpdb->get_row("select tesis_isim from basvuru_tesisler where tesis_id = ". (int) $tesis_id);
                  $branslar = $wpdb->get_results("select * from basvuru_branslar where tesis_id = ".(int) $tesis_id." order by brans_isim asc");
                ?>
                <optgroup label="<?=e($tesis->tesis_isim ?? '')?>">
                  <?php
                  foreach ($branslar as $brans) {
                    $toplam_basvuru = $wpdb->get_var("SELECT COUNT(basvuru_id) FROM basvuru_basvuru where brans_id = " . $brans->brans_id);
                    echo '<option value="./?bolum=basvurular&islem=ekle&id='.e($brans->brans_id).'"';
                    if ($brans->brans_id == $id) {
                      echo ' selected';
                    }
                    echo '>'.e($brans->brans_isim).'</option>';
                  }
                  ?>                
                </optgroup>
              <?php
              }
              ?>
            </select>            
          </div>
        </div>

      <?php
      if ($id && ($brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = ". $id)) && in_array($brans->tesis_id, $_SESSION['uye_yetki'])) {
      ?>
      <div class="bs-docs-section">
        <div class="bs-callout bs-callout-info">
          <?php
          $brans = $wpdb->get_row("select * from basvuru_branslar where brans_id = ". $id);
          $toplam_basvuru = $wpdb->get_var("SELECT COUNT(basvuru_id) FROM basvuru_basvuru where brans_id = " . $id);
          $yas = $wpdb->get_row("select * from basvuru_yas where yas_id = ". (int) ($brans->yas_id ?? 0));
          ?>
          <h4>Branş Bilgileri</h4>
          <p>Kontenjan Sayısı : <span class="badge"><?=e($brans->brans_kontenjan ?? '')?></span><br>
          Toplam Başvuru : 
          <?php if ($toplam_basvuru>=$brans->brans_kontenjan) {
          echo '<span class="badge alert-danger">'.$toplam_basvuru.'</span>';
          } else {
          echo '<span class="badge alert-info">'.$toplam_basvuru.'</span>';
          }?><br>
          Branş Ücreti : <?=e($brans->brans_ucret ?? '')?><br>
          Branş Yaş : <?=e($yas->yas_isim ?? '')?>
          </p>
        </div>
      </div>

      <?php
      if ($toplam_basvuru >= $brans->brans_kontenjan) {
      ?>
      <div class="bs-docs-section">
        <div class="bs-callout bs-callout-danger">
          <h4>DİKKAT</h4>
          <p>Bu branşa ait toplam başvuru sayısına ulaşıldığı için yeni kayıt alınamamaktadır.</p>
        </div>
      </div>
      <?php } else { ?>
          <input type="hidden" name="tesis_id" value="<?=e($brans->tesis_id ?? '')?>"/>
          <input type="hidden" name="brans_id" value="<?=e($brans->brans_id ?? '')?>"/>

          <div class="control-group">
            <label class="control-label" for="tckimlik">TC Kimlik</label>
            <div class="controls">
              <input name="tckimlik" id="tckimlik" type="text" class="form-control bfh-phone" data-format="ddddddddddd" required>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="adsoyad_sporcu">Sporcunun Adı Soyad</label>
            <div class="controls">
              <input id="adsoyad_sporcu" name="adsoyad_sporcu" type="text" placeholder="" class="form-control" required>            
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dtarih">Doğum Tarihi (Gün-Ay-Yıl)</label>
              <div class="controls">
                <input name="dtarih" id="dtarih" type="text" class="form-control bfh-phone" data-format="dd-dd-dddd" required>
              </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="telefon_cep">Cep Telefonu</label>
            <div class="controls">
              <input name="telefon_cep" id="telefon_cep" type="text" class="form-control bfh-phone" data-format="ddd-ddd-dd-dd" required>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="odeme_turu">Ödeme Türü</label>
            <div class="controls">
                <select class="form-control" name="odeme_turu" required>
                    <option selected value="">Seçiniz...</option>
                    <option value="nakit">Nakit</option>
                    <option value="kredi-karti">Kredi Kartı</option>
                </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="odenen_tutar">Ödenen Tutar</label>
            <div class="controls">
              <input name="odenen_tutar" id="odenen_tutar" type="text" class="form-control bfh-phone" data-format="ddddd" required>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="aciklama">Açıklama</label>
            <div class="controls">
              <textarea id="aciklama" name="aciklama" class="form-control"></textarea>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="ekle"></label>
            <div class="controls">
              <button id="ekle" name="ekle" class="btn btn-primary">Ekle</button>
            </div>
          </div>
      <?php
        }
      }
      ?>
        </fieldset>
      </form>
      <?php
        } elseif ($islem == "ekle_post") {
          $tckimlik       = sql_clear($_POST["tckimlik"] ?? '');
          $adsoyad_sporcu = sql_clear($_POST["adsoyad_sporcu"] ?? '');
          $dtarih         = sql_clear($_POST["dtarih"] ?? '');
          $telefon_cep    = sql_clear($_POST["telefon_cep"] ?? '');
          $odeme_turu     = sql_clear($_POST["odeme_turu"] ?? '');
          $odenen_tutar   = (float) sql_clear($_POST["odenen_tutar"] ?? '');
          $aciklama       = sql_clear($_POST["aciklama"] ?? '');

          $brans_id       = (int) sql_clear($_POST["brans_id"] ?? '');

          $brans = $wpdb->get_row("select brans_kontenjan, tesis_id from basvuru_branslar where brans_id = ". $brans_id);
          $tesis_id       = (int) ($brans->tesis_id ?? 0);
          $toplam_basvuru = $wpdb->get_var("SELECT COUNT(basvuru_id) FROM basvuru_basvuru where brans_id = " . $brans_id);

          if ($brans && $toplam_basvuru >= $brans->brans_kontenjan) {
            echo '<div class="bs-docs-section">
                    <div class="bs-callout bs-callout-danger">
                      <h4>DİKKAT</h4>
                        <p>Bu branşa ait toplam başvuru sayısına ulaşıldığı için yeni kayıt alınamamaktadır.</p>
                    </div>
                  </div>'. GERI_DON;
          } else {

            if (!in_array($tesis_id, $_SESSION['uye_yetki'])) {
              echo 'Bu Tesise kayıt almak için yetkili değilsiniz.'. GERI_DON;
            } elseif (!$tesis_id && !$brans_id) {
              echo 'Başvurunun kayıt edileceği tesis/branşı seçiniz.'. GERI_DON;
            } elseif (!$tckimlik || strlen($tckimlik)<11 || strlen($tckimlik)>11 || !is_numeric($tckimlik)) {
              echo 'Geçerli bir TC Kimlik numarası yazınız.'. GERI_DON;
            } elseif (!$adsoyad_sporcu) {
              echo 'Sporcunun Adını ve Soyadını yazınız.' . GERI_DON;
            } elseif (tarih_kontrol($dtarih) == false ) {
              echo 'Geçerli bir doğum tarihi yazınız.' . GERI_DON;
            } elseif (!$telefon_cep) {
              echo 'Cep telefonunu yazınız.' . GERI_DON;
            } elseif (!$odenen_tutar) {
              echo 'Ödenen tutarı yazınız.' . GERI_DON;
            } else {
              $kontrol = $wpdb->get_row("select * from basvuru_basvuru where basvuru_tckimlik = '".$tckimlik."' and brans_id=".$brans_id." and tesis_id=".$tesis_id);
                if ($kontrol) {
                  echo 'Bu TC Kimlik no ile bu branşa daha önce kayıt yapılmış.' . GERI_DON;
                } else {
                  $sql = "INSERT INTO basvuru_basvuru SET ";
                  $sql.="basvuru_tckimlik = '" . $tckimlik . "', ";
                  $sql.="basvuru_dtarih = '" . mysql_datetime($dtarih) ."', ";
                  $sql.="basvuru_adsoyad_sporcu = '" . $adsoyad_sporcu ."', ";
                  $sql.="basvuru_telefon_cep = '" . $telefon_cep ."', ";
                  $sql.="basvuru_odeme_turu = '" . $odeme_turu ."', ";
                  $sql.="basvuru_odenen_tutar = " . $odenen_tutar .", ";
                  $sql.="basvuru_aciklama = '" . $aciklama ."', ";
                  $sql.="basvuru_zamani = now(), ";
                  $sql.="uye_id = " . (int) $_SESSION['uye_id'] .", ";
                  $sql.="brans_id = " . $brans_id .", ";
                  $sql.="tesis_id = " . $tesis_id ."";

                  if($wpdb->query($sql)){
                    echo 'Yeni Başvuru kaydı başarıyla eklenmiştir.' . GERI_DON_BOS;
                  } else {
                    echo 'Yeni Başvuru kaydı eklenirken bir hata oluştu.' . GERI_DON_BOS;
                  }
                }
            }
          }
        } 
      } elseif ($bolum == "uye_cikis") {
        session_destroy();
        redirect("./?bolum=cikis");
      } elseif ($bolum == "cikis") {
        echo 'Tebrikler!</strong> Çıkış işleminiz başarıyla tamamlandı.';
      } elseif ($bolum == "uyeler" && $_SESSION['uye_admin'] == "admin") {
        if ($islem == "yeni_ekle") {
      ?>
      <form class="form-horizontal" method="post" action="./?bolum=uyeler&islem=ekle">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="adsoyad">Ad Soyad</label>
          <div class="controls">
            <input id="adsoyad" name="adsoyad" type="text" placeholder="" class="form-control" required autofocus>            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="email">E-Mail</label>
          <div class="controls">
            <input id="email" name="email" type="email" placeholder="" class="form-control" required>            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="sifre1">Şifre</label>
          <div class="controls">
            <input id="sifre1" name="sifre1" type="password" placeholder="" class="form-control" required>            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="sifre2">Şifre Tekrar</label>
          <div class="controls">
            <input id="sifre2" name="sifre2" type="password" placeholder="" class="form-control" required>            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="tesis">Admin Yetkisi</label>
          <div class="controls">
            <label class="checkbox" for="uye_admin">
              <input type="checkbox" name="uye_admin" id="uye_admin" value="admin">
              Administrator
            </label>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="tesis">Yetkiler</label>
          <div class="controls">
          <?php
            $tesisler = $wpdb->get_results("select * from basvuru_tesisler order by tesis_isim asc");

            foreach ($tesisler as $tesis) {
          ?>
            <label class="checkbox" for="tesis-<?=e($tesis->tesis_id ?? '')?>">
              <input type="checkbox" name="tesis[]" id="tesis-<?=e($tesis->tesis_id ?? '')?>" value="<?=e($tesis->tesis_id ?? '')?>">
              <?=e($tesis->tesis_isim ?? '')?>
            </label>
          <?php
          }
          ?>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="ekle"></label>
          <div class="controls">
            <button id="ekle" name="ekle" class="btn btn-primary">Ekle</button>
          </div>
        </div>

        </fieldset>
      </form>
      <?php
        } elseif ($islem == "ekle") {
          $adsoyad    = sql_clear($_POST["adsoyad"] ?? '');
          $email      = sql_clear($_POST["email"] ?? '');
          $sifre1     = sql_clear($_POST["sifre1"] ?? '');
          $sifre2     = sql_clear($_POST["sifre2"] ?? '');
          $uye_admin  = sql_clear($_POST["uye_admin"] ?? '');
          $tesis      = sql_clear($_POST["tesis"] ?? '');

            if (!$adsoyad) {
              echo 'Ad Soyad alanını boş bırakmayınız.'. GERI_DON;
            } elseif (!$email || valid_email($email) == false) {
              echo 'Geçerli bir E-Mail adresi yazınız.' . GERI_DON;
            } elseif (!$sifre1 || !$sifre2) {
              echo 'Şifre alanlarını boş bırakmayınız';
            } elseif (($sifre1 || $sifre2) && ($sifre1<>$sifre2)) {
              echo 'Şifreleriniz birbiriyle uyumlu olmalıdır' . GERI_DON;
            } elseif (($sifre1 || $sifre2) && check_password($sifre1) == false) {
              echo 'Hatalı Şifre kombinasyonu. (En az 1 harf ve 1 rakam olmalı)' . GERI_DON;
            } elseif (!is_array($tesis)) {
              echo 'En az 1 Tesis için yetki belirtin.' . GERI_DON;
            } else {
              $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_email = '".$email."'");
                if ($kontrol) {
                  echo 'Bu E-Mail adresi ile daha önce kayıt yapılmış.' . GERI_DON;
                } else {
                  $sql = "INSERT INTO basvuru_uyeler SET ";
                  $sql.="uye_adsoyad = '" . $adsoyad ."', ";
                  $sql.="uye_email = '" . $email ."', ";
                  $sql.="uye_sifre = '" . sifre_hash($_POST["sifre1"]) ."', ";
                  $sql.="uye_yetki = '" .serialize($tesis) ."'";
                  if($uye_admin){$sql.=", uye_admin = 'admin'";}else{$sql.=", uye_admin = 'uye'";}

                  if($wpdb->query($sql)){
                      $id = $wpdb->insert_id;
                      $tesisler = $wpdb->get_results("select * from basvuru_tesisler");
                        foreach ($tesisler as $tesis_db) {
                          $tesis_yetkili = yetki_listesi($tesis_db->tesis_yetkili);
                          if (!in_array($id,$tesis_yetkili) && in_array($tesis_db->tesis_id,$tesis)) {
                            $tesis_yetkili[] = $id;
                          }
                        $wpdb->query("UPDATE basvuru_tesisler SET tesis_yetkili = '".serialize($tesis_yetkili)."' where tesis_id=".$tesis_db->tesis_id);
                        }
                    echo 'Yeni Üye başarıyla eklenmiştir.' . GERI_DON_BOS;
                  } else {
                    echo 'Yeni Üye eklenirken bir hata oluştu.' . GERI_DON_BOS;
                  }
                }
            }
        } elseif ($islem == "listele") {
      ?>
      <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Ad Soyad</th>
          <th>Yetki</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $listele = $wpdb->get_results("select * from basvuru_uyeler order by uye_admin asc");

      $listesira = 1;
      foreach ($listele as $liste) {
      ?>
        <tr>
          <td><?=$listesira?></td>
          <td><a href="./?bolum=uyeler&islem=duzenle&id=<?=e($liste->uye_id ?? '')?>"><?=e($liste->uye_adsoyad ?? '')?></a><?php if ($liste->uye_admin == "admin") {echo " *";}?></td>
          <td>
          <?php
          if ($liste->uye_yetki) {
            $yetkiler = yetki_listesi($liste->uye_yetki);
            foreach ($yetkiler as $yetki) {
              $tesis = $wpdb->get_row("select * from basvuru_tesisler where tesis_id = ".(int) $yetki);
              if ($tesis) echo '<a href="/?bolum=tesisler&islem=tesis_duzenle&id='.e($tesis->tesis_id).'">'.e($tesis->tesis_isim).'</a><br>';
            }
          } else {
            echo '-';
          }
          ?>
          </td>
        </tr>
      <?php
      $listesira++;
      }
      ?>
      </tbody>
    </table>
      <?php
        } elseif ($islem == "duzenle") {
          $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_id = ". $id);
          $yetkiler = yetki_listesi($kontrol->uye_yetki ?? null);
      ?>
      <form class="form-horizontal" method="post" action="./?bolum=uyeler&islem=guncelle&id=<?=$id?>">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="adsoyad">Ad Soyad</label>
          <div class="controls">
            <input id="adsoyad" name="adsoyad" type="text" placeholder="" class="form-control" required="" value="<?=e($kontrol->uye_adsoyad ?? '')?>">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="email">E-Mail</label>
          <div class="controls">
            <input id="email" name="email" type="email" placeholder="" class="form-control" required=""  value="<?=e($kontrol->uye_email ?? '')?>">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="sifre1">Şifre</label>
          <div class="controls">
            <input id="sifre1" name="sifre1" type="password" placeholder="" class="form-control">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="sifre2">Şifre Tekrar</label>
          <div class="controls">
            <input id="sifre2" name="sifre2" type="password" placeholder="" class="form-control">            
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="tesis">Admin Yetkisi</label>
          <div class="controls">
            <label class="checkbox" for="uye_admin">
              <input type="checkbox" name="uye_admin" id="uye_admin" value="admin"<?php if ($kontrol->uye_admin == "admin") { echo " checked";}?>>
              Administrator
            </label>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="tesis">Yetkiler</label>
          <div class="controls">
          <?php
            $tesisler = $wpdb->get_results("select * from basvuru_tesisler order by tesis_isim asc");

            foreach ($tesisler as $tesis) {
          ?>
            <label class="checkbox" for="tesis-<?=e($tesis->tesis_id ?? '')?>">
              <input type="checkbox" name="tesis[]" id="tesis-<?=e($tesis->tesis_id ?? '')?>" value="<?=e($tesis->tesis_id ?? '')?>"<?php if ($kontrol->uye_yetki && (in_array($tesis->tesis_id, $yetkiler))) { echo ' checked';}?>>
              <?=e($tesis->tesis_isim ?? '')?>
            </label>
          <?php
          }
          ?>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="guncelle"></label>
          <div class="controls">
            <button id="guncelle" name="guncelle" class="btn btn-primary">Güncelle</button>
          </div>
        </div>

        </fieldset>
      </form>
      <?php
        } elseif ($islem == "guncelle") {
          $adsoyad    = sql_clear($_POST["adsoyad"] ?? '');
          $email      = sql_clear($_POST["email"] ?? '');
          $sifre1     = sql_clear($_POST["sifre1"] ?? '');
          $sifre2     = sql_clear($_POST["sifre2"] ?? '');
          $uye_admin  = sql_clear($_POST["uye_admin"] ?? '');
          $tesis      = sql_clear($_POST["tesis"] ?? '');

            if (!$email) {
              echo 'E-Mail adresini ve Şifre alanını boş bırakmayınız.' . GERI_DON;
            } elseif (valid_email($email) == false) {
              echo 'Hatalı E-Mail adresi' . GERI_DON;
            } elseif (($sifre1 || $sifre2) && ($sifre1<>$sifre2)) {
              echo 'Şifreleriniz birbiriyle uyumlu olmalıdır' . GERI_DON;
            } elseif (($sifre1 || $sifre2) && check_password($sifre1) == false) {
              echo 'Hatalı Şifre kombinasyonu. (En az 1 harf ve 1 rakam olmalı)' . GERI_DON;
            } elseif (!is_array($tesis)) {
              echo 'En az 1 Tesis için yetki belirtin.' . GERI_DON;
            } else {
              $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_email = '".$email."'");
                if ($kontrol && $kontrol->uye_id != $id) {
                  echo 'Bu E-Mail adresi ile daha önce kayıt yapılmış.' . GERI_DON;
                } else {
                  $sql = "UPDATE basvuru_uyeler SET ";
                  $sql.="uye_adsoyad = '" . $adsoyad ."', ";
                  $sql.="uye_email = '" . $email ."', ";
                  if($uye_admin){$sql.="uye_admin = 'admin', ";}else{$sql.="uye_admin = 'uye', ";}
                  if($sifre1){$sql.="uye_sifre = '" . sifre_hash($_POST["sifre1"]) ."', ";}
                  $sql.="uye_yetki = '" .serialize($tesis) ."' ";
                  $sql.= "where uye_id = " . $id;

                  if($wpdb->query($sql)){
                      $tesisler = $wpdb->get_results("select * from basvuru_tesisler");
                        foreach ($tesisler as $tesis_db) {
                          $tesis_yetkili = yetki_listesi($tesis_db->tesis_yetkili);
                          if (!in_array($id,$tesis_yetkili) && in_array($tesis_db->tesis_id,$tesis)) {
                            $tesis_yetkili[] = $id;
                          } elseif (in_array($id,$tesis_yetkili) && !in_array($tesis_db->tesis_id,$tesis)) {
                            unset($tesis_yetkili[array_search($id, $tesis_yetkili)]);
                          }
                        $wpdb->query("UPDATE basvuru_tesisler SET tesis_yetkili = '".serialize($tesis_yetkili)."' where tesis_id=".$tesis_db->tesis_id);
                        }
                    echo 'Üye bilgileri başarıyla güncellenmiştir.' . GERI_DON;
                  } else {
                    echo 'Üye bilgileri güncellenirken hata oluştu.' . GERI_DON;
                  }
                }
            }
        }
      } elseif ($bolum == "profilim" && $_SESSION['giris']) {
        $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_id = ". (int) $_SESSION['uye_id']);
        $yetkiler = yetki_listesi($kontrol->uye_yetki ?? null);

        if (!$islem) {
      ?>
          <form class="form-horizontal" method="post" action="./?bolum=profilim&islem=guncelle">
            <fieldset>
            <div class="control-group">
              <label class="control-label" for="adsoyad">Ad Soyad</label>
              <div class="controls">
                <input id="adsoyad" name="adsoyad" type="text" placeholder="" class="form-control" required="" value="<?=e($kontrol->uye_adsoyad ?? '')?>">            
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="email">E-Mail</label>
              <div class="controls">
                <input id="email" name="email" type="email" placeholder="" class="form-control" required=""  value="<?=e($kontrol->uye_email ?? '')?>">            
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="sifre1">Şifre</label>
              <div class="controls">
                <input id="sifre1" name="sifre1" type="password" placeholder="" class="form-control">            
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="sifre2">Şifre Tekrar</label>
              <div class="controls">
                <input id="sifre2" name="sifre2" type="password" placeholder="" class="form-control">            
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="guncelle"></label>
              <div class="controls">
                <button id="guncelle" name="guncelle" class="btn btn-primary">Güncelle</button>
              </div>
            </div>

            </fieldset>
          </form>
      <?php
        } else {
          $adsoyad    = sql_clear($_POST["adsoyad"] ?? '');
          $email      = sql_clear($_POST["email"] ?? '');
          $sifre1     = sql_clear($_POST["sifre1"] ?? '');
          $sifre2     = sql_clear($_POST["sifre2"] ?? '');

            if (!$email) {
              echo 'E-Mail adresini ve Şifre alanını boş bırakmayınız.' . GERI_DON;
            } elseif (valid_email($email) == false) {
              echo 'Hatalı E-Mail adresi' . GERI_DON;
            } elseif (($sifre1 || $sifre2) && ($sifre1<>$sifre2)) {
              echo 'Şifreleriniz birbiriyle uyumlu olmalıdır' . GERI_DON;
            } elseif (($sifre1 || $sifre2) && check_password($sifre1) == false) {
              echo 'Hatalı Şifre kombinasyonu. (En az 1 harf ve 1 rakam olmalı)' . GERI_DON;
            } else {
              $kontrol = $wpdb->get_row("select * from basvuru_uyeler where uye_email = '".$email."'");
                if ($kontrol && $kontrol->uye_id != $_SESSION['uye_id']) {
                  echo 'Bu E-Mail adresi ile daha önce kayıt yapılmış.' . GERI_DON;
                } else {
                  $sql = "UPDATE basvuru_uyeler SET ";
                  $sql.="uye_adsoyad = '" . $adsoyad ."', ";
                  $sql.="uye_email = '" . $email ."' ";
                  if($sifre1){$sql.=", uye_sifre = '" . sifre_hash($_POST["sifre1"]) ."' ";}
                  $sql.= "where uye_id = " . (int) $_SESSION['uye_id'];

                  if($wpdb->query($sql)){
                    echo 'Üye bilgileri başarıyla güncellenmiştir.' . GERI_DON;
                  } else {
                    echo 'Üye bilgileri güncellenirken hata oluştu.' . GERI_DON;
                  }
                }
            }
        }
      } else {
        redirect("./");
      }
      ?>
      </div>

      <div class="well">
        <p>&copy; 2019 Bursa Büyükşehir Belediyesi <a href="https://www.bursa.bel.tr/?sayfa=idari&id=185" target="_blank">Gençlik ve Spor Hizmetleri Şb. Md.</a> tarafından hazırlanmıştır.</p>
      </div>


    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/bootstrap-formhelpers.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/jquery.number.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.js"></script>
    <script>
      $(function(){
        $('#brans_ucret').number( true, 2, '.' );
      });

      $(document).ready(function() {
        $('#basvurular').dataTable( {
          "processing": true,
          "serverSide": true,
          "aoColumnDefs" : [ {
              "bSortable" : false,
              "aTargets" : ['_all'],
          } ],
          "iDisplayLength": 50,
          "ajax": "./basvuru_listele.php?id=<?=$id?>"
        } );
      } );
    </script>
</body>
</html>