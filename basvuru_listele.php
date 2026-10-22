<?php
require_once('db.php');

	header('Content-Type: application/json; charset=utf-8');

	// Sadece giriş yapmış ve branşın tesisine yetkili kullanıcılar listeleyebilir
	$brans = $wpdb->get_row("select tesis_id from basvuru_branslar where brans_id = ".$id);
	if (!$_SESSION['giris'] || !$brans || !in_array($brans->tesis_id, $_SESSION['uye_yetki'])) {
		http_response_code(403);
		echo '{"draw":0,"recordsTotal":0,"recordsFiltered":0,"data":[]}';
		exit;
	}

	$aramax = '';
	$arama = is_array($_GET["search"] ?? null) ? sql_clear($_GET["search"]['value'] ?? '') : '';
		if ($arama) {
			$arama = addcslashes($arama, '%_');
			$aramax = " and (basvuru_tckimlik like '%".$arama."%' or basvuru_adsoyad_sporcu like '%".$arama."%')";
		}
	$KayitSayisi = max(1, min(500, (int) ($_GET["length"] ?? 50)));
	$Baslangic = max(0, (int) ($_GET["start"] ?? 0));
	$basvurular = $wpdb->get_results("select * from basvuru_basvuru WHERE brans_id=".$id."".$aramax." order by basvuru_zamani asc LIMIT ".$Baslangic.",".$KayitSayisi);
	$toplam_basvuru = (int) $wpdb->get_var("SELECT COUNT(basvuru_id) FROM basvuru_basvuru WHERE brans_id=".$id."".$aramax);
	$records["draw"] = (int) ($_GET["draw"] ?? 0);
	$records["recordsTotal"] = $toplam_basvuru;
	$records["recordsFiltered"] = $toplam_basvuru;
	$records["data"] = array();

	foreach ( $basvurular as $basvuru ) {
		$silme = '';
		$_SESSION['uye_admin'] != 'admin' ?: $silme = '<a href="./?bolum=basvurular&islem=basvuru_sil&id='.(int) $basvuru->basvuru_id.'" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i> Sil</a>';

		$records["data"][] = array(
		'<a href="./?bolum=basvurular&islem=duzenle&id='.(int) $basvuru->basvuru_id.'">'.e($basvuru->basvuru_tckimlik).'</a>',
		e($basvuru->basvuru_adsoyad_sporcu),
		$silme,
		);
	}
	echo json_encode($records);
