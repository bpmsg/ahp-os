<?php
class AhpHierarchyES {

public $titles = array(
  "pageTitle"   =>  "Jerarquía AHP - AHP-OS",
  "h1title"     =>  "<h1>Jerarquía AHP</h1>",
  "h2subTitle"  =>  "<h2>Jerarquía de decisiones de AHP-OS</h2>",
  "h4pDescr"    =>  "<h4>Descripción del Proyecto</h4>",
  "h3hInfo"     =>  "\n<h3>Información de la jerarquía</h3>",
  "h3Proj"      =>  "<h3>Proyecto: <span class= 'var'>%s</span></h3>",
  "h2ieHier"    =>  "<h2>Entrada/Editar jerarquía</h2>"
);
public $err = array(
  "giH"     =>  "Error en la entrada de la jerarquía"
);
public $msg = array(
  "lgin"    =>  "<span class='msg'>Para una funcionalidad completa, regístrese e inicie sesión.</span>",
  "pInp"    =>  "<p class='msg'>Entrada para proyecto <span class='var'>%s</span></p>", 
  "pMod"    =>  "<p class='msg'>Modificación de proyecto <span class='var'>%s</span></p>",
  "pNew"    =>  "<p class='msg'>Nuevo proyecto</p>",
  "hMode"   =>  "<p class='msg'>Modo: evaluación de la jerarquía</p>",
  "aMode"   =>  "<p class='msg'>Modo: evaluación alternativa<span class='var'>%g</span> alternatives</p>",
  "giUpd"   =>  "<span class='msg'> %g juicio(s) actualizado(s). </span>",
  "giIns"   =>  "<span class='msg'> %g sentencia(s) insertada(s). </span>",
  "giTu"    =>  "¡Gracias por su participación!",
  "giNcmpl" =>  "¡Aún no se han completado las comparaciones por pares!",
  "giNds"   =>  "No hay datos almacenados. ",
  "giPcmpl" =>  "Primero complete todas las comparaciones por pares. "
);

public $info = array(
  "intro"   =>  "<div class='entry-content'><p style='text-align:justify;'>
		Defina una jerarquía de decisión de criterios y calcule sus ponderaciones 
		basándose en comparaciones por pares utilizando el Proceso de jerarquía analítica AHP. 
                En un paso siguiente, usted define un conjunto de alternativas y las evalúa con respecto
		a su lista de criterios para encontrar la alternativa más preferible y resolver su problema
		de decisión.
                </p><p style='text-align:justify;'>
                Para un cálculo simple de prioridades basado en comparaciones por pares
                puedes usar la <a href='ahp-calc.php'> Calculadora de prioridad AHP </a>. 
                Si le gusta la herramienta y la encuentra útil, haga clic en el botón <i>like</i>
		al final de la página. ¡Gracias!</p></div>",
  "clkH"    =>  "Click en <input type='button' class='btnr' value='AHP'> para completar comparaciones por pares. ",
  "clkA"    =>  "Click en <b>Alternatives</b>, then <b>AHP</b> para completar comparaciones por pares.",
  "clkS"    =>  "Click en <input type='button' value='Save judgments'> para finalizar y guardar sus juicios.",
  "txtfld"  =>  "Ingrese o edite texto en el área de texto a continuación, luego envíelo. (See <a href='ahp-examples.php'>examples</a>)",
  "synHelp" =>  "<br><span style='text-align:justify; font-size:small;'>
                En el área de entrada de texto de arriba puede definir una nueva jerarquía. 
                Los nodos son seguidos por un<b><i>colon</i></b>, las hojas están separadas por <b><i>coma</i></b>, 
                y cada rama tiene que ser terminado por un <b><i>semicolon</i></b>. 
                El carácter tilde (~) se descarta. Los nombres de las categorías y subcategorías deben ser únicos. 
		No se permiten números como nombres de categorías,
		<i>e.g.</i> use \"100 $\" instead of \"100\". Una categoría no puede tener una sola subcategoría. De forma predeterminada,
		todas las prioridades se establecen por igual para sumar el 100% en cada categoría o subcategoría. 
		Nota: la entrada distingue entre mayúsculas y minúsculas.</span>",
  "nlg"     =>  "<p class='hl'>Como usuario registrado, puede descargar prioridades y guardar la jerarquía definida como proyecto.</p>",
  "lgi"     =>  "<p class='msg'>Para la evaluación de prioridad de AHP <i> Guardar / Actualizar </i> y abrir desde la página de su proyecto, para comenzar comparaciones por pares.
		Para la evaluación de prioridad de AHP <i> Guardar / Actualizar </i> y abrir desde la página de su proyecto, para comenzar comparaciones por pares.                
		<i>Guardar</i> del menú alternativo.</p>",
  "giPcmpl" =>  "Click en <input type='button' value='Alternatives'> then <input class='btnr ' type='button' value='AHP'>"
);

public $mnu = array(
  "lgd11"   =>  "Menú de entrada de jerarquía",
  "btn11"   =>  "Enviar",
  "btn12"   =>  "Guardar / actualizar",
  "btn13"   =>  "Descargar(.csv)",
  "lbl11"   =>  "dic. comma",
  "btn14"   =>  "Restablecer prioridades",
  "btn15"   =>  "Restablecer todo",
  "btn16"   =>  "Hecho",
  "lgd21"   =>  "Menú de entrada de grupo",
  "btn21"   =>  "Salvar los juicios",
  "btn22"   =>  "Ver resultado del grupo",  
);
}


