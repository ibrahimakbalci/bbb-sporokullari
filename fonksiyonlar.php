<?php
	function sef_link($string)
	{
	$find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
	$replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
	$string = strtolower(str_replace($find, $replace, $string));
	$string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
	$string = trim(preg_replace('/\s+/', ' ', $string));
	$string = str_replace(' ', '-', $string);
	return $string;
	}

	function sql_clear($variable) 
	{
		global $wpdb;
		if (!is_array($variable)) {
			// ezSQL escape() stripslashes uyguladığı için doğrudan mysqli kullanılır (eski mysql_real_escape_string davranışı)
			$variable = trim((string) $variable);
			$variable = strip_tags($wpdb->dbh ? mysqli_real_escape_string($wpdb->dbh, $variable) : addslashes($variable));
			return $variable;
		} else {
			// Dizi olarak sadece tesis ID listeleri (tesis[]) geliyor; serialize edilip SQL'e yazıldığı için int'e çevrilir
			return array_map('intval', $variable);
		}
	}

	// uye_yetki / tesis_yetkili alanlarındaki serialize edilmiş ID listesini her zaman dizi olarak döndürür
	function yetki_listesi($serialized)
	{
		$liste = is_string($serialized) ? @unserialize($serialized, ['allowed_classes' => false]) : false;
		return is_array($liste) ? $liste : array();
	}

	function sifre_hash($sifre)
	{
		return password_hash((string) $sifre, PASSWORD_DEFAULT);
	}

	// Eski kayıtlar md5(sql_clear(şifre)) olarak tutuluyor; yeni kayıtlar password_hash ile
	function sifre_dogrula($sifre, $hash)
	{
		$hash = (string) $hash;
		if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
			return hash_equals(strtolower($hash), md5(sql_clear($sifre)));
		}
		return password_verify((string) $sifre, $hash);
	}

	// Excel dosyasını diske yazmadan doğrudan indirtir
	function excel_indir($writer, $dosya_adi)
	{
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $dosya_adi . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		exit;
	}

	// HTML çıktısı için kaçış
	function e($str)
	{
		return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
	}

    function valid_email($str)
    {
    return ( ! preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str ) ) ? FALSE : TRUE;
    }

	function check_password($pwd) {
	    if (strlen($pwd) < 4) {
	        return false;
	    } elseif (!preg_match("#[0-9]+#", $pwd)) {
	        return false;
	    } elseif (!preg_match("#[a-zA-Z]+#", $pwd)) {
	        return false;
	    } else {
	    	return true;
	    }
	}

    function redirect($link) {
    		header ("Location:$link"); 
    		exit;
    }

	function mysql_datetime($tarih) {
	  if ($tarih) {
	      $son_tarih = date("Y/m/d", strtotime($tarih));
	      return $son_tarih;
	  }
	}

	function mysql_to_datetime($tarih) {
	  if ($tarih) {
	      $son_tarih = date("d-m-Y", strtotime($tarih));
	      echo $son_tarih;
	  }
	}

	function mysql_to_extra($tarih) {
	  if ($tarih) {
	      $son_tarih = date("d-m-Y H:i:s", strtotime($tarih));
	      echo $son_tarih;
	  }
	}	

	function tarih_kontrol($tarih) {
		$tarih_arr  = explode('-', (string) $tarih);
		if (count($tarih_arr) != 3 || !ctype_digit(implode('', $tarih_arr))) {
			return false;
		}
		return checkdate((int) $tarih_arr[1], (int) $tarih_arr[0], (int) $tarih_arr[2]);
	}