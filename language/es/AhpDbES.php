<?php
class AhpDbES {
  public $titles = array(
  "h3pDat"    =>  "<h3>Datos del proyecto</h3>",
  "h3pPart"   =>  "<h3>Participantes en el proyecto</h3>\n",
  "h3pAlt"    =>  "<h3>Alternativas en el Proyecto</h3>"
  );

  public $err = array(
  "dbType"    =>  "No existe tal tipo de base de datos SQL: ",
  "scInv"     =>  "Código de sesión no válido ",
  "scInUse"   =>  "Código de sesión en uso ",
  "dbWrite"   =>  "No se pudieron escribir datos en la base de datos. Por favor, inténtelo de nuevo más tarde.",
  "dbWriteA"  =>  "Error de base de datos, no se pudieron almacenar alternativas ",
  "dbUpd"     =>  "No se pudieron actualizar los datos. Por favor, inténtelo de nuevo más tarde.",
  "dbSubmit"  =>  " ",
  "noSess"    =>  "No hay sesiones guardadas ",
  "dbReadSc"  =>  "Error de base de datos al obtener datos para ",
  "pClosed"   =>  "Proyecto cerrado. No se permiten entradas de comparación por pares.",
  "pNoMod"    =>  "El proyecto tiene participantes, la jerarquía no se puede modificar."
  );

  public $msg = array(
  "noSess"    => "No hay sesiones almacenadas"
  );

  public $tbl = array(
  "scTblTh"   => "<thead><tr>
                    <th>No</th>
                    <th>Sesión</th>
                    <th>Proyecto</th>
                    <th>Tipo<sup>1</sup></th>
                    <th>Status<sup>2</sup></th>
                    <th>Descripción</th>
                    <th>Parte.<sup>3</sup></th>
                    <th>creado</th></tr></thead>",
  "scTblFoot" =>  "<tfoot><tr><td colspan='8'>
                  <sup>1</sup> H: Jerarquía de evaluación prioritaria, A: Evaluación alternativas, 
                  <sup>2</sup>Status del proyecto: 1 - abierto 0 - cerrado, 
                  <sup>3</sup> Número de participantes</td>
                  </tr></tfoot>",
  "pdTblTh"   =>  "<thead><tr>
                    <th>Campo</th>
                    <th>Contenido</th></tr></thead>\n",
  "pdTblR1"   =>  "<tr><td>Sesión Code</td><td class='res'>%s</td></tr>\n",
  "pdTblR2"   =>  "<tr><td>Nombre del proyecto</td><td class='res'>%s</td></tr>\n",
  "pdTblR3"   =>  "<tr><td>Descripción </td><td class='res'>%s</td></tr>\n",
  "pdTblR4"   =>  "<tr><td>Autor</td><td class='res'>%s</td></tr>\n",
  "pdTblR5"   =>  "<tr><td>Fecha</td><td class='res'>%s</td></tr>\n",
  "pdTblR6"   =>  "<tr><td>Status</td><td class='res'>%s</td></tr>\n",
  "pdTblR7"   =>  "<tr><td>Tipo</td><td class='res'>%s</td></tr>\n",
  "paTblTh"   =>  "<thead><tr>
                    <th>No</th>
                    <th>Alternativas</th>
                  </tr></thead>\n",
  "ppTblTh"   =>  "<thead><tr>
                    <th>No</th>
                    <th>Sel</th>
                    <th>Name</th>
                    <th>Date</th>
                  </tr></thead>\n",
  "ppTblLr1"  =>  "<tr><td colspan='4'><input id='sbm0' type='submit' name='pselect' value='Refresh Selection'>&nbsp;<small>
                  <input class='onclk0' type='checkbox' name='ptick' value='0' ",
  "ppTblLr2"  =>  ">&nbsp;check all&nbsp;<input class='onclk0' type='checkbox' name='ntick' value='0' ",
  "ppTblLr3"  =>  ">&nbsp;uncheck all</small></td></tr>",
  "ppTblFoot" =>  "<tfoot><tr><td colspan='4'>
                    <small>Si no se selecciona ninguno, se incluirán todos.</small>
                  </td></tr></tfoot>"
  );

  public $info = array(
  "shPart"    => "<p><span class='var'>%g</span> participants. <button class='toggle'>Show/Hide</button> all.</p>"
  );
}

