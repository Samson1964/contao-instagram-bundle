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
 * Palette des Inhaltselements
 *
 * Zwei Kernfelder unterscheiden sich zwischen den Fassungen und werden nur
 * eingetragen, wenn der Kern sie definiert hat: „guests" (nur Contao 4.13)
 * und „title" (nur Contao 5). Die DCA-Datei des Bundles wird nach der des
 * Kerns geladen (siehe ContaoManager\Plugin). Der Aufbau läuft in einer
 * anonymen Funktion, damit keine Hilfsvariablen im Dateibereich
 * zurückbleiben — Contao 5 fasst alle DCA-Dateien einer Tabelle in einer
 * Cache-Datei zusammen.
 */
(static function (): void
{
	$hatGuests = isset($GLOBALS['TL_DCA']['tl_content']['fields']['guests']);
	$hatTitle = isset($GLOBALS['TL_DCA']['tl_content']['fields']['title']);

	$GLOBALS['TL_DCA']['tl_content']['palettes']['instagram'] = '{type_legend},type' . ($hatTitle ? ',title' : '') . ',headline'
		. ';{instagram_legend},instagramUrl,instagramCaption'
		. ';{template_legend:hide},customTpl'
		. ';{protected_legend:hide},protected'
		. ';{expert_legend:hide},' . ($hatGuests ? 'guests,' : '') . 'cssID'
		. ';{invisible_legend:hide},invisible,start,stop';
})();

/**
 * Felder
 *
 * instagramUrl nimmt den Link zu einem Beitrag, Reel oder IGTV-Video auf.
 * Die Prüfung und das Vereinheitlichen übernimmt der save_callback aus der
 * services.yaml (PostLinkListener). „decodeEntities" verhindert, dass Contao
 * das & in angehängten Parametern als &amp; an den Rückruf reicht.
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['instagramUrl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['instagramUrl'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities'=>true, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['instagramCaption'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['instagramCaption'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);
