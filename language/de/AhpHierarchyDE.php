<?php

class AhpHierarchyDE
{
    public $titles = array(
    "pageTitle" 	=>	"AHP Hierarchie - AHP-OS",
    "h1title" 		=>	"<h1>AHP Hierarchie</h1>",
    "h2subTitle"	=>	"<h2>AHP-OS Entscheidungs Hierarchy</h2>",
    "h4pDescr"		=>	"<h4>Projekt Beschreibung</h4>",
    "h3hInfo"			=>	"\n<h3>Hiearchie Information</h3>",
    "h3Proj"			=>	"<h3>Projekt: <span class= 'var'>%s</span></h3>",
    "h2ieHier"		=>	"<h2>Hierarchie - Eingabe/Editieren</h2>"
);
    public $err = array(
    "giH"			=>	"Fehler in der Hierarchie Definition"
);
    public $msg = array(
    "lgin"		=>	"<span class='msg'>Für volle Funktionalität bitte registrieren und einloggen.</span>",
    "pInp"		=>	"<p class='msg'>Eingabe für Projekt <span class='var'>%s</span></p>",
    "pMod"		=>	"<p class='msg'>Modifikation des bestehenden Projekt's <span class='var'>%s</span></p>",
    "pNew"		=>	"<p class='msg'>Neues Projekt</p>",
    "hMode"		=>	"<p class='msg'>Mode: Hierarchie Evaluierung</p>",
    "aMode"		=>	"<p class='msg'>Mode: Alternativen Evaluierung <span class='var'>%g</span> alternatives</p>",
    "giUpd"		=>	"<span class='msg'> %g Beurteilung(en) aktualisiert. </span>",
    "giIns"		=>	"<span class='msg'> %g Beurteilung(en) eingefügt. </span>",
    "giTu"		=>	"Danke für die Teilnahme!",
    "giNcmpl"	=>	"Der Paarvergleich ist noch nicht komplett",
    "giNds"		=>	"Keine Daten gespeichert. ",
    "giPcmpl"	=>	"Bitte alle Paarvergleiche erst abschliessen. "
);

    public $info = array(
    "intro"		=>	"<div class='entry-content'><p style='text-align:justify;'>
								Definiere eine Entscheidungs-Hierarchie von Kriterien und berechne deren Gewichtung 
								basierend auf Paarvergleichen mit dem Analytischen Hierarchie Prozess AHP. 
								In einem weitern Schritt kannst du dann einen Satz von Entscheidungs-Alternativen  
								mit Bezug auf die Kriterienliste definieren, um die am meisten bevorzugte  
								Alternative zu finden und dein Entscheidungsproblem zu lösen.
								</p><p style='text-align:justify;'>
								Für eine einfache Berechnung mit Paarvergleichen kannst du auch den <a href='ahp-calc.php'>
								AHP Prioritätsrechner</a> einsetzen.</p></div>",
    "clkH"		=>	"Klicke auf <input type='button' class='btnr' value='AHP'> zur Komplettierung der Paarvergleiche. ",
    "clkA"		=>	"Klicke auf <b>Alternativen</b>, dann auf <b>AHP</b> zur Komplettierung der Paarvergleiche.",
    "clkS"		=>	"Klicke auf <input type='button' value='Speichere Beurteilungen'> zum Abschliessen und Speichern deiner Beurteilungen.",
    "txtfld"	=>	"Eingabe und Modifikation des Textes im Textfeld unten, dann Klick auf Submit. (Siehe <a href='ahp-examples.php'>Beispiele</a>)",
    "synHelp"	=>	"<br><span style='text-align:justify; font-size:small;'>
								Im Textfeld oben kannst du eine neue Entscheidungshierarchie definieren. 
								Auf die Knoten folgt ein <b>Doppelpunkt</b>, die Blätter werden durch <b>Kommata</b> getrennt,
								und jeder Zweig muss mit einem <b>Semikolon</b> abgeschlossen werden. 
								Das Tilde-Zeichen (~) wird verworfen. Namen für Kategorien and Unterkategorien müssen eindeutig sein . 
								Reine Zahlen sind als Kategorie Namen nicht erlaubt, z.B. wähle \"100 $\" anstatt \"100\". Eine Kategorie 
								kann nicht eine einzelne Unterkategorie haben. Standardmäßig sind alle Prioritäten so eingestellt, 
								dass sie in jeder Kategorie oder Unterkategorie in der Summe 100% ergeben. Anmerkung: Bei der Eingabe 
								wird zwischen Groß- und Kleinschreibung unterschieden.</span>",
    "nlg"			=>	"<p class='msg'>Als registrierter Nutzer kannst du die Hierarchie als Projekt speichern und die Ergebnisse im csv Format
								runterladen.</p>",
    "lgi"			=>	"<p class='msg'>Zur Evaluierung der AHP Gewichtung <i>Speichern/Aktual.</i>, dann öffne das Projekt von der Projekt Seite,
								um die Paarvergleiche durchzuführen. 
								Zur Evaluierung von Alternativen nehme ein Hierarchie mit ausgewerteten Prioritäten zur Definition von Alternativen.
								Die Speicherung erfolgt dann unter <i>Speichern</i> vom Alternativen Menü aus.</p>",
    "giPcmpl"	=>	"Klicke auf <input type='button' value='Alternativen'> dann <input class='btnr ' type='button' value='AHP'>"
);

    public $mnu	= array(
    "lgd11"		=>	"Hierarchie Eingabe Menü",
    "btn11"		=>	"Abschicken",
    "btn12"		=>	"Speichern/Aktual.",
    "btn13"		=>	"Download (.csv)",
    "lbl11"		=>	"Dez. Komma",
    "btn14"		=>	"Prioritäten zurücksetzen",
    "btn15"		=>	"Alles zurücksetzen",
    "btn16"		=>	"Fertig",
    "lgd21"		=>	"Gruppen Eingabe Menü",
    "btn21"		=>	"Speichere Beurteilungen",
    "btn22"		=>	"Gruppen Ergebnis",
);
}
