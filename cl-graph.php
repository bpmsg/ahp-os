<?php
/* Bar graph generation for global priorities
* @author Klaus D. Goepel
* @copyright 2014 Klaus D. Goepel
* @package AHP-OS
* @version 2014-03-13
* @version 2016-06-03 last version w/o SVN
*
* Last Change: $LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $
* Revision: $Rev: 120 $
*
    Copyright (C) 2022  <Klaus D. Goepel>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/
include 'includes/phpgraphlib/phpgraphlib.php';
        $graph = new PHPGraphLib(600, 400);
        $data = unserialize(urldecode(stripslashes($_GET['dta'])));
        if (is_array($data['min']) && array_key_exists('min', $data)) {
            $graph->addData($data['min'], $data['nom'], $data['max']);
            $graph->setLegend(true);
            $graph->setLegendTitle('min', 'Result', 'max');
            $graph->setDataValues(false);
        } else {
            $graph->addData($data['nom']);
            $graph->setDataValues(true);
        }
        $graph->setTitle('Priorities Average (AIJ) over cluster');
        $graph->setupXAxis(29);
        $graph->setYValues(true);
        $graph->setDataFormat("percent");
        $graph->setBarColor("#D9D9D9", "green", "gray");
        $graph->createGraph();
