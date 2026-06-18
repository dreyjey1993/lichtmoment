# Lichtmoment – Hochzeitsfotografie-Webseite

Eine stilvolle, moderne Webseite für Hochzeitsfotografen. Gebaut mit **Laravel 13**, **Tailwind CSS v4**, **Three.js** und **SQLite**. Enthält ein vollständiges Admin-Panel für Projekt-Management, Foto-Upload und ein Share-System für Brautpaare.

**Live-Demo:** [foto.dreyjey.ddnss.de](https://foto.dreyjey.ddnss.de)

---

## Inhaltsverzeichnis

- [Features](#features)
- [Tech-Stack](#tech-stack)
- [Projektstruktur](#projektstruktur)
- [Installation (Lokal mit Docker)](#installation-lokal-mit-docker)
- [Deployment auf Produktion (Debian + Caddy)](#deployment-auf-produktion-debian--caddy)
- [Deployment auf Shared Hosting (z.B. Netcup)](#deployment-auf-shared-hosting-zb-netcup)
- [Docker-Konfiguration](#docker-konfiguration)
- [Datenbank](#datenbank)
  - [Migrationen](#migrationen)
  - [Modelle](#modelle)
  - [Seeders](#seeders)
- [Konfiguration](#konfiguration)
  - [.env](#env)
  - [Dateisystem (Storage)](#dateisystem-storage)
- [Routen & API](#routen--api)
  - [Öffentliche Routen](#öffentliche-routen)
  - [Admin-Routen](#admin-routen)
  - [Share-Routen](#share-routen)
- [Authentifizierung & Sicherheit](#authentifizierung--sicherheit)
  - [Admin-Auth](#admin-auth)
  - [Share-Passwort-Schutz](#share-passwort-schutz)
  - [Sicherheitsmaßnahmen](#sicherheitsmaßnahmen)
- [Admin-Panel](#admin-panel)
  - [Login](#login)
  - [Dashboard](#dashboard)
  - [Projekt-Management](#projekt-management)
  - [Foto-Upload](#foto-upload)
  - [Ordner-System](#ordner-system)
  - [Share-Links](#share-links)
- [Share-Galerie](#share-galerie)
- [Frontend](#frontend)
  - [Landing Page](#landing-page)
  - [Three.js Bokeh-Effekt](#threejs-bokeh-effekt)
  - [Portfolio-Galerie](#portfolio-galerie)
  - [Design-System (Farben & Typografie)](#design-system-farben--typografie)
- [Tests](#tests)
  - [Teststruktur](#teststruktur)
  - [Tests ausführen](#tests-ausführen)
- [Anpassungen](#anpassungen)
  - [Fotografen-Daten](#fotografen-daten)
  - [Farbschema](#farbschema)
  - [Logo](#logo)
- [Wartung & Troubleshooting](#wartung--troubleshooting)
  - [Nach Container-Rebuild](#nach-container-rebuild)
  - [Vite Build-Dateien aktualisieren](#vite-build-dateien-aktualisieren)
  - [Häufige Probleme](#häufige-probleme)
- [Lizenz](#lizenz)

---

## Features

| Feature | Beschreibung |
|---|---|
| **Three.js Landing Page** | Animierter goldener Bokeh-Partikel-Hintergrund mit WebGL-Shadern |
| **Portfolio-Galerie** | Masonry-Layout mit Lightbox, Touch-Support und Zoom |
| **Admin-Panel** | Login-geschützt, vollständiges Projekt- und Foto-Management |
| **Drag & Drop Upload** | Mehrfach-Upload mit Fortschrittsanzeige und Vorschau |
| **Ordner-System** | Projekte mit beliebig verschachtelbaren Unterordnern |
| **Share-Links** | Token-basierte Links für Brautpaare, optional mit Passwort und Ablaufdatum |
| **Download-System** | Einzel-Fotos oder Gesamt-Paket als ZIP, sperrbar pro Projekt |
| **Responsive Design** | Mobile-first mit Tailwind CSS v4, optimiert für alle Bildschirmgrößen |
| **QR-Code kompatibel** | Share-Links funktionieren auf Visitenkarten und WhatsApp |
| **Let's Encrypt** | Automatische HTTPS-Zertifikate via host-seitiges Caddy |
| **SQLite** | Kein MySQL/MariaDB nötig – einfache Dateneinrichtung |
| **Umfangreiche Tests** | 40+ Feature- und Security-Tests mit PHPUnit |

---

## Tech-Stack

| Komponente | Technologie | Version |
|---|---|---|
| Backend | PHP + Laravel | 8.4 / 13.x |
| Frontend | Tailwind CSS + Vanilla JS | v4 |
| 3D-Effekte | Three.js (CDN via Importmap) | aktuell |
| Datenbank | SQLite 3 | – |
| Build-Tool | Vite + Laravel Plugin | aktuell |
| Dependency Mgmt | Composer + NPM | – |
| Testing | PHPUnit | 12.x |
| Container | Docker (PHP-FPM + Nginx) | Alpine |
| Reverse Proxy | Caddy (Host) | aktuell |
| Webserver | Nginx (Container) + Caddy (Host) | – |

---

## Projektstruktur

```
lichtmoment/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php        # Landing Page, Impressum, Datenschutz
│   │   │   ├── AdminController.php       # Admin-Logik (CRUD, Upload, Share)
│   │   │   └── ShareController.php       # Share-Galerie, Download, Passwort
│   │   └── Middleware/
│   │       └── AdminAuth.php             # Admin-Authentifizierung
│   ├── Models/
│   │   ├── Project.php                   # Projekt-Model (name, slug, cover, description)
│   │   ├── Folder.php                    # Ordner-Model (verschachtelbar via parent_id)
│   │   ├── Photo.php                     # Foto-Model (filename, original_name, file_size)
│   │   ├── ShareLink.php                 # Share-Link-Model (token, password, expiry)
│   │   └── User.php                      # User-Model (Admin-Auth)
│   └── Providers/
│       └── AppServiceProvider.php
├── config/
│   ├── app.php
│   ├── auth.php                          # Auth-Konfiguration (web guard, users provider)
│   └── filesystems.php                   # Storage-Konfiguration (public disk → public/storage)
├── database/
│   ├── factories/                        # Model-Factories für Tests
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_01_01_000000_create_lichtmoment_tables.php  # Haupt-Migration
│   │   └── 2026_06_11_092228_add_username_to_users_table.php
│   └── seeders/
│       └── DatabaseSeeder.php            # Admin-User, Beispiel-Projekt + Fotos
├── docker/
│   ├── php.Dockerfile                    # PHP 8.4 FPM + Nginx Container
│   └── Caddyfile                         # Host-seitige Caddy-Konfiguration
├── public/
│   ├── index.php                         # Laravel Entry Point
│   ├── build/                            # Vite Build (CSS, JS, Fonts)
│   │   ├── manifest.json
│   │   └── assets/
│   └── storage/                          # Upload-Bilder (Docker Volume)
│       ├── portfolio/                    # Portfolio-Bilder
│       └── projects/                     # Projekt-Fotos + Cover-Bilder
├── resources/
│   ├── css/app.css                       # Tailwind CSS + Design-System-Variablen
│   ├── js/app.js                         # Three.js Bokeh + Portfolio Lightbox
│   └── views/
│       ├── layouts/app.blade.php         # Haupt-Layout (Meta, Nav, Footer, CSRF)
│       ├── home.blade.php                # Landing Page (Hero, Portfolio, About)
│       ├── impressum.blade.php           # Impressum (§ 5 TMG)
│       ├── datenschutz.blade.php         # Datenschutzerklärung (DSGVO)
│       ├── admin/
│       │   ├── login.blade.php           # Admin-Login-Formular
│       │   ├── dashboard.blade.php       # Projekt-Übersicht + Schnell-Erstellen
│       │   ├── create.blade.php          # Neues Projekt (Name, Beschreibung, Cover)
│       │   ├── project.blade.php         # Projekt-Detail (Upload, Ordner, Shares)
│       │   └── partials/
│       │       └── design-system.blade.php  # Admin-Design-System (Buttons, Cards)
│       └── share/
│           ├── gallery.blade.php         # Share-Galerie (Passwort, Filter, Download)
│           └── error.blade.php           # Fehlerseite (404, 410 abgelaufen)
├── routes/web.php                        # Alle Web-Routen
├── tests/
│   ├── TestCase.php
│   ├── Feature/
│   │   ├── LichtmomentTest.php           # 30+ Feature-Tests
│   │   └── SecurityTest.php              # 25+ Security-Tests
│   └── Unit/
│       ├── AdminAuthMiddlewareTest.php
│       ├── ModelsTest.php
│       ├── PhotoModelTest.php
│       └── ProjectModelTest.php
├── docker-compose.yml                    # Docker Compose (App-Container)
├── vite.config.js                        # Vite + Tailwind + Laravel Plugin
├── phpunit.xml                           # PHPUnit-Konfiguration
├── composer.json                         # PHP-Dependencies
├── package.json                          # JS-Dependencies
└── .env.example                          # Beispiel-Konfiguration
```

---

## Installation (Lokal mit Docker)

### Voraussetzungen

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- [Composer](https://getcomposer.org/) (für lokale Entwicklung)
- [Node.js](https://nodejs.org/) + NPM (für Vite Build)

### Schritt-für-Schritt

```bash
# 1. Repository klonen
git clone https://github.com/dreyjey1993/lichtmoment.git
cd lichmoment

# 2. Environment-Datei erstellen
cp .env.example .env
php artisan key:generate

# 3. Docker Container starten
docker compose up -d --build

# 4. Datenbank migrieren und seeden
docker exec lichtmoment-app php artisan migrate --force
docker exec lichtmoment-app php artisan db:seed --force

# 5. Frontend-Assets bauen
npm install
npm run build

# 6. Build-Dateien in Container kopieren
docker cp public/build/manifest.json lichtmoment-app:/var/www/html/public/build/manifest.json
docker cp public/build/assets/ lichtmoment-app:/var/www/html/public/build/assets/
```

Die Webseite ist nun unter `http://localhost` erreichbar.

### Admin-Zugang

- **URL:** `http://localhost/admin`
- **Benutzername:** `admin`
- **Passwort:** `wasd1234`

---

## Deployment auf Produktion (Debian + Caddy)

### Voraussetzungen

- Debian-Server mit Docker & Docker Compose
- Caddy als Reverse Proxy auf dem Host
- Domain mit DNS-Eintrag auf die Server-IP

### 1. Repository auf dem Server

```bash
cd /home/hermes
git clone https://github.com/dreyjey1993/lichtmoment.git
cd lichmoment
```

### 2. Environment konfigurieren

```bash
cp .env.example .env
# .env bearbeiten:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://foto.dreyjey.ddnss.de
# DB_CONNECTION=sqlite
```

### 3. Container starten

```bash
docker compose up -d --build
docker exec lichtmoment-app php artisan migrate --force
docker exec lichtmoment-app php artisan db:seed --force
```

### 4. Caddy konfigurieren

Die Caddyfile auf dem Host (`/etc/caddy/Caddyfile`):

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

### 5. Netzwerk

Der Container muss im selben Docker-Netzwerk wie Caddy sein (`compose_default`):

```bash
docker network create compose_default
```

---

## Deployment auf Shared Hosting (z.B. Netcup)

1. `composer install --no-dev --optimize-autoloader`
2. `npm run build`
3. Alle Dateien per FTP/SFTP hochsenden
4. Schreibrechte für `storage/` und `public/storage/` setzen (775)
5. SQLite funktioniert ohne separates Setup
6. Domain auf den Server-Pointen, PHP 8.3+ aktivieren

**Hinweis:** Shared Hosting mit PHP 8.x und SQLite wird unterstützt. Kein Node.js auf dem Server nötig – nur die gebauten Dateien hochladen.

---

## Docker-Konfiguration

### docker-compose.yml

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/php.Dockerfile
    container_name: lichtmoment-app
    restart: unless-stopped
    volumes:
      - lichtmoment_storage:/var/www/html/storage        # Laravel Storage
      - lichtmoment_uploads:/var/www/html/public/storage # Upload-Bilder
      - lichtmoment_db:/var/www/html/database             # SQLite-Datenbank
    networks:
      - compose_default
    environment:
      - APP_DEBUG=false
      - APP_URL=https://foto.dreyjey.ddnss.de
      - DB_CONNECTION=sqlite
      - DB_DATABASE=/var/www/html/database/database.sqlite
    expose:
      - "9000"

volumes:
  lichtmoment_storage:
  lichtmoment_uploads:
  lichtmoment_db:

networks:
  compose_default:
    external: true
```

### Docker Volumes

| Volume | Container-Pfad | Zweck |
|---|---|---|
| `lichtmoment_storage` | `/var/www/html/storage` | Laravel Storage (Logs, Cache, Sessions) |
| `lichtmoment_uploads` | `/var/www/html/public/storage` | Hochgeladene Bilder (Portfolio + Projekte) |
| `lichtmoment_db` | `/var/www/html/database` | SQLite-Datenbank-Datei |

**Wichtig:** `public/storage` und `storage/app/public` sind **separate** Docker Volumes. Ein Symlink ist nicht möglich. Die `public`-Disk in `filesystems.php` zeigt direkt auf `public_path('storage')`.

### Dockerfile (docker/php.Dockerfile)

- Basis: `php:8.4-fpm-alpine`
- Extensions: `gd` (Bildverarbeitung), `pdo_sqlite`
- Webserver: Nginx (läuft neben PHP-FPM im Container)
- PHP-FPM lauscht auf `127.0.0.1:9000`
- Start-Script startet PHP-FPM + Nginx

---

## Datenbank

### Migrationen

Die Haupt-Migration (`2025_01_01_000000_create_lichtmoment_tables.php`) erstellt alle Tabellen:

#### `users`
| Spalte | Typ | Beschreibung |
|---|---|---|
| id | bigint | Primärschlüssel |
| name | string | Benutzername |
| email | string (unique) | E-Mail (Login-Identifier) |
| password | string | Bcrypt-Hash |
| remember_token | string | Remember-Me-Token |
| timestamps | | created_at / updated_at |

#### `projects`
| Spalte | Typ | Beschreibung |
|---|---|---|
| id | bigint | Primärschlüssel |
| name | string | Projektname |
| description | text | Projektbeschreibung |
| slug | string (unique) | URL-Slug |
| cover_image | string (nullable) | Cover-Bild-Dateiname |
| download_enabled | boolean | Download erlaubt (default: true) |
| password_hash | string (nullable) | Veraltet (wird über ShareLinks gesteuert) |
| timestamps | | created_at / updated_at |

#### `folders`
| Spalte | Typ | Beschreibung |
|---|---|---|
| id | bigint | Primärschlüssel |
| project_id | bigint (FK) | Zugehöriges Projekt |
| name | string | Ordnername |
| parent_id | bigint (FK, nullable) | Eltern-Ordner (Verschachtelung) |
| sort_order | integer | Sortierreihenfolge |
| timestamps | | created_at / updated_at |

#### `photos`
| Spalte | Typ | Beschreibung |
|---|---|---|
| id | bigint | Primärschlüssel |
| project_id | bigint (FK, nullable) | Zugehöriges Projekt (null = Portfolio) |
| folder_id | bigint (FK, nullable) | Zugehöriger Ordner |
| filename | string | Dateiname auf Disk |
| original_name | string | Original-Dateiname beim Upload |
| file_size | integer | Dateigröße in Bytes |
| sort_order | integer | Sortierreihenfolge |
| timestamps | | created_at / updated_at |

#### `share_links`
| Spalte | Typ | Beschreibung |
|---|---|---|
| id | bigint | Primärschlüssel |
| project_id | bigint (FK) | Zugehöriges Projekt |
| token | string (unique, 64) | Zufälliger Token (32 Zeichen) |
| password_hash | string (nullable) | Bcrypt-Hash für Passwort-Schutz |
| download_enabled | boolean | Download erlaubt (default: true) |
| expires_at | timestamp (nullable) | Ablaufdatum |
| access_count | integer | Zugriffszähler |
| timestamps | | created_at / updated_at |

### Modelle

#### Project
```php
$fillable = ['name', 'description', 'slug', 'cover_image', 'download_enabled'];

// Relationen
folders(): HasMany        // Ordner im Projekt
photos(): HasMany         // Fotos im Projekt
shareLinks(): HasMany     // Share-Links des Projekts
```

#### Folder
```php
$fillable = ['project_id', 'name', 'parent_id', 'sort_order'];

// Relationen
project(): BelongsTo      // Zugehöriges Projekt
photos(): HasMany         // Fotos im Ordner
parent(): BelongsTo       // Eltern-Ordner
children(): HasMany       // Unter-Ordner
```

#### Photo
```php
$fillable = ['project_id', 'folder_id', 'filename', 'original_name', 'file_size', 'sort_order'];

// Relationen
project(): BelongsTo      // Zugehöriges Projekt
folder(): BelongsTo       // Zugehöriger Ordner
```

#### ShareLink
```php
$fillable = ['project_id', 'token', 'password_hash', 'download_enabled', 'expires_at', 'access_count'];

// Relationen
project(): BelongsTo      // Zugehöriges Projekt

// Methoden
isExpired(): bool         // Prüft ob Link abgelaufen
```

#### User
```php
$fillable = ['name', 'email', 'password'];
$hidden = ['password', 'remember_token'];
// Erbt von Authenticatable (Laravel Standard)
```

### Seeders

Der `DatabaseSeeder` erstellt automatisch:

1. **Admin-User:** `admin@lichtmoment.de` / Passwort: `wasd1234`
2. **Beispiel-Projekt:** "Sarah & Thomas – Hochzeit im Schloss"
3. **4 Ordner:** Zeremonie, Empfang, Portraits, Details & Deko
4. **10 Beispiel-Fotos:** Verteilt auf die Ordner
5. **12 Portfolio-Platzhalter:** Für die Portfolio-Galerie

---

## Konfiguration

### .env

```env
APP_NAME=Lichtmoment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://foto.dreyjey.ddnss.de
APP_KEY=base64:...              # Wird von artisan key:generate erzeugt

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

LOG_CHANNEL=stack
LOG_LEVEL=debug

FILESYSTEM_DISK=local
```

**Wichtig:** Die `.env` wird im Container **nicht** persistiert. Nach jedem Rebuild muss sie neu erstellt werden.

### Dateisystem (Storage)

Die `config/filesystems.php` konfiguriert zwei Disks:

| Disk | Root | Zweck |
|---|---|---|
| `local` | `storage/app/private` | Privates Storage |
| `public` | `public_path('storage')` (= `/var/www/html/public/storage`) | Öffentliche Uploads |

**Wichtig:** Die `links`-Section ist leer – es wird kein Symlink erstellt. Alle Uploads landen direkt in `public/storage/`.

**Upload-Pfade:**
- Portfolio-Bilder: `public/storage/portfolio/`
- Projekt-Fotos: `public/storage/projects/`
- Cover-Bilder: `public/storage/projects/` (mit `cover_`-Präfix)

---

## Routen & API

### Öffentliche Routen

| Methode | Route | Controller | Beschreibung |
|---|---|---|---|
| GET | `/` | HomeController@index | Landing Page |
| GET | `/impressum` | HomeController@impressum | Impressum |
| GET | `/datenschutz` | HomeController@datenschutz | Datenschutzerklärung |

### Admin-Routen

| Methode | Route | Controller | Middleware | Beschreibung |
|---|---|---|---|---|
| GET | `/admin/login` | AdminController@loginPage | – | Login-Seite |
| POST | `/admin/login` | AdminController@login | throttle:5,1 | Login (Rate-Limited) |
| GET | `/admin/logout` | AdminController@logout | – | Abmelden |
| GET | `/admin` | AdminController@dashboard | admin.auth | Dashboard |
| GET | `/admin/project/new` | AdminController@newProject | admin.auth | Neues Projekt Formular |
| POST | `/admin/project/create` | AdminController@createProject | admin.auth | Projekt erstellen |
| GET | `/admin/project/{id}` | AdminController@projectDetail | admin.auth | Projekt-Detail |
| POST | `/admin/upload` | AdminController@uploadPhoto | admin.auth | Foto-Upload |
| POST | `/admin/folder/create` | AdminController@createFolder | admin.auth | Ordner erstellen |
| POST | `/admin/share/create` | AdminController@createShareLink | admin.auth | Share-Link erstellen |
| POST | `/admin/project/{id}/settings` | AdminController@updateProjectSettings | admin.auth | Einstellungen speichern |
| POST | `/admin/project/{id}/update` | AdminController@updateProject | admin.auth | Projekt aktualisieren |
| GET | `/admin/api/shares/{projectId}` | AdminController@getShareLinks | admin.auth | Share-Links abrufen |
| POST | `/admin/api/delete` | AdminController@deleteItem | admin.auth | Element löschen |
| POST | `/admin/api/bulk-delete-photos` | AdminController@bulkDeletePhotos | admin.auth | Mehrere Fotos löschen |
| POST | `/admin/api/delete-all-photos` | AdminController@deleteAllPhotos | admin.auth | Alle Fotos löschen |

### Share-Routen

| Methode | Route | Controller | Beschreibung |
|---|---|---|---|
| GET | `/share/{token}` | ShareController@show | Share-Galerie anzeigen |
| POST | `/share/api/gallery` | ShareController@loadGallery | Galerie-Daten laden (AJAX) |
| POST | `/share/api/check-password` | ShareController@checkPassword | Passwort prüfen |
| GET | `/share/download/photo/{id}` | ShareController@downloadPhoto | Einzel-Foto downloaden |
| POST | `/share/download/zip` | ShareController@downloadZip | ZIP-Download |

---

## Authentifizierung & Sicherheit

### Admin-Auth

Die Admin-Authentifizierung läuft über zwei Mechanismen:

1. **Session-basiert:** `session('admin_id')` wird beim Login gesetzt
2. **Laravel Auth:** `Auth::check()` für `actingAs()` in Tests

Der `AdminAuth` Middleware prüft beide:
```php
if (Auth::check()) return $next($request);
if (session()->has('admin_id')) return $next($request);
return redirect()->route('admin.login');
```

**Login-Prozess:**
1. Benutzername + Passwort werden validiert
2. User wird per E-Mail (`admin@lichtmoment.de`) gefunden
3. Passwort wird mit `Hash::check()` verifiziert
4. Bei Erfolg: `session()->regenerate()` (Session-Fixierung-Schutz) + `session(['admin_id' => $user->id])`
5. Bei Fehlgeschlagen: Redirect mit Fehlermeldung

### Share-Passwort-Schutz

Share-Links können optional mit einem Passwort geschützt werden:

1. Share-Link wird mit `password_hash` erstellt
2. Beim Aufruf wird geprüft, ob `share_access_{token}` in der Session ist
3. Falls nicht: Passwort-Modal wird angezeigt
4. Passwort wird per AJAX an `/share/api/check-password` gesendet
5. Bei Erfolg: Session-Flag wird gesetzt, Galerie wird angezeigt

### Sicherheitsmaßnahmen

| Maßnahme | Implementierung |
|---|---|
| **CSRF-Schutz** | Laravel CSRF-Token in Meta-Tag, automatisch bei allen POST-Requests |
| **Rate Limiting** | Login-Rate-Limit: 5 Versuche pro Minute (`throttle:5,1`) |
| **Session-Fixierung** | `session()->regenerate()` beim Login |
| **SQL-Injection** | Laravel Eloquent/Query Builder (parameterized queries) |
| **XSS-Schutz** | Blade `{{ }}` escapet automatisch HTML |
| **Datei-Upload-Validierung** | Nur `jpg, jpeg, webp, png` erlaubt, max. 20MB pro Foto, 5MB für Cover |
| **Passwort-Hashing** | Bcrypt für Admin und Share-Passwörter |
| **Token-basiert** | Share-Links mit 32-Zeichen zufälligem Token |
| **Ablaufdatum** | Share-Links können zeitlich begrenzt werden |
| **Authorization** | Admin-Middleware schützt alle Admin-Routen |

---

## Admin-Panel

### Login

- **URL:** `/admin/login`
- Benutzername/Passwort Formular
- Rate-Limited (5 Versuche/Minute)
- Session-Regeneration bei Erfolg

### Dashboard

- **URL:** `/admin`
- Übersicht aller Projekte als Karten-Grid
- Statistiken: Anzahl Projekte und Fotos
- "Schnell erstellen" Formular (Projekt per Klick erstellen)
- Cover-Bild-Vorschau oder Initialen-Platzhalter

### Projekt-Management

**Projekt erstellen:**
- Name (erforderlich, max. 255 Zeichen)
- Beschreibung (optional)
- Cover-Bild (optional, Drag & Drop)
- Automatische Erstellung eines "Alle Fotos"-Ordners

**Projekt bearbeiten:**
- Titel inline bearbeiten (klick auf Titel)
- Beschreibung inline bearbeiten
- Cover-Bild hochladen / entfernen
- Download aktivieren/deaktivieren
- Projekt löschen (kaskadiert: Fotos + Ordner + Shares)

### Foto-Upload

- **Drag & Drop** oder Klick auf Upload-Zone
- **Mehrfach-Upload** (mehrere Dateien gleichzeitig)
- Unterstützte Formate: JPG, JPEG, PNG, WebP
- Max. 20MB pro Datei
- Automatische Speicherung in `public/storage/projects/`
- Dateinamen werden randomisiert (16 Zeichen)

### Ordner-System

- Ordner pro Projekt erstellen
- Verschachtelung via `parent_id`
- Sortierung per `sort_order`
- Fotos können Ordnern zugeordnet werden
- Ordner löschen (Fotos werden nicht gelöscht, nur `folder_id` auf null)

### Share-Links

**Erstellung:**
- Token wird automatisch generiert (32 Zeichen)
- Optional: Passwort-Schutz
- Optional: Ablaufdatum (in Tagen)
- Download aktivieren/deaktivieren

**Verwaltung:**
- Liste aller Share-Links pro Projekt
- URL wird automatisch generiert
- Zugriffszähler
- Share-Links löschen

---

## Share-Galerie

Die Share-Galerie ist der öffentlich zugängliche Bereich für Brautpaare.

**URL-Format:** `https://foto.dreyjey.ddnss.de/share/{token}`

**Features:**
- Passwort-Schutz (optional)
- Ordner-Filter (wenn mehrere Ordner vorhanden)
- Lightbox mit Touch-Support und Zoom
- Einzel-Foto-Download (wenn aktiviert)
- ZIP-Download aller Fotos (wenn aktiviert)
- Ablaufdatum-Anzeige
- Responsive Grid-Layout
- Zugriffszähler (im Admin sichtbar)

**Zustandsablauf:**
1. Token gültig + kein Passwort → Galerie anzeigen
2. Token gültig + Passwort → Passwort-Modal
3. Token ungültig → 404 Fehlerseite
4. Token abgelaufen → 410 Fehlerseite mit Ablaufdatum

---

## Frontend

### Landing Page

Die Landing Page besteht aus drei Sektionen:

1. **Hero:** Vollbild-Section mit Three.js Bokeh-Hintergrund
2. **Portfolio:** Masonry-Galerie mit Lightbox
3. **About:** Fotografen-Bio mit Kontaktdaten

### Three.js Bokeh-Effekt

- 80 goldene Partikel mit WebGL-Shadern
- Individuelle Größen, Opazitäten und Geschwindigkeiten
- Reflektion an den Rändern
- Responsive (resize bei Fenstergröße)
- Pixel-Ratio-Begrenzung (max. 2) für Performance

```javascript
// Partikel-Eigenschaften
const particleCount = 80;
const color = 0xc9a94e; // Gold
// Custom Shader für weiche Bokeh-Kreise
```

### Portfolio-Galerie

- Masonry-Layout (CSS Columns)
- Lightbox mit:
  - Touch-Swipe-Navigation
  - Mausrad-Zoom
  - Tastatur-Navigation (Pfeiltasten)
  - Vollbild-Ansicht
- Lazy Loading für Bilder

### Design-System (Farben & Typografie)

**Farbpalette (CSS-Variablen in `resources/css/app.css`):**

```css
/* Gold */
--color-gold-50: #fdf8ef;
--color-gold-100: #f9edcf;
--color-gold-200: #f3d99e;
--color-gold-300: #e8c46d;
--color-gold-400: #c9a94e;  /* Primär */
--color-gold-500: #b8943f;
--color-gold-600: #a68a3c;
--color-gold-700: #8a6f2f;
--color-gold-800: #6e5624;
--color-gold-900: #523f1a;

/* Neutral */
--color-cream: #f5f3ef;
--color-offwhite: #faf9f7;
--color-warm: #FDFDFC;
```

**Typografie:**
- **Sans-Serif:** Instrument Sans (400, 500, 600) – UI-Texte
- **Serif:** Cormorant Garamond – Überschriften & Logo

**Schriftarten-Hosting:** Bunny Fonts (via Laravel Vite Plugin)

---

## Tests

### Teststruktur

```
tests/
├── TestCase.php                          # Basis-Testcase
├── Feature/
│   ├── LichmomentTest.php                # 30+ Feature-Tests
│   │   ├── Landing Page (5 Tests)
│   │   ├── Static Pages (2 Tests)
│   │   ├── Admin Auth (6 Tests)
│   │   ├── Project CRUD (7 Tests)
│   │   ├── Photo Upload (3 Tests)
│   │   ├── Folder CRUD (2 Tests)
│   │   ├── Share Links (6 Tests)
│   │   ├── Project Settings (2 Tests)
│   │   └── Delete Operations (5 Tests)
│   └── SecurityTest.php                  # 25+ Security-Tests
│       ├── CSRF Protection
│       ├── SQL Injection
│       ├── XSS Prevention
│       ├── Authorization (Guest-Blockierung)
│       ├── Input Validation
│       ├── Share Link Security
│       ├── Session Fixation
│       ├── Rate Limiting
│       ├── Cover Image Upload
│       ├── Share Password Session
│       ├── Bulk Delete
│       └── Site Meta
└── Unit/
    ├── AdminAuthMiddlewareTest.php
    ├── ModelsTest.php
    ├── PhotoModelTest.php
    └── ProjectModelTest.php
```

### Tests ausführen

```bash
# Alle Tests im Container
docker exec lichtmoment-app php artisan test --testdox

# Nur Feature-Tests
docker exec lichtmoment-app php artisan test --testsuite=Feature

# Nur Unit-Tests
docker exec lichtmoment-app php artisan test --testsuite=Unit

# Einzelne Testdatei
docker exec lichtmoment-app php artisan test tests/Feature/SecurityTest.php

# Mit Coverage (lokal, falls Xdebug installiert)
php artisan test --coverage
```

**Test-Konfiguration (phpunit.xml):**
- SQLite In-Memory-Datenbank für Tests
- Bcrypt auf 4 Runden beschleunigt
- Array-Cache und Array-Session (kein DB/File nötig)
- Queue: sync (kein Worker nötig)

**Test-Status:** Alle Tests passen.

---

## Anpassungen

### Fotografen-Daten

Die Kontaktdaten werden in `app/Http/Controllers/HomeController.php` definiert:

```php
$photographer = [
    'name' => 'Markus Knuth',
    'tagline' => 'Hochzeitsfotografie mit Seele',
    'bio' => ['...'],
    'phone' => '+49 171 234 56 78',
    'email' => 'info@lichtmoment.de',
];
```

### Farbschema

Alle Farben als CSS-Variablen in `resources/css/app.css`. Primärfärbung: `--color-gold-400: #c9a94e`.

### Logo

Das Logo ist aktuell als Text ("Lichtmoment") in `home.blade.php` gerendert. Für ein SVG-Logo die Hero-Sektion anpassen.

### Impressum & Datenschutz

Die Platzhalter-Daten in `resources/views/impressum.blade.php` und `resources/views/datenschutz.blade.php` müssen an die echten Daten angepasst werden.

---

## Wartung & Troubleshooting

### Nach Container-Rebuild

**Kritisch:** Die `.env` wird nicht persistiert. Nach jedem Rebuild:

```bash
# .env neu erstellen (APP_KEY muss gesetzt sein!)
docker exec lichtmoment-app php -r "
    file_put_contents('.env', 'APP_NAME=Lichtmoment\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=https://foto.dreyjey.ddnss.de\nAPP_KEY=base64:DEIN_KEY_HIER\nDB_CONNECTION=sqlite\nDB_DATABASE=/var/www/html/database/database.sqlite\n');
"
```

### Vite Build-Dateien aktualisieren

Nach CSS/JS-Änderungen:

```bash
npm run build
docker cp public/build/manifest.json lichtmoment-app:/var/www/html/public/build/manifest.json
docker cp public/build/assets/ lichtmoment-app:/var/www/html/public/build/assets/
```

**Wichtig:** `docker cp` für JS-Dateien kann bei Sonderzeichen problematisch sein. Alternative: Base64-Encoding + PHP `file_put_contents()`.

### Häufige Probleme

| Problem | Ursache | Lösung |
|---|---|---|
| OPcache zeigt alte Dateien | PHP OPcache cached Blade-Views | Container mit `--force-recreate` neu starten |
| Bilder nicht sichtbar | Falscher Storage-Pfad | Prüfen: `public/storage/projects/` (nicht symlinked) |
| Blade-Template-Fehler | Ternary ohne false-Branch | Immer `condition ? 'a' : 'b'` verwenden |
| Delete-Buttons nicht sichtbar | `opacity-0 group-hover` | Buttons immer sichtbar machen (nicht hover-abhängig) |
| JS-Fehler im Admin | CSRF-Token fehlt | JS in `@push('scripts')` NICHT in `try/catch` wrappen |
| SQLite-Permissions | `www-data` muss Besitzer sein | `chown www-data:www-data database/database.sqlite` |

---

## Lizenz

MIT License – siehe [LICENSE](LICENSE) für Details.
