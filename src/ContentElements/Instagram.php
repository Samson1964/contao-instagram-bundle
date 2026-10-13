<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt ein Inhaltselement bereit, das einen einzelnen
 * Instagram-Beitrag über die Einbettung von Instagram anzeigt — lauffähig
 * unter Contao 4.13 und Contao 5.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoInstagramBundle\ContentElements;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoInstagramBundle\Instagram\PostLink;

/**
 * Inhaltselement „Instagram".
 *
 * Gibt einen einzelnen Instagram-Beitrag in der Form aus, die Instagram unter
 * „Einbetten" selbst erzeugt: ein blockquote mit der Adresse des Beitrags,
 * das das Skript embed.js beim Laden der Seite durch den Beitrag ersetzt. Der
 * Beitrag erscheint sofort, ohne vorgeschalteten Klick.
 *
 * Der Klassenname der Elternklasse steht voll qualifiziert da: Contao 5 legt
 * keine globalen Klassenaliasse mehr an.
 */
class Instagram extends ContentElement
{
	/**
	 * Name des Frontend-Templates ohne Dateiendung.
	 *
	 * Ein Name ohne Schrägstrich gilt in Contao 5 als Legacy-Template, die
	 * vorhandene .html5-Datei bedient damit beide Fassungen.
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_instagram';

	/**
	 * Erzeugt die Ausgabe des Elements.
	 *
	 * Im Backend erscheint statt des Beitrags die Platzhalterdarstellung
	 * be_wildcard mit dem eingetragenen Link — die Elementliste lädt dann
	 * keine fremden Inhalte. Ohne gültigen Link bleibt das Element im
	 * Frontend leer; das kommt nur bei Datensätzen vor, die am save_callback
	 * vorbei geschrieben wurden.
	 *
	 * @return string Das Markup des Elements oder eine leere Zeichenkette
	 */
	public function generate()
	{
		if ($this->isBackendRequest())
		{
			$template = new BackendTemplate('be_wildcard');
			$template->title = StringUtil::specialchars((string) $this->instagramUrl);

			return $template->parse();
		}

		if (null === PostLink::permalink((string) $this->instagramUrl))
		{
			return '';
		}

		return parent::generate();
	}

	/**
	 * Füllt das Template mit der Adresse des Beitrags und meldet das Skript an.
	 *
	 * Das Skript kommt über $GLOBALS['TL_BODY'] ans Ende der Seite. Der feste
	 * Schlüssel sorgt dafür, dass es auch bei mehreren Elementen auf einer
	 * Seite nur einmal eingebunden wird; ein Durchlauf von embed.js wandelt
	 * alle Beiträge der Seite um.
	 */
	protected function compile(): void
	{
		$this->Template->permalink = StringUtil::specialchars((string) PostLink::permalink((string) $this->instagramUrl));
		$this->Template->embedVersion = PostLink::EMBED_VERSION;
		$this->Template->captioned = (bool) $this->instagramCaption;
		$this->Template->linkText = $GLOBALS['TL_LANG']['MSC']['instagramAnsehen'] ?? 'Diesen Beitrag auf Instagram ansehen';

		$GLOBALS['TL_BODY']['contaoinstagram'] = '<script async src="'.PostLink::EMBED_SCRIPT.'"></script>';
	}

	/**
	 * Stellt fest, ob das Element gerade für die Backend-Vorschau gerendert wird.
	 *
	 * Der frühere Vergleich mit der Konstante TL_MODE ist unter Contao 5 nicht
	 * mehr möglich; der Dienst contao.routing.scope_matcher ist in beiden
	 * Fassungen öffentlich. Ohne laufende Anfrage — etwa auf der
	 * Kommandozeile — gilt das Element als Frontend.
	 *
	 * @return bool true in der Backend-Vorschau, sonst false
	 */
	private function isBackendRequest(): bool
	{
		$container = System::getContainer();
		$request = $container->get('request_stack')->getCurrentRequest();

		return null !== $request && $container->get('contao.routing.scope_matcher')->isBackendRequest($request);
	}
}
