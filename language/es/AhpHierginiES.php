<?php

class AhpHierginiES
{
// Errors
    public $err = array(
  "pExc"    =>  "Se superó el número de proyectos, no se pueden guardar los datos. Cancela y elimina algunos de tus proyectos.",
  "noSc"    =>  "Proporcione un código de sesión. ",
  "noName"  =>  "Proporcione su nombre. ",
  "pwcCompl"=>  "Comparaciones por pares no <span class='var'>%s</span> completedas.",
  "hDefP"   =>  "La jerarquía no tiene prioridades definidas. El proyecto no se puede actualizar.",
  "unknw"   =>  "Error desconocido - propietario: %s retFlg: %g"
);

    public $titles = array(
  "pageTitle" =>  "Entrada a la sesión AHP AHP-OS",
  "h1Title"   =>  "<h1>Entrada a la sesión AHP</h1>",
  "subTitle1" =>  "Aporte del participante de AHP-OS",
  "subTitle2" =>  "Guardar / actualizar proyecto AHP",
  "subTitle3" =>  "Entrada de comparación por pares ",
  "h3Pwc"     =>  "<h3>Comparación por pares <span class='var'>%s</span></h3>",
  "h3Res"     =>  "<h3 align='center'>Prioridades resultantes</h3>",
  "h2siMnu"   =>  "<h2>Menú de entrada de sesión AHP</h2>"
);

    // Messages
    public $msg = array(
  "nProj"   =>  "Nuevo proyecto, haz clic en \"Ir \" para guardar",
  "pMod"    =>  "¡El proyecto existente será modificado y sobrescrito!"
);

    // Information
    public $info= array(
  "intro"   =>  "<div class='entry-content'>
                <p style='text-align:justify;'>AHP-OS es una herramienta en línea 
		para apoyar la toma de decisiones racional basada en el 
		<i> Proceso de jerarquía analítica </i> (AHP).
                Como participante seleccionado, por favor <b> ingrese su código 
		de sesión y nombre, trabaje en el cuestionario y envíe sus comentarios
		para la evaluación grupal </b>. Esto ayudará a reflejar sus aportes en la decisión final. ¡Gracias!</p>
                </div>",
  "act1"    =>  "Nuevo proyecto. Código de sesión %s. ",
  "act2"    =>  "Actualizar proyecto. ",
  "act3"    =>  "El proyecto tiene %g participante(s). ",
  "ok"      =>  "<p class='msg'>Okay. Haga clic en \"Ir \" para continuar</p>",
  "siSc"    =>  "Proporcione su código de sesión para participar en la sesión del grupo AHP ",
  "siNm1"   =>  "<a href='%s?logout'>Logout</a> como creador de la sesión para ingresar el nombre de otro participante. ",
  "siNm2"   =>  "Su nombre, ya que se reflejará en la sesión de grupo (3 - 25 caracteres alfabéticos).",
  "pName"   =>  "Nombre del proyecto AHP:",
  "pStat"   =>  "Estado del proyecto:",
  "pDescr"  =>  "Descripción breve del proyecto:",
  "descr" =>  "</br><small>El texto se mostrará a los participantes de la sesión de grupo, 400 caracteres como máximo.
              Puede utilizar etiquetas HTML, como & lt; em & gt; o & lt; font & gt; para enfatizar o resaltar el texto.</small>"
);

    // Menu and buttons
    public $mnu = array(
  "lgd1"    =>  "Entrada de sesión AHP",
  "lgd2"    =>  "Menú de entrada de sesión AHP",
  "sc"      =>  "Código de sesión:",
  "nm"      =>  "Tu nombre:",
  "btn1"    =>  "Ir",
  "btn2"    =>  "Comprobar entrada",
  "btn3"    =>  "Ver resultado del grupo",
  "btn4"    =>  "Reiniciar",
  "btn5"    =>  "Cancelar"
);
}
