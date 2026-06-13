# Lichtmoment – Hochzeitsfotografie-Webseite

Eine stilvolle, moderne Webseite für Hochzeitsfotografen mit Laravel 13, Tailwind CSS v4, Admin-Panel und Share-System für Brautpaare.

## Features

- **Three.js Landing Page** – Animierter goldener Bokeh-Partikel-Hintergrund
- **Portfolio-Galerie** – Masonry-Layout mit Tailwind CSS
- **Admin-Panel** – Login-geschützt, Projekt-Management, Foto-Upload (Drag & Drop)
- **Projekt-System** – Projekte mit Unterordnern zur Sortierung der Fotos
- **Share-Links** – Token-basierte Links für Brautpaare, optional mit Passwort und Ablaufdatum
- **Download-System** – Einzel-Fotos oder Gesamt-Paket als ZIP, sperrbar pro Projekt
- **Responsive** – Mobile-first Design mit Tailwind CSS v4
- **QR-Code kompatibel** – Share-Links funktionieren auf Visitenkarten und WhatsApp
- **Let's Encrypt** – Automatische HTTPS-Zertifikate via host-seitiges Caddy

## Tech-Stack

| Komponente | Technologie |
|---|---|
| Backend | PHP 8.4 + Laravel 13 |
| Frontend | Tailwind CSS v4 + Vanilla JS |
| 3D-Effekte | Three.js (CDN via Importmap) |
| Datenbank | SQLite (kein MySQL nötig!) |
| Dependency Management | Composer + NPM |
| Hosting | Docker (PHP-FPM) + Caddy Reverse Proxy |
| Webserver | Host-seitiges Caddy mit Let's Encrypt |

## Installation (Lokal mit Docker)

```bash
# Repository klonen
git clone https://github.com/dreyjey1993/lichtmoment.git
cd lichmoment

# Bilder herunterladen (Beispielbilder von Unsplash)
# Portfolio-Bilder: public/storage/portfolio/hochzeitsfoto_01-12.jpg
# Projekt-Fotos: public/storage/projects/photo_01-10.jpg

# Docker Container starten
docker compose up -d --build

# Datenbank migrieren und seeden
docker exec lichtmoment-app php artisan migrate --force
docker exec lichtmoment-app php artisan db:seed --force

# Tailwind CSS bauen
npm run build
# Build-Dateien in Container kopieren:
docker cp public/build/manifest.json lichtmoment-app:/var/www/html/public/build/manifest.json
docker cp public/build/assets/ lichtmoment-app:/var/www/html/public/build/assets/
```

## Host-Seitiges Caddy Setup

Die Caddyfile auf dem Host (`/etc/caddy/Caddyfile`) enthält:

```
{
    email andrei.metzler@hotmail.de
}

foto.dreyjey.ddnss.de {
    root * /var/www/html/public

    php_fastcgi lichtmoment-app:9000 {
        env SCRIPT_FILENAME /var/www/html/public/index.php
    }

    handle_path /storage/* {
        file_server {
            root /var/www/html/public/storage
        }
    }

    file_server
}
```

Nach Änderungen: `sudo caddy reload --config /etc/caddy/Caddyfile`

## .env Konfiguration

```env
APP_NAME=Lichtmoment
APP_DEBUG=false
APP_URL=https://foto.dreyjey.ddnss.de
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
```

## Admin-Zugang

- **URL:** `https://foto.dreyjey.ddnss.de/admin`
- **Benutzername:** `admin`
- **Passwort:** `wasd1234`

## Tests

```bash
# Tests im Container ausführen
docker exec lichtmoment-app php artisan test --testdox
```

Aktueller Status: **15/16 Tests passen** (1 risky wegen PHPUnit/Blade Output-Buffer)

## Projektstruktur

```
lichtmoment/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # HomeController, AdminController, ShareController
│   │   └── Middleware/     # AdminAuth
│   └── Models/             # Project, Folder, Photo, ShareLink, User
├── config/                 # app.php, auth.php, filesystems.php
├── database/
│   ├── factories/          # ProjectFactory, PhotoFactory, etc.
│   ├── migrations/         # 2025_01_01_000000_create_lichtmoment_tables.php
│   └── seeders/            # DatabaseSeeder
├── public/                 # index.php, build/, storage/
│   ├── build/              # Vite Build (CSS, JS, Fonts)
│   └── storage/            # Portfolio + Projekt-Bilder (symlink)
├── resources/
│   ├── css/app.css         # Tailwind CSS
│   ├── js/app.js
│   └── views/
│       ├── layouts/app.blade.php
│       ├── home.blade.php          # Landing Page mit Three.js
│       ├── impressum.blade.php
│       ├── datenschutz.blade.php
│       ├── admin/
│       │   ├── login.blade.php
│       │   ├── dashboard.blade.php
│       │   └── project.blade.php
│       └── share/
│           ├── gallery.blade.php
│           └── error.blade.php
├── routes/web.php
├── tests/Feature/LichtmomentTest.php
├── docker/
│   ├── php.Dockerfile
│   └── Caddyfile
├── docker-compose.yml
└── vite.config.js
```

## Deployment auf netcup

1. `composer install --no-dev --optimize-autoloader`
2. `npm run build`
3. Alle Dateien per FTP/SFTP hochsenden
4. Schreibrechte für `storage/` und `public/storage/` setzen (775)
5. SQLite funktioniert ohne separates Setup
6. Domain auf den Server-Pointen, PHP 8.3+ aktivieren

**Hinweis:** Netcups kleines Paket unterstützt PHP 8.x und SQLite. Kein Node.js nötig!

## Share-Links

1. Im Admin-Panel ein Projekt öffnen
2. "Share-Link erstellen" klicken
3. Optional: Ablaufdatum, Passwort, Download-Sperre konfigurieren
4. Der generierte Link (`https://foto.dreyjey.ddnss.de/share/abc123...`) kann per WhatsApp,
   QR-Code auf Visitenkarten oder E-Mail geteilt werden

## Anpassungen

### Fotografen-Daten
Die Kontaktdaten können in `app/Http/Controllers/HomeController.php` angepasst werden.

### Farbschema
Alle Farben als CSS-Variablen in `resources/css/app.css`:

```css
--color-gold-400: #c9a94e;
--color-gold-300: #e8c46d;
--color-gold-500: #b8943f;
```

### Logo
Das Logo ist aktuell als Text in `home.html.twig` gerendert. Für ein SVG-Logo die `hero__logo` Sektion anpassen.

## Lizenz

MIT License
