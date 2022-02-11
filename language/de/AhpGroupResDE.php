<?php

class AhpGroupResDE
{
    public $titles = array(
    "pageTitle1"	=>	"AHP Ergebnis - AHP-OS",
    "h1title1"		=>	"<h1>AHP Gruppen Ergebnis</h1>",
    "h2subTitle1"	=>	"<h2>Projekt Ergebnis Daten</h2>",

    "pageTitle2"	=>	"AHP Project Input data - AHP-OS",
    "h1title2"		=>	"<h1>AHP Gruppen Ergebnis</h1>",
    "h2subTitle2"	=>	"<h2>Projekt Eingangs Daten</h2>",

    "h2hier"			=>	"<h2>Hierarchie mit konsolidierten Prioritäten</h2>",
    "h2consP"			=>	"<h2>Konsolidierte globale Gewichtung</h2>",
    "h2consA"			=>	"<h2>Konsolidierte Gewichtung der Alternativen</h2>",
    "h2sens"			=>	"<h2>Empfindlichkeits Analyse</h2>",
    "h3wUncrt"		=>	"<h3>Unsicherheiten Gewichtung</h3>",
    "h2nodes"			=>	"\n<h2>Aufschlüsselung nach Knoten</h2>",
    "h4wCons"			=>	"<h4>Konsolidierte Prioritäten</h4>",
    "h4mCons"			=>	"<h4>Konsolidierte Entscheidungs Matrix</h4>",
    "h4part"			=>	"<h4>Gruppen Ergebnis und Prioritäten der Teilnehmer</h4>",
    "h2pGlob"			=>	"<h2>Globale Prioritäten</h2>",
    "h3rob"				=>	"<h3>Robustheit</h3>",
    "h2alt"				=>	"<h2>Alternativen nach Teilnehmern</h2>",
    "h2crit"			=>	"<h2>Aufschlüsselung nach Kriterien</h2>",
    "h4group"			=>	"<h4>Gruppen Ergebnis und Prioritäten der Teilnehmer</h4>",
    "h2grMenu"		=>	"<h2>Gruppen Ergebnis Menü</h2>",

    "h2dm"				=>	"<h2>Paarvergleichs Entscheidungs Matrizen</h2>",
    "h4dm"				=>	"<h4>Entscheidungs Matrix</h4>",
    "h4crit"			=>	"<h4>Kriterium: <span class='res'>%s</span></h4>",
    "h3part"			=>	"<h3>Teilnehmer <span class='res'>%s</span></h3>",
    "h4nd"				=>	"<h4>Knoten: <span class='res'>%s</span></h4>"
);

    public $wrd	 = array(
    "crit"			=>	"Kriterien",
    "alt"				=>	"Alternativen"
);

    public $res  = array(
    "cr"					=>	"Konsistenz Ratio CR: <span class='res'>%02.1f%%</span>",
    "consens1"		=>	"<p>AHP Gruppenkonsens: <span class='res'>%02.1f%%</span> ",
    "consens2"		=>	" Kriterium: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
    "gCons"				=>	" - AHP Gruppenkonsens: <span class='res'>%02.1f%%</span> ",
    "consens4"		=>	"<p><small>Konsens in der Bewertung der Alternativen mit Bezug auf Kriterium 
											<span class='res'>%s</span>: <span class='res'>%02.1f%%</span>",
    "nodeCr"			=>	" Knoten: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
    "ovlp"				=>	"Folgende %s sind ohne Überlappung:<br>",
    "ovlpNo"			=>	"Keine Überlappung der %s innerhalb der Unsicherheiten",
    "ovlpAll"			=>	"Alle %s überlappen innerhalb der Unsicherheiten",
    "ovlpGrp"			=>	"Die folgende(n) Gruppe(n) von %s überlappen innehalb der Unsicherheiten:<br>",
    "rtrb"				=>	"<p class='msg'>1. Die Lösung für die Top-Alternative <span class='res'>%s</span> ist robust.<br>",
    "rt10"				=>	"<p class='msg'>1. Das <i>Prozent-top</i> kritische Kriterium ist <span class='res'>%s</span>: 
										eine Änderung von <span class='res'>%g%%</span> um absolut <span class='res'>%g%%</span> ändert 
										den Rang zwischen den Alternativen <span class='res'>%s</span> und <span class='res'>%s</span>.<br>",
    "rt11"				=>	"2. Das <i>Prozent-any</i> kritische Kriterium ist <span class='res'>%s</span>: 
										eine Änderung von <span class='res'>%g%%</span> um absolut <span class='res'>%g %%</span> 
										ändert den Rang zwischen den Alternativen <span class='res'>%s</span> und 
										<span class='res'>%s</span>.<br>",
    "rt11s"				=>	"2. Das <i>Prozent-any</i> kritische Kriterium ist das gleiche wie oben.<br>",
    "rt20"				=>	"3. Das <i>Prozent-any</i> kritische Leistungsmass liegt bei Alternative <span class='res'>%s</span> 
										mit Bezug auf Kriterium <span class='res'>%s</span>. Eine Änderung von <span class='res'>%g%%</span> um absolut 
										<span class='res'>%g%%</span> ändert den Rang zwischen <span class='res'>%s</span> und 
										<span class='res'>%s</span>."
    );

    public $msg  = array(
    "scaleSel"		=>	"<p class='msg'>Gewählte Skalenfunktion: <span class ='hl'>%s</span></p>",
    "wMethod"			=>	"<p>Methode: <span class ='hl'>Weighted product method (WPM)</span></p>",
    "rMethod"			=>	"<p>Monte-Carlo Simulation: <span class ='hl'>basierend auf Standardabweichung</span></p>",
    "mcVar"				=>	"<p class='msg'>Geschätzte Unsicherheiten basierend auf <span class='res'>%g</span> Beurteilungs Variationen.",
    "pSel"				=>	"<p>Selektierte Teilnehmer: <span class='res'>%s</span></p>",
    "noSens"			=>	"<p class='msg'>Keine Unsicherheits Analyse möglich.</p>",
    "noPwc1"			=>	"<span class='msg'> - Keine paarweisen Vergleichsdaten verfügbar.</span>",
    "noPwc2"			=>	"<p class='msg'>Keine paarweisen Vergleichsdaten von den Teilnehmern verfügbar.</p>",
    "noPwc3"			=>	" - Keine paarweisen Vergleichsdaten von den Teilnehmern verfügbar.",
    "noPwc4"			=>	"<p>Warnung: <span class='msg'>%s</span></p>",
    "noRt"				=>	"<p class='msg'>Kein Robustheits-Test möglich.</p>",
    "pCnt"				=>	"Aggregation der Beurteilungen von %g Teilnehmer(n)",
    "nlgin"				=>	"<p class='msg'>You need to be a registered user and login to handle projects.</p>"
);

    public $err  = array(
    "incompl"			=>	"<p class='err'>Projekt Bewertung ist noch nicht abgeschlossen</p>",
    "consens0"		=>	"<p>AHP Gruppenkonsens: <span class='err'>n/a</span>",
    "consens1"		=>	" - Konsens <span class='res err'>n/a</span>",
    "consens2"		=>	"<p><small>in der Bewertung der Alternativen bzgl. dem Kriterium <span class='res err'>n/a</span>"
);

    public $info = array(
    "sensDl"			=>	"<p><small>Anmerkung: Komplette Analyse über Download.</small></p>",
    "cpbd"				=>	"Konsolidierte Preferenzen für die Alternativen in Bezug auf einzelne Kriterien",
    "pwcfor"			=>	"Pairwise comparisons for: <br>"
);

    public $mnu = array(
    "btnNdD"	=> 	"<p><button href='#%s' class='nav-toggle'>Details</button>",
    "lgd1"		=>	"Guppen Ergebnis Menü",
    "lbl4"		=>	"Dez. Komma",
    "btn1"		=>	"Aktualisieren",
    "btn2"		=>	"Eingangsdaten",
    "btn3"		=> 	"Download (.csv)",
    "btn4"		=>	"Def. Alternativen",
    "btn5"		=>	"Fertig",
    "lgd2"		=>	"Projekt Input Daten Menü"

);
}
