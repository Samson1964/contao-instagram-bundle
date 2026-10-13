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
 * Inhaltselement: Name und Beschreibung für die Typauswahl in tl_content
 */
$GLOBALS['TL_LANG']['CTE']['instagram'] = array('Instagram', 'Displays a single Instagram post.');

/**
 * Text des Links, der stehen bleibt, solange oder falls das Skript von
 * Instagram den Beitrag nicht lädt (etwa bei abgeschaltetem JavaScript)
 */
$GLOBALS['TL_LANG']['MSC']['instagramAnsehen'] = 'View this post on Instagram';
