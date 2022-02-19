<?php
/** AHP hierarchy project initialize group sessions
* @author Klaus D. Goepel
* @package AHP-OS
* @since 2013-02-20 release first version ahp hierarchy
* @version 2018-09-15 last modification w/o SVN
*
* session parameters used:
* $_SESSION['groupSession']
* $_SESSION['sessionCode']
* $_SESSION['project']
* $_SESSION['name']
* $_SESSION['description']
* $_SESSION['owner']
* $_SESSION['mod']
*
* CHECK, SUBMIT, VIEW, CANCEL
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

session_start();
if (!isset($_SESSION['sid'])) {
    $sid = startNewSession();
}

$login = new Login();
$class = 'AhpHiergini' . $lang;
$ahpHiergini = new $class();
$_SESSION['lang'] = $lang;

// reset in case back from edit form
if (isset($_SESSION['REFERER'])) {
    unset($_SESSION['REFERER']);
}
$loggedIn = $login->isUserLoggedIn();

$msg ="";
$subTitle = $ahpHiergini->titles['subTitle1'];

$version = substr('$LastChangedDate: 2022-02-19 14:01:53 +0800 (Sa, 19 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 156 $', "$");

/** get data (name, session code, description from POST parameter
* called from ahp-hiergini page
* @return int $retCode:
*  bit 0 = session code      sets $_SESSION['sessionCode']
*  bit 1 = name              sets $_SESSION['name']
*  bit 3 = prj. description  sets $_SESSION['description']
*  bit 4 = project title     sets $_SESSION['project']
*/
function getPrjSessionData()
{
    global $loggedIn;
    $retCode = 0;
    if (filter_has_var(INPUT_POST, 'name')
     ||  filter_has_var(INPUT_POST, 'sc')
     ||  filter_has_var(INPUT_POST, 'project')
     ||  filter_has_var(INPUT_POST, 'description')) {
        $para = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        // Session code - char(6)
        if (filter_has_var(INPUT_POST, 'sc') && $para['sc']!="") {
            $sessionCode =  trim($para['sc'], " \t\n\r\0\x0B");
            $sessionCode = preg_replace('~[^\p{L}\p{N}]++~u', ' ', $sessionCode);
            $sessionCode = mb_substr($sessionCode, 0, 6);
            if (strlen($sessionCode) == 6) {
                $_SESSION['sessionCode'] = $sessionCode;
                $retCode |= 1;
            }
        }
        // Name - varchar(64)
        if (filter_has_var(INPUT_POST, 'name')&& $para['name']!="") {
            $name = trim($para['name'], " \t\n\r\0\x0B");
            $name = preg_replace('~[^\p{L}\p{N}]++~u', ' ', $name);
            $name = mb_substr($name, 0, 64);
            if (mb_strlen($name)>2) {
                $_SESSION['name'] = $name;
                $retCode |= 2;
            } else {
                $name="";
            }
        }
        // Project description - text(400)
        if (filter_has_var(INPUT_POST, 'description') && $para['description']!="") {
            $description = mb_substr(trim($para['description']), 0, 400);
            $_SESSION['description'] = $description;
            $retCode |= 4;
        }
        // Project name - varchar(64)
        if (filter_has_var(INPUT_POST, 'project') && $para['project']!="") {
            $project = mb_substr(trim($para['project']), 0, 64);
            $_SESSION['project'] = $project;
            $retCode |= 8;
        }
    }
    if ($loggedIn) {
        $_SESSION['name'] = $_SESSION['user_name'];
        $retCode |= 2;
    }
    return $retCode;
}

$user = "";
$prjct = "";
$descr = "";
$sessCode = "";
$pCnt = 0;
$viewFlg = false;
$ahpDb = new AhpDb();
$action = "";
$iniOwner = false;

    $errMsg = "";

    // --- TRY GET SESSION CODE FROM GET
        $ahpH = new AhpHier();
        $sessCode = $ahpH->getSessionCode();
        unset($ahpH);

    if ($sessCode !="" &&  strlen($sessCode) == 6) {
        $_SESSION['sessionCode'] = $sessCode;
    } elseif (isset($_SESSION['sessionCode'])) {
        $sessCode = $_SESSION['sessionCode'];
    }

    if ($loggedIn) {
        $user = $_SESSION['user_name'];
    }

// new/update project
    if (isset($_SESSION['mod'])) {
        $subTitle = $ahpHiergini->titles['subTitle2'];
        if ($loggedIn) {
            $user = $owner = $_SESSION['user_name'];
            $iniOwner = true;
        }
        if (!isset($_SESSION['sessionCode']) || $_SESSION['sessionCode']=="") {
            // generate a new session code
            $sessCode = $ahpDb->generateSessionCode(6, 7);
            $action = sprintf($ahpHiergini->info['act1'], $sessCode);
            $storedSessions = $ahpDb->getStoredSessions($user);
            $sessionCnt = count($storedSessions);
            if ($sessionCnt >= SESSIONLMT) {
                $errMsg .= $ahpHiergini->err['pExc'];
                $retFlg = false;
            } else {
                $msg = $ahpHiergini->msg['nProj'];
            }
            // --- modify/update
        } else {
            $action = $ahpHiergini->info['act2'];
            $sessCode = $_SESSION['sessionCode'];
            $scChk = $ahpDb->checkSessionCode($sessCode);
            if ($scChk) {
                // session file exists - read participants
                $pCnt = count($ahpDb->getParticipants($sessCode));
                $action .= sprintf($ahpHiergini->info['act3'], $pCnt);
            }
            $msg = $ahpHiergini->msg['pMod'];
        }
        if (isset($_SESSION['project'])) {
            $prjct = $_SESSION['project'];
        }
        if (isset($_SESSION['name'])) {
            $user = $_SESSION['name'];
        }
        if (isset($_SESSION['description'])) {
            $descr = $_SESSION['description'];
        }
    }

// --- GET POSTED DATA
    $retCode = getPrjSessionData();
    if ($retCode == 0) {
        $errMsg = "";
    }
    if ($retCode & 1) {
        $sessCode = $_SESSION['sessionCode'];
    } else {
        if (!$iniOwner && $sessCode == "") {
            $errMsg .= $ahpHiergini->err['noSc'];
        }
    }
    if ($retCode & 2) {
        $user = $_SESSION['name'];
        if ($iniOwner) {
            $_SESSION['owner'] = $user;
        }
    } elseif (!$loggedIn) {
        $errMsg .= $ahpHiergini->err['noName'];
    }
    if ($retCode & 4) {
        $descr = $_SESSION['description'];
    }
    if ($retCode & 8) {
        $prjct = $_SESSION['project'];
    }

// participant: iniOwner = false
    if (isset($_SESSION['name']) && $_SESSION['name'] != $user) {
        $iniOwner = false;
    }

    if (!isset($_SESSION['mod']) && $errMsg == "") {
        $subTitle = $ahpHiergini->titles['subTitle3'];
        // Read Project data and pwc
        $url = $ahpDb->setSessfmPrjc($sessCode);
        if ($url == "") {
            $errMsg = $ahpDb->getErrors();
        } else {
            $pCnt = count($ahpDb->getParticipants($sessCode));
            $action .= sprintf($ahpHiergini->info['act3'], $pCnt);
            if (isset($_SESSION['pwcaDone']) && $_SESSION['pwcaDone']) {
                $errMsg = sprintf($ahpHiergini->err['pwcCompl'], $user);
            } elseif (isset($_SESSION['pwcDone']) && !isset($_SESSION['alt'])) {
                $errMsg = sprintf($ahpHiergini->err['pwcCompl'], $user);
            }
        }
    }

// --- SUBMIT EVALUATION AND REDIRECT BACK TO HIERARCHY	(Go)
    if (isset($_POST['SUBMIT']) && $errMsg=="") {
        $retFlg = true;
        if (isset($_SESSION['mod'])) {
            $alt = isset($_SESSION['alt']) ? $_SESSION['alt'] : array();
            $storedSessions = $ahpDb->getStoredSessions($user);
            $sessionCnt = count($storedSessions);
            if (in_array($sessCode, $storedSessions)) { // Modification of existing project
                if (isset($_SESSION['alt']) && !$_SESSION['pwcDone']) {
                    $errMsg .= $ahpHiergini->err['hDefP'];
                    $retFlg = false;
                } else { // Update project data. Todo: modify hText with $prjct
                    $retFlg = $ahpDb->updateProjectData($sessCode, $prjct, $descr, $_SESSION['hText'], $alt);
                }
            } else { // Write project data (owner)
                $retFlg = $ahpDb->writeProjectData($sessCode, $prjct, $descr, $_SESSION['hText'], $user, $alt);
            }
            // retFlg: unset group session, mod, return to session-admin
            if ($retFlg) {
                $ahpH = new AhpHier();
                $ahpH->closeHier();
                unset($_SESSION['mod']);
                // redirect
                $url = $urlSessionAdmin;
            } else {
                $errMsg .= $ahpDb->getErrors();
            }
        } else { // no mod - input pairwise comparisons
            $_SESSION['groupSession'] = true;
            // check for complete judgment
        }

        // --- retFlg: return to ahpHierarchy or ahp_alt
        if ($retFlg) {
            header("Location: " . $url);
            exit();
        } elseif ($errMsg == "") {
            $errMsg = sprintf($ahpHiergini->err['unknw'], $iniOwner, $retFlg);
        }
    } // end submit (Go)

// --- CANCEL
    if (isset($_POST['CANCEL'])) {
        if ($loggedIn) {
            $url = $urlSessionAdmin;
            $ahpH = new AhpHier();
            $ahpH->closeHier();
        } else {
            $url = $urlAhp;
            closeSession();
        }
        header("Location: " . $url);
        exit();
    }

// --- VIEW GROUP RESULT
    if (isset($_POST['VIEW'])) {
        unset($_SESSION['groupSession']);
        unset($_SESSION['sessionCode']);
        $url = $urlGroupRes . "?sc=" . htmlspecialchars($sessCode);
        header("Location: " . $url);
        exit();
    }

// --- RESET FORM
    if (isset($_POST['reset'])) {
        unset($_SESSION['lang']);
        if (!$iniOwner) {
            $sessCode="";
        } else {
            $descr="";
        }
        $user="";
    }

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpHiergini->titles['pageTitle']);
echo "<div style='display:block;float:left'>
		$loginHeaderText</div><div style='clear:both;'></div>";
echo $ahpHiergini->titles['h1Title'];

if (defined('SYS_MSG')) {
    echo "<p class='err'>" . SYS_MSG . "</p>";
}

$webHtml->displayLanguageSelection();
echo "<h2>$subTitle</h2>";
echo "<p class='msg'>$action</p>";
if (!isset($_SESSION['groupSession']) && !isset($_SESSION['mod'])) {
    echo $ahpHiergini->info['intro'];
}

if ($errMsg != "") {
    echo "<p class='err'>$errMsg</p>";
} elseif ($msg != "") {
    echo "<p class='msg'>$msg</p>";
} else {
    printf($ahpHiergini->info['ok'], $pCnt);
}
include 'views/hierginiMenu.html';
$webHtml->webHtmlFooter($version);
