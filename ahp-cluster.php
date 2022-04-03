<?php
/* Consensus analysis */

session_start();
// max file size
define('MAX_SIZE', 400000);
define('PCNT_MIN', 3);      // Minimum sample count
define('PCNT_MAX', 150);    // Max sample count to display matrix

include 'includes/config.php';

$version = substr('$LastChangedDate: 2022-04-02 17:51:07 +0800 (Sa, 02 Apr 2022) $', 18, 10);
$rev = trim('$Rev: 190 $', "$");

$err = array();
$extensions = array("json", "JSON");
$priorities = array();
$pgwidth = 900;

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
                || !array_key_exists("Scale",$priorities )
                || !array_key_exists("Priorities",$priorities ))
                    $err[] = "Wrong input data, please check file.";
                else
                    $_SESSION['prjson'] = $priorities;
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

    if (!$new && empty($err) || $thrFl) {
        // --- Do analysis for $priorities['Priorities'][$node]
        $ahpCluster = new AhpCluster($priorities['Priorities'][$node]);
        $fct = ($node == 'pTot' ? "S" : "S*");
        $ahpCluster->betaMatrix($fct,(int) $priorities['Scale'][0]);
        // --- Consensus whole group
        $gcons = $ahpCluster->calcGroupSim(range(1, $ahpCluster->sampleCnt - 1));
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
            of higher similarity based on a consensus threshold.</p>";

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
        // --- input data
        echo "<h2>Input Data</h2>";
        echo "<p>Project session code: <span class='res'>",
            $priorities['Project'], "</span>
            <br>Node: <span class='res'>$node</span>, 
            <br>Number of categories: <span class='res'>$cCnt</span>,  
            <br>Number of participants: <span class='res'>$pCnt</span>
            <br>Scale: <span class='res'>", $priorities['Scale'], "</span></p>";
        if ($gcons >0.7) {
            echo "<p class='hl'>Homogeneity/Group consensus of the whole group is with ",
            round(100* $gcons), "% higher than 70%, no clustering
                required.</p>";
        }
        echo "<h2>Consensus Threshold</h2>";
        echo "<p>The program calculates the max consensus threshold, 
            where the group is divided in at least two sub-goups, 
            and the number of unclustered members is less than five.
            The consensus range is scanned between 70% and 97.5% in steps
            of 2.5%. Based on the threshold table below, 
            you can <span class='msg'>input a different threshold manually 
            </span>in the <i>AHP Group Consensus Menu</i> below.</p>";

        if(empty($err)){
            $brnk = $ahpCluster->calcThreshold();
            $ahpCluster->printThrhTable($brnk);

            // --- determine threshold
            if ($thrFl) {
                echo "<p class='msg'>
                Manual threshold is set to <span class='var'>$threshold</span></p>";
            } else {
                $threshold = $ahpCluster->findThreshold();
            }

            $brnk = $ahpCluster->cluster($threshold);
            $clCnt = sizeof($brnk['cluster'])-1;
            // --- RESULT for selected node
            echo "<h2>Result for Node \"$node\"</h2>";
            $term =
             ($node == "pTot" ? "Rel. Homogeneity <i>S</i>" : "AHP Group Consensus <i>S</i>*");

            printf(
                "<p>%s without clustering = <span class='res'>%02.1f%%</span> (%s)</p>",
                $term,
                100 * $gcons,
                $ahpCluster->consensusWording(100. * $gcons)
            );

            echo "<p class='msg'><span class='res'>$clCnt</span> Cluster(s):</p>";

            // --- clustered
            echo "<ul>";
            for ($icl = 0; $icl < $clCnt; $icl++) {
                $gcons = $ahpCluster->calcGroupSim($brnk['cluster'][$icl]);
                echo "<li>Subgroup <span class='res'>",$icl+1, "</span>: ";
                printf("%s = <span class='res'>%02.1f%%</span>", $term, 100 * $gcons);
                printf(" (%s) among ", $ahpCluster->consensusWording(100. * $gcons));
                echo "<span class='res'>", sizeof($brnk['cluster'][$icl]),
                    "</span> Members:<br> <span class='var sm'>";
                foreach ($brnk['cluster'][$icl] as $ip => $p) {
                    echo " $p - ", $ahpCluster->samples[$ip+1], ",";
                }
                echo "</span></li>";
            }
            echo "</ul>";
            // --- unclustered
            if (!empty($brnk['unclust'])) {
                echo "<p>Unclustered: <span class='res'>",
                            sizeof($brnk['unclust']);
                echo "</span> Member(s):<br> <span class='var sm'>";
                foreach ($brnk['unclust'] as $ip => $p) {
                    echo " $p - ", $ahpCluster->samples[$ip+1];
                }
                echo "</span></p>";
            }
            echo "</p>";
            // --- Similarity Matris
            echo "<h2>Similarity matrix</h2>";
            if($ahpCluster->sampleCnt > PCNT_MAX){
                echo "<p class='msg'>For more than" . PCNT_MAX . 
                "participants the similarity matrix can not be displayed</p>";
            } else {
                $ahpCluster->printColorPalette();
                echo "<p></p>";
                $ahpCluster->printBetaMatrix();
            }
        }
    }
    echo "<p></p>"; ?>

    <!--- html Menu TODO: into views --->
    <form action="<?php echo $myUrl; ?>" method="POST" enctype="multipart/form-data">
        <input type='hidden' name='formToken' value='<?php echo $_SESSION['formToken']; ?>'>
        <fieldset><legend>Group Consensus Menu</legend>
            <div style='display:block;float:left;padding:2px;'>
                <?php
                if ($new) {
                    echo "<input type='file' name='file' >";
                } else {
                    echo "Selected node: ";
                    if (!empty($nodes)) {
                        $cnt = count($nodes);
                        echo "&nbsp;&nbsp;<select name='node' maxlength='8'>";
                        if ($node !="")
                            echo "<option value='$node'>$node</option>";
                        foreach ($nodes as $val) {
                            if ($val != $node)
                                echo "<option value='$val'>$val</option>";
                        }
                        echo "</select>";
                    }
                } ?>
                &nbsp;&nbsp;<input type="submit" value="Analyze" name="submit">
                <?php if (!$new) { ?>                
                    &nbsp;&nbsp;<small>Threshold (optional): </small>
                    <input type='text' size='4' value=' ' name='thrh'>
                <?php } ?>
            </div><div style='float:right'>
                <?php if (!$new) {
                    echo "<input type='submit' name='CLEAR' value='Load new data' >";
                } ?>
                &nbsp;&nbsp;<input type="submit" value="Done" name="CANCEL" >
            </div>
        </fieldset>
        <div class='msg' style='clear:both;'></div>
    </form>
<?php
}
$webHtml->webHtmlFooter($version);
