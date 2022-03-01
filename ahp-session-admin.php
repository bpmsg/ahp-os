<?php
/*
 * Project Administration Page
 * @uses $_SESSION['sessionCode'], $_SESSION['hText']
 * @version 2017-04-11
 * @version 2017-09-28 last version w/o SVN
 *
 * OPEN, NEW, EXIT, VRES, GINP, HMOD, PMOD, STATUS, EXPORT, DELP, DEL, CLOSE
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

$storedSessions = array();
$psel = array();
$errMsg = "";
$msg = "";
$sessionName= "";
// todo: replace by $urlProjectImport from config file
$urlPjImport= "ahp-project-import.php";

$version = substr('$LastChangedDate: 2022-03-01 11:44:47 +0800 (Di, 01 Mär 2022) $', 18, 10);
$rev = trim('$Rev: 178 $', "$");

$login = new Login();

$class = 'AhpSessionAdmin' . $lang;
$sessionAdmin = new $class();

unset($_SESSION['alt']);
unset($_SESSION['altNum']);
if (isset($_SESSION['groupSession'])) {
    unset($_SESSION['groupSession']);
}
if (isset($_SESSION['mod'])) {
    unset($_SESSION['mod']);
}

$ahpDb =    new AhpDb();
$ahpAdmin = new AhpAdmin();
$ahpH =     new AhpHierAlt();

if ($login->isUserLoggedIn() === true) {
    // the user is logged in.
    $sid  = $phpUtil->startNewSession();
    $name = $_SESSION['name'] = $_SESSION['user_name'];

    /*
    *  --- MENU SWITCH ---
    */

    // --- EXIT ---
    if (isset($_POST['EXIT'])) {
        header('HTTP/1.0 200');
        header("Location: " . $urlAhp);
    }

    // --- NEW ---
    if (isset($_POST['NEW'])) {
        unset($_SESSION['sessionCode']);
        unset($_SESSION['ipart']);
        header('HTTP/1.0 200 ok');
        header("Location: " . $urlAhpH);
    }

    // --- CLOSE ---
    if (isset($_POST['CLOSE'])) {
        $sessionName ="";
        $ahpH->closeHier();
        unset($_SESSION['name']);
        unset($_SESSION['ipart']);
        header('HTTP/1.0 200 ok');
        header("Location: " . $urlSessionAdmin);
    }

    // --- OPEN ---
    if (isset($_POST['OPEN'])) {
        $para = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (filter_has_var(INPUT_POST, 'sessionName')) {
            $sessionName = mb_substr(preg_replace(
                '~[^\p{L}\p{N}]++~u',
                '',
                $para['sessionName']
            ), 0, 6);
            if ($ahpDb->checkSessionCode($sessionName)) {
                $_SESSION['sessionCode'] = $sessionName;
            } else {
                $sessionName="";
                $errMsg = $sessionAdmin->err['invSess1'];
            }
        }
    }

    // --- IMPORT --- import project from json file
    if (isset($_POST['IMPORT'])) {
        header('HTTP/1.0 200');
        header("Location: " . $urlPjImport);
    }

    // reads session code from url
    if ($sessionName =="") {
        $sessionName = $ahpH->getSessionCode();
        if ($sessionName != "") {
            if ($ahpDb->checkSessionCode($sessionName)) {
                $_SESSION['sessionCode'] = $sessionName;
            } else {
                $sessionName="";
                $errMsg = $sessionAdmin->err['invSess2'];
            }
        }
    }
    // reads session code from $_SESSION variable
    if (isset($_SESSION['sessionCode']) && (isset($_POST['pselect'])
        || isset($_POST['VRES']) || isset($_POST['GINP'])
        || isset($_POST['DELP']) || isset($_POST['DEL'])
        || isset($_POST['PMOD']) || isset($_POST['HMOD'])
        || isset($_POST['HEDIT'])|| isset($_POST['STATUS'])
        || isset($_POST['EXPORT']))) {
        $sessionName = $_SESSION['sessionCode'];
    }

    if ($sessionName !="") {
        $pcnt = count($ahpDb->getParticipants($sessionName));
        $pd = $ahpDb->readProjectData($sessionName);

        // --- DELP --- delete selected participants
        if (isset($_POST['DELP'])) {
            $pnerr = "";
            $pndel = "";
            if ($pd['project_author'] != $name) {
                $errMsg = $sessionAdmin->err['noAuth'];
            } else {
                foreach ($_SESSION['ipart'] as $pn) {
                    if ($ahpDb->delParticipant($sessionName, $pn) == true) {
                        $pndel .= $pn . " ";
                    } else {
                        $pnerr .= $pn . " ";
                    }
                }
                if ($pnerr !="") {
                    $errMsg .= "<span class='var'>" . $pnerr . "</span>"
                     . $sessionAdmin->err['noDel'];
                }
                if ($pndel !="") {
                    $msg = sprintf($sessionAdmin->msg['sDelp'], $pndel);
                }

                unset($_SESSION['ipart']);
                // read new number of participants
                $pcnt = count($ahpDb->getParticipants($sessionName));
            }
        }

        // --- DEL --- delete selected session
        if (isset($_POST['DEL'])) {
            if ($pd['project_author'] != $name) {
                $errMsg = $sessionAdmin->err['noAuth'];
            } else {
                if ($ahpDb->deleteRecord($sessionName) != 0) {
                    $msg = sprintf($sessionAdmin->msg['sDel'], $sessionName);
                    $sessionName = "";
                    unset($_SESSION['sessionCode']);
                } else {
                    $errMsg = "Session " . $sessionName . ", "
                    . $ahpDb->getErrors();
                }
            }
        }

        // --- VRES --- view result
        if (isset($_POST['VRES'])) {
            $url = $urlGroupRes . "?sc=" . $sessionName;
            header('HTTP/1.0 200 ok');
            header("Location: " . $url);
            exit();
        }

        // --- GINP --- pwc  input
        if (isset($_POST['GINP'])) {
            if ($pd['project_status'] == 0) {
                $erMsg = $sessionAdmin->err["pClosed"];
            } else {
                $url = $ahpDb->setSessfmPrjc($sessionName);
                if (isset($_SESSION['pwcaDone']) && $_SESSION['pwcaDone']) {
                    $msg = sprintf($sessionAdmin->msg['pwcCompl'], $name);
                } elseif (isset($_SESSION['pwcDone']) && !isset($_SESSION['alt'])) {
                    $msg = sprintf($sessionAdmin->msg['pwcCompl'], $name);
                } else {
                    header('HTTP/1.0 200 ok');
                    header("Location: " . $url);
                    exit();
                }
            }
        }

        // --- HMOD --- use hierarchy
        if (isset($_POST['HMOD'])) {
            $_SESSION['hText'] = 	$pd['project_hText'];
            if (isset($pd['project_alt'])) {
                $_SESSION['altNum'] = count($pd['project_alt']);
                $_SESSION['alt'] = $pd['project_alt'];
            }
            unset($_SESSION['sessionCode']);
            header('HTTP/1.0 200 ok');
            header("Location: " . $urlAhpH);
            exit();
        }

        // --- PMOD --- rename project
        if (isset($_POST['PMOD'])) {
            $_SESSION['project'] = $pd['project_name'];
            $_SESSION['hText'] = 	$pd['project_hText'];
            if (isset($pd['project_alt'])) {
                $_SESSION['altNum'] = count($pd['project_alt']);
                $_SESSION['alt'] = $pd['project_alt'];
            }
            $_SESSION['description'] = $pd['project_description'];
            $_SESSION['mod'] = true;
            header('HTTP/1.0 200 ok');
            header("Location: " . $urlGroupInit);
            exit();
        }

        // --- HEDIT --- edit
        if (isset($_POST['HEDIT']) && $pcnt == 0) {
            $_SESSION['hText'] = 	$pd['project_hText'];
            if (isset($pd['project_alt'])) {
                $_SESSION['altNum'] = count($pd['project_alt']);
                $_SESSION['alt'] = $pd['project_alt'];
            }
            $_SESSION['description'] = $pd['project_description'];
            header('HTTP/1.0 200 ok');
            header("Location: " . $urlAhpH);
            exit();
        }

        // --- STATUS --- toggle project status
        if (isset($_POST['STATUS'])) {
            $st = $ahpDb->toggleStatus($sessionName);
            $pd['project_status'] = $st;
            $msg = $sessionAdmin->msg['pStat1']
            . ($st == 1 ? $sessionAdmin->msg['pStatO'] : $sessionAdmin->msg['pStatC']);
        }

        // --- EXPORT --- export project
        if (isset($_POST['EXPORT'])) {
            // --- only admin can export projects from other users
            if (in_array($_SESSION['user_id'], $admin)) {
                $author = "%";
            } else {
                $author  = $login->user_name;
            }
            $ahpPj = array( 'pj' => $ahpDb->getAllProjects($author, $sessionName));
            $pwc  = $ahpDb->getAllPwc($sessionName);
            if (!empty($pwc)) {
                $ahpPj += array('pwc' => $pwc);
            }
            $alt = $ahpDb->getAllAlt($sessionName);
            if (!empty($alt)) {
                $ahpPj += array('alt' => $alt);
            }
            // JSON encode
            $ahpPjJs = json_encode($ahpPj, JSON_PRETTY_PRINT);
            $ahp = new AhpCalcIo(0);
            $ahp->txtDownload(
                $sessionName . "-AHP-project" . ".JSON",
                $ahpPjJs,
                'application/json'
            );
            session_write_close();
            exit();
        }

        // --- pselect --- read selected participants
        if (isset($_POST['pselect'])) {
            $psel = $ahpDb->getSelectedParticipants($sessionName);
            if (isset($_POST['ntick'])) {
                $psel = array();
            }
        }

        // moved from web output part
        $acnt = 0;
        if (isset($pd['project_alt'])) {
            $acnt = count($pd['project_alt']);
            $ahpH->altNum = $acnt;
            $ahpH->alt = array_merge($ahpH->alt, $pd['project_alt']);
            $hierMode = false;
        } else {
            $hierMode = true;
        }
        $res = $ahpH->setHierarchy($pd['project_hText']);
        if (!empty($res)) {
            $ahpH->setPglb();
        }
    } // session code != empty


    // --- selection list ---
    $storedSessions = $ahpDb->getStoredSessions($name);
} else { // user not logged in
    if (isset($_SESSION['name'])) {
        unset($_SESSION['name']);
    }
} // end if logged in

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($sessionAdmin->titles['pageTitle']);
//$webHtml->usrBackLnk = $urlAhp;
echo '<script src="js/ahp-session-admin.js"></script>';
    include('includes/login/form.login-hl.php');
// Title
echo $sessionAdmin->titles['h1title'];

if (defined('SYS_MSG')) {
    echo "<p class='hl'>" . SYS_MSG . "</p>";
}

if (!isset($_SESSION['lang'])) {
    $webHtml->displayLanguageSelection();
}
if ($errMsg !="") {
    echo "<p class='err'>", $errMsg, "</p>";
}
if ($msg !="") {
    echo "<p class='msg'>", $msg, "</p>";
}

if (!empty($login->errors) || !empty($login->messages)) {
    echo "<p class='err'>", $login->getErrors(), "</p>";
}

if (!empty($ahpDb->err)) {
    echo "<p class='err'>", $ahpDb->getErrors(), "</p>";
}


if ($login->isUserLoggedIn() === true) {
    if ($sessionName !="") {

        // --- Project Summary
        echo $sessionAdmin->titles['h2ahpProjSummary'];
        $ahpDb->displayProjectDetails($sessionName, true);
        if ($pcnt > 0) {
            if (isset($_SESSION['ipart'])) {
                printf($sessionAdmin->msg["selPart"], implode(", ", $psel));
            } else {
                printf($sessionAdmin->msg["selPart"], "none");
            }
        }

        // --- Group input link
        if ($pd['project_status'] == 0) {
            echo $sessionAdmin->titles['h3groupInpLnk'];
            echo $sessionAdmin->msg['pClsd'];
        } elseif ($ahpH->pwcDoneFlg && $acnt == 0) {
            // Project with predefined priorities w/o alternatives
        } else {
            echo $sessionAdmin->titles['h3groupInpLnk'];
            $url = htmlspecialchars(pathinfo($myUrl, PATHINFO_DIRNAME)) . "/";
            $url .= $urlGroupInit;
            printf($sessionAdmin->info['sc'], $sessionName);
            $ptc = (isset($_SERVER['HTTPS']) ? "https:" : "http:");
            echo  $sessionAdmin->info['scLnk1'];
            printf($sessionAdmin->info['scLnk2'], $ptc .$url, $sessionName);
            printf($sessionAdmin->info['scLnk3'], $ptc .$url, $sessionName);
        }

        // --- Project Structure
        echo $sessionAdmin->titles['h3projStruc'];
        if (empty($res)) {
            echo "<p class='err'>Error in hierarchy definition: ";
            echo implode(", ", $ahpH->err);
            echo "</p>";
        } else {
            if ($ahpH->pwcDoneFlg) {
                $_SESSION['pwcDone'] = true;
                echo $sessionAdmin->msg['hInfo1'];
                if ($acnt == 0) {
                    echo $sessionAdmin->msg['hInfo2'];
                } else {
                    printf($sessionAdmin->msg['hInfo3'], $acnt);
                }
            } else {
                unset($_SESSION['pwcDone']);
            }
        }
        unset($res);
        echo "<p><button href='#collapse1' class='nav-toggle'>
                Show project structure</button>";
        echo "<div id='collapse1' style='display:none'>";
        $ahpH->displayHierarchyInfo();
        $ahpH->displayHierarchyTable($acnt, false, false, false);

        // --- show hierarchy text
        echo $sessionAdmin->titles['h4hierDefTxt'];
        $phl = explode(";", $pd['project_hText']);
        echo "<code><p>";
        foreach ($phl as $line) {
            if (mb_strlen($line) >1) {
                echo $line, ";<br>";
            }
        }
        echo "</p></code>";
        echo "</div>";

        // --- AHP project menu
        echo $sessionAdmin->titles['h2ahpProjectMenu'];
        include 'views/ahpProjectAdminMenu.html';
    } else {
        // --- Project session table
        // --- get number of registered active user count and latest users
        $regUserCnt = $ahpAdmin->getActiveUserCnt();
        $uCnt = count($ahpAdmin->getLatestUsers(LHRS));
        $sessionCnt = count($storedSessions);
        $counts = $ahpDb->getActivityLevel($name);
        $don = $ahpAdmin->checkDonation($name);
        echo $sessionAdmin->titles['h2subTitle'];
        printf($sessionAdmin->msg["usrStat1"], $regUserCnt);
        printf($sessionAdmin->msg["usrStat2"], $uCnt, LHRS);
        printf($sessionAdmin->msg["usrStat3"], $name, $sessionCnt);
        printf($sessionAdmin->msg["usrStat4"], $counts['actlv']);
        if (DONATIONS) {
            echo(!$don ? $sessionAdmin->msg["usrDon1"] : $sessionAdmin->msg["usrDon2"]) . "! ";
        }
        echo $sessionAdmin->titles['h2myProjects'];
        if ($sessionCnt > SESSIONLMT) {
            echo $sessionAdmin->err["sLmt"];
        } else {
            echo "<p>";
            if ($sessionCnt > 0) {
                echo $sessionAdmin->info['pOpen1'];
            }
            printf($sessionAdmin->info['pOpen2'], $urlAhpH);
            echo "</p>";
        }
        $ahpDb->displaySessionTable($name);
        // --- AHP project administration menu
        echo $sessionAdmin->titles['h2ahpSessionMenu'];
        include 'views/ahpSessionAdminMenu.html';
    }
} else {
    // User is not logged in - need to register first
    echo $sessionAdmin->titles['h2subTitle'];
    printf($sessionAdmin->info['logout'], $urlAhp);
}

$webHtml->webHtmlFooter($version);
