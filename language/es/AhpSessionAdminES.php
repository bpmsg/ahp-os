<?php

class AhpSessionAdminES
{
    public $titles = array(
    'pageTitle'         => "Proyectos AHP - AHP-OS",
    'h1title'           => "<h1>AHP Administración del proyecto</h1>",
    'h2subTitle'        => "<h2>AHP-OS: fácil toma de decisiones racional</h2>",
    'h2ahpProjSummary'  => "<h2>Resumen del proyecto</h2>" ,
    'h2ahpSessionMenu'  => "<h2>Menú de sesión de AHP</h2>",
    'h2ahpProjectMenu'  => "<h2>Menú Proyecto AHP</h2>",
    'h2myProjects'      => "<h2>Mis proyectos AHP</h2>",
    'h3groupInpLnk'     => "<h3>Enlace de entrada de grupo</h3>" ,
    'h3projStruc'       => "<h3>Estructura del proyecto</h3>",
    'h4hierDefTxt'      => "<h4>Texto con la definición de la jerarquía</h4>",
);
    public $msg = array(
    'sDel'          => "<span class='msg'> La sesión <span class='var'>%s</span> fue exitosamente borrada</span>",
    'sDelp'         => "El/Los participante(s) fueron <span class='res'>%s</span> exitosamente borrados ",
    'pwcCompl'      => "Comparaciones por pares bajo el nombre <span class='var'>%s</span> completedas.",
    'pClsd'         => "<p class='msg'>El proyecto está cerrado. Haga clic en <i>Toggle Proj. Status</i> para abrirlo nuevamente.</p>",
    'pStat1'        => "El estado del proyecto cambió a ",
    'pStatO'        => "abierto.",
    'pStatC'        => "cerrado.",
    'selPart'       => "<span class='msg'> Participante(s) seleccionados: </span><span class='var'>%s</span>",
    'hInfo1'        => "<span class='msg'>La jerarquía de decisiones tiene prioridades definidas</span>",
    'hInfo2'        => "<span class='msg'>. El proyecto se puede utilizar para definir alternativas. <br>Click en <i>Use Hierarchy</i></span>",
    'hInfo3'        => "<span class='msg'> y el proyecto tiene <span class='var'>%g</span> alternativas definidas.</span>",
    'usrStat1'      => "<p class='msg'><small> AHP-OS tiene <span class='res'>%s</span> usuarios registrados, ",
    'usrStat2'      => "<span class='res'>%g</span> usuarios activos en las últimas %g horas.</small></p>",
    'usrStat3'      => "<p class='msg'>%s, tu tienes <span class='res'>%g</span> proyectos. ",
    'usrStat4'      => "El índice de uso de su programa es <span class=res>%g%%</span>. ",
    'usrDon1'       => "Por favor considere realizar una <a href='ahp-news.php'>donación</a>",
    'usrDon2'       => "Gracias por tu donacion"
);
    public $err = array(
    "invSess1"      => "Código de sesión no válido.",
    "invSess2"      => "Código de sesión no válido en url.",
    "noAuth"        => "Como no es el autor del proyecto, no puede eliminar participantes.",
    "pClosed"       => "Proyecto cerrado. No se permiten entradas de comparación por pares.",
    "noDel"         => "no se pudo eliminar.",
    "sLmt"          => "<p><span class='err'>Se alcanzó el límite de sesiones.</span> Primero elimine algunas sesiones antiguas. </p>"
);
    public $info = array(
    'sc'                => "El código de sesión es <span class='var'>%s</span>.",
    'scLnk1'        => "Proporcione este código de sesión o el siguiente enlace a sus participantes:</span><br>",
    'scLnk2'        => "<textarea rows='1' cols='78'>%s?sc=%s</textarea><br>",
    'scLnk3'        => "Ir al enlace de arriba: <a href='%s?sc=%s' >Group Input</a><br>",
    'pOpen1'        => "Haga clic en el enlace de la sesión en la tabla a continuación para abrir un proyecto.",
    'pOpen2'        => "<br>Crear un <a href='%s'>new hierarchy</a>.",
    'logout'        => "<div class='entry-content'>
                        En la página de administración de proyectos AHP puede administrar sus proyectos AHP: 
                        crear nuevas jerarquías, abrir, editar o eliminar y ver proyectos existentes. 
                        <p class='msg'>Debe ser un usuario registrado e iniciar sesión para manejar proyectos.</p>
                        <p><a href='%s'>Hecho</a></p></div>"
);
    public $mnu = array(
    'lgd1'          => "Menú de administración de sesiones",
    'lbl1'          => "Código de sesión del proyecto: ",
    'btnps1'        => "Abrir Proyecto",
    'btnps2'        => "Nuevo Projecto",
    'btnps3'        => "Hecho",
    'btnps4'        => "Importa Projecto",
    'lgd2'          => "Menú de administración de proyectos",
    'btnpa1'        => "Ver resultado",
    'btnpa2'        => "Entrada de PWC",
    'btnpa3'        => "Usar jerarquía",
    'btnpa4'        => "Renombrar",
    'btnpa5'        => "Editar",
    'btnpa6'        => "Borrar participantes seleccionados",
    'btnpa7'        => "Borrar Proyecto",
    'btnpa8'        => "Alternar estado del proyecto",
    'btnpa9'        => "Hecho",
    'btnpa10'       => "Exporta proyecto"
);
}
