# Bursa Büyükşehir Belediyespor — Spor Okulları Online Kayıt Sistemi

Spor okulu başvurularını tesis / branş / yaş grubu bazında toplayan ve yöneten PHP uygulaması.
PHP 8.3 + MariaDB 10.6, Docker ile çalışır.

## Yapı

| Dosya | Görev |
|---|---|
| `index.php` | Tüm sayfalar (`?bolum=&islem=` ile yönlendirme) |
| `db.php` | Oturum, veritabanı bağlantısı (`.env`'den okur) |
| `fonksiyonlar.php` | Yardımcı fonksiyonlar (şifre, yetki, XSS kaçışı, Excel indirme) |
| `basvuru_listele.php` | DataTables için JSON başvuru listesi |
| `ezsql/` | ezSQL veritabanı katmanı (mysqli sürücüsü) |
| `docker/` | Dockerfile, Apache/PHP ayarları, DB şema + migration |

Excel çıktıları PhpSpreadsheet ile bellekte üretilip doğrudan indirilir, diske yazılmaz.

## Geliştirme ortamı

```bash
cp .env.example .env        # şifreleri değiştirin
docker compose up -d --build
```

- Uygulama: http://localhost:8080 — phpMyAdmin: http://localhost:8081 (portlar `.env`'den değiştirilebilir)
- İlk açılışta veritabanına `docker/db/init/01-schema.sql` (boş şema) yüklenir.
  Gerçek bir döküm kullanmak için `.env` içine `DB_INIT_SQL=./dosya.sql` yazın.
  Şema yalnızca DB volume'ü ilk oluşturulurken yüklenir; yeniden yüklemek için `docker compose down -v`.

### İlk admin kullanıcısı

Boş şemada kullanıcı yoktur. Şifre hash'ini üretip ekleyin:

```bash
docker compose exec app php -r 'echo password_hash("GüçlüBirŞifre", PASSWORD_DEFAULT), "\n";'

docker compose exec -T db sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' <<'SQL'
INSERT INTO basvuru_uyeler (uye_email, uye_sifre, uye_adsoyad, uye_yetki, uye_admin)
VALUES ('admin@example.com', '<yukarıdaki hash>', 'Yönetici', 'a:0:{}', 'admin');
SQL
```

## Production

TLS'in sunucudaki reverse proxy (Nginx, Traefik vb.) tarafından sonlandırıldığı varsayılır; container yalnızca `127.0.0.1:${APP_PORT}` üzerinden dinler.

```bash
# Sunucuda, docker-compose.yml ile aynı klasörde .env oluşturun (FORCE_HTTPS=1, güçlü şifreler)
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

Mevcut bir veritabanından geçiş yapılıyorsa dökümü yükleyip migration'ı **bir kez** çalıştırın:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T db \
  sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' < dump.sql
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T db \
  sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' < docker/db/init/02-migration.sql
```

Eski MD5 şifreler, kullanıcı ilk giriş yaptığında otomatik olarak `password_hash` (bcrypt) ile yenilenir.

## Güvenlik notları

- `.env`, SQL dökümleri ve `excel/` (TC kimlik numarası içerir) repoya ve Docker imajına **girmez** (`.gitignore`, `.dockerignore`).
- `.htaccess` hassas dosya ve klasörlere web erişimini engeller.
