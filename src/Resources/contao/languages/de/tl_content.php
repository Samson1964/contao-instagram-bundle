<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Beschriftungen der Instagram-Felder
 */
$GLOBALS['TL_LANG']['tl_content']['instagram_legend'] = 'Instagram-Einstellungen';
$GLOBALS['TL_LANG']['tl_content']['instagramUrl'] = array('Link zum Beitrag', 'Der Link zu einem Instagram-Beitrag, Reel oder Video (https://www.instagram.com/p/…/ oder …/reel/…/). Der Beitrag muss öffentlich sein. Angehängte Parameter werden beim Speichern entfernt.');
$GLOBALS['TL_LANG']['tl_content']['instagramCaption'] = array('Bildunterschrift anzeigen', 'Den Text des Beitrags unter dem Bild oder Video einblenden.');

/**
 * Fehlermeldung beim Speichern des Links
 */
$GLOBALS['TL_LANG']['tl_content']['instagramFehlerLink'] = 'Bitte den Link zu einem Instagram-Beitrag eintragen (https://www.instagram.com/p/…/ oder …/reel/…/). Teilen-Links (…/share/…) enthalten den Beitrag nicht und werden nicht unterstützt.';
