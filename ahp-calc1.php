<?php
/*
 * AHP-OS

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
include 'includes/config.php';
session_start();

$version = substr('$LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 120 $', "$");

$lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");
$class = 'AhpPrioCalc' . $lang;
$ahpPrioCalc = new $class();

    $webHtml = new WebHtml($ahpPrioCalc->titles4['pageTitle'], 640);
    $ahp = new AhpCalcIo(2);

    /** Start **/
    $act = substr_replace($urlAct, ".php", -5);
    $ahp->setNamesFromGet($ahp->n, $ahp->header, $ahp->criteria, CRITMAX, 'Crit-');

    echo $ahpPrioCalc->titles4['h1title'];
    echo $ahpPrioCalc->msg['inpA'];
    $n = $ahp->n;
    $ahp->getNewNames($act, $n, $ahp->header, $ahpPrioCalc->wrd['crit']);

    echo "<p></p>";
$webHtml->webHtmlFooter($version);
