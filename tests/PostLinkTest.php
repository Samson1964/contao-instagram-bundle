<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoInstagramBundle\Tests;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoInstagramBundle\EventListener\PostLinkListener;
use Schachbulle\ContaoInstagramBundle\Instagram\PostLink;

/**
 * Prüft das Auswerten von Instagram-Links und den save_callback.
 */
class PostLinkTest extends TestCase
{
	/**
	 * Prüft, dass alle unterstützten Schreibweisen auf die einheitliche
	 * Adresse abgebildet werden und alles andere null ergibt.
	 *
	 * Die Fälle laufen in einer Schleife statt über einen Data Provider:
	 * PHPUnit 9 kennt dafür nur die Anmerkung im Kommentar, PHPUnit 11 meldet
	 * sie als veraltet und verlangt ein Attribut.
	 */
	public function testPermalink(): void
	{
		$faelle = array(
			'Beitrag'                 => array('https://www.instagram.com/p/fA9uwTtkSN/', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'ohne Schrägstrich'       => array('https://www.instagram.com/p/fA9uwTtkSN', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'mit Parametern'          => array('https://www.instagram.com/p/DAb-c_d1234/?igsh=MWx5&utm_source=ig_web_copy_link', 'https://www.instagram.com/p/DAb-c_d1234/'),
			'mit Leerraum'            => array("  https://www.instagram.com/p/fA9uwTtkSN/\n", 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'Reel'                    => array('https://www.instagram.com/reel/C8abcDEfgh1/', 'https://www.instagram.com/reel/C8abcDEfgh1/'),
			'Reel in der Mehrzahl'    => array('https://www.instagram.com/reels/C8abcDEfgh1/', 'https://www.instagram.com/reel/C8abcDEfgh1/'),
			'IGTV'                    => array('https://www.instagram.com/tv/B1abcDEfgh1/', 'https://www.instagram.com/tv/B1abcDEfgh1/'),
			'mit Kontonamen'          => array('https://www.instagram.com/schach.bund_1/p/fA9uwTtkSN/', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'Reel mit Kontonamen'     => array('https://www.instagram.com/schachbund/reel/C8abcDEfgh1/?hl=de', 'https://www.instagram.com/reel/C8abcDEfgh1/'),
			'ohne www'                => array('http://instagram.com/p/fA9uwTtkSN/', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'Kurzdomäne'              => array('https://instagr.am/p/fA9uwTtkSN/', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'Einbettungsadresse'      => array('https://www.instagram.com/p/fA9uwTtkSN/embed/captioned/', 'https://www.instagram.com/p/fA9uwTtkSN/'),
			'Teilen-Link'             => array('https://www.instagram.com/share/p/BAbcDeFgHi/', null),
			'Teilen-Link eines Reels' => array('https://www.instagram.com/share/reel/BAbcDeFgHi/', null),
			'Profil'                  => array('https://www.instagram.com/schachbund/', null),
			'Story'                   => array('https://www.instagram.com/stories/schachbund/3141592653589793238/', null),
			'Tonstück'                => array('https://www.instagram.com/reels/audio/1234567890/', null),
			'fremder Rechner'         => array('https://example.com/p/fA9uwTtkSN/', null),
			'täuschender Rechner'     => array('https://notinstagram.com/p/fA9uwTtkSN/', null),
			'ohne Schema'             => array('www.instagram.com/p/fA9uwTtkSN/', null),
			'anderes Schema'          => array('javascript://www.instagram.com/p/fA9uwTtkSN/', null),
			'Markup im Kurzcode'      => array('https://www.instagram.com/p/"><script>/', null),
			'bloßer Kurzcode'         => array('fA9uwTtkSN', null),
			'leer'                    => array('', null),
		);

		foreach ($faelle as $name => $fall)
		{
			self::assertSame($fall[1], PostLink::permalink($fall[0]), $name);
		}
	}

	/**
	 * Prüft, dass der save_callback den Link vereinheitlicht und eine leere
	 * Eingabe durchlässt (die Pflichtfeldprüfung übernimmt Contao).
	 */
	public function testSaveCallbackVereinheitlicht(): void
	{
		$listener = new PostLinkListener();

		self::assertSame('', $listener->onSave('  '));
		self::assertSame('https://www.instagram.com/reel/C8abcDEfgh1/', $listener->onSave('https://www.instagram.com/schachbund/reel/C8abcDEfgh1/?igsh=abc'));
	}

	/**
	 * Prüft, dass der save_callback einen Teilen-Link mit einer Meldung
	 * abweist, die den Grund nennt.
	 */
	public function testSaveCallbackWeistTeilenLinkAb(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Teilen-Links');

		(new PostLinkListener())->onSave('https://www.instagram.com/share/p/BAbcDeFgHi/');
	}
}
