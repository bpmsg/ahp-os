<?php

class AhpNewsES
{
    public $titles1 = array(
    'pageTitle' =>  "AHP-OS Noticias",
    'h1title'   =>  "<h1>AHP Online System - BPMSG</h1>",
    'h2welc'    =>  "<h2>Bienvenido %s!</h2>",
    'h3release' =>  "<h3>AHP-OS Release %s (%s)</h3>",
    'h3don'     =>  "<h3>Donaciones</h3>",
    'h3news2'   =>  "<h3>AHP-OS en otros idiomas</h3>",

);

    public $msg = array(
    'tu'    =>  "Muchas gracias!",
    'cont'  =>  "Continuar"
);

    public $info = array(
    'news0' =>  "Ahora hemos modificado el programa para que <span class='hl'>los participantes puedan cambiar sus 
                comparaciones de pares</span>, siempre que el estado del proyecto sea \"open\". 
                Como propietario del proyecto, puede comprobar el estado del proyecto en el menú 
                \"Menú de administración de proyectos\" con <i>Alterna estado del proyecto</i>.",
    'news1' =>  "<p>Esta última versión de AHP-OS incluye una función para analizar las decisiones del grupo. 
                En <span class='hl'>Análisis de clúster de consenso de grupo</span> en la página principal de AHP-OS, 
                puede acceder a la página de consenso de AHP. El programa intenta agrupar a un grupo de tomadores de 
                decisiones en subgrupos más pequeños con mayor consenso. Para cada par de tomadores de decisiones, 
                <span class='hl'>Shannon α y β entropía</span> se utiliza para calcular la similitud de las prioridades. 
                Este análisis puede ser útil si en un grupo de cuatro o más participantes el consenso general del grupo es bajo, 
                pero desea ver si el grupo se puede dividir en subgrupos más pequeños de participantes con mayor consenso.</p>
                <p>Más información <a href='https://bpmsg.com/group-consensus-cluster-analysis/' target='_blank'>aquí</a></p>",
    'news2' => "<p>Esta versión soporta varios idiomas para AHP-OS. Actualmente un Inglés, Alemán, Portugués y Español. 
                Si está interesado en contribuir y traducir los archivos a su idioma,
                por favor póngase en contacto conmigo.</p>",
    'don'   =>  "<p>
                Antes de comenzar: Si eres un usuario activo o te gusta el programa, por favor ayudanos a mantener la web con vida.
                Esta página tiene costos de hosting, certificaciones, protección contra spam y mantenimiento y quiero mantener
                AHP-OS gratis para todos los usuarios. Como donante, su cuenta se mantendrá activa sin la solicitud de 
                reactivación, incluso si no accedes a él durante un período superior a 3 meses.
                </p>"
);
}
