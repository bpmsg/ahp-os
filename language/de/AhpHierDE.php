<?php
class AhpHierDE {
	
public $wrd = array(
	"lvl"				=>	"Ebene",
	"nd"				=>	"Knoten",
	"lvls"			=>	"Hierarchie Ebene(n)",
	"lfs"				=>	"Hierarchie Endknoten",
	"nds"				=>	"Hierarchie Knoten",
	"chr"				=>	"Hierarchie Character",
	"glbP"			=>	"Glb Prio.",
	"alt"				=>	"Alternativen"
);

public $wrn = array(
	"glbPrioS"	=>	"Summe der globalen Prioritäten ist nicht 100%. Check Hierarchie! ",
	"prioSum"		=>	"Warnung! Summe der Prioritäten ist nicht 100% in Kategorie: "
);

public $err  = array(
	"hLmt"			=>	"Programm Grenzen überschritten. ",
	"hLmtLv"		=>	"Zuviele Hierarchie Ebenen.",
	"hLmtLf"		=>	"Zuviele Hierarchie Endknoten.",
	"hLmtNd"		=>	"Zuviele Hierarchie Knoten.",
	"hEmpty"		=>	"Hierarchie ist leer oder hat keine Knoten, bitte Hierarchie definieren. ",
	"hSemicol"	=>	"Fehlendes Semicolon am Ende ",
	"hTxtlen"		=>	"Max. Länge des Eingabetextes überschritten ",
	"hNoNum"		=>	"Name von Kategorien oder Subkategorien dürfen keine Zahlen sein; wie: ",
	"hEmptyCat"	=>	"Leerer Kategorie Name ",
	"hEmptySub"	=>	"Leerer Subkategorie Name ",
	"hSubDup"		=>	"Doppelter Subkategorie name(n): ",
	"hNoSub"		=>	"Weniger als 2 Subkategorien in einer Kategorie ",
	"hCatDup"		=>	"Doppelte(r) Kategorie Name: ",
	"hColSemi"	=>	"Ungleiche Anzahl von <i>Doppelpunkt</i> und <i>Semikolons</i>, prüfe Hierarchie Definition",
	"hHier"			=>	"Fehler in der Hierarchie, bitte Text prüfen. ",
	"hMnod"			=>	"Hierarchie startet mit mehr als einem Knoten - ",
	"unkn"			=>	"<span class='err'>Unbekannter Fehler - Bitte Evaluierung wiederholen %s </span>"
);

public $msg = array(
	"sbmPwc1"		=>	"<small><span class='msg'>Bitte paarweisen Vergleich komplettieren (Klicke auf \"AHP\")</span></small>",
	"sbmPwc2"		=>	"<small><span class='msg'>OK für Gruppen oder Evaluierung der Alternativen</span></small>",
	"aPwcCmplN"	=>	"<small><span class='msg'>%g von %g Paarvergleichen abgeschlossen</span></small>",
	"aPwcCmplA"	=>	"<small><span class='msg'>Alle Evaluierungen sind komplett.</span></small>"
);

public $tbl	= array(
	"hTblCp"		=>	"<caption>Entscheidungs Hierarchie</caption>",
	"aTblCp"		=>	"<caption>Hierarchie mit Alternativen</caption>",
	"aTblTh"		=>	"<th>Nr</th><th>Knoten</th><th>Kriterium</th><th>Glb Prio.</th><th>Vergl.</th>",
	"aTblTd1"		=>	"Gesamtwichtung der Alternativen: "
);

}