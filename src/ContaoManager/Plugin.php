<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoInstagramBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoInstagramBundle\ContaoInstagramBundle;

/**
 * Meldet das Bundle beim Contao Manager an.
 *
 * Ohne diese Klasse taucht das Bundle nicht im Kernel auf, weil Contao die
 * Bundle-Liste aus den Plugins aller installierten Pakete zusammensetzt.
 */
class Plugin implements BundlePluginInterface
{
	/**
	 * Meldet das Bundle beim Kernel an.
	 *
	 * Das Bundle wird nach dem Contao-Core geladen, damit dessen DCA-Datei
	 * tl_content bereits vorliegt, wenn Palette und Felder ergänzt werden.
	 * Die Palette prüft dabei, ob die Kernfelder „guests" (nur Contao 4.13)
	 * und „title" (nur Contao 5) vorhanden sind.
	 *
	 * @param ParserInterface $parser Wird nicht ausgewertet, weil das Bundle
	 *                                keine Konfigurationsdateien parsen lässt
	 *
	 * @return array<int, BundleConfig> Die Konfiguration dieses einen Bundles
	 */
	public function getBundles(ParserInterface $parser): array
	{
		return array(
			BundleConfig::create(ContaoInstagramBundle::class)
				->setLoadAfter(array(ContaoCoreBundle::class)),
		);
	}
}
