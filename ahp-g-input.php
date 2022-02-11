<?php
/* Page to view all input data
 *  @author Klaus D. Goepel
 *  @copyright 2016 Klaus D. Goepel
 *  @package AHP-OS
 *  @since 2016-10-31
 *  @version 2018-08-20 last version w/o SVN
 *
 *  Allows to get all decision matrices (input data)
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
    $class = 'AhpGroupRes' . $lang;
    $ahpGroupRes = new $class();

    // reset in case back from edit form
    if (isset($_SESSION['REFERER'])) {
        unset($_SESSION['REFERER']);
    }

    $loggedIn = $login->isUserLoggedIn();

    $version = substr('$LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $', 18, 10);
    $rev = trim('$Rev: 120 $', "$");


// --- START ---
$errMsg = "";
$checkWarn ="";
$altNum = 0;
$hierMode = true;
$part = array();  // List of participants
$partCnt = 0;		  // number of participants
$nodes = array();	// hierarchy nodes (priority vectors)
$dm = array();    // decision matrix;
$psel = array();
$owner = false;
$ahpH = new AhpHier();

$sessCode = $ahpH->getSessionCode(); // reads session code from url
if ($sessCode == "" && isset($_SESSION['sessionCode'])) {
    $sessCode = $_SESSION['sessionCode'];
}

$ahpDb = new AhpDb();
$part = $ahpDb->getParticipants($sessCode);

if (!empty($part)) {
    // ok - get project data
    $pjd = $ahpDb->readProjectData($sessCode);
    $psel = $ahpDb->getSelectedParticipants($sessCode);
    $partCnt = count($part);
} else {
    $errMsg = implode(" ", array("No participants in the project"));
}
    // ---  Here we know that session code is valid and participants exist
    $urlAct = $urlAct . "?sc=" . urlencode($sessCode);

    $altNum = (isset($pjd['project_alt']) ? count($pjd['project_alt']) : 0);
    if ($altNum > 1) {
        $hierMode = false;
    }
if ($errMsg == "") {
    $hierarchy = $ahpH->setHierarchy($pjd['project_hText']);
    $ahpH->setPglb();
    if ($loggedIn && $_SESSION['user_name'] == $pjd['project_author']) {
        $owner = true;
    }
} // end $errMsg == ""

/*
 * --- MENU SWITCH ---
 */
        if (isset($_POST['DONE'])) {
            $url = $urlGroupRes . "?sc=" . urlencode($sessCode);
            header("Location: " . $url);
            die();
        }
        // export result in csv
        if (isset($_POST['download'])) {
            $ds = (isset($_POST['csv']) ? ',' : '.');
            $text = $ahpDb->exportProjectDetails($sessCode, $ds);
            $ahp = new AhpCalcIo(0);
            // Spaces are replaced by underscores
            $ahp->txtDownload($sessCode . "-AHPinputData" . '.csv', $text);
            session_write_close();
            die();
        }

/*
 * --- Web Page HTML OUTPUT ---
 */

$webHtml =  new WebHtml($ahpGroupRes->titles['pageTitle2']);
include('includes/login/form.login-hl.php');

echo $ahpGroupRes->titles['h1title2'];
echo $ahpGroupRes->titles['h2subTitle2'];

if ($errMsg !="") {
    echo "<p class='err'>", $errMsg, "</p>";
} elseif (!empty($ahpDb->err)) {
    echo "<p class='err'>", implode(", ", $ahpDb->err), "</p>";
}
if ($login->isUserLoggedIn() === true) {

// --- Show Project data Summary
    $ahpDb->displayProjectDetails($sessCode);
    if (!empty($ahpDb->wrn)) {
        echo "<p class='err'>", implode(", ", $ahpDb->wrn),"</p>";
    }

    // --- Hierarchy details
    echo $ahpGroupRes->titles['h2dm'];
    $ahp = new AhpCalcIo(0); // for method print_matrix
    echo "<div style='margin-left:auto;margin-right:auto;'>";
    if ($hierMode) {
        foreach ($psel as $pname) {
            printf($ahpGroupRes->titles['h3part'], $pname);
            $pwcA = $ahpDb->getPwcArray($sessCode, $pname);
            // show result breakdown by nodes
            foreach ($pwcA as $node=>$pwc) {

                    // LEFT COLUMN
                echo "<div style='width:40%;height:auto;float:left;padding:10px;'>";
                printf($ahpGroupRes->titles['h4nd'], $node);
                // this call requires setting of hierarchy!
                $leafs = $ahpH->getTreeNode($hierarchy, $node);
                if (is_array($leafs)) {
                    echo $ahpGroupRes->info['pwcfor'];
                    $il = 1;
                    foreach ($leafs as $leaf) {
                        echo $il++, " = $leaf <br>";
                    }
                } else {
                    // no leafs
                }
                echo "</div>";

                // RIGHT COLUMN
                echo "<div style='float:left;padding:10px;'>";
                echo $ahpGroupRes->titles['h4dm'];
                $dm = $ahp->getMatrixFromPwc($pwc);
                $ahp->print_matrix($dm);
                echo "</div>";

                echo "<div style='clear:both;'></div>";
            } // next node
        }
        echo "<div style='text-align:left;padding:10px;'>";
        echo "</div>";
    } else {

// --- Alternatives details
        foreach ($psel as $pname) {
            printf($ahpGroupRes->titles['h3part'], $pname);
            $pwcA = $ahpDb->getPwcArray($sessCode, $pname);
            // show result breakdown by criteria (leafs)
            foreach ($pwcA as $node=>$pwc) {
                // LEFT COLUMN
                echo "<div style='width:40%;height:auto;float:left;padding:10px;'>";
                printf($ahpGroupRes->titles['h4crit'], $node);
                echo $ahpGroupRes->info['pwcfor'];
                $il = 1;
                foreach ($pjd['project_alt'] as $alt) {
                    echo $il++, " = $alt <br>";
                }
                echo "</div>";
                // RIGHT COLUMN
                $dm = $ahp->getMatrixFromPwc($pwc);
                echo "<div style='text-align:left;float:left;padding:10px;'>";
                echo $ahpGroupRes->titles['h4dm'];
                $ahp->print_matrix($dm);
                echo "</div>";
                echo "<div style='clear:both;'></div>";
            }
            echo "<div style='text-align:left;padding:10px;'>";
            echo "</div>";
        }
    }
    echo "</div>";
} else { // user not logged in
    echo $ahpGroupRes->msg['nlgin'];
    echo "<p><a href='$urlAhp'>back</a></p>";
}

// --- Menu
    echo $ahpGroupRes->titles['h2subTitle2'];
    include 'views/ahpGinputMenu.html';

$webHtml->webHtmlFooter($version);
