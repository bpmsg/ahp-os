<?php

class AhpCalcES
{
// Titles
    public $titles = array(
  "h3ResP"  =>  "<h3>Prioridades</h3>",
  "h3ResDm" =>  "<h3>Matriz de decisiones</h3>"
);

    // Errors
    public $err = array(
  "ePwc"    =>  "<span class='err'>Error de entrada</span>",
  "adjPwc"  =>  "<span class='err'>Ajustar los juicios resaltados para mejorar la coherencia</span>",
  "nCrit"   =>  "<span class='err'>Advertencia, n debe estar entre 1 y %g, n se estableció como predeterminado.</span>"
);

    // Result text
    public $res = array(
  "npc"     =>  "Número de comparaciones = <span class='res'>%g</span><br>",
  "cr"      =>  "<b>Ratio de consistencia CR</b> = <span class='res'>%2.1f%%</span><br>",
  "ev"      =>  "Principal auto valor = <span class='res'>%2.3f</span><br>",
  "it"      =>  "Solucion autovector: <span class='res'>%d</span> iterations, 
                     delta = <span class='res'>%01.1E</span>"
);
    // Messages
    public $msg = array(
  "ok"      =>  "<span class='msg'>OK</span>",
  "sPwc"    =>  "<span class='msg'>Por favor, inicie la comparación de pares</span>",
  "def"     =>  "<span class='msg'>Algunos nombres seteados como default</span>"
);
    // Information
    public $info= array(
  "pwcAB"   =>  "A - Importancia - o B? ",
  "resP"    =>  "Estos son los pesos resultantes para los criterios basados en sus comparaciones por pares:",
  "resDm"   =>  "Los pesos resultantes se basan en el vector propio principal de la matriz de decisión:",
  "cNbr"    =>  "<span class='hl'>Ingrese el número y los nombres (2 - %g) </span>",
  "wlMax"   =>  "<small>máx. %g carácter ea.</small>"
);
    // Tables
    public $tbl = array(
  "cTblTh"  =>  "<thead><tr class='header'>
                  <th colspan='3' style='text-align:center;'>%s</th>
                  <th>Igual</th>,
                  <th style='text-align:center;'>¿Cuánto más?</th></tr></thead>",
  "pTblTh"  => "<th colspan='2' style='text-align:left'>Cat</th>
                  <th>Prioridad</th>
                  <th>Rank</th>",
  "gcTblTh" =>  "<tr><th colspan='2' style='text-align:center;' >Nombre de los %s</th></tr>"
);
    // Menu and buttons
    public $mnu = array(
  "btnChk"  =>  "<input %s type='submit' value='Compruebe la coherencia' name='pc_submit' />",
  "btnSbm"  =>  "<input type='submit' value='%s' name='%s' %s %s />",
  "btnDl"   =>  "coma dec."
);
}
