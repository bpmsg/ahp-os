<?php
/* AHP hierarchy project calculation with alternatives
 * @author Klaus D. Goepel
 * @package AHP-OS
 * @since   2013-12-01 release first version ahp hierarchy
 * @version 2018-09-15 last mod w/o SVN
 *
 * $_SESSION['user_name'] is the login name
 * $_SESSION['name'] actual name, can be participant's, login or project author
 * $_SESSION['owner'] is the name of the project owner
 * $_SESSION['sessionCode'] active session code for group eval ($groupMode = true)
 * $_SESSION['sessionCode'] and $_SESSION['name'] is also set from ahp-hiergini
 *
 * $groupMode is set, when $_SESSION['groupSession'] is set
 * $_SESSION['groupSession'] is set when PCs are expected
 *
 * Menus:
 * newHierMenu: h_submit, save, download, csv, prio, reset, exit
 * AHP, eval
 * ahpGroupMenu: result, group, exit
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
$class = 'AhpHierarchy' . $lang;
$ahpHier = new $class();

// reset in case back from edit form
if (isset($_SESSION['REFERER'])) {
    unset($_SESSION['REFERER']);
}
$loggedIn = $login->isUserLoggedIn();

$version = substr('$LastChangedDate: 2022-02-20 10:18:07 +0800 (So, 20 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 162 $', "$");

$defaultHierarchy = "AHP-Project:Crit-1,Crit-2,Crit-3;";

$ahpH =     new AhpHierAlt();
$ahp =      new AhpCalcIo(2);

// ***** Start *****

$limitError   = ""; // Error text for program limits exceeded
$inputError   = ""; // Error text for hierarchy input
$checkWarn2   = ""; // Warnings for groups session mode

$groupMode = false;
$hierMode = true;
// $hflg = false; // hierarchy flag: hierarchy pwc is done
// $aflg = false; // alternatives: alternative pwc is done
$groupOwner;
$leave = false;

    // -- RESET SESSION 'reset' ---
    if (isset($_POST['reset'])) {
        $ahpH->closeHier();
    }
    // -- EXIT SESSION 'reset' (exit in menu) ---
    if (isset($_POST['exit'])) {
        $ahpH->closeHier();
        unset($_SESSION['sessionCode']);
        if ($loggedIn) {
            header("Location: " . $urlSessionAdmin);
        } else {
            header("Location: " . $urlAhp);
        }
        exit();
    }

    // --- GET SESSION CODE (reads url parameter)
    $ahpDb = new AhpDb();
    $sessCod = $ahpH->getSessionCode();
    if ($sessCod) { // url parameter given, check validity
        $project = $ahpDb->readProjectData($sessCod);
        // read project data
        if (!empty($project)) {
            $_SESSION['owner'] = $project['project_author'];
            $text = $_SESSION['hText'] = $project['project_hText'];
            $_SESSION['description'] = $project['project_description'];
            if (!empty($project['project_alt'])) {
                $_SESSION['alt'] = $project['project_alt'];
                $_SESSION['altNum'] = count($project['project_alt']);
            } else {
                unset($_SESSION['alt']);
                unset($_SESSION['altNum']);
            }
            unset($_SESSION['pwc']);

            // check whether logged in user is project owner
            // if so, resume group session to evaluate as participant
            if ($loggedIn && $_SESSION['user_name'] == $project['project_author']) {
                $_SESSION['sessionCode'] = $sessCod;
            }
        } else {
            $inputError= $ahpDb->getErrors();
        }
    } elseif (isset($_SESSION['groupSession']) && isset($_SESSION['sessionCode'])) {
        $sessCod = $_SESSION['sessionCode'];
        $project = $ahpDb->readProjectData($sessCod);
        // read project data
        if (!empty($project)) {
            // READ PROJECT DATA
            $_SESSION['owner'] = $project['project_author'];
            // check wether logged in user is project owner
            if ($loggedIn && $_SESSION['user_name'] == $project['project_author']) {
                $_SESSION['name'] = $_SESSION['user_name'];
            }
            $groupMode = true;
        } else {
            $inputError= $ahpDb->getErrors();
        }
    }

    // -- Check group owner
    $groupOwner = $groupMode
        && isset($_SESSION['name'])
        && ($_SESSION['name'] == $_SESSION['owner']);

    // -- GET NEW HIERARCHY 'h_submit' ---
    if (isset($_POST['h_submit'])) {
        $ahpH->newHsession();
    }

    // -- HIERARCHY TEXT FROM SESSION OR DEFAULT
    if (isset($_SESSION['hText'])) {
        $text = $_SESSION['hText'];

        // -- CLEAR PRIORITIES FROM text 'prio' ---
        if (isset($_POST['prio'])) {
            $text = $ahpH->clearTextPrio($text);
            $_SESSION['hText'] = $text;
            $ahpH->clearPwc();
            // Clear alternatives
            unset($_SESSION['altNum']);
            unset($_SESSION['alt']);
            unset($_SESSION['pwcaDone']);
            unset($ahpH->prioAlt);
            $ahpH->altNum = $altnum = 2;
            $ahpH->alt = array();
        }
    } else {
        // default hierarchy
        $text = (isset($_POST['reset']) ? "" : $defaultHierarchy);
        $_SESSION['hText'] = $text;
    }

    // -- SET PROPERTIES ---
    $hierarchy = $ahpH->setHierarchy($text);
    if (!empty($hierarchy)) {
        $_SESSION['project'] = $ahpH->project;
        $_SESSION['pwcDone'] = $ahpH->pwcDoneFlg;

        // -- GET ALTERNATIVES -- this part needs to be reworked
        if (isset($_SESSION['altNum'])) {
            $ahpH->altNum = $altNum = $_SESSION['altNum'];
        } else {
            // default
            $ahpH->altNum = $altNum = 2;
        }
        // reads alternatives from session parameter if set
        if (isset($_SESSION['alt'])) {
            $ahpH->alt = $_SESSION['alt'];
            $hierMode = false;
        }
        if (isset($_SESSION['pwcaDone'])) {
            $ahpH->altNum = $altNum;
            $ahpH->pwcaDoneFlg = true;
        }
        if (isset($_SESSION['prioAlt'])) {
            $ahpH->altNum = $altNum;
            $ahpH->prioAlt = $_SESSION['prioAlt'];
        } else {
            $altNum = 0;
        }
        $ahpH->setPglb();

        // -- DOWNLOAD 'download' ---
        if (isset($_POST['download'])) {
            $ds = (isset($_POST['csv']) ? ',' : '.');
            $txtbuf = $ahpH->exportHierarchyTable($ds);
            $txtbuf .= $ahpH->showAllMat($ds);
            /* track download via piwik
            if(is_object($webHtml->t))
                $webHtml->t->doTrackAction($myUrl, 'download');
            */
            $ahp->txtDownload('ahp.csv', $txtbuf);
            unset($_POST['download']);
            session_write_close();
            die();
        }

        // -- GET AHP PRIORITIES 'AHP' - redirect to ahp_hiercalc.php
        if (isset($_POST['AHP'])) {
            $node = key($_POST['AHP']);
            $crit = $ahpH->getTreeNode($ahpH->hierarchy, $node);
            if (is_array($crit)) {
                $n = count($crit);
            } else {
                trigger_error("H: " . $text . "Nd: " . $node, E_USER_NOTICE);
            }
            $_SESSION['hText'] = $text;
            if ($n > CRITMAX) {
                $limitError = "More than" . CRITMAX . "criteria, please modify hierarchy";
            } else {
                session_write_close();
                // -- redirect to AHP calculation
                $url = $ahp->getUrlCode($urlAhpC, $n, $node, $crit);
                header("Location: $url");
                die();
            }
        }

        // -- EVALUATE ALTERNATIVES 'eval' - redirect to ahp_alt.php
        if (isset($_POST['eval'])) {
            //code url parameter
            $urlAltCalc = $ahp->getUrlCode($urlAlt, $ahpH->altNum, $ahpH->project, $ahpH->alt);
            header("Location: $urlAltCalc");
            die();
        }

        // -- SAVE HIERARCHY 'save' - redirect to ahp-hiergini.php
        // -- ONLY if user is registered and logged in
        if (isset($_POST['save'])) {
            if (isset($_SESSION['user_name'])) {
                $_SESSION['name'] =  $_SESSION['user_name'];
                $_SESSION['owner'] = $_SESSION['user_name'];
            }
            if (isset($_SESSION['groupSession']) && !empty($_SESSION['sessionCode'])) {
                $sessCod = $_SESSION['sessionCode'];
            } else {
                $sid = startNewSession();
                $_SESSION['mod'] = true;
                if ($hierMode) {
                    $_SESSION['hText'] = $text;
                    unset($_SESSION['pwc']);
                }
                header("Location: $urlGroupInit");
                exit();
            }
        }

        // -- VIEW GROUP RESULTS 'result' - redirect to ahp-group.php
        if (isset($_POST['result'])) {
            if (isset($_SESSION['pwc'])|| $groupOwner) {
                $url = $urlGroupRes . "?sc=" . $sessCod;
                unset($_SESSION['groupSession']);
                $ahpH->closeHier();
                header("Location:" . $url);
                die();
            } else {
                $checkWarn2 = "Please complete all pairwise comparisons first (Click on ";
                $checkWarn2 .= ($hierMode ? "" : "\"Alternatives\", then")
                . "<input style='border:2px solid #c00;' type='button' value='AHP'> button).";
            }
        }

        // -- SUBMIT DATA FOR GROUP EVALUATION 'group' -
        if (isset($_POST['group'])) {
            if (isset($_SESSION['pwc']) || $ahpH->pwcaDoneFlg) {
                $ahpDb = new AhpDb();
                $insState = $ahpDb->submitGroupData($sessCod, $_SESSION['name'], $_SESSION['pwc']);
                // store pwc in session file
                if (!empty($insState)) {
                    if ($insState['upd'] >0) {
                        $checkWarn2 = sprintf($ahpHier->msg['giUpd'], $insState['upd']);
                    }
                    if ($insState['ins'] >0) {
                        $checkWarn2 = sprintf($ahpHier->msg['giUpd'], $insState['ins']);
                    }
                    if ($ahpH->pwcDoneFlg || $ahpH->pwcaDoneFlg) {
                        $leave = true;
                        $checkWarn2 .= $ahpHier->msg['giTu'];
                    } else {
                        $checkWarn2 .= $ahpHier->msg['giNcmpl'];
                        $leave = false;
                    }
                } else {
                    if ($ahpH->pwcDoneFlg || $ahpH->pwcaDoneFlg) {
                        // pairwise comparison done
                        $leave = true;
                    } else {
                        $checkWarn2 = $ahpHier->msg['giNds'] . implode(", ", $ahpDb->err);
                    }
                }
                $ahpDb = null;
            } else {
                // pwc not completed
                $checkWarn2 = $ahpHier->msg['giPcmpl'];
                if (!$hierMode) {
                    $ahpHier->info['giPcmpl'];
                }
            }
        } // ---endif isset $_POST['group']
    } else { // hierarchy empty
        $inputError = (empty($ahpH->err) ? $ahpHier->err['giH'] : implode("", $ahpH->err));
    }

// --- HTML page ---
$webHtml =  new WebHtml($ahpHier->titles['pageTitle']);

    if (!$groupMode) {
        include('includes/login/form.login-hl.php');
    } else { // no login field
        echo "<div style='display:block;float:left'>
            $loginHeaderText</div>
            <div style='clear:both;'></div>";
    }

    echo "<form method='POST' action='$urlAct'>";

    echo $ahpHier->titles['h1title'];

    if (defined('SYS_MSG')) {
        echo "<p class='hl'>" . SYS_MSG . "</p>";
    }

    echo $ahpHier->titles['h2subTitle'];

    if (!$loggedIn) {
        if (!$groupMode) { // Intro text for people not logged in
            echo $ahpHier->info['intro'];
            echo $ahpHier->msg['lgin'];
        }
    } elseif (isset($_SESSION['sessionCode'])) {
        if ($groupMode) {
            printf($ahpHier->msg['pInp'], $_SESSION['sessionCode']);
        } else {
            printf($ahpHier->msg['pMod'], $_SESSION['sessionCode']);
        }
    } else {
        echo $ahpHier->msg['pNew'];
    }

    echo "<div class='page-break'></div>";
    echo "\n<!-- HIERARCHY -->\n";

    printf($ahpHier->titles['h3Proj'], $ahpH->project);
    if (isset($_SESSION['description'])) {
        echo $ahpHier->titles['h4pDescr'];
        echo "<p style='margin-right:40px;'>",$_SESSION['description'],"</p>";
    }

    // --- Display errors
    if ($limitError || $inputError) {
        // --- Something wrong - display error messages ---
        echo "<span class='err'>$limitError</span>";
        echo "<span class='err'>$inputError</span>";

        // New Hierarchy Input
        include 'views/hierarchyMenu1.html';
    } else {	 // no errors
        $ahpFlg = (
            (!$loggedIn && !$groupMode)
                || ($groupMode && $hierMode && !$leave)
        );
        $altFlg = (!$loggedIn && !$groupMode)
            || ($groupMode && !$hierMode)
            || ($loggedIn && $ahpH->pwcDoneFlg && !$groupMode);
        $shwa = ($ahpH->pwcaDoneFlg || !empty($ahpH->prioAlt) ? $altNum : 0);

        // --- Display hierarchy table ---
        // --- Show alternatives only after pwc is completed
        $ahpH->displayHierarchyTable($shwa, $altFlg, $ahpFlg);

        if (!$groupMode) { // --- individual or group session
            echo $ahpHier->titles['h3hInfo'];
            // New Hierarchy Input ($text, $loggedIn)
            if ($hierMode) {
                echo $ahpHier->msg['hMode'];
            } else {
                printf($ahpHier->msg['aMode'], $ahpH->altNum);
            }

            // show warnings
            if (!empty($ahpH->wrn) || $checkWarn2 !="") {
                echo "<p class='err'>";
                echo(empty($ahpH->wrn) ? "" : implode(", ", $ahpH->wrn));
                echo " $checkWarn2";
                echo "</p>";
            }

            $ahpH->displayHierarchyInfo();
            echo $ahpHier->titles['h2ieHier'];
            // --- input/edit textfield for hierarchy definition ---
            include 'views/hierarchyMenu1.html';
        } else { // --- Group Input Session
            $ahpDb = new AhpDb();
            $pCnt = $ahpDb->getParticipants($sessCod);
            $pCnt = count($pCnt);

            echo "<p class='msg'>";
            if (!$ahpH->pwcDoneFlg && !$ahpH->pwcaDoneFlg) {
                if ($hierMode) {
                    echo $ahpHier->info['clkH'];
                } else {
                    echo $ahpHier->info['clkA'];
                }
                echo $ahpHier->info['clkS'];
            } elseif (!$leave) {
                echo $ahpHier->info['clkS'];
            }
            // --- show warnings
            if (!empty($ahpDb->wrn) || $checkWarn2 !="") {
                echo "<p class='err'>";
                echo(!empty($ahpDb->wrn) ? implode(", ", $ahpDb->wrn) : "") ;
                echo "$checkWarn2";
            }
            echo "</p>";

            // --- ahpGroupMenu --- $groupOwner, uses $pCnt, $leave
            include 'views/hierarchyMenu2.html';
            echo "<p></p>";
            $ahpDb = null;
        }
    } // endif if( $limitError || $inputError )

    echo "</form>";
    echo "<div class='page-break'></div>";
    $webHtml->webHtmlFooter($version);
