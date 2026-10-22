-- PHP 8.3 revizyonu: MD5 (32 karakter) yerine password_hash (bcrypt 60 karakter, ileride argon2 daha uzun)
-- Docker'da ilk açılışta otomatik çalışır. Mevcut prod veritabanında deploy'dan ÖNCE bir kez çalıştırılmalıdır.
ALTER TABLE IF EXISTS `basvuru_uyeler` MODIFY `uye_sifre` varchar(255) DEFAULT NULL;

-- Serialize edilmiş yetki listeleri varchar(250) sınırına dayanmış durumda (tesis 1, 14, 24: 248-249 karakter).
-- Yeni üye yetkilendirmesi strict modda reddediliyor, non-strict modda listeyi kesip bozuyordu.
ALTER TABLE IF EXISTS `basvuru_tesisler` MODIFY `tesis_yetkili` text DEFAULT 'a:0:{}';
ALTER TABLE IF EXISTS `basvuru_uyeler` MODIFY `uye_yetki` text DEFAULT NULL;
