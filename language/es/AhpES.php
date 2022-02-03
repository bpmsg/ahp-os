<?php
class AhpES {

public $titles = array(
  "pageTitle"   =>  "AHP Online System - AHP-OS",
  "h1title"     =>  "<h1>AHP Online System - AHP-OS</h1>",
  "h2subTitle"  =>  "<h2>Toma de decisiones multi-criterio usando Analytic Hierarchy Process</h2>",
  "h2contact"   =>  "<h2>Contacto y Feedback</h2>"
);

public $msg = array(
  "tu"    =>  "Muchas gracias!",
  "cont"  =>  "Continuar"
);

public $info = array(
  "contact" =>  "<p>
		Por favor, sientete libre de dejar un comentario,
                <a href='%s'>comment</a> 
                un me gusta o comparte este sitio.
                </p>",
  "intro11" =>  "<div class='entry-content'><p style='text-align:justify;'>
		Esta solución <b>AHP web gratuita</b> es una herramienta de soporte
		para el proceso de toma de decisiones.
		Esta herramienta puede ser de ayuda en tu trabajo diario para abordar problemas de decisión simples y muy 
                complejos. Participa en una sesión de grupo o prueba algún 
                <a href='https://bpmsg.com/participate-in-an-ahp-group-session-ahp-practical-example/'> ejemplo práctico</a>. 
                Puedes descargar la <a href='docs/BPMSG-AHP-OS-QuickReference.pdf' target='_blank'>Guía de referencia rápida</a> 
                o el <a href='docs/BPMSG-AHP-OS.pdf' target='_blank'>AHP-OS manual</a>. 
		Para acceder a todas las funcionalidades necesitás hacer LOG IN, por favor si no tienes una cuenta 
		<a href='includes/login/do/do-register.php'>registrate aquí</a> como usuario nuevo ¡Es gratis!
                </p></div>",
  "intro12" =>  "<ol style='line-height:150%;'>
                <li><span style='cursor:help;' 
                title='Para gestionar proyectos completos de AHP y sesiones grupales, debes ser un usuario registrado e iniciar sesión.' > 
                  <a href='ahp-session-admin.php'>My AHP Projects</a></span></li>
                <li><span style='cursor:help;' 
                title='The AHP priority calculator calcula prioridades o ponderaciones para un conjunto de criterios basándose en comparaciones por pares.' >
                  <a href='ahp-calc.php'>AHP Priority Calculator</a></span></li>
                <li><span style='cursor:help;' 
                title='Maneje problemas de decisión completos bajo AHP. Defina una jerarquía de criterios y evalue alternativas.'>
                  <a href='ahp-hierarchy.php'>AHP Hierarchies</a></span></li>
                <li><span style='cursor:help;' 
                title='Participe en sesiones grupales de AHP para evaluar criterios o alternativas como miembro de un grupo' >
                <a href='ahp-hiergini.php'>AHP Group Session</a></span></li>
                </ol>",
  "intro13" =>  "<p style='text-align:justify;'>
                En las opciones 2 y 3, puede exportar los resultados como archivos .csv (con los valores separados por comas) para
                continuar su procesamiento en Ms Excel.",
  "intro14" =>	"<p style='text-align:justify;'>
				<b>Para los términos de uso por favor vea nuestro </b> 
                <a href='https://bpmsg.com/about/user-agreement-and-privacy-policy/'>acuerdo de usuario y política de privacidad.</a></p>",
  "intro15"	=>	"<p style='text-align:justify;'>
				Si te gusta el programa, <span class='err'>por favor ayudame con una 
                <a href='ahp-news.php'>donación</a> para mantener la web</span>.</p>",
  "intro16" =>  "<p><b>En tu paper académico por favor cita este trabajo como:</b><br>
                <code>Goepel, K.D. (2018). Implementation of an Online Software Tool for the Analytic Hierarchy 
                Process (AHP-OS). <i>International Journal of the Analytic Hierarchy Process</i>, Vol. 10 Issue 3 2018, pp 469-487,
                <br><a href='https://doi.org/10.13033/ijahp.v10i3.590'>https://doi.org/10.13033/ijahp.v10i3.590</a>
                </code></p>",

  "intro21" => "<h3>Introducción</h3>
                <div style='display:inline;'>
                <img src='images/AHP-icon-150x150.png' alt='AHP' style='float: left; height:15%; width:15%; padding:5px;'>
                </div><div class='entry-summary'>
                <p style='text-align:justify;'>
                AHP significa <i>Analytic Hierarchy Process</i>. Este es un método para soportar
                toma de decisiones multi-criterio, y fue originalmente desarrollado por el Prof. Thomas L. Saaty. AHP deriba 
                <i>ratio scales</i> a partir de comparaciones pareadas de criterios, y permite algunas pequeñas inconsistencias en
                los juicios. Las entradas pueden ser medidas reales, pero también opiniones subjetivas. Como resultado, 
                se calcularán <i>prioridades</i> (pesos) y un <i>ratio de consistencia</i>.
                A nivel internacional, el AHP se utiliza en una amplia gama de aplicaciones, por ejemplo, para la evaluación 
		de proveedores, en la gestión de proyectos, en el proceso de contratación o en la evaluación del desempeño de la empresa.</p></div>",

  "intro22" =>" <div style='display:block;clear:both;'>
                <h3>Beneficio de AHP</h3>
                <p style='text-align:justify;'>
                El uso de AHP como herramienta de apoyo para la toma de decisiones ayudará a
                ganar <i>una mejor comprensión de los problemas de decisión complejos</i>. Como necesita estructurar el problema como una
                jerarquía, le obliga a pensar en el problema, considerar posibles criterios de decisión y seleccionar
                los criterios más significativos con respecto al objetivo de decisión. Usar comparaciones por pares
                ayuda a descubrir y corregir inconsistencias lógicas. El método también permite\"traducir\" 
                opiniones subjetivas, como preferencias o sentimientos, en relaciones numéricas mensurables.
                AHP ayuda a tomar decisiones de forma más racional y a hacerlas más transparentes y
                comprensibles.
                </p>",

  "intro23" =>" <h3>Método</h3>
                <p style='text-align:justify;'>
                 Matemáticamente el método se basa en la solución de 
                 un problema de eigenvalues. Los resultados de las comparaciones por pares se organizan en una matriz.
                 El primer eigenvector normalizado (dominante) de la matriz da la escala de razón (la ponderación), el
                 eigenvalue la relación de consistencia.
                </p>",
  
  "intro24" =>" <h3>AHP Ejemplos</h3>
                <p style='text-align:justify;'>
		Para que el método sea más fácil de entender y mostrar la
                amplia gama de posibles aplicaciones, damos algunos <a href='ahp-examples.php' >ejemplos</a> 
                para diferentes jerarquías de decisión.
                </p>
                <p style='text-align:justify;'>
                Una simple introducción al método  
                es dada en <a href='docs/AHP-articel.Goepel.en.pdf' target='_blank'>aquí</a>.
                </p></div>"
);

public $tbl = array(
  "grTblTh"     =>  "\n<thead><tr class='header'><th>Participantes</th>",
  "grTblTd1"    =>  "<td><strong>Resultado grupal</strong></td>"

);
}

