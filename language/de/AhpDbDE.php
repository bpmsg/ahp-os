<?php
/* AhpDbDE is called from the classes AhpDb */
class AhpDbDE {
		public $err = array(
	"dbType"		=>	"Falscher SQL Datenbank Typ: ",
	"scInv"			=>	"Ungültige Projektkennung ",
	"scInUse"		=>	"Projektkennung in Verwendung ",
	"dbWrite"		=>	"Die Daten konnten nicht in die Datenbank geschrieben werden.",
	"dbWriteA"	=>	"Datenbank Fehler, Alternativen wurden nicht gespeichert ",
	"dbUpd"			=>	"Daten konnten nicht aktualiesiert werden. ",
	"dbSubmit"	=>	"Daten bereits eingereicht ",
	"noSess"		=>	"Keine gespeicherten Projekte ",
	"dbReadSc"	=>	"Datenbankfehler beim Abruf der Daten für ",
	"pClosed"		=>	"Projekt ist abgeschlossem. Keine Paarvergleich's Eingabe erlaubt.",
	"pNoMod"		=>	"Projekt hat Teilnehmer, Hierarchie kann nicht modifiziert werden."
	);
	public $msg = array(
	"noSess" 		=>	"Keine gespeicherten Projekte"
	);
	public $titles = array(
	"h3pDat"		=>	"<h3>Projekt Daten</h3>",
	"h3pPart"		=>	"<h3>Projekt Teilnehmer</h3>\n",
	"h3pAlt"		=>	"<h3>Projekt Alternativen</h3>"
	);
	public $tbl = array(
	"scTblTh"		=>	"<thead><tr>
									 <th>Nr</th>
									 <th>Kennung</th>
									 <th>Projekt</th>
									 <th>Typ<sup>1</sup></th>
									 <th>Status<sup>2</sup>
									 </th><th>Beschreibung</th>
									 <th>Teiln.<sup>3</sup></th>
									 <th>Erstellt</th></tr></thead>",
	"scTblFoot"	=>	"<tfoot><tr><td colspan='8'>
									 <sup>1</sup> H: Hierarchie, A: Hierarchie mit Alternativen, 
									 <sup>2</sup> Project Status: 1 = Aktiv 0 = Geschlossen, 
									 <sup>3</sup> Anzahl der Teilnehmer</td></tr></tfoot>",
	"pdTblTh"		=>	"<thead><tr>
									 <th>Attribut</th>
									 <th>Inhalt</th></tr></thead>\n",
	"pdTblR1"		=>	"<tr><td>Projektkennung</td><td class='res'>%s</td></tr>\n",
	"pdTblR2"		=>	"<tr><td>Projektname</td><td class='res'>%s</td></tr>\n",
	"pdTblR3"		=>	"<tr><td>Beschreibung</td><td class='res'>%s</td></tr>\n",
	"pdTblR4"		=>	"<tr><td>Autor</td><td class='res'>%s</td></tr>\n",
	"pdTblR5"		=>	"<tr><td>Datum</td><td class='res'>%s</td></tr>\n",
	"pdTblR6"		=>	"<tr><td>Status</td><td class='res'>%s</td></tr>\n",
	"pdTblR7"		=>	"<tr><td>Typ</td><td class='res'>%s</td></tr>\n",
	"paTblTh"		=>	"<thead><tr>
									 <th>Nr</th>
									 <th class='nwr'>Alternativen</th></tr></thead>\n",
	"ppTblTh"		=>	"<thead><tr>
									 <th>Nr</th>
									 <th>Sel</th>
									 <th>Name</th>
									 <th>Datum</th></tr></thead>\n",
	"ppTblLr1"	=>	"<tr><td colspan='4'><input id='sbm0' type='submit' name='pselect' value='Ausw. aktualisieren'>&nbsp;<small>
									<input class='onclk0' type='checkbox' name='ptick' value='0' ",
	"ppTblLr2"	=>	">&nbsp;Alle sel.&nbsp;<input class='onclk0' type='checkbox' name='ntick' value='0' ",
	"ppTblLr3"	=>	">&nbsp;Alle desel.</small></td></tr>",
	"ppTblFoot"	=>	"<tfoot><tr><td colspan='4'><small>Wenn kein Teiln. selektiert ist, werden alle einbezogen</small></td></tr></tfoot>"
	);
		
	public $info = array(
	"shPart"		=> "<p><span class='var'>%g</span> Teilnehmer. <button class='toggle'>Zeige/Verberge</button> alle.</p>"
	);

}