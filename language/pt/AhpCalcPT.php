<?php

class AhpCalcPT
{
// Titles
    public $titles = array(
    "h3ResP"	=>	"<h3>Prioridades</h3>",
    "h3ResDm"	=>	"<h3>Matriz de Decisão</h3>"
);

    // Errors
    public $err = array(
    "ePwc"		=>	"<span class='err'>Erro de Entrada</span>",
    "adjPwc"	=>	"<span class='err'>Por Favor ajuste os julgamentos em destaque para aumentar a consistência</span>",
    "nCrit"		=>	"<span class='err'>Atenção , n deve estar entre 1 e %g, n  foi definido como padrão.</span>"
);

    // Result text
    public $res = array(
    "npc"			=>	"Número de comparações = <span class='res'>%g</span><br>",
    "cr"			=>	"<b>Razão de Consistência CR</b> = <span class='res'>%2.1f%%</span><br>",
    "ev"			=>	"Autovalor principal = <span class='res'>%2.3f</span><br>",
    "it"			=>	"Solução de Autovetor : <span class='res'>%d</span> interações, 
										 delta = <span class='res'>%01.1E</span>"
);
    // Messages
    public $msg = array(
    "ok"			=>	"<span class='msg'>OK</span>",
    "sPwc"		=>	"<span class='msg'>Por favor inicie a comparação entre pares</span>",
    "def"			=>	"<span class='msg'>Alguns nomes foram definidos como padrão.</span>"
);
    // Information
    public $info= array(
    "pwcAB"		=>	"A - Importância -ou B? ",
    "resP"		=>	"Estes são os resultados ponderados pelos critérios escolhidos na sua comparação entre pares:",
    "resDm"		=>	"Os resultados ponderados são baseados no principal autovetor da matriz de decisão:",
    "cNbr"		=>	"<span class='hl'>Entre com número e nomes  (2 - %g) </span>",
    "wlMax"		=>	"<small>max. %g character ea.</small>"
);
    // Tables
    public $tbl	= array(
    "cTblTh"	=>	"<thead><tr class='header'>
	 								<th colspan='3' class='ca' >%s</th>
	 								<th>Igual</th>
	 								<th class='ca' >Quanto mais?</th></tr></thead>",
    "pTblTh"	=> "<th colspan='2' class='la' >Cat</th>
									<th>Prioridade</th>
									<th>Rank</th>",
    "gcTblTh"	=>	"<tr><th colspan='2' class='ca' >Nome do %s</th></tr>"
);
    // Menu and buttons
    public $mnu = array(
    "btnChk"	=>	"<input id='sbm1' %s type='submit' value='Calcular' name='pc_submit' />",
    "btnSbm"	=>	"<input type='submit' value='%s' name='%s' %s %s />",
    "btnDl"		=>	"dec. comma"
);
}
