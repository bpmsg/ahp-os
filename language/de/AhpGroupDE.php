<?php

/* Called from class AhpGroup */
class AhpGroupDE
{
    public $wrn = array(
    "noPart"		=>	"Projekt hat keine Teilnehmer",
    "noPwc"			=>	"<br>Knoten <span class='res'>%s</span> keine paarweisen Vergleiche verfügbar",
    "fUncEst"		=>	"<br>Knoten <span class='res'>%s</span> nur %g Variationen für Unsicherheits-Abschätzungen",
    "nUncEst1"	=>	"<br>Knoten <span class='res'>%s</span> keine Unsicherheits-Abschätzung",
    "nUncEst2"	=>	"<br>Knoten <span class='res'>%s</span> keine Unsicherheits-Abschätzung möglich"
);
    public $err  = array(
    "noAlt"		=>	"Projekt hat keine Alternativen",
    "invSc"		=>	"Ungültige Projektkennung",
    "dbE"			=>	"PWC vom Teiln. <span class='var'>%s</span> passt nicht zum Hierarchieknoten <span class='var'>%s</span>"
);
    public $info = array(
    "cont"				=>	"<p><br><small>forts.</small></p>"
);
    public $tbl	= array(
    "grTblTh"			=> 	"\n<thead><tr class='header'><th>Teiln.</th>",
    "grTblTd1"		=>	"<td><strong>Gruppe</strong></td>"

);
}
