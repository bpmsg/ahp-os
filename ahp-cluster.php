<?php
/* Consensus cluster analysis
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
 * TODO: all english texts to implement into language files
 *       consensus menu move to views
 *
 */


session_start();

define('MAX_SIZE', 1000000); // Max file size
define('PCNT_MIN', 3);      // Minimum sample count
define('PCNT_MAX', 150);    // Max sample count to display matrix
define('SD', false);        // Testing purpose: display standard dev.

include 'includes/config.php';

$version = substr('$LastChangedDate: 2022-04-28 14:14:51 +0800 (Do, 28 Apr 2022) $', 18, 10);
$rev = trim('$Rev: 206 $', "$");

$err = array();
$extensions = array("json", "JSON");
$priorities = array();
$pgwidth = 900;


/*
 *  Function to display averaged priorities as graphic
 */
function displayDia($sel)
{
    global $ahpCluster;
    $tmp = array();
    $tmp = $ahpCluster->calcAvgDistr($sel);
    foreach ($tmp['avg'] as $k=>$val) {
        $dta['nom'][$k] = round($val * 100, 1);
        if (SD) {
            $dta['min'][$k] = round(($val - $tmp['sd'][$k]) * 100, 1);
            $dta['max'][$k] = round(($val + $tmp['sd'][$k]) * 100, 1);
        }
    }
    $data = urlencode(serialize($dta));
    echo "<div class='ofl'>
        <div style='margin-left:auto;margin-right:auto;width:700px;'>";
    echo "<img src='cl-graph.php?dta=$data' alt='Ahp-dia'>";
    echo "</div></div>";
}


/* TODO
$class = 'Ahp' . $lang;
$ahpOs = new $class();
$_SESSION['lang'] = $lang;
*/

$login = new Login();

// --- DONE
    if (isset($_POST['CANCEL'])) {
        if (isset($_SESSION['prjson'])) {
            unset($_SESSION['prjson']);
        }
        header('HTTP/1.0 200');
        header("Location: " . $urlAhp);
    }

// --- CLEAR to load a new json file
    if (isset($_POST['CLEAR'])) {
        unset($_SESSION['prjson']);
        header('HTTP/1.0 200');
        header('Refresh:0; url=' . $url);
    }

// --- Load priorities from SESSION variable
    if (isset($_SESSION['prjson'])) {
        $new = false;
        $priorities = $_SESSION['prjson'];
    } else {
        $new = true;
    }

// --- Upload/import new json priorities
    if ($new && isset($_FILES['file'])) {
        $file_name = $_FILES['file']['name'];
        $file_size =$_FILES['file']['size'];
        $file_tmp =$_FILES['file']['tmp_name'];
        $file_type=$_FILES['file']['type'];
        $file_ext = explode('.', $file_name);
        $file_ext= strtolower(end($file_ext));

        if ($file_size < 128) {
            $err[] = "Please select a valid json file.";
        } elseif (!in_array($file_ext, $extensions)) {
            $err[]="$file_ext extension is not allowed.";
        } elseif ($file_size > MAX_SIZE) {
            $err[]="File size must not exceed "
                . $phpUtil->display_filesize(MAX_SIZE) . '.';
        } elseif (preg_match('/^[a-zA-Z0-9-_()\s]+\.ext$/', $file_name)) {
            $err[]="Invalid filename.";
        } else {
            $file_name = str_replace(' ', '_', $file_name);
        }

        if (isset($_POST['formToken'])
            && $_POST['formToken'] != $_SESSION['formToken']) {
            $err[] = "Form submission error.";
        } else {
            $_SESSION['formToken']= uniqid();
        }

        if (empty($err)) {
            if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
                $flag = false;
                $err[]= "Possible file upload attack: ";
            } else {
                $flag = true;
                // --- file uploaded successfully
                $string = file_get_contents($_FILES['file']['tmp_name']);
            }
            // --- decode
            $priorities=json_decode($string, true, 6);
            if (json_last_error() == 0) {
                // --- check variables
                if (sizeof($priorities) != 3
                || !array_key_exists("Scale", $priorities)
                || !array_key_exists("Priorities", $priorities)) {
                    $err[] = "Wrong input data, please check file.";
                } else {
                    $_SESSION['prjson'] = $priorities;
                }
            } else { // --- json decode error
                $err[] = json_last_error_msg();
            }
        }
        if ($flag == false) { // --- other general error
            $err[]="Upload Error";
        }
    }

// --- Node selection list
    if (isset($_SESSION['prjson'])) {
        $new = false;
        $nodes = array();
        $nodes = array_merge($nodes, array_keys($priorities['Priorities']));
        if (isset($_POST['node'])) {
            $node = $_POST['node'];
        } else {
            $node = "pTot";
        }
        if ($node !="pTot") {
            $i = array_search($node, $nodes);
            if ($i >0) {
                $nodes[$i] = $nodes[0];
                $nodes[0] = $node;
            }
        }
        $ndCnt = sizeof($priorities['Priorities']); // number of nodes pTot
        $participants = array_keys($priorities['Priorities'][$node]);
        $pCnt =  count($participants) - 1; // number of participants
        if ($pCnt < PCNT_MIN) {
            $err[] = "The node has $pCnt participant(s) only, 
                at least " . PCNT_MIN . " are required for cluster analysis.";
            unset($_SESSION['prjson']);
            $new = true;
        }
        $pgwidth = ($pCnt > 25 ? 1400 : 900);
        $cCnt = count($priorities['Priorities'][$node][$participants[0]]);
    }

// --- Manual Threshold
    if (isset($_POST['thrh'])) {
        $options = array(
            'options' => array(
            'min_range' => 0.6,
            'max_range' => 0.999,
                            ));
        $val = $_POST['thrh'];
        if (filter_var($val, FILTER_VALIDATE_FLOAT, $options)) {
            $threshold = floatval($val);
            $thrFl = true;
        } elseif ($val != " ") {
            $err[] = "Invalid threshold, please input value between 0.6 and 0.999.";
            $threshold = " ";
        }
    }


    if (!$new || $thrFl) {
        // --- Do analysis for $priorities['Priorities'][$node]
        $fct = ($node == 'pTot' && $ndCnt != 2) ? "S" : "S*";
        $ahpCluster = new AhpCluster(
            $priorities['Priorities'][$node],
            $fct,
            (int) $priorities['Scale'][0]
        );
        // --- Consensus whole group
        $tmp =
            $ahpCluster->calcGroupSim(range(0, $ahpCluster->sampleCnt-1));
        $gcons = $tmp['sim'];
        $term =
            $fct == "S" ? "Rel. Homogeneity <i>S</i>" : "AHP Group Consensus <i>S</i>*";
        $catEff = round(exp($tmp['gamma']),2);
        $har0 = 100 * ( exp($tmp['beta']) - 1.)/($ahpCluster->sampleCnt -1);
        unset($tmp);
    }

$pageTitle = "AHP Sample Test Page";
$title     = "AHP Consensus";
$subTitle  = "Group Consensus Cluster Analysis";


/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($pageTitle, $pgwidth);
    include 'includes/login/form.login-hl.php';
    if (!empty($login->errors)) {
        echo $login->getErrors();
    }
    echo "<h1>$title</h1>";
    echo "<h2>$subTitle</h2>";

    if (!empty($err)) {
        echo "<p class='err'>", implode(' ', $err), "</p>";
    }

    echo "<p>The program clusters a group of 
            decision makers into smaller subgroups with higher consensus.
            For each pair of decision makers the similarity of priorities
            is calculated, using Shannon alpha and beta entropy. The result
            is arranged in a similarity matrix and sorted into clusters
            of higher similarity based on a consensus threshold.<br>
            See 
            <a href='https://bpmsg.com/wordpress/wp-content/uploads/2022/04/ConsensusAHP-goepel-2022.pdf'>
            Goepel 2022 (preprint)</a> for more details.</p>";

if ($login->isUserLoggedIn() === false) {
    echo "<p class='hl'>You need to register and log in.</p>";
    echo "<p><a href='$urlAhp'>back</a></p>";
} else {
    if ($new) {
        echo "<p class='msg'>Please select a previously exported AHP priority file (json).
            Export is possible from your projects result page (AHP group result menu). 
            For this group analysis it should contain priorities of at least 4 participants. 
            You then can select different project nodes of the decision hierarchy.</p>";
    } else {
        // --- Input data
        echo "<h2>Input Data</h2>";
        echo "<p>Project session code: <span class='res'>",
            $priorities['Project'], "</span>
            <br>Nodes: <span class='res'>$ndCnt</span>
            <br>Selected Node: <span class='res'>$node</span>
            <br>Number of categories: <span class='res'>$cCnt</span> 
            <br>Number of participants: <span class='res'>$pCnt</span>
            <br>Scale: <span class='res'>", $priorities['Scale'], "</span>
            <br>$term: <span class='res'>", round($gcons * 100, 1), "%</span>";

        // --- For testing purposes
        // echo "<br>Effective number of categories = <span class='res'>$catEff</span>";
        // printf("<br>Harrison H0: <span class='res'>%02.1f%%</span></p>",$har0);

        if ($gcons > 0.7) {
            echo "<p class='hl'>Homogeneity/Group consensus of the whole group is with ",
            round(100* $gcons, 1), "% higher than 70%, no clustering
                required.</p>";
        }
        // --- Input data Graphic
        displayDia(range(0, $pCnt-1));
        echo "<p></p>";
        
        // --- Optional unclustered Similarity Matrix
        // $ahpCluster->printBetaMatrix();

        // --- Consensus threshold table
        echo "<h2>Consensus Threshold</h2>";
        echo "<p>The program calculates the max consensus threshold, 
            where the group is divided in at least two sub-goups, 
            and the number of unclustered members is less than five.
            The consensus range is scanned between 70% and 97.5% in steps
            of 2.5%. Based on the threshold table below, 
            you can <span class='msg'>input a different threshold manually 
            </span>in the <i>AHP Group Consensus Menu</i> below.</p>";

        if (empty($err)) {
            $ahpCluster->printThrhTable();
            // --- determine threshold
            if ($thrFl) {
                echo "<p class='msg'>
                Manual threshold is set to <span class='var'>$threshold</span></p>";
            } else {
                $threshold = $ahpCluster->findThreshold();
            }
            // --- Cluster Algorithm
            $brnk = $ahpCluster->cluster($threshold);
            $clCnt = sizeof($brnk['cluster'])-1;

            // --- RESULT for selected node
            echo "<h2>Result for Node \"$node\"</h2>";
            printf(
                "<p>%s without clustering = <span class='res'>%02.1f%%</span>",
                $term,
                100 * $gcons
            );
            if ($fct == "S*") {
                echo " (", $ahpCluster->consensusWording(100. * $gcons), ")";
            }
            echo "</p>";
            echo "<p class='msg'><span class='res'>";
            echo ($clCnt == -1) ? "0" : $clCnt;
            echo "</span> Cluster(s):</p>";
            // --- clustered
            echo "<ul>";
            $distrCl = array(0); // --- necessary as first element is dropped
            for ($icl = 0; $icl < $clCnt; $icl++) {
                $resCl = $ahpCluster->calcGroupSim($brnk['cluster'][$icl]);
                $distrCl[] = $ahpCluster->calcAvgDistr($brnk['cluster'][$icl])['avg'];
                asort($brnk['cluster'][$icl]); // --- sort
                $clc = sizeof($brnk['cluster'][$icl]);
                $gcons = 100 * $resCl['sim'];
                echo "<li>Subgroup <span class='res'>",$icl+1, "</span>: ";
                // echo "<br>Effective number of categories = ",
                //    round(exp($resCl['gamma']),2),"<br>";
                printf("%s = <span class='res'>%02.1f%%</span> ", $term, $gcons);
                if ($fct == "S*") {
                    printf("(%s)", $ahpCluster->consensusWording($gcons));
                }
                echo " among <span class='res'>$clc</span> of $pCnt ";
                echo "(", round(100 * $clc/$pCnt), "%) participants :<br> <span class='var sm'>";
                // --- Group members
                foreach ($brnk['cluster'][$icl] as $ip => $p) {
                    echo "<span class='hl'>", $p+1, "</span> - ", $ahpCluster->samples[$p], ", ";
                }
                echo "</span></li><p></p>";
                // --- Cluster data Graphic
                displayDia($brnk['cluster'][$icl]);
            }
            echo "</ul>";
                        // --- unclustered
            if (!empty($brnk['unclust'])) {
                echo "<p>Unclustered: <span class='res'>",
                            sizeof($brnk['unclust']);
                echo "</span> participant(s):<br> <span class='var sm'>";
                foreach ($brnk['unclust'] as $ip => $p) {
                    echo "<span class='hl'>",$p+1, "</span> - ",
                     $ahpCluster->samples[$p]. " ";
                }
                echo "</span></p>";
                // --- Unclustered data Graphic
                displayDia($brnk['unclust']);
            }
            echo "</p>";

            // --- Testing purpose: Horn Indices of overlap
            if($clCnt >= 1 && false){
                echo "<h3>Indices of Overlap</h3>";
                $ahpCl = new AhpCluster($distrCl,"H",0);
                $tmp = $ahpCl->calcGroupSim(range(0,$clCnt-1));
                if ($clCnt > 1)
                    $har1 = 100 * (exp($tmp['beta'])-1)/($clCnt-1);
                printf("<p>Harrison: <span class='res'>%02.1f%%</span><br>",
                $har1);
                printf("Horn: Min: <span class='res'>%02.1f%%</span>,
                    max: <span class='res'>%02.1f%%</span></p>",
                    100*$ahpCl->bmin, 100*$ahpCl->bmax);
                $ahpCl->printBetaMatrix();
            }

            // --- Similarity Matrix
            echo "<h2>Similarity matrix</h2>";
            if ($ahpCluster->sampleCnt > PCNT_MAX) {
                echo "<p class='msg'>For more than" . PCNT_MAX .
                "participants the similarity matrix can not be displayed</p>";
            } else {
                $ahpCluster->printColorPalette();
                echo "<p></p>";
                $ahpCluster->printBetaMatrix();
            }
        }
    }
    echo "<p></p>";
    include 'views/ahpClusterMenu.html';
}
$webHtml->webHtmlFooter($version);
