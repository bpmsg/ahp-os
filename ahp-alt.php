<?php
/** AHP hierarchy project calculation of alternatives
* @author Klaus D. Goepel
* @package AHP-OS
* @since 2013-12-01 release first version ahp hierarchy
* @version 2017-10-04 last version w/o SVN
* @todo: Alternative menu: check for saved judgments b4 "done"!
*
*
* Copyright (C) 2022  <Klaus D. Goepel>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <https://www.gnu.org/licenses/>.
*
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
$loggedIn = $login->isUserLoggedIn();

$ahpH =    new AhpHierAlt();
$ahp =     new AhpCalcIo(2);

$version = substr('$LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 120 $', "$");

// function to set url string for form action to get new n and alternative names
// equals file name with "1"
function setUrlNewn($urlact)
{
    return substr_replace($urlact, "1.php", -4);
}

// ***** Start *****

$urlNewAlt = setUrlNewn($urlAct);	// url to get new n and alternatives
$errMsg ="";
$checkWarn = "";
// -- ALTNUM, PROJECT, ALTERNATIVES from GET PARAMETER
    $getErr = $ahp->setNamesFromGet($ahpH->altNum, $ahpH->project, 
		$ahpH->alt, ALTAHP, "Alt-");
    $project = $ahpH->project;
    $altNum = $ahpH->altNum;
    $_SESSION['altNum'] = $altNum;
    $alternatives = $ahpH->getAlternatives();
    $_SESSION['alt'] = $alternatives;


// -- GET SESSION MODE -- check condition
    if (isset($_SESSION['groupSession'])) {
        $groupMode = true;
    } else {
        $groupMode = false;
    }

// -- Reset alternatives
    if (isset($_POST['resAlt'])) {
        unset($_SESSION['alt']);
        unset($_SESSION['altNum']);
        unset($_SESSION['prioTot']);
        unset($_SESSION['prioAlt']);
        unset($_SESSION['pwc']);
        unset($_SESSION['pwcaDone']);
        header("Location: $urlAhpH");
        die();
    }

// -- HIERARCHY TEXT FROM SESSION
    if (isset($_SESSION['hText'])) {
        // get hierarchy from session hText
        $text = $_SESSION['hText'];
        $ahpH->setHierarchy($text);
        $project = $ahpH->project;
        $ahpH->setPrioAltDef();
        // read priorities of alternatives from session parameter
        if (isset($_SESSION['prioAlt'])) {
            foreach ($_SESSION['prioAlt'] as $key => $values) {
                for ($iAlt = 0; $iAlt < $altNum; $iAlt++) {
                    if (isset($values[$iAlt])) {
                        $ahpH->prioAlt[$key][$iAlt] = $values[$iAlt];
                    } else {
                        $ahpH->prioAlt[$key][$iAlt] = 0.;
                        unset($_SESSION['pwc'][$key]);
                    }
                }
            }
        }
        $ahpH->getPglbFromPloc();
        $ahpH->setPrioTot();
        $_SESSION['prioTot'] = $ahpH->prioTot;

        // -- AHP calculation requested, redirect to ahp_altcalc.php
        if (isset($_POST['AHP'])) {
            $node = key($_POST['AHP']);
            $alt = $ahpH->alt;
            $url = $ahp->getUrlCode($urlAhpA, $ahpH->altNum, $node, $alt);
            session_write_close();
            header("Location: $url");
            die();
        }

        // -- redirect to  ahp-hierarchy.php
        if (isset($_POST['back'])) {
            if (isset($_SESSION['sessionCode']) && $loggedIn) {
                $ahpH->closeHier();
                header("Location: $urlSessionAdmin");
            } elseif (isset($_SESSION['sessionCode']) && !$loggedIn) {
                header("Location: $urlAhp");
            } else {
                header("Location: $urlAhpH");
            }
            die();
        }

        $actUrl = $ahp->getUrlCode($urlNewAlt, $altNum, $project, $alternatives);
        $urlAct = $ahp->getUrlCode($urlAct, $ahpH->altNum, $ahpH->project, $ahpH->alt);

        // -- SUBMIT DATA FOR GROUP EVALUATION 'group' -
        if (isset($_POST['group'])) {
            if (isset($_SESSION['pwc'])) {
                $ahpDb = new AhpDb();
                $insState = $ahpDb->submitGroupData(
					$_SESSION['sessionCode'], $_SESSION['name'], $_SESSION['pwc']);
                // store pwc in session file
                if (!empty($insState)) {
                    if ($insState['upd'] >0) {
                        $checkWarn = sprintf($ahpPrioCalc->msg['giUpd'],
						$insState['upd']);
                    }
                    if ($insState['ins'] >0) {
                        $checkWarn = sprintf($ahpPrioCalc->msg['giUpd'],
						$insState['ins']);
                    }
                    if ($ahpH->pwcaDoneFlg) {
                        $leave = true;
                        $checkWarn .= $ahpPrioCalc->msg['tu'];
                    } else {
                        $checkWarn .= $ahpPrioCalc->err['pwcInc'];
                        $leave = false;
                    }
                } else {
                    if ($ahpH->pwcaDoneFlg) {
                        $leave = true;
                    }
                    $checkWarn = "<span class='err'>" . implode(", ", $ahpDb->err);
                    $checkWarn .= "</span>";
                }
                $ahpDb = null;
            } else {
                // pwc not completed
                $checkWarn  = $ahpPrioCalc->msg['noPwc1'];
                /* if(!$hierMode)
                    $checkWarn .= $ahpPrioCalc->msg['noPwc2'];  */
                $checkWarn .= $ahpPrioCalc->msg['noPwc3'];
            }
        } // ---endif isset $_POST['group']

        // -- Save -- only for logged in
        if (isset($_POST['save'])) {
            $_SESSION['name'] =  $_SESSION['user_name'];
            $_SESSION['owner'] = $_SESSION['user_name'];
            if (isset($_SESSION['groupSession']) && !empty($_SESSION['sessionCode'])) {
                $sessCod = $_SESSION['sessionCode'];
            } else {
                $_SESSION['mod'] = $_SESSION['sid'];
                header("Location: $urlGroupInit");
                exit();
            }
        }
    } else {
        $errMsg = "<span class='err'>Error - No hierarchy defined</span>
			 - Please <a href='$urlAhpH' >define hierarchy first</a>";
    }
    $ahpFlg = !$loggedIn || $groupMode && $ahpH->pwcaDoneFlg == false;

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpPrioCalc->titles3['pageTitle']);

    if ($groupMode) { // no login field for participants
        echo "<div style='display:block;float:left'>$loginHeaderText
			</div><div style='clear:both;'></div>";
    } else {
        include('includes/login/form.login-hl.php');
    }
    echo "\n<!-- START -->\n";
    echo $ahpPrioCalc->titles3['h1title'];

    if ($errMsg !="") {
        echo "<p>$errMsg</p>";
    } else {
        if (!$groupMode) {
            echo $ahpPrioCalc->titles3['h2alt'];
            echo $ahpPrioCalc->info['inpAlt'];
            $ahp->ahpHtmlGetNewNames($altNum, $project, $actUrl, ALTAHP, $getErr);
        } else {
            printf($ahpPrioCalc->titles3['h2subTitle'], $project);
            echo "\n<!-- ALTERNATIVE TABLE -->\n";
            echo $ahpPrioCalc->info['doPwcA1'];
        }
        echo $ahpPrioCalc->titles3['h3tblA'];
        // Show hierarchy with alternative table
        $ahpH->displayAlternativesTable($ahpFlg);
    }

    // Alternative menu $loggedIn && !$groupMode
    // when logged in return to hierarchy to submit
    if ($ahpH->pwcaDoneFlg) {
        $urlAct = $urlAhpH;
    }
    if ($checkWarn !="") {
        echo "<p>$checkWarn</p>";
    }

    echo $ahpPrioCalc->titles3['h3Mnu'];
    include 'views/ahpAlternativeMenu.html';

    // -- Result -- add graphic to result
    if ($ahpH->pwcaDoneFlg) {
        foreach ($ahpH->prioTot as $k => $val) {
            $dta[] = round(100*$val, 1);
        }
        $dta = array_combine($ahpH->alt, $dta);
        $dta = array( "nom" => $dta);
        $dta = urlencode(serialize($dta));
        echo "\n<!-- RESULT -->\n";
        echo $ahpPrioCalc->titles3['h3Res'];
        // --- Diagram ---
        echo "<div class='ofl'>";
        echo "<div style='margin-left:auto;margin-right:auto;width:700px;'>";
        echo "<img src='ahp-group-graph.php?dta=$dta' alt='alt-dia'>";
        echo "</div></div>";
        echo "<div style='margin-left:auto;margin-right:auto;'>";
        // --- Result table ---
        echo $ahpPrioCalc->titles3['h4Res'];
        $ahp->printVector($ahpH->alt, $ahpH->prioTot, 1);
        echo "</div>";
        echo "<div style='clear:both'>";
        $_SESSION['pwcaDone'] = true;
    }
$webHtml->webHtmlFooter($version);
