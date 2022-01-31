<?php
class AhpHierginiDE {
	
// Errors
public $err = array(
	"pExc"		=>	"Max. Anzahl der Projekte überschritten, Daten können nicht gespeichert werden.
								 Bitte abbrechen und einige deiner Projekte löschen. ",
	"noSc"		=>	"Bitte Projektkennung eingeben. ",
	"noName"	=>	"Bitte Namen eingeben. ",
	"pwcCompl"=>	"Paarvergleiche unter dem Namen <span class='var'>%s</span> sind komplett.",
	"hDefP"		=>	"Die Hierarchie hat keine definierten Prioritäten. Projekt kann nicht aktualisiert werden.",
	"unknw"		=>	"Unbekannter Fehler - owner: %s retFlg: %g"
);

public $titles = array(
	"pageTitle" =>	"AHP Eingabe Formular AHP-OS",
	"h1Title" 	=>	"<h1>AHP Eingabe Formular</h1>",
	"subTitle1" =>	"AHP-OS Teilnehmer Eingabe",
	"subTitle2"	=>	"Speichern/Aktualisieren AHP Projekt",
	"subTitle3"	=>	"Paarvergleich ",
	"h3Pwc"			=>	"<h3>Paarvergleich <span class='var'>%s</span></h3>",
	"h3Res"			=>	"<h3 align='center'>Resultierende Gewichtung</h3>",
	"h2siMnu"		=>	"<h2>AHP Projekt Eingabe Menü</h2>"
);

// Messages
public $msg = array(
	"nProj"		=>	"Klicke auf \"Weiter\" zum speichern",
	"pMod"		=>	"Vorhandenes Projekt wird modifiziert und überschrieben."
);

// Information
public $info= array(
	"intro"		=>	"<div class='entry-content'><p style='text-align:justify;'>
								AHP-OS ist ein web-basiertes Programm zur Unterstützung rationaler 
								Eintscheidungsfindung, basierend auf dem <i>Analytischen Hierarchie Prozess</i> (AHP). 
								Als ausgewählter Teilnehmer gebe bitte die Projektkennung und den Teilnehmernamen ein,
								und beantworte die Paarvergleiche, um deinen Input in die Gruppenentscheidung
								einfließen zu lassen. Vielen Dank!
								</p></div>",
	"act1"		=>	"Neues Projekt. Projektkennung <span class='var'>%s</span>. ",
	"act2"		=>	"Projekt Aenderung. ",
	"act3"		=>	"Das Projekt hat %g Teilnehmer. ",
	"ok"			=>	"<p class='msg'>Ok. Klicke auf \"Weiter\" zur Fortsetzung.</p>",
	"siSc"		=>	"Zur Teilnahme an der Gruppensitzung bitte Projektkennung eingeben.",
	"siNm1"		=>	"<a href='%s?logout'>Logout</a> als Projektautor, um einen anderen Teilnehmernamen einzugeben.",
	"siNm2"		=>	"Dein Name, wie er in der Gruppenauswertung angezeigt wird (3 - 25 Alphanum. Zeichen).",
	"pName"		=>	"AHP Projekt Name:",
	"pStat"		=>	"Projekt Status:",
	"pDescr"	=>	"Projekt Kurzbeschreibung:",
	"descr"		=>	"</br><small>Dieser Text wird den Teinehmern der Gruppensitzung angezeigt (max. 400 Zeichen). 
								Du kannst HTML tags, wie &lt;em&gt; or &lt;font&gt; nutzen, um Textstellen hervorzuheben.</small>"
);

// Menu and buttons
public $mnu = array(
	"lgd1"		=>	"Projektkennung u. Teilnehmer Name",
	"lgd2"		=>	"Projekt Eingabe Menü",
	"sc"			=>	"Projektkennung:",
	"nm"			=>	"Teilnehmer Name:",
	"btn1"		=>	"Weiter",
	"btn2"		=>	"Eingabe prüfen",
	"btn3"		=>	"Gruppen Ergebnis",
	"btn4"		=>	"Zurücksetzen",
	"btn5"		=>	"Abbrechen"
);

}