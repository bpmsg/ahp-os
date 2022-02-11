<?php

class AhpPrioCalcDE
{
    public $wrd = array(
    "crit"	=>  "Kriterien",
    "alt"		=>	"Alternativen"
);

    // Errors
    public $err = array(
    "pgm"						=>	"<br><span class='err'>Program Fehler</span>",
    "pwcInc"				=>	"<span class='err'>Paarvergleiche noch nicht komplett!</span>"
);
    // calc (priority calculator)
    public $titles1 = array(
    "pageTitle" 		=>	"AHP Rechner - AHP-OS",
    "h1title" 			=>	"<h1>AHP Prioritätsrechner</h1>",
    "h2subTitle" 		=>	"<h2>AHP Kriterien</h2>",
    "h3Pwc"					=>	"<h3>Paarweiser Vergleich <span class='var'>%s</span></h3>",
    "h3Res"					=>	"<h3 align='center'>Resultierende Gewichtung</h3>"
);
    // hiercalc
    public $titles2 = array(
    "pageTitle" 	=>	"PWC Crit AHP-OS",
    "h1title" 		=>	"<h1>Paarweiser Vergleich AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Evaluierung der Kriterien für <span class='var'>%s</span></h2>",
);
    // altcalc
    public $titles3 = array(
    "pageTitle" 	=>	"PWC Alt AHP-OS",
    "h1title" 		=>	"<h1>Paarweiser Vergleich AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Evaluierung der Alternativen für das Projekt <span class='var'>%s</span></h2>",
    "h2alt"				=>	"<h2>Alternativen</h2>",
    "h3Mnu"				=>	"<h3>Alternativen Menü</h3>",
    "h3tblA"			=>	"<h3>Projekt Struktur</h3>",
    "h3Res"				=>	"<h3>Ergebnis für die Alternativen</h3>",
    "h4Res"				=>	"<h4>Gewichtung und Rang</h4>"
);

    // calc1
    public $titles4 = array(
    "pageTitle" 	=>	"AHP Kriterien",
    "h1title" 		=>	"<h1 class='ca' >Kriterien Bezeichnung</h1>"
);

    // alt1
    public $titles5 = array(
    "pageTitle" 	=>	"AHP Alternativen",
    "h1title" 		=>	"<h1 class='ca' >Alternativen Bezeichnung</h1>"
);

    // Messages
    public $msg = array(
    "nPwc"		=>	"<span class='msg'>%g paarweise(r) Vergleich(e). </span>",
    "pwcAB"		=>	"A - bzgl. <span class='var'>%s</span> - oder B?",
    "giUpd"		=>	"<span class='msg'> %g Beurteilung(en) aktualisiert. </span>",
    "giIns"		=>	"<span class='msg'> %g Beurteilung(en) eingefügt. </span>",
    "noPwc1"	=>	"<span class='msg'>Bitte alle Paarvergleiche abschliessen. Klicke auf ",
    "noPwc2"	=>	"<input type='button' value='Alternativen'> dann ",
    "noPwc3"	=>	"<input class='btnr ' type='button' value='AHP'></span>",
    "tu"			=>	"Vielen Dank für die Teilnahme!",
    "inpA"		=>	"<p class='ca' >Bitte ausfüllen</p>"
);

    // Information
    public $info= array(
    "intro"		=>	"Wähle Anzahl und Namen der Kriterien, dann starte paarweise Vergleiche
								zur Berechnung der Prioritäten mit
		 						dem Analytischen Hierarchie Prozess (AHP).",
    "pwcQ"		=>	"<p><span class='hl'>Mit Bezug auf 
								<i><span class='var'>%s</span></i>, welches Kriterium ist wichtiger,
								und um wieviel auf einer Skala von 1 bis 9%s</span></p>",
    "pwcQA"		=>	"<p><span class='hl'>In Hinblick auf  
								<i><span class='var'>%s</span></i>, welche Alternative passt besser oder ist
								vorzuziehen, und um wieviel mehr auf einer Skala von 1 bis 9%s</span></p>",
    "selC"		=>	"Wähle die Anzahl der Kriterien:",
    "scale"		=>	"<p style='font-size:small'>AHP Skala: 1- Gleich wichtig, 3- Etwas wichtiger,
 								5- Deutlich wichtiger, 7- Sehr viel wichtiger, 9- Extrem wichtiger 
 								(2,4,6,8 Zwischenwerte).</p>",
    "doPwc"		=>	"Bitte vergleiche alle Kriterien Paare. Wenn fertig, 
								klicke auf <i>Berechne</i> zur Anzeige der resultierenden Prioritäten.<br>",
    "doPwcA"	=>	"Bitte vergleiche alle Alternativen in Hinblick auf die einzelnen Kriterien.
								 Wenn fertig, klicke auf <i>Berechne</i> zur Anzeige
								der resultierenden Prioritäten, und <i>Submit Priorities</i> zur Fortsetzung des Programms. ",
    "doPwcA1"	=>	"<p>Vergleiche die Alternativen in Hinblick auf die Kriterien (klick auf AHP). 
								Wie gut passen die Alternativen zu jedem Kriterium?</p>",
    "adj"			=>	"<p class='msg'>Zur Verbesserung der Konsistenz kannst du die hervorgehobenen  
								Bewertungen um plus oder minus ein oder zwei Punkte auf der Skala anpassen. CR sollte 
								möglichst unter 10% liegen.</p>",
    "inpAlt"	=>	"Hier kannst du Anzahl und Bezeichnung der Alternativen eingeben:",
    "pSave"		=>	"<p>Klick auf <i>Speichere als Projekt</i> um das Projekt mit den definierten Alternativen 
								für die weitere Evaluierung zu speichern.</p>"
);

    public $mnu = array(
    "btnSbm"	=>	"Ok - weiter",
    "lgd1"		=>	"AHP Prioritätsrechner",
    "done"		=>	"Fertig",
    "next"		=>	"Weiter",
    "lgd2"		=>	"Alternativen Menü",
    "btn1"		=>  "Beurteilungen speichern",
    "btn2"		=>	"Alternativen zurücksetzen",
    "btn3"		=>	"Speichere als Projekt"
);
}
