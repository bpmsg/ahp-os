<?php
/**
 * AHP hierarchy project calculation of priorities
 * is called from ahp-hierarchy
 * Calculates AHP priority vectors from AHP hierarchy
 *
 * @author Klaus D. Goepel
 * @copyright 2014 Klaus D. Goepel
 * @package AHP-OS
 * @since 2014-01-09
 * @version 2019-05-10 last mod w/o SVN
 *
 *  Copyright (C) 2022  <Klaus D. Goepel>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

include 'includes/config.php';

$login = new Login();

$lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");
$class = 'AhpPrioCalc' . $lang;
$ahpPrioCalc = new $class();

$version = substr('$LastChangedDate: 2022-02-16 11:53:54 +0800 (Mi, 16 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 139 $', "$");

// reset in case back from edit form
if (isset($_SESSION['REFERER'])) {
    unset($_SESSION['REFERER']);
}

if (!defined("CRITMAX")) {
    define("CRITMAX", 10);
}

$ahpH = new AhpHierAlt();
$ahp = new AhpCalcIo(2);

$pwc = array();

$submit = array(
        'txt' => $ahpPrioCalc->mnu['btnSbm'],
        'var' => 'calc');

$err = "";
$cr = 0.;

// --- MAIN ---
    if (isset($_SESSION['hText'])) {
        $text = $_SESSION['hText'];
    } else {
        // redirect to main program
        header("Location: $urlAhpH");
        die();
    }

// --- GET CRITERIA set n and criteria from url string if any
    $errGet =  $ahp->setNamesFromGet(
        $ahp->n,
        $ahp->header,
        $ahp->criteria,
        CRITMAX,
        'Crit-'
    );
    $criteria = $ahp->criteria;
    $n =       $ahp->n;
    $m_pc =   $ahp->get_npc($n); // pair-wise comparison for n criteria
    $ptitle =  $ahp->header;
    $actUrl = $ahp->getUrlCode($urlAct, $n, $ptitle, $criteria);

// --- check POST parameter and set pwc from there
    if (isset($_SESSION['pwc'][$ptitle])) {
        $pwc = $_SESSION['pwc'][$ptitle];
        $ahp->pwc = $pwc;
    } else {
        $pwc = $ahp->set_pwc_def($n);
    }
    $err_post = $ahp->set_pwc($n);
// -- RETURN TO HIERARCHY redirect after calculation is done
    if (isset($_POST['calc'])) {
        session_write_close();
        header("Location: $urlAhpH");
        die();
    }

    if ($err_post == 0) {
        // ok - Start calculation of results using AHP
        $pwc = $ahp->pwc;
        $_SESSION['pwc'][$ptitle] = $pwc;
        // solve eigen vector with evm
        $ahp->set_evm($pwc);
        $ev = $ahp->evm_evec;
        $cr = $ahp->cr_alo;
        // Insert calculated priorities in hierarchy text
        $nodeTxt = $ahp->getNodeTxt();
        $txtNa = explode(":", $nodeTxt);
        $node = $ahp->header;
        $text = $ahpH->setNewText($text, $node, $txtNa[1]);
        $_SESSION['hText'] = $text;
    // error message
    } elseif ($err_post == 2) {
        $err = $ahpPrioCalc->err['pgm'];
    }

    $pwc = $ahp->pwc;
    $project = $_SESSION['project'];
    $prjd = ($project == $ptitle ? "" : $project);

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpPrioCalc->titles2['pageTitle']);
    // No login field for pwc page
    echo '<div style="display:block;float:left">',
        $loginHeaderText,'</div>
        <div style="clear:both;"></div>';
    echo $ahpPrioCalc->titles2['h1title'];
    printf($ahpPrioCalc->titles2['h2subTitle'], $project);
    printf($ahpPrioCalc->titles1['h3Pwc'], $ahp->header);
    printf($ahpPrioCalc->msg['nPwc'], $m_pc);
    echo $ahpPrioCalc->info['doPwc'];

    if ((float) $cr >= 0.1) {
        echo $ahpPrioCalc->info['adj'];
    }

    echo $ahpPrioCalc->info['scale'];
    printf($ahpPrioCalc->info['pwcQ'], $ahp->header, "?");

    // get pairwise comparisons
    $hdl = sprintf($ahpPrioCalc->msg['pwcAB'], $ahp->header);
    $ahp->get_pair_comp($actUrl, $submit, $err_post, $hdl, $pwc);
    // to do - better error handling!
    echo $err;

    if ($err_post == 0) {
        echo "\n<!-- DISPLAY RESULT -->\n";
        echo $ahpPrioCalc->titles1['h3Res'];
        $ahp->printVector($ahp->criteria, $ev, 1);
    }
echo "<p></p>";
$webHtml->webHtmlFooter($version);
