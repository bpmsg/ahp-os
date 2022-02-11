<?php

class AhpHierES
{
    public $wrd = array(
  "lvl"       =>  "Nivel",
  "nd"        =>  "Nodo",
  "lvls"      =>  "nivel(es) de jerarquía",
  "lfs"       =>  "hojas de jerarquía",
  "nds"       =>  "nodo(s) de jerarquía",
  "chr"       =>  "características de la jerarquía",
  "glbP"      =>  "Glb Prio.",
  "alt"       =>  "Alternativas"
);

    public $wrn = array(
  "glbPrioS"  => "Suma de prioridades globales no totaliza 100%. ¡Compruebe la jerarquía!",
  "prioSum"   =>  "¡Advertencia! Suma de prioridades no totaliza 100% en la categoría: "
);

    public $err  = array(
  "hLmt"      =>  "Se excedieron los límites del programa. ",
  "hLmtLv"    =>  "Demasiados niveles de jerarquía. ",
  "hLmtLf"    =>  "Demasiadas hojas de jerarquía. ",
  "hLmtNd"    =>  "Demasiados nodos de jerarquía. ",
  "hEmpty"    =>  "Jerarquía vacía o sin nodo, defina la Jerarquía.",
  "hSemicol"  =>  "Falta un punto y coma al final",
  "hTxtlen"   =>  "Se superó la longitud del texto de entrada! ",
  "hNoNum"    =>  "El nombre de las categorías / subcategorías no debe ser números; encontró:",
  "hEmptyCat" =>  "Nombre de categoría vacío",
  "hEmptySub" =>  "Nombre de subcategoría vacío ",
  "hSubDup"   =>  "Nombre(s) de subcategoría duplicado: ",
  "hNoSub"    =>  "Menos de 2 subcategorías en la categoría ",
  "hCatDup"   =>  "Nombres de categoría duplicados: ",
  "hColSemi"  =>  "Número desigual de<i>dos puntos</i> y <i>punto y coma</i>, comprobar la definición de jerarquía",
  "hHier"     =>  "Error en la jerarquía, verifique el texto. ",
  "hMnod"     =>  "La jerarquía comienza con más de un nodo: ",
  "unkn"      =>  "<span class='err'>Error desconocido: repita la evaluación %s </span>"
);

    public $msg = array(
  "sbmPwc1"   =>  "<small><span class='msg'>Complete las comparaciones por pares (haga clic en \"AHP \")</span></small>",
  "sbmPwc2"   =>  "<small><span class='msg'>Ok. Enviar para evaluación de grupo o evaluación alternativa.</span></small>",
  "aPwcCmplN" =>  "<small><span class='msg'>%g de %g comparaciones completadas</span></small>",
  "aPwcCmplA" =>  "<small><span class='msg'>Se completan todas las evaluaciones.</span></small>"
);

    public $tbl = array(
  "hTblCp"    =>  "<caption>Jerarquía de decisiones</caption>",
  "aTblCp"    =>  "<caption>Jerarquía con alternativas</caption>",
  "aTblTh"    =>  "<th>No</th><th>Node</th><th>Criterio</th><th>Glb Prio.</th><th>Comparar</th>",
  "aTblTd1"   =>  "Peso total de alternativas:"
);
}
