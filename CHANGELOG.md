# Instagram-Bundle Changelog

## Version 1.0.0 (2026-10-13)

* Add: Inhaltselement „Instagram" in der Gruppe der Medienelemente. Zeigt einen
  einzelnen Beitrag (Foto, Reel oder Video) sofort über die offizielle Einbettung von
  Instagram an (`blockquote.instagram-media` plus `embed.js`), ohne Facebook-App und
  ohne Zugriffsschlüssel.
* Add: Feld `instagramUrl` in `tl_content` für den Link zum Beitrag. Ein
  `save_callback` vereinheitlicht ihn (ohne Kontonamen und ohne
  Nachverfolgungsparameter) und weist Teilen-Links, Profile, Storys und fremde
  Adressen ab.
* Add: Feld `instagramCaption`, das die Bildunterschrift des Beitrags einblendet.
* Add: Das Skript `embed.js` wird je Seite nur einmal eingebunden, auch bei mehreren
  Elementen.
* Add: Backend-Vorschau über `be_wildcard` mit dem eingetragenen Link statt des
  Beitrags.
* Add: Deutsche und englische Beschriftungen.
* Add: Lauffähig unter Contao 4.13 und Contao 5 aus derselben Fassung; die Palette
  berücksichtigt die nur in 4.13 (`guests`) beziehungsweise nur in 5 (`title`)
  vorhandenen Kernfelder. Geprüft gegen Contao 4.13.58 und 5.7.7 mit PHP 8.4.24.
* Add: Unit-Tests, Prüfstand `tests/pruefstand.php`, README und Metadaten für den
  Contao Manager.
