<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

use Schachbulle\ContaoInstagramBundle\ContentElements\Instagram;

/**
 * Inhaltselement
 *
 * Das Element steht in der Kerngruppe „media" neben YouTube und Vimeo. Die
 * klassische Anmeldung über $GLOBALS['TL_CTE'] mit einem Klassennamen wird
 * von beiden Contao-Fassungen ausgewertet (ContentElement::findClass).
 */
$GLOBALS['TL_CTE']['media']['instagram'] = Instagram::class;
