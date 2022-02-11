<?php

class AhpGroupResES
{
    // for ahp-group.php AND  ahp-g-input.php
    /* Titles and headings */
    public $titles = array(
  "pageTitle1"  =>  "AHP Resultados del grupo - AHP-OS",
  "h1title1"    =>  "<h1>AHP Resultados del grupo</h1>",
  "h2subTitle1" =>  "<h2>Datos de resultado del proyecto</h2>",

  "pageTitle2"  =>  "AHP ingreso de datos - AHP-OS",
  "h1title2"    =>  "<h1>AHP Resultados del grupo</h1>",
  "h2subTitle2" =>  "<h2>Ingreso de datos al proyecto</h2>",

  "h2hier"      =>  "<h2>Jerarquía con prioridades consolidadas</h2>",
  "h2consP"     =>  "<h2>Prioridades globales consolidadas</h2>",
  "h2consA"     =>  "<h2>Pesos consolidados de las alternativas</h2>",
  "h2sens"      =>  "<h2>Análisis de sensibilidad</h2>",
  "h3wUncrt"    =>  "<h3>Incertidumbres de peso</h3>",
  "h2nodes"     =>  "\n<h2>Desglose por nodos</h2>",
  "h4wCons"     =>  "<h4>Prioridades consolidadas</h4>",
  "h4mCons"     =>  "<h4>Matriz de decisión consolidada</h4>",
  "h4part"      =>  "<h4>Resultado grupal y prioridades de los participantes individuales</h4>",
  "h2pGlob"     =>  "<h2>Prioridades globales</h2>",
  "h3rob"       =>  "<h3>Robustez</h3>",
  "h2alt"       =>  "<h2>Alternativas por los participantes</h2>",
  "h2crit"      =>  "<h2>Desglose por criterios</h2>",
  "h4group"     =>  "<h4>Resultado grupal y prioridades de los participantes individuales</h4>",
  "h2grMenu"    =>  "<h2>Menú de resultados de grupo</h2>",

  "h2dm"        =>  "<h2>Matrices de decisión de comparación por pares</h2>",
  "h4dm"        =>  "<h4>Matriz de decisión</h4>",
  "h4crit"      =>  "<h4>Criterio: <span class='res'>%s</span></h4>",
  "h3part"      =>  "<h3>Participante <span class='res'>%s</span></h3>",
  "h4nd"        =>  "<h4>Nodo: <span class='res'>%s</span></h4>"
);

    /* Individual words */
    public $wrd  = array(
  "crit"      =>  "criterios",
  "alt"       =>  "alternativas"
);

    /* Result output */
    public $res  = array(
  "cr"          =>  "Ratio de consistencia CR: <span class='res'>%02.1f%%</span>",
  "consens1"    =>  "<p>Concenso grupal AHP: <span class='res'>%02.1f%%</span> ",
  "consens2"    =>  " Criterio: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
  "gCons"       =>  " - Concenso grupal AHP: <span class='res'>%02.1f%%</span> ",
  "consens4"    =>  "<p><small>Consenso en la evaluación de las alternativas WRT al criterio 
                    <span class='res'>%s</span>: <span class='res'>%02.1f%%</span>",
  "nodeCr"      =>  " Nodo: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
  "ovlp"        =>  "Los siguientes %s no se superponen:<br>",
  "ovlpNo"      =>  "Sin superposición de %s dentro de las incertidumbres",
  "ovlpAll"     =>  "Todos los %s se superponen con incertidumbres.",
  "ovlpGrp"     =>  "Los siguientes grupos de %s se superponen dentro de las incertidumbres:<br>",
  "rtrb"        =>  "<p class='msg'>1. La solución para la mejor alternativa <span class='res'>%s</span> is robust.<br>",
  "rt10"        =>  "<p class='msg'>1. El <i>porcentaje superior</i> el criterio crítico es <span class='res'>%s</span>: 
                    un cambio de <span class='res'>%g%%</span> por absoluto <span class='res'>%g%%</span> cambiará 
                    el ranking entre alternativas <span class='res'>%s</span> y <span class='res'>%s</span>.<br>",
  "rt11"        =>  "2. El <i>por ciento-cualquiera</i> el criterio crítico es<span class='res'>%s</span>: 
                    un cambio de <span class='res'>%g%%</span> por absoluto <span class='res'>%g %%</span> 
                    cambiará la clasificación entre alternativas <span class='res'>%s</span> and 
                    <span class='res'>%s</span>.<br>",
  "rt11s"       =>  "2. El <i>por ciento-cualquiera</i> el criterio crítico es el mismo que el anterior.<br>",
  "rt20"        =>  "3. El <i>por ciento-cualquiera</i> lLa medida de rendimiento crítica es una alternativa <span class='res'>%s</span> 
                    bajo criterio <span class='res'>%s</span>. Un cambio de <span class='res'>%g%%</span> por absoluto
                    <span class='res'>%g%%</span> cambiará la clasificación entre <span class='res'>%s</span> y
                    <span class='res'>%s</span>."
  );

    /* Messages */
    public $msg  = array(
  "scaleSel"    =>  "<p class='msg'>Escala seleccionada: <span class ='hl'>%s</span></p>",
  "wMethod"     =>  "<p>Método: <span class ='hl'> Método de producto ponderado (WPM)</span></p>",
  "rMethod"     =>  "<p>Variación aleatoria: <span class ='hl'>basado en la desviación estándar</span></p>",
  "mcVar"       =>  "<p class='msg'>Incertidumbres de peso estimadas basadas en <span class='res'>%g</span> variaciones de juicio.",
  "pSel"        =>  "<p>Participantes seleccionados: <span class='res'>%s</span></p>",
  "noSens"      =>  "<p class='msg'>No es posible realizar un análisis de sensibilidad.</p>",
  "noPwc1"      =>  "<span class='msg'> - Sin datos de comparación por pares.</span>",
  "noPwc2"      =>  "<p class='msg'>No hay datos de comparación por parejas de los participantes.</p>",
  "noPwc3"      =>  " - No hay datos de comparación por parejas de los participantes.",
  "noPwc4"      =>  "<p>Alerta: <span class='msg'>%s</span></p>",
  "noRt"        =>  "<p class='msg'>No es posible realizar una prueba de robustez.</p>",
  "pCnt"        =>  "Agregación de juicios individuales para %g Participante(s)",
  "nlgin"       =>  "<p class='msg'>Debe ser un usuario registrado e iniciar sesión para manejar proyectos.</p>"
);

    /* Errors */
    public $err  = array(
  "incompl"     =>  "<p class='err'>La evaluación del proyecto está incompleta</p>",
  "consens0"    =>  "<p>Consenso del grupo AHP: <span class='err'>n/a</span>",
  "consens1"    =>  " - Consenso <span class='res err'>n/a</span>",
  "consens2"    =>  "<p><small>en la evaluación de las alternativas WRT al criterio <span class='res err'>n/a</span>"
);

    /* Information output */
    public $info = array(
  "sensDl"      =>  "<p><small>Nota: análisis completo mediante descarga.</small></p>",
  "cpbd"        =>  "Preferencias consolidadas por alternativas con respecto a cada criterio",
  "pwcfor"      =>  "Comparaciones por pares para: <br>"
);

    /* Menu and buttons */
    public $mnu = array(
  "btnNdD"  =>  "<p><button href='#%s' class='nav-toggle'>
                 Details</button>",
  "lgd1"    =>  "Menú de resultados de grupo",
  "lbl4"    =>  "dic. coma",
  "btn1"    =>  "Actualizar",
  "btn2"    =>  "Ver datos de entrada",
  "btn3"    =>  "Descargar (.csv)",
  "btn4"    =>  "Utilice Consol. Prio.",
  "btn5"    =>  "Hecho",
  "lgd2"    =>  "Menú de datos de entrada del proyecto"
);
}
