# Contao Instagram Bundle

Inhaltselement für Contao 4.13 und Contao 5, das einen einzelnen Instagram-Beitrag
(Foto, Reel oder Video) anzeigt. Es genügt der Link zum Beitrag. Eine Facebook-App
oder ein Zugriffsschlüssel ist nicht nötig.

## Voraussetzungen

* Contao 4.13 oder Contao 5 (geprüft mit 4.13.58 und 5.7.7)
* PHP 7.4 oder neuer (geprüft mit PHP 8.4.24)
* Der Beitrag muss öffentlich sein.

## Installation

```bash
composer require schachbulle/contao-instagram-bundle
```

Danach die Datenbank aktualisieren (Contao Manager oder `contao:migrate`). Das Bundle
ergänzt in `tl_content` die Felder `instagramUrl` und `instagramCaption`.

## Verwendung

Das Inhaltselement **Instagram** steht in der Typauswahl in der Gruppe
„Medienelemente“ neben YouTube und Vimeo.

| Feld | Bedeutung |
| --- | --- |
| Link zum Beitrag | Link der Form `https://www.instagram.com/p/…/`, `…/reel/…/` oder `…/tv/…/`, auch mit Kontonamen davor |
| Bildunterschrift anzeigen | Blendet den Text des Beitrags unter dem Bild oder Video ein |

Beim Speichern wird der Link vereinheitlicht: Der Kontoname im Pfad und angehängte
Parameter (`?igsh=…`, `?utm_source=…`) fallen weg, gespeichert wird
`https://www.instagram.com/p/<Kurzcode>/`.

**Nicht unterstützt** werden Teilen-Links (`https://www.instagram.com/share/…`): Sie
enthalten den Kurzcode des Beitrags nicht. Den richtigen Link liefert Instagram im
Browser in der Adresszeile des geöffneten Beitrags oder über „Link kopieren“. Ebenfalls
abgewiesen werden Profile, Storys und fremde Adressen.

## Ausgabe

Das Element gibt den Beitrag in der Form aus, die Instagram unter „Einbetten“ selbst
erzeugt. Das Skript `embed.js` von Instagram ersetzt das `blockquote` beim Laden der
Seite durch den Beitrag und passt dessen Höhe an; der Beitrag erscheint sofort, ohne
vorgeschalteten Klick:

```html
<div class="ce_instagram block">
  <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/fA9uwTtkSN/" data-instgrm-version="14">
    <a href="https://www.instagram.com/p/fA9uwTtkSN/" target="_blank" rel="noopener">Diesen Beitrag auf Instagram ansehen</a>
  </blockquote>
</div>
```

Das Skript wird je Seite nur einmal eingebunden (am Ende des `body`), auch wenn mehrere
Beiträge auf der Seite stehen. Der Link im `blockquote` bleibt sichtbar, falls das
Skript nicht lädt.

Die Breite bestimmt Instagram selbst (mindestens 326, höchstens 540 Pixel). Im Backend
zeigt die Elementliste statt des Beitrags nur den eingetragenen Link.

### Abstand zum nächsten Element

Das Bundle bringt die Stildatei `bundles/contaoinstagram/instagram.css` mit und bindet
sie je Seite einmal ein. Sie gibt dem Element 20 Pixel Abstand nach unten und nimmt dem
Beitrag dafür die 12 Pixel, die `embed.js` ihm als Inline-Stil mitgibt — sonst kämen
beide zusammen.

Ein anderer Abstand lässt sich im eigenen Stylesheet über eine CSS-Variable einstellen,
der Wert `0` schaltet ihn ab:

```css
.ce_instagram {
    --instagram-abstand: 40px;
}
```

Die Stildatei steht vor denen des Themes; eine eigene Regel
`.ce_instagram { margin-bottom: … }` überschreibt sie deshalb ebenfalls.

### Template

Das Template heißt `ce_instagram` und lässt sich wie üblich im Ordner `templates`
überschreiben oder am Element durch ein eigenes `ce_instagram_…` ersetzen. Es bekommt
`permalink` (für HTML maskiert), `embedVersion`, `captioned` und `linkText`.

## Datenschutz

Der Browser des Besuchers lädt Skript und Beitrag unmittelbar von `instagram.com`;
dabei erfährt Meta mindestens die IP-Adresse und die aufrufende Seite. Eine
vorgeschaltete Einwilligung („Zwei-Klick-Lösung“) bringt das Bundle bewusst nicht mit.
Wer sie braucht, hängt sie über ein eigenes Template oder ein Consent-Werkzeug davor.

## Entwicklung

Unit-Tests (ohne Contao-Installation und ohne Netzwerk):

```bash
composer install --no-plugins
vendor/bin/phpunit
```

Prüfstand gegen eine Contao-Installation, in die das Bundle als Pfad-Paket
eingebunden ist (Container-Cache vorher leeren):

```bash
php tests/pruefstand.php /pfad/zur/contao-installation
```

### Aufbau

| Datei | Aufgabe |
| --- | --- |
| `src/Instagram/PostLink.php` | Bildet Links auf die einheitliche Adresse des Beitrags ab; ohne Contao, ohne Netzwerk |
| `src/EventListener/PostLinkListener.php` | `save_callback` an `tl_content.instagramUrl`: prüft und vereinheitlicht den Link |
| `src/ContentElements/Instagram.php` | Das Inhaltselement (klassisch über `$GLOBALS['TL_CTE']`, läuft so unter 4.13 und 5) |
| `src/Resources/contao/templates/ce_instagram.html5` | Frontend-Template |
| `src/Resources/public/instagram.css` | Abstand zum nächsten Element |

## Lizenz

LGPL-3.0-or-later
