<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoInstagramBundle\Instagram;

/**
 * Wertet Links zu Instagram-Beiträgen aus.
 *
 * Die Klasse kommt ohne Contao und ohne Netzwerkzugriff aus, damit sie sich
 * mit reinen Unit-Tests prüfen lässt.
 */
final class PostLink
{
	/**
	 * Adresse des Skripts, das jedes blockquote.instagram-media der Seite
	 * durch den iframe des Beitrags ersetzt und dessen Höhe nachführt.
	 */
	public const EMBED_SCRIPT = 'https://www.instagram.com/embed.js';

	/**
	 * Fassung des Einbettungsformats, die Instagram im Attribut
	 * data-instgrm-version erwartet. Entspricht dem Wert, den Instagram
	 * selbst unter „Einbetten" in seinen Code schreibt.
	 */
	public const EMBED_VERSION = '14';

	/**
	 * Bildet einen Link auf die einheitliche Adresse des Beitrags ab.
	 *
	 * Erkannt werden Beiträge (/p/…), Reels (/reel/… und /reels/…) und
	 * IGTV-Videos (/tv/…), jeweils auch mit vorangestelltem Kontonamen
	 * (/konto/p/…). Angehängte Parameter wie ?igsh=… oder ?utm_source=…
	 * fallen weg: Sie dienen nur der Nachverfolgung und haben auf einer
	 * fremden Website nichts verloren.
	 *
	 * Nicht erkannt werden Teilen-Links (/share/…), weil sie den Kurzcode des
	 * Beitrags nicht enthalten, sowie Profile, Storys und Audio-Seiten.
	 *
	 * @param string $eingabe Der im Backend eingetragene Wert
	 *
	 * @return string|null Adresse der Form https://www.instagram.com/p/code/
	 *                     (bzw. /reel/ oder /tv/), oder null wenn die Eingabe
	 *                     keinen einzelnen Beitrag bezeichnet
	 */
	public static function permalink(string $eingabe): ?string
	{
		$teile = parse_url(trim($eingabe));

		if (!\is_array($teile) || !\in_array(strtolower($teile['scheme'] ?? ''), array('http', 'https'), true))
		{
			return null;
		}

		$host = strtolower($teile['host'] ?? '');

		if ('instagram.com' !== $host && '.instagram.com' !== substr($host, -14) && 'instagr.am' !== $host)
		{
			return null;
		}

		// Der Kontoname vor dem Typ ist freigestellt. „share" ist dort
		// ausgeschlossen, weil /share/p/… wie ein Konto namens „share"
		// aussähe; „audio" hinter /reels/ ist die Seite eines Tonstücks.
		if (1 !== preg_match('~^/(?:(?!share/)[A-Za-z0-9._]+/)?(p|reels?|tv)/(?!audio/)([A-Za-z0-9_-]{5,})(?:/|$)~', $teile['path'] ?? '', $treffer))
		{
			return null;
		}

		return 'https://www.instagram.com/'.('reels' === $treffer[1] ? 'reel' : $treffer[1]).'/'.$treffer[2].'/';
	}
}
