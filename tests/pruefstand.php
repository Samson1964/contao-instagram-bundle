<?php

declare(strict_types=1);

/*
 * Prüfstand für schachbulle/contao-instagram-bundle.
 *
 * Bootet den Contao-Kernel einer Testinstallation, prüft die Anmeldung des
 * Inhaltselements und des save_callbacks, lädt die DCA von tl_content und
 * rendert das Element im Frontend- und im Backend-Kontext.
 *
 * Aufruf: php tests/pruefstand.php <pfad-zur-installation>
 *
 * Voraussetzung: Das Bundle ist in der Installation als Pfad-Paket
 * eingebunden und der Container-Cache (var/cache) wurde danach geleert.
 */

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerBundle\HttpKernel\ContaoKernel;
use Contao\System;
use Schachbulle\ContaoInstagramBundle\ContentElements\Instagram;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Request;

$root = $argv[1] ?? null;

if (!$root || !is_dir($root))
{
	fwrite(STDERR, "Pfad zur Installation fehlt\n");
	exit(1);
}

require $root . '/vendor/autoload.php';

$fehler = 0;

/**
 * Gibt eine Prüfzeile aus und zählt Fehlschläge.
 *
 * @param string $name Bezeichnung der Prüfung
 * @param bool   $ok   Ergebnis
 * @param string $info Zusatzangabe, die in Klammern angehängt wird
 */
function melde(string $name, bool $ok, string $info = ''): void
{
	global $fehler;

	if (!$ok)
	{
		++$fehler;
	}

	printf("%-62s %s%s\n", $name, $ok ? 'OK' : 'FEHLER', '' !== $info ? '  (' . $info . ')' : '');
}

/**
 * Erzeugt das Inhaltselement aus einer Datensatzzeile und rendert es.
 *
 * @param array<string, mixed> $zeile Felder des Datensatzes
 *
 * @return string Das erzeugte Markup; bei einer Ausnahme deren Meldung,
 *                eingeleitet mit "AUSNAHME:"
 */
function rendere(array $zeile): string
{
	$model = new ContentModel();
	$model->setRow($zeile + array('id' => 1, 'pid' => 1, 'ptable' => 'tl_article', 'type' => 'instagram', 'headline' => '', 'cssID' => '', 'invisible' => '', 'protected' => '', 'instagramCaption' => ''));

	try
	{
		return (new Instagram($model, 'main'))->generate();
	}
	catch (Throwable $e)
	{
		return 'AUSNAHME: ' . get_class($e) . ': ' . $e->getMessage();
	}
}

putenv('APP_ENV=prod');
$_SERVER['APP_ENV'] = 'prod';

$kernel = ContaoKernel::fromInput($root, new ArrayInput(array()));
$kernel->boot();
$container = $kernel->getContainer();

echo 'Contao ' . ContaoCoreBundle::getVersion() . ', PHP ' . PHP_VERSION . "\n";
echo str_repeat('-', 78) . "\n";

melde('Bundle im Kernel angemeldet', isset($kernel->getBundles()['ContaoInstagramBundle']));

// Framework im Frontend-Kontext hochfahren
$feRequest = Request::create('http://localhost/');
$feRequest->attributes->set('_scope', 'frontend');
$container->get('request_stack')->push($feRequest);
$container->get('contao.framework')->initialize();

melde('Element instagram in TL_CTE[media]', ($GLOBALS['TL_CTE']['media']['instagram'] ?? null) === Instagram::class);

// DCA und Sprache laden
Controller::loadDataContainer('tl_content');
System::loadLanguageFile('default', 'de');
System::loadLanguageFile('tl_content', 'de');

$felder = $GLOBALS['TL_DCA']['tl_content']['fields'];
$palette = $GLOBALS['TL_DCA']['tl_content']['palettes']['instagram'] ?? '';
$fehlend = array();

foreach (preg_split('/[;,]/', preg_replace('/\{[^}]+\}/', '', $palette)) as $feld)
{
	$feld = trim($feld);

	if ('' !== $feld && !isset($felder[$feld]))
	{
		$fehlend[] = $feld;
	}
}

melde('Palette instagram gesetzt', '' !== $palette);
melde('Alle Palettenfelder definiert', array() === $fehlend, implode(', ', $fehlend));
melde('guests nur wenn Feld existiert', isset($felder['guests']) === (false !== strpos($palette, 'guests')));
melde('title nur wenn Feld existiert', isset($felder['title']) === (false !== strpos($palette, ',title')));
melde('Feld instagramUrl mit SQL-Definition', !empty($felder['instagramUrl']['sql']));
melde('Feld instagramCaption mit SQL-Definition', !empty($felder['instagramCaption']['sql']));

// save_callback: Der Compiler-Pass des Kerns trägt den getaggten Dienst ein
$rueckruf = null;

foreach ($felder['instagramUrl']['save_callback'] ?? array() as $eintrag)
{
	if (is_array($eintrag) && 'schachbulle_contao_instagram.listener.post_link' === $eintrag[0])
	{
		$rueckruf = $eintrag;
	}
}

melde('save_callback an instagramUrl eingetragen', null !== $rueckruf);

if (null !== $rueckruf)
{
	$dienst = System::importStatic($rueckruf[0]);
	melde('save_callback vereinheitlicht den Link', 'https://www.instagram.com/p/fA9uwTtkSN/' === $dienst->{$rueckruf[1]}('https://www.instagram.com/konto/p/fA9uwTtkSN/?igsh=abc', null));

	try
	{
		$dienst->{$rueckruf[1]}('https://www.instagram.com/share/p/BAbcDeFgHi/', null);
		melde('save_callback weist Teilen-Link ab', false);
	}
	catch (RuntimeException $e)
	{
		melde('save_callback weist Teilen-Link ab', $e->getMessage() === $GLOBALS['TL_LANG']['tl_content']['instagramFehlerLink']);
	}
}

// Sprachschlüssel
$fehlendeLabels = array();

foreach (array('instagram_legend', 'instagramUrl', 'instagramCaption', 'instagramFehlerLink') as $key)
{
	if (empty($GLOBALS['TL_LANG']['tl_content'][$key]))
	{
		$fehlendeLabels[] = 'tl_content.' . $key;
	}
}

if (empty($GLOBALS['TL_LANG']['CTE']['instagram']) || empty($GLOBALS['TL_LANG']['MSC']['instagramAnsehen']))
{
	$fehlendeLabels[] = 'default';
}

melde('Alle Beschriftungen vorhanden', array() === $fehlendeLabels, implode(', ', $fehlendeLabels));

// Frontend
$zeile = array(
	'instagramUrl'     => 'https://www.instagram.com/p/fA9uwTtkSN/',
	'instagramCaption' => '1',
	'headline'         => serialize(array('unit' => 'h2', 'value' => 'Unser Beitrag')),
	'cssID'            => serialize(array('meine-id', 'meine-klasse')),
);

$html = rendere($zeile);
melde('Element rendert ohne Fehler', 0 !== strpos($html, 'AUSNAHME'), 0 === strpos($html, 'AUSNAHME') ? $html : '');
melde('Behälter mit Klassen und ID', false !== strpos($html, '<div class="ce_instagram meine-klasse block" id="meine-id"'));
melde('Überschrift ausgegeben', false !== strpos($html, '<h2>Unser Beitrag</h2>'));
melde('blockquote mit Adresse, Fassung und Bildunterschrift', false !== strpos($html, '<blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/fA9uwTtkSN/" data-instgrm-version="14" data-instgrm-captioned>'));
melde('Ersatzlink im blockquote', false !== strpos($html, '<a href="https://www.instagram.com/p/fA9uwTtkSN/" target="_blank" rel="noopener">Diesen Beitrag auf Instagram ansehen</a>'));
melde('Skript einmal in TL_BODY angemeldet', '<script async src="https://www.instagram.com/embed.js"></script>' === ($GLOBALS['TL_BODY']['contaoinstagram'] ?? null));

echo "\n--- Markup ---\n" . trim($html) . "\n--- Ende ---\n\n";

melde('Ohne Bildunterschrift: Attribut fehlt', false === strpos(rendere(array('instagramCaption' => '') + $zeile), 'data-instgrm-captioned'));
melde('Ungültiger Link: leere Ausgabe', '' === rendere(array('instagramUrl' => 'https://www.instagram.com/share/p/BAbcDeFgHi/')));

// Backend-Vorschau
$beRequest = Request::create('http://localhost/contao');
$beRequest->attributes->set('_scope', 'backend');
$container->get('request_stack')->push($beRequest);

$htmlBe = rendere($zeile);
melde('Backend: rendert ohne Fehler', 0 !== strpos($htmlBe, 'AUSNAHME'), 0 === strpos($htmlBe, 'AUSNAHME') ? $htmlBe : '');
melde('Backend: Platzhalter mit Link, kein blockquote', false !== strpos($htmlBe, 'instagram.com/p/fA9uwTtkSN/') && false === strpos($htmlBe, '<blockquote'));

$container->get('request_stack')->pop();

echo str_repeat('-', 78) . "\n";
echo 0 === $fehler ? "Alle Prüfungen bestanden.\n" : $fehler . " Prüfung(en) fehlgeschlagen.\n";

exit(0 === $fehler ? 0 : 1);
