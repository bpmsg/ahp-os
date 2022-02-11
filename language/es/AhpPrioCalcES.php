<?php

class AhpPrioCalcES
{
    public $wrd = array(
  "crit"  =>  "Criterios",
  "alt"   =>  "Alternativas"
);

    // Errors
    public $err = array(
  "pgm"         =>  "<br><span class='err'>Error de programa</span>",
  "pwcInc"      =>  "<span class='err'>¡Aún no se han completado las comparaciones por pares!</span>"
);

    // calc (priority calculator)
    public $titles1 = array(
  "pageTitle"   =>  "Calculadora AHP - AHP-OS",
  "h1title"     =>  "<h1>Calculadora de prioridad AHP</h1>",
  "h2subTitle"  =>  "<h2>Criterios AHP</h2>",
  "h3Pwc"       =>  "<h3>Comparación por pares <span class='var'>%s</span></h3>",
  "h3Res"       =>  "<h3 align='center'>Prioridades resultantes</h3>"
);

    // hiercalc
    public $titles2 = array(
  "pageTitle"   =>  "PWC Crit AHP-OS",
  "h1title"     =>  "<h1>Pairwise Comparison AHP-OS</h1>",
  "h2subTitle"  =>  "<h2>Evaluation of Criteria for <span class='var'>%s</span></h2>",
);

    // altcalc
    public $titles3 = array(
  "pageTitle"   =>  "PWC Alt AHP-OS",
  "h1title"     =>  "<h1>Comparación por pares AHP-OS</h1>",
  "h2subTitle"  =>  "<h2>Evaluación de alternativas para<span class='var'>%s</span></h2>",
  "h3Mnu"	=>  "<h3>Menú alternativo</h3>",
  "h2alt"       =>  "<h2>Alternativas</h2>",
  "h3tblA"      =>  "<h3>Estructura del proyectp</h3>",
  "h3Res"       =>  "<h3>Resultado para alternativas</h3>",
  "h4Res"       =>  "<h4>Prioridades y ranking</h4>"
);

    // calc1
    public $titles4 = array(
  "pageTitle"   =>  "Criterios AHP",
  "h1title"     =>  "<h1 style='text-align:center;'>Nombres de criterios AHP</h1>"
);

    // alt1
    public $titles5 = array(
  "pageTitle"   =>  "Alternativas AHP",
  "h1title"     =>  "<h1 style='text-align:center;'>Nombres alternativos de AHP</h1>"
);

    // Messages
    public $msg = array(
  "nPwc"    =>  "<span class='msg'>%g pairwise comparison(s). </span>",
  "pwcAB"   =>  "Cuál prefiere A - <span class='var'>%s</span> - o B?",
  "noPwc1"  =>  "<span class='msg'>Primero complete todas las comparaciones por pares. Haga clic en ",
  "noPwc2"  =>  "<input type='button' value='Alternativas'> then ",
  "noPwc3"  =>  "<input class='btnr ' type='button' value='AHP'></span>",
  "tu"      =>  "¡Gracias por su participación!",
  "giUpd"   =>  "<span class='msg'> %g judgment(s) updated. </span>",
  "giIns"   =>  "<span class='msg'> %g judgment(s) inserted. </span>",
  "inpA"    =>  "<p style='text-align:center;'>Por favor, completalos</p>"
);

    // Information
    public $info= array(
  "intro"   =>  "Seleccione el número y los nombres de los criterios, luego comience por la comparación de pares 
                comparaciones para calcular prioridades usando
                el proceso de jerarquía analítica.",
  "pwcQ"    =>  "<p><span class='hl'>Con respecto a
                <i><span class='var'>%s</span></i>, que criterio es mas importante,
                y cuánto más en una escala del 1 al 9%s</span></p>",
  "pwcQA"   =>  "<p><span class='hl'>Con respecto a
                <i><span class='var'>%s</span></i>, qué alternativa encaja es más preferible,
		y cuánto más en una escala del 1 al 9%s</span></p>",
  "selC"    =>  "Seleccione el número de criterios:",
  "scale"   =>  "<p style='font-size:small'>Escala AHP: 1- Importancia igual, 3- Importancia moderada,
                5- Importancia fuerte, 7- Importancia muy fuerte, 9- Importancia extrema 
                (2,4,6,8 valores intermedios).</p>",
  "doPwc"   =>  "Haga la comparación por pares de todos los criterios. Cuando esté completo, 
                click <i>Check Consistency</i> para obtener las prioridades.<br>",
  "doPwcA"  =>  "Haga la comparación por pares de todas las alternativas para indicar qué tan bueno
                cumplen cada criterio. Una vez terminado, haga clic en <i> Compruebe la coherencia
                </i> para obtener los pesos, y <i>Submit Priorities</i> proceder. ",
  "doPwcA1" =>  "<p>Compare alternativas con respecto a los criterios (haga clic en AHP). 
                ¿Qué tan bueno es el ajuste de las alternativas con cada criterio?</p>",
  "adj"     =>  "<p class='msg'>Para mejorar la coherencia, ajuste ligeramente 
		los juicios resaltados en más o menos uno o dos puntos en la escala.</p>",
  "inpAlt"  =>  "Aquí puede ingresar el número y los nombres de sus alternativas.",
  "pSave"       =>  "<p>Click en <i>Save as project</i> para guardar el proyecto con las 
		alternativas definidas para evaluación alternativa.</p>"
);

    // Menu and buttons
    public $mnu = array(
  "btnSbm"  =>  "Enviar",
  "lgd1"    =>  "Calculadora de prioridad AHP",
  "done"    =>  "Hecho",
  "next"    =>  "Próximo",
  "lgd2"    =>  "Menú alternativo",
  "btn1"    =>  "Guardar los juicios",
  "btn2"    =>  "Restablecer alternativas",
  "btn3"    =>  "Salvar como proyecto"

);
}
