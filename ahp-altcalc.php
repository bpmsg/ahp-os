<?php
/**
* AHP project calculation of priorities for alternatives
* is called from ahp_alt.php (Evaluation of alternatives)
* Calculates AHP priority vectors called from ahp_alt
*
* @author Klaus D. Goepel
* @copyright 2014 Klaus D. Goepel
* @package AHP
* @uses ahp_io Class
* @version 2014-01-09
* @version 2019_05_10 last version w/o SVN
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

include 'includes/config.php';

$login = new Login();

$lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");
$class = 'AhpPrioCalc' . $lang;
$ahpPrioCalc = new $class();


// reset in case back from edit form
if (isset($_SESSION['REFERER'])) {
    unset($_SESSION['REFERER']);
}

$version = substr('$LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 120 $', "$");

$project = ""; // project name, only set in AhpDb::setSessfmPrjc($sc)
// --- initialize classes
$ahp =  new AhpCalcIo(2);

$pwc = array();
$submit = array(
    'txt' => $ahpPrioCalc->mnu['btnSbm'],
    'var' => 'calc');
$cr = 0.;

// --- MAIN ---
    if (!isset($_SESSION['hText'])) {
        // redirect to main program
        header("Location: $urlAlt");
        die();
    }

// --- GET CRITERIA
// set n, criteria and return url
    $errGet =  $ahp->setNamesFromGet($ahp->n, $ahp->header, $ahp->criteria, CRITMAX, 'Crit-');
    $criteria = $ahp->criteria;
    $n = 	   $ahp->n;
    $m_pc =   $ahp->get_npc($n); // number of pair-wise comparison for n criteria
    $header =  $ahp->header;
    if (isset($_SESSION['project'])) {
        $project = $_SESSION['project'];
    }
    $actUrl = $ahp->getUrlcode($urlAct, $n, $header, $criteria);
    $retUrl = $ahp->getUrlCode($urlAlt, $n, $project, $criteria);
// -- RETURN TO ALTERNATIVES
    if (isset($_POST['calc'])) {
        // write priority vector as session parameter
        session_write_close();
        header("Location: $retUrl");
        die();
    }
    // check POST parameter and set pwc from there
    if (isset($_SESSION['pwc'][$header])) {
        $pwc = $_SESSION['pwc'][$header];
        $ahp->pwc = $pwc;
    } else {
        $pwc = $ahp->set_pwc_def($n);
    }
    $err_post = $ahp->set_pwc($n);

// Sets $_SESSION['pwc'] and $_SESSION['prioAlt']
// ok - Start calculation of results
    if ($err_post == 0) {
        $pwc = $ahp->pwc;
        $_SESSION['pwc'][$header] = $pwc;
        // solve eigen vector with evm
        $ahp->set_evm($pwc);
        $ev = $ahp->evm_evec;
        $cr = $ahp->cr_alo;
        $_SESSION['prioAlt'][$header] = $ev;
    }

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpPrioCalc->titles3['pageTitle']);
    // no login field in this form
    echo '<div style="display:block;float:left">',
            $loginHeaderText,'</div>
			<div style="clear:both;"></div>';
    echo "\n<!-- DO COMPARISON -->\n";
    echo $ahpPrioCalc->titles3['h1title'];
    printf($ahpPrioCalc->titles3['h2subTitle'], $project);
    printf($ahpPrioCalc->titles1['h3Pwc'], $ahp->header);

    printf($ahpPrioCalc->msg['nPwc'], $m_pc);
    echo $ahpPrioCalc->info['doPwcA'];

    if ((float) $cr >= 0.1) {
        echo $ahpPrioCalc->info['adj'];
    }

    echo $ahpPrioCalc->info['scale'];
    printf($ahpPrioCalc->info['pwcQA'], $ahp->header, "?");

    // get pairwise comparisons
    $hdl = sprintf($ahpPrioCalc->msg['pwcAB'], $ahp->header);
    $ahp->get_pair_comp($actUrl, $submit, $err_post, $hdl, $pwc);

    if ($err_post == 0) {
        echo "\n<!-- DISPLAY RESULT -->\n";
        echo $ahpPrioCalc->titles1['h3Res'];
        $ahp->printVector($ahp->criteria, $ev, 1);
    }

    echo "<p></p>";
$webHtml->webHtmlFooter($version);
