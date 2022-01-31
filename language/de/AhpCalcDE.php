<?php
/* AhpCalcDE is called from the classes AhpCalc and AhpCalcIo */
class AhpCalcDE{
// Errors
public $err = array(
	"ePwc"		=>	"<span class='err'>Eingabe Fehler</span>",
	"adjPwc"	=>	"<span class='err'>Zur Verbesserung der Konsistenz kannst du die hervorgehobenen Beurteilungen anpassen.</span>",
	"nCrit"		=>	"<span class='err'>Warnung, n sollte zwischen 1 und %g liegen, n ist auf Default zurückgesetzt.</span>"
);
// Titles
public $titles = array(
	"h3ResP"	=>	"<h3>Prioritäten</h3>",
	"h3ResDm"	=>	"<h3>Entscheidungs Matrix</h3>"
);
// Result text
public $res = array(
	"npc"			=>	"Anzahl der Vergleiche = <span class='res'>%g</span><br>",
	"cr"			=>	"<b>Konsistenz Ratio CR</b> = <span class='res'>%2.1f%%</span><br>",
	"ev"			=>	"Dominanter Eigenwert = <span class='res'>%2.3f</span><br>",
	"it"			=>	"Eigenvektorlösung: <span class='res'>%d</span> Iterationen, 
								Delta = <span class='res'>%01.1E</span>"
);
// Messages
public $msg = array(
	"ok"			=>	"<span class='msg'>OK</span>",
	"sPwc"		=>	"<span class='msg'>Bitte beginne mit dem Paarvergleich</span>",
	"def"			=>	"<span class='msg'>Einige Namen sind auf Default gesetzt.</span>"
);
// Information
public $info= array(
	"pwcAB"		=>	"A- Preferenz? - oder B",
	"resP"		=>	"Dies ist die Gewichtung der Kriterien basierend auf den Paarvergleichen:",
	"resDm"		=>	"Die Gewichtung resultiert aus dem Eigenvektor der Entscheidungsmatrix:",
	"cNbr"		=>	"<span class='hl'>Eingabe Anzahl und Namen (2 - %g) </span>",
	"wlMax"		=>	"<small>max. je %g Zeichen</small>"
);
// Tables
public $tbl	= array(
	"cTblTh"	=>	"<thead><tr class='header'>
	 								<th colspan='3' class='ca' > %s </th>
	 								<th>Gleich</th>
	 								<th class='ca'>Um wieviel mehr? </th></tr></thead>",
	"pTblTh"	=> "<th colspan='2' class='la'>Kateg.</th>
									<th>Priorität</th>
									<th>Rang</th>",
	"gcTblTh"	=>	"<tr><th colspan='2' class='ca' >Name der %s</th></tr>"
);
// Menu and buttons
public $mnu = array(
	"btnChk"	=>	"<input id='sbm1' %s type='submit' value='Berechne' name='pc_submit' />",
	"btnSbm"	=>	"<input type='submit' value='%s' name='%s' %s %s />",
	"btnDl"		=>	"dez. Komma"
);

}