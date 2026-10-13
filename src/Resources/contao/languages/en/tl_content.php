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
$GLOBALS['TL_LANG']['tl_content']['instagram_legend'] = 'Instagram settings';
$GLOBALS['TL_LANG']['tl_content']['instagramUrl'] = array('Post link', 'The link to an Instagram post, reel or video (https://www.instagram.com/p/…/ or …/reel/…/). The post must be public. Appended parameters are removed on save.');
$GLOBALS['TL_LANG']['tl_content']['instagramCaption'] = array('Show caption', 'Show the text of the post below the image or video.');

/**
 * Fehlermeldung beim Speichern des Links
 */
$GLOBALS['TL_LANG']['tl_content']['instagramFehlerLink'] = 'Please enter the link to an Instagram post (https://www.instagram.com/p/…/ or …/reel/…/). Share links (…/share/…) do not contain the post and are not supported.';
