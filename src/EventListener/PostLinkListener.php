<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoInstagramBundle\EventListener;

use Schachbulle\ContaoInstagramBundle\Instagram\PostLink;

/**
 * Prüft beim Speichern den Instagram-Link eines Inhaltselements.
 *
 * Der Rückruf sorgt dafür, dass in tl_content.instagramUrl immer die
 * einheitliche Adresse eines Beitrags steht — ohne Kontonamen im Pfad und
 * ohne die Nachverfolgungsparameter, die Instagram beim Kopieren anhängt.
 */
class PostLinkListener
{
	/**
	 * Prüft und vereinheitlicht den eingetragenen Link (save_callback).
	 *
	 * Der Rückruf arbeitet ohne Netzwerkzugriff: Ob es den Beitrag gibt und
	 * ob er öffentlich ist, zeigt sich erst im Frontend, wo Instagram bei
	 * gelöschten oder privaten Beiträgen einen eigenen Hinweis ausgibt.
	 *
	 * @param mixed $value Der eingetragene Wert; Contao liefert eine Zeichenkette
	 * @param mixed $dc    Der Data Container; wird nicht ausgewertet
	 *
	 * @return string Die einheitliche Adresse des Beitrags, oder eine leere
	 *                Zeichenkette bei leerer Eingabe
	 *
	 * @throws \RuntimeException Wenn die Eingabe keinen einzelnen Beitrag
	 *                           bezeichnet. Contao zeigt die Meldung am Feld
	 *                           an und speichert nicht.
	 */
	public function onSave($value, $dc = null): string
	{
		$value = trim((string) $value);

		if ('' === $value)
		{
			return '';
		}

		$permalink = PostLink::permalink($value);

		if (null === $permalink)
		{
			$text = $GLOBALS['TL_LANG']['tl_content']['instagramFehlerLink'] ?? null;

			// Der deutsche Ersatztext greift, wenn die Sprachdatei nicht
			// geladen ist — etwa im Unit-Test.
			throw new \RuntimeException(\is_string($text) && '' !== $text ? $text : 'Bitte den Link zu einem Instagram-Beitrag eintragen (https://www.instagram.com/p/…/ oder …/reel/…/). Teilen-Links (…/share/…) enthalten den Beitrag nicht und werden nicht unterstützt.');
		}

		return $permalink;
	}
}
