<?php
/* Page to view AHP group results
 *
 * @author Klaus D. Goepel
 * @copyright 2014 Klaus D. Goepel
 * @package AHP-OS
 * @since 2014-04-11
 * @version 2017-03-11 cleanup
 * @version 2019-02-06 last version w/o SVN
 *
 * The actual result of AHP projects is calculated "on-the-fly", when
 * the user calls the result page (ahp-group.php). In the database only
 * the decision hierarchy definition and alternative names and the
 * pairwise comparisons are stored.
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
 */
    include 'includes/config.php';

    $login = new Login();

    $lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");
    $class = 'AhpGroupRes' . $lang;
    $ahpGroupRes = new $class();
    $uid = (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "");

    // reset in case back from edit form
    if (isset($_SESSION['REFERER'])) {
        unset($_SESSION['REFERER']);
    }

    $loggedIn = $login->isUserLoggedIn();

    $version = substr('$LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $', 18, 10);
    $rev = trim('$Rev: 120 $', "$");

    // --- START ---
    $errMsg = "";
    $partCnt = 0;		// number of participants
    $pSelCnt = 0;   	// number of selected participants
    $nodes = array();	// hierarchy nodes (priority vectors)
    $pwcCons = array(); // consolidated pwcs for uncertainty analysis
    $altNum = 0;
    $hierMode = true;
    $consens = 0.;
    $consens1 = 0.;
    $rflg = 0;
    $wpm = 0;
    $sd = false;
    $iScale = 0;


    $ahpH = new AhpHierAlt();

    $sessCode = $ahpH->getSessionCode(); // reads session code from url
    if ($sessCode == "" && isset($_SESSION['sessionCode'])) {
        $sessCode = $_SESSION['sessionCode'];
    }

    $ahpG = new AhpGroup($sessCode);
    if (empty($ahpG->err)) {
        $pSelCnt = count($ahpG->pSel);
        $partCnt = count($ahpG->part);
    } else {
        $errMsg = implode(",", $ahpG->err);
    }

    // ---  Here we know that session code is valid and participants exist
    $urlAct = $urlAct . "?sc=" . urlencode($sessCode);

    $altNum = (isset($ahpG->pj['project_alt']) ? count($ahpG->pj['project_alt']) : 0);
    if ($altNum > 1) {
        $hierMode = false;
    }

    // --- Scale selection
    $scCnt = count($ahpG->ahpScale);
    foreach ($ahpG->ahpScale as $i => $sn) {
        $scaleName[$i] = $sn[0];
    }
    if (isset($_POST)) {
        if (isset($_POST['scale'])) {
            $iScale = substr($_POST['scale'], 0, 1);
            $scaleSel = $_POST['scale'];
            if ($iScale <0 || $iScale >$scCnt) {
                $iScale = 0;
            }
        } else {
            $iScale = 0;
            $scaleSel = $ahpG->ahpScale[$iScale][0];
        }
        // random variations
        if (isset($_POST['var'])) {
            $rflg = 1;
        }
        // WPM instead of WSM
        if (isset($_POST['wpm'])) {
            $wpm = 1;
        }
        // Standard dev for group evaluations
        if (isset($_POST['sd'])) {
            $sd = 1;
        }
    }

    $ahpG->wpm = $wpm;
    $ahpH->wpm = $wpm;

    // --- DONE ---
    if (isset($_POST['DONE'])) {
        $ahpH->closeHier();
        unset($_SESSION['ipart']);
        unset($_POST['ipart']);
        unset($_SESSION['sessionCode']);
        if ($loggedIn) {
            header("Location: " . $urlSessionAdmin);
        } else {
            header("Location: " . $urlAhp);
        }
        die();
    }

if ($errMsg == "") {
    if ($hierMode) {
        // --- CONSOLIDATION OF PRIORITIES FOR SELECTED PARTICIPANTS

        // -- Consolidation
        $text = $ahpG->getPrioHier($iScale);

        // Prepare CONSOLIDATED hierarchy
        $hierarchy = $ahpH->setHierarchy($text);
        $ahpH->setPglb();
        $incmplt = !$ahpH->pwcDoneFlg;
    } else {
        // --- CONSOLIDATION OF ALTERNATIVES
        // Prepare hierarchy
        $text = $ahpG->pj['project_hText'];
        $hierarchy = $ahpH->setHierarchy($text);
        $ahpH->setPglb();
        $ahpH->alt = $ahpG->pj['project_alt'];
        $ahpH->altNum = count($ahpH->alt);
        // -- Consolidation
        $ahpG->getPrioAlt($iScale, $ahpH);
        // --- Robustness test
        if ($ahpH->pwcaDoneFlg) {
            $rt1 = $ahpH->robTest1($ahpH); // sensitivity on criteria
            $rt2 = $ahpH->robTest2($ahpH); // sensitivity on alternative eval.
        }
        $incmplt = !$ahpH->pwcaDoneFlg;
    } // hier or alt
    // --- Uncertainty analysis for alternatives
    if ($rflg && !$incmplt) {
        if ($hierMode) {
            $nVar = min(NVAR, 20000/$ahpH->nodeCnt);
            $varNo = $ahpH->nodeCnt * $nVar;
        } else {
            $nVar = min(NVAR, 140000/$ahpH->altNum/count($ahpH->leafs));
            $varNo = $ahpH->altNum * count($ahpH->leafs) * $nVar;
        }
        if ($sd) {
            $dv = ($pSelCnt >1 ? 0 : 0.5/sqrt($pSelCnt));
        } else {
            $dv = 0.5/sqrt($pSelCnt);
        }
        $pwcr = $ahpG->generateRandPwc($nVar, $dv);
        // min and max of priorities, set flag for standard deviation
        $prio_var = $ahpG->uncertainty($pwcr, $ahpH, $iScale, $sd, $hierMode);

        foreach ($prio_var['min']['pTot'] as $k=>$val) {
            $data['min'][$k] = sprintf("%.1f", 100 * $prio_var['min']['pTot'][$k]);
            $data['max'][$k] = sprintf("%.1f", 100 * $prio_var['max']['pTot'][$k]);
        }
        // --- Overlaps due to uncertainties
        $ol = $ahpG->getOverlap($hierMode);
    }

    // prepare graphic
    $dta = $ahpG->prio[0]['pTot'];
    foreach ($dta as $k=>$val) {
        $data['nom'][$k] = sprintf("%.1f", 100*$val);
    }
    if (strlen(serialize($data['nom']))>1600) {
        unset($data['min']);
        unset($data['max']);
    }
    $data = urlencode(serialize($data));

    // Show detailed input data
    if (isset($_POST['vinput'])) {
        $url = $urlGinput . "?sc=" . urlencode($sessCode);
        header("Location: " . $url);
        die();
    }
} // end $errMsg == ""


// --- GROUP RESULT MENU SWITCH ---

    // return to ahp hierarchy when logged in and project owner
    $owner = false;
    if (isset($ahpG->pj['project_author'])
        && $loggedIn
        && $_SESSION['user_name'] == $ahpG->pj['project_author']) {
        $owner = true;
        $url = $urlAhpH . "?sc=" . urlencode($sessCode);
        // use consolidated priorities - redirect to ahpHierarchy
        if ($hierMode && isset($_POST['h_submit'])) {
            $ahpH->closeHier();
            $_SESSION['hText']= $text;
            unset($_SESSION['sessionCode']);
            header("Location: " . $urlAhpH);
            die();
        }
    }
    if (isset($_POST['gi_subm'])) {
        $url = $urlAhpH . "?sc=" . urlencode($sessCode);
        header("Location: " . $url);
        die();
    }

    // export/download result in csv
    // We have $ahpG->pj, $ahpG->part, $ahpG->prio,$ahpG->cr, $ahpG->consens
    if (isset($_POST['download'])) {
        $ds = (isset($_POST['csv']) ? ',' : '.');
        $txtbuf = $ahpG->exportGroupResult($hierMode, $ds, substr($iScale, 0, 1));
        if ($rflg && !$hierMode) {
            // add robustness test tables
            $txtbuf .= $ahpH->exportRobTest($ds, $rt1, $rt2);
        }
        $ahp = new AhpCalcIo(0);
        $ahp->txtDownload($sessCode . "-AHPgResult" . '.csv', $txtbuf);
        session_write_close();
        die();
    }


/* --- Web Page HTML OUTPUT ---  */
$webHtml =  new WebHtml($ahpGroupRes->titles['pageTitle1']);

// toggle hide/show
echo '<script src="js/ahp-group.js"></script>';
echo "\n<!-- INTRO -->\n";
include('includes/login/form.login-hl.php');
echo $ahpGroupRes->titles['h1title1'];
echo $ahpGroupRes->titles['h2subTitle1'];

printf($ahpGroupRes->msg['scaleSel'], $ahpG->ahpScale[$iScale][0]);
if (!$hierMode && $wpm) {
    echo $ahpGroupRes->msg["wMethod"];
}
if ($rflg) {
    if ($pSelCnt > 1 && $sd) {
        echo $ahpGroupRes->msg["rMethod"];
    }
    printf($ahpGroupRes->msg['mcVar'], $ahpG->simCnt);
}

if ($errMsg !="") {
    echo "<p class='err'>", $errMsg, "</p>";
} else {
    if ($owner || in_array($uid, $admin)) {
        $ahpG->ahpDb->displayProjectDetails($sessCode);
    }
    if (!empty($ahpH->err)) {
        echo "<p>Error: <span class='err'>",
            implode(", ", $ahpH->err), "</span></p>";
    }
    if (!empty($ahpG->err)) {
        echo "<p>Error: <span class='err'>",
            implode(", ", $ahpG->err), "</span></p>";
    }
    if (!empty($ahpG->wrn)) {
        printf(
            $ahpGroupRes->msg['noPwc4'],
            implode(", ", $ahpG->wrn)
        );
    }
    if ($incmplt) {
        echo $ahpGroupRes->err['incompl'];
    }

    echo $ahpGroupRes->titles["h2hier"];
    if (count($ahpG->pSel) == count($ahpG->part)) {
        printf($ahpGroupRes->msg['pSel'], "All");
    } else {
        printf($ahpGroupRes->msg['pSel'], implode(", ", $ahpG->pSel));
    }

    //	$ahpH->prioAlt = $ahpG->prio[0]; is set in ahpG already
    $ahpH->displayHierarchyTable($altNum, false, false);
    echo($hierMode ? $ahpGroupRes->titles["h2consP"] : $ahpGroupRes->titles["h2consA"]);

    // --- Diagram ---
    echo "<div class='ofl'><div style='margin-left:auto;margin-right:auto;width:700px;'>";
    echo "\n<img src='ahp-group-graph.php?dta=$data' alt='Group-dia'>\n";
    echo "</div></div>";

    // --- Display result for overlapping of criteria/alternatives
    if ($rflg) {
        echo $ahpGroupRes->titles["h2sens"];
        echo "<p class='msg'>";
        if ($incmplt) {
            echo $ahpGroupRes->msg['noSens'];
        } else {
            if (!$hierMode) {
                echo $ahpGroupRes->info['sensDl'];
            }
            // $olInfo: overlapping $olInfo1: w/o overlap
            $i=1;
            $j=1 ;
            $olInfo = "";
            $olInfo1 = "";
            foreach ($ol as $k => $criteria) {
                if ($criteria != "" && count($criteria) > 1) {
                    $olInfo .= $i++ . ": <span class='res'>"
                . implode(", ", $criteria) . "</span><br>";
                } else {
                    if ($criteria != "") {
                        $olInfo1 .= $j++ .": <span class='res'>"
                    . implode(", ", $criteria)  . "</span><br>";
                    }
                }
            }
            echo $ahpGroupRes->titles["h3wUncrt"];
            echo "<p class='msg'>";
            $wrd = $hierMode ? $ahpGroupRes->wrd['crit'] : $ahpGroupRes->wrd['alt'];
            if ($olInfo == "") {
                printf($ahpGroupRes->res['ovlpNo'], $wrd);
            } else {
                if ($olInfo1 != "") {
                    printf($ahpGroupRes->res['ovlp'], $wrd);
                    echo $olInfo1;
                }
                if ($hierMode && count($ol[0]) == count($ahpG->leafs)) {
                    printf($ahpGroupRes->res['ovlpAll'], $wrd);
                } elseif (!$hierMode && count($ol[0]) == count($ahpG->prio[0]['pTot'])) {
                    printf($ahpGroupRes->res['ovlpAll'], $wrd);
                } else {
                    printf($ahpGroupRes->res['ovlpGrp'], $wrd);
                    echo $olInfo;
                }
            }
            echo "</p>";
        }
    }

    // --- Hierarchy details ---
    if ($hierMode) {
        // show result breakdown by nodes
        echo $ahpGroupRes->titles["h2nodes"];
        echo "<div style='margin-left:auto;margin-right:auto;'>";
        $i=0;
        foreach ($ahpG->nodes as $node) {
            $collapse = "collapse" . ++$i;
            $pPwc = $ahpG->pwcCnt[$node];
            if ($pPwc > 1) {
                $consens = $ahpG->getConsensus($node, $iScale);
                $cdta[$node] = round(100*$consens, 1);
            }
            printf($ahpGroupRes->mnu['btnNdD'], $collapse);
            printf(
                $ahpGroupRes->res['nodeCr'],
                $node,
                round(100 * $ahpG->cr[0][$node], 1)
            );
            // Nodes
            if ($pPwc > 1) {
                if ($cdta[$node] <0) {
                    echo $ahpGroupRes->err["consens0"];
                } else {
                    printf($ahpGroupRes->res['gCons'], $cdta[$node]);
                }
                echo $ahpG->consensusWording($cdta[$node]);
            }
            if (empty($ahpG->dmCons[$node])) {
                echo $ahpGroupRes->msg['noPwc1'];
            }
            echo "</p>";
            echo "<div id='", $collapse, "' style='display:none'>";
            // include code to print matrix
            $ahp = new AhpCalcIo(0); // for method print_matrix

            // LEFT COLUMN
            echo "<div style='width:50%;height:auto;float:left;padding:10px;'>";
            echo $ahpGroupRes->titles["h4wCons"];
            printf($ahpGroupRes->res['cr'], 100* $ahpG->cr[0][$node]);
            if ($rflg && isset($ahpG->prioVar['min'][$node])) {
                $tol = array( "min"=>array_values($ahpG->prioVar['min'][$node]),
                              "max"=>array_values($ahpG->prioVar['max'][$node]));
            } else {
                $tol= array();
            }
            $ahp->printVector(
                array_keys($ahpG->prio[0][$node]),
                array_values($ahpG->prio[0][$node]),
                1,
                $tol
            );
            echo "</div>";

            // RIGHT COLUMN
            echo "<div style='float:left;padding:10px;'>";
            echo $ahpGroupRes->titles["h4mCons"];
            if (empty($ahpG->dmCons[$node])) {
                echo $ahpGroupRes->msg['noPwc2'];
            } else {
                printf($ahpGroupRes->msg['pCnt'], $ahpG->pwcCnt[$node]);
                $ahp->print_matrix($ahpG->dmCons[$node]);
            }
            echo "</div>";
            echo "<div style='clear:both;'></div>";

            // GROUP RESULT
            if ($pPwc > 1  && 	count($ahpG->nodes) > 1
                && 	($owner || in_array($uid, $admin))) {
                // Priorities by participant
                echo $ahpGroupRes->titles["h4part"];
                $ahpG->printAhpGrpResult($node, false);
            }
            echo "</div>"; // collapse
        }
        // Global priorities
        echo $ahpGroupRes->titles["h2pGlob"];
        if ($pPwc > 1) {
            if ($ahpG->consens >= 0) {
                printf($ahpGroupRes->res['consens1'], 100*$ahpG->consens);
            } else {
                echo $ahpGroupRes->res['consens0'];
            }
            echo $ahpG->consensusWording(100*$ahpG->consens) . "</p>";
        }
        if ($owner || in_array($uid, $admin)) {
            $ahpG->printAhpGrpResult('pTot', true);
        }
        echo "</div>";
    } else {
        // --- Alternatives details
        if ($rflg) {
            // --- Robustness test
            echo $ahpGroupRes->titles["h3rob"];
            $alt = array_values($ahpG->prio[0]['pTot']);
            arsort($alt);
            $top = key($alt);
            if ($incmplt || (count($ol[0]) == count($alt))) {
                echo $ahpGroupRes->msg['noRt'];
            } else {
                // --- relative top	in $rt1[0]
                $ata = null;
                $ai = array();
                if (empty($rt1[0])) {
                    printf($ahpGroupRes->res["rtrb"], $ahpH->alt[$top]);
                } else {
                    $ata = key($rt1[0]);  // key of first element (smallest)
                    $atv = $rt1[0][$ata]; // value of first element, percentage top
                    $ai = explode('~', $ata); // Ai and Aj
                    printf(
                        $ahpGroupRes->res['rt10'],
                        $ai[2],
                        round(100 * $ahpH->pGlb[$ai[2]], 1),
                        round(-100 * ($ahpH->pGlb[$ai[2]] * $atv), 1),
                        $ahpH->alt[$ai[0]],
                        $ahpH->alt[$ai[1]]
                    );
                }
                // --- relative any  in rt1[1]
                if (!empty($rt1[1])) {
                    $aaa = key($rt1[1]);
                    $aav = $rt1[1][$aaa]; // percentage any critical
                    $ai = explode('~', $aaa);
                    $aac = $ai[2];
                    if ($aaa == $ata && $aav == $atv) {
                        echo $ahpGroupRes->res['rt11s'];
                    } else {
                        printf(
                            $ahpGroupRes->res['rt11'],
                            $ai[2],
                            round(100 * $ahpH->pGlb[$ai[2]], 1),
                            round(-100 * ($aav * $ahpH->pGlb[$ai[2]]), 1),
                            $ahpH->alt[$ai[0]],
                            $ahpH->alt[$ai[1]]
                        );
                    }
                }
                // --- measure of performance rt2
                if (!empty($rt2[0])) {
                    $rtk = key($rt2[0]);  // first key
                    $aiv = $rt2[0][$rtk]; // value
                    $ai = explode("~", $rtk); // Ai, Ak, crit
                    $uc = -$aiv * $ahpG->prio[0][$ai[2]][$ahpH->alt[$ai[0]]];
                    /* todo something like
                     *	( $uc > 0 ? $ahpG->prio[0][$ai[2]][$ai[0]]
                     *  - $ahpG->prioVar['min'][$ai[2]][$ai[0]] :
                     *	$ahpG->prioVar['max'][$ai[2]][$ai[0]]
                     * - $ahpG->prio[0][$ai[2]][$ai[0]]);
                     */
                    printf(
                        $ahpGroupRes->res['rt20'],
                        $ahpH->alt[$ai[0]],
                        $ai[2],
                        round(100 * $ahpG->prio[0][$ai[2]][$ahpH->alt[$ai[0]]], 1),
                        round(100 * $uc, 1),
                        $ahpH->alt[$ai[0]],
                        $ahpH->alt[$ai[1]]
                    );
                    /* todo something like
                     * if( abs($aiv * $ahpG->prio[0][$ai[2]][$ai[0]])
                     * <= abs($uc) )
                     * echo "<span class='hl'>
                     * This is within the estimated uncertainty of ",
                     * round($uc*100,1), "%</span>";
                     * echo "</p>"; */
                }
            }
        } // robustness test

        if ($owner || in_array($uid, $admin)) {
            echo $ahpGroupRes->titles["h2alt"];
            if (empty($ahpG->pwcEmpty)) {
                $pPwc = count($ahpG->pSel);
            } else {
                $pPwc = 1;
                foreach ($ahpG->leafs as $leaf) {
                    $pPwc = max($pPwc, $ahpG->pwcCnt[$leaf]);
                }
            }
            if ($pPwc>1) {
                if ($ahpG->consens < 0) {
                    echo "<p>AHP group consensus: <span class='err'>n/a</span>";
                } else {
                    printf($ahpGroupRes->res['consens1'], 100*$ahpG->consens);
                }
                echo $ahpG->consensusWording(100*$ahpG->consens) . "</p>";
            }
            echo "<div style='margin-left:auto;margin-right:auto;'>";
            $ahpG->printAhpGrpResult('pTot', true);
            echo $ahpGroupRes->titles["h2crit"];
            echo $ahpGroupRes->info['cpbd'];
            $i=0;
            foreach ($ahpG->leafs as $leaf) {
                $pPwc = $ahpG->pwcCnt[$leaf];
                $collapse = "collapse" . ++$i;
                printf($ahpGroupRes->mnu['btnNdD'], $collapse);
                if (isset($ahpG->cr[0][$leaf])) {
                    printf(
                        $ahpGroupRes->res['consens2'],
                        $leaf,
                        round(100 * $ahpG->cr[0][$leaf], 1)
                    );
                }
                if ($pPwc>1) {
                    $consens = $ahpG->getConsensus($leaf, $iScale);
                    if ($consens < 0) {
                        printf($ahpGroupRes->err['consens1']);
                    } else {
                        printf($ahpGroupRes->res['gCons'], 100 * $consens);
                    }
                    echo $ahpG->consensusWording(100 * $consens),"</small>";
                }
                if (empty($ahpG->dmCons[$leaf])) {
                    echo $ahpGroupRes->msg['noPwc3'];
                }
                echo "</p>";
                echo "<div id='", $collapse, "' style='display:none'>";
                // include code to print matrix
                $ahp = new AhpCalcIo(0); // for method print_matrix
                // LEFT COLUMN
                echo "<div style='width:50%;height:auto;float:left;padding:10px;'>";
                echo $ahpGroupRes->titles["h4wCons"];
                if (isset($ahpG->cr[0][$leaf])) {
                    printf($ahpGroupRes->res['cr'], 100* $ahpG->cr[0][$leaf]);
                }
                if ($rflg && isset($ahpG->prioVar['min'][$leaf])) {
                    $tol = array( "min"=>array_values($ahpG->prioVar['min'][$leaf]),
                            "max"=>array_values($ahpG->prioVar['max'][$leaf]));
                } else {
                    $tol= array();
                }
                if (isset($ahpG->prio[0][$leaf])) {
                    $ahp->printVector(
                        array_keys($ahpG->prio[0]['pTot']),
                        array_values($ahpG->prio[0][$leaf]),
                        1,
                        $tol
                    );
                } else {
                    echo $ahpGroupRes->msg['noPwc2'];
                }
                echo "</div>";
                // RIGHT COLUMN
                echo "<div style='float:left;padding:10px;'>";
                echo $ahpGroupRes->titles["h4mCons"];
                if (empty($ahpG->dmCons[$leaf])) {
                    echo $ahpGroupRes->msg['noPwc2'];
                } else {
                    printf($ahpGroupRes->msg['pCnt'], $ahpG->pwcCnt[$leaf]);
                    $ahp->print_matrix($ahpG->dmCons[$leaf]);
                }
                echo "</div>";
                echo "<div style='clear:both;'></div>";

                if ($pPwc>1) {
                    $consens = $ahpG->getConsensus($leaf, $iScale);
                    if ($consens < 0) {
                        echo $ahpGroupRes->err['consens2'];
                    } else {
                        printf(
                            $ahpGroupRes->res['consens4'],
                            $leaf,
                            100 * $consens
                        );
                        echo $ahpG->consensusWording(100 * $consens),
                            "</small></p>";
                    }
                    // Priorities by participant
                    echo $ahpGroupRes->titles["h4group"];
                    $ahpG->printAhpGrpResult($leaf, false);
                }
                echo "</div>"; // collapse
            }
            echo "</div>";
        }
    }
}
// Menu
    echo $ahpGroupRes->titles["h2grMenu"];
    include 'views/ahpGresultMenu.html';
$webHtml->webHtmlFooter($version);
