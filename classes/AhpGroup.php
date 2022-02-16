<?php
/**
 * Analytic Hierarchy Process group sessions 2014-01-06
 * functions to calculate group results and AHP consensus indicator
 *
 * The actual result of AHP projects is calculated "on-the-fly", when
 * the user calls the result page (ahp-group.php). In the database only
 * the decision hierarchy definition and alternative names and the
 * pairwise comparisons are stored. The AhpGroup class has all methods
 * to calculate and display the final results.
 *
 * $LastChangedDate: 2022-02-16 11:53:54 +0800 (Mi, 16 Feb 2022) $
 * $Rev: 139 $
 *
 * @author Klaus D. Goepel
 * @since 2014-01-06
 * @version 2019-06-25 __construct($sc) last version w/o SVN
 * @uses colorClass, ahpDbclass, ahpClass, ahpHierClass
 * @uses $_SESSION['ipart'] in function exportGroupResult
 * (selected participants)
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
 * public function getPrioHier($iScale)        - main method hierarchy
 * public function getPrioAlt ($iScale, $ahpH) - main method alternatives
 * public function generateRandPwc($n ,$dj)	   - Monte Carlo
 * public function uncertainty($pwcr, $ahpH, $iScale, $sdFlg=0, $hierMode)
 * public function getConsensus($node, $iScale)- AHP consensus
 *
 * HTML/CSV output
 * public  function printAhpGrpResult($node, $tflg)   - HTML output
 * public  function exportGroupResult($hierMode, $ds) - CSV output
 *
 * private function predecessor($node, $prio)
 * private function ahpConsolidate($iScale, $ahpH, $hierMode)
 * private function pwcGeoMean($pwcArray) calculates sd in pwc['SD']
 * private function relHomogeneity
 * private function getShannonBeta($node)
 * private function getConsensus($node, $iScale)
 * private function getGlbConsensus()
 * private function ahpShannonCor($nCrit, $mScale = 9, $kPart)
 * private function hamin($nCrit, $mScale = 9)
 * private function hgmax($nCrit, $mScale = 9, $kPart)
 * private function normd()
 */

class AhpGroup
{
    /** Class constants */
    public const MAXCOL = 10;           // maximal numer of columns for result table
    public const NEWL = "\n";           // for csv file export
    public const ENCL = '"';
    public const RGBBASE = "#50D27B";
    public const RGBEND  = "#EBB5A2";
    public const CRLMT = 0.25;          // limit of cr for randomized pwcs

    /** Properties */

    public $sessionCode;        // Session code
    public $pj = array();       // project data
    public $nodes;              // hierarchy nodes
    public $leafs;              // hierarchy leafs

    public $part = array();     // all participants
    public $pSel = array();     // selected participants; key not squential!
    public $pwcCnt = array();   // pwcCnt[$node] number of consolidated judgments for $node
    public $pwcEmpty = array(); // array[$node] contains names of participants w/o pwc
    public $pwcN = array();     // array[$node] contains names of part with pwc

    public $prio = array();     // $prio[$partNo][$node][$branch] $partNo = 0 consolidated
    public $prioVar = array();
    public $simCnt;             // total number random simulations

    public $consens1;           // beta diversity for global priorities
    public $consens;            // consensus for global priorities/alternatives
    public $pwcCons = array();  // unscaled geomean of participants, includes sd

    public $dmCons = array();   // $dmCons[$node][$i][$j] consolidated matrices
    public $cr = array();       // consistency ratio cr[$partNo][$node] set in prioHier

    public $err = array();
    public $wrn = array();

    public $ahpDb;              // class for AHP database functions
    public $ahpGroupTxt;        // language class
    private $lang;

    /* ahp scales and maximum m for x = 9 */
    public $ahpScale = array(
    0 => array( "0 - Standard AHP linear scale",9),
    1 => array( "1 - Logarithmic scale",3.321928095),
    2 => array( "2 - Root square scale",3),
    3 => array( "3 - Inverse linear scale",9),
    4 => array( "4 - Balanced scale",9),
    5 => array( "5 - Balanced-n scale",9),
    6 => array( "6 - Adaptive-bal scale", 0),
    7 => array( "7 - Adaptive scale", 0),
    8 => array( "8 - Power scale",81),
    9 => array( "9 - Geometric scale",256)
    );

    /* Interpretation of AHP consensus indicator */
    public function consensusWording($c)
    {
        if ($c<=50) {
            return " <span class='res'>very low</span>";
        } elseif ($c >50 && $c <= 65) {
            return " <span class='res'>low </span>";
        } elseif ($c >65 && $c <= 75) {
            return " <span class='res'>moderate </span>";
        } elseif ($c >75 && $c <= 85) {
            return " <span class='res'>high </span>";
        } elseif ($c >85 && $c <= 100) {
            return " <span class='res'>very high</span>";
        } else {
            return "";
        }
    }

    /** Methods */

    /* Initialize class
     * sets session code $this->sessionCode if valid
     * sets project data $this->pj
     * sets participants $this->part
     */
    public function __construct($sc)
    {
        mb_internal_encoding('UTF-8');
        global $lang;
        $class = get_class() . $lang;
        $this->ahpGroupTxt = new $class();
        $this->lang = $lang;

        $this->ahpDb = new AhpDb(); // Initialize AHP database class

        if ($this->ahpDb->checkSessionCode($sc)) {
            $this->sessionCode = $sc;
            $this->part = $this->ahpDb->getParticipants($sc);
            if (empty($this->part)) {
                // Project has no participants
                $this->err[] = $this->ahpGroupTxt->wrn['noPart'];
            } else {
                $this->pj =   $this->ahpDb->readProjectData($sc);
                $this->pSel = array_values($this->ahpDb->getSelectedParticipants($sc));
                if (!empty($this->ahpDb->err)) {
                    $this->err = array_merge($this->err, $this->ahpDb->err);
                }
            }
            $this->simCnt = 0;
        } else {
            // Invalid Session code
            $this->err[] = $this->ahpGroupTxt->err['invSc'];
            $this->sessionCode = "";
        }
    }


    /* Main method of the class for hierarchies:
     *
     * Calculate consolidated priorities of the hierarchy
     * $this->prio is a numeric array containing results for each participant
     * in keys [1] ... [pn].
     * [0] contains the consolidated result (AIJ) of all participants.
     * The second key is is the node of the decision hierarchy.
     * Node ['pTot] gives the global priorities.
     * The third key are the leafs belonging to each node.
     *
     * @return string $text hierarchy text for the consolidated hierarchy
     * with consolidated priorities set using "=0.xxxx"
     */
    public function getPrioHier($iScale = 0)
    {
        $text = "";
        $ahpH = new AhpHier($this->pj['project_hText']);
        // get hierarchy nodes
        $this->nodes = $ahpH->nodes;
        // consolidate
        $text = $this->ahpConsolidate($iScale, $ahpH, true);
        // calculate consolidated global priorities
        $pc = count($this->pSel);
        for ($i = 0; $i <= $pc; $i++) {
            $ahpH->priorities = $this->prio[$i];
            $ahpH->pLoc = array();
            $ahpH->setPlocAll();
            $ahpH->setPglb();
            $this->prio[$i]['pTot'] = $ahpH->pGlb;
        }
        $this->pwcN['pTot'] = $this->pSel;
        $this->pwcCnt['pTot'] = count($this->pSel);
        // calculate Hbeta and beta Diversity
        $hBeta = $this->getShannonBeta('pTot');
        $nClass = count($this->prio[0]['pTot']);
        if ($hBeta !== "n/a") {
            $this->consens1 = $this->relHomogeneity(exp($hBeta), $nClass);
        }
        // calculate min/max entropy
        if ($pc > 1) {
            $this->getGlbConsensus($iScale);
        }

        $this->wrn = array_merge($this->wrn, $ahpH->wrn);
        return $text;
    }


    /* Main method of the class for alternatives:
     *
     * Calculation of consolidated priorities for alternatives
     * $this->prio is a numeric array containing results for each participant
     * in keys [1] ... [pn].
     * [0] contains the consolidated result from all participants.
     * The second key is the name of each criterion.
     * The weighted sum/weighted product total result has the key [pTot]
     * The third key is the name of each alternative.
     *
     */
    public function getPrioAlt($iScale, $ahpH)
    {
        if (isset($this->pj['project_alt'])) {
            $altNum = count($this->pj['project_alt']);
        } else {
            $this->err[] = $this->ahpGroupTxt->err['noAlt'];
            return false;
        }
        $this->ahpConsolidate($iScale, $ahpH, false);
        // calculate total priority for each alternative and all participants
        $ahpH->pwcaDoneFlg = true;
        $pc = count($this->pSel);
        for ($i=$pc; $i>=0; $i--) { // Loop through all selected participants
            foreach ($this->leafs as $leaf) {
                if (isset($this->prio[$i][$leaf])) {
                    $ahpH->prioAlt[$leaf] = array_values($this->prio[$i][$leaf]);
                } else {
                    $ahpH->pwcaDoneFlg = false; // incomplete project evaluation
                    $ahpH->prioAlt[$leaf] = array_fill(0, $altNum, null);
                }
            }
            $this->prio[$i]['pTot'] = array_combine($ahpH->alt, $ahpH->setPrioTot());
        }
        $this->pwcN['pTot'] = $this->pSel;
        $this->pwcCnt['pTot'] = $pc;
        // calculate Hbeta and beta Diversity
        // global priorities in ahpH->pGlb
        $hBeta = $this->getShannonBeta('pTot');
        // todo: removed n/a and replaced by 0
        if ($hBeta !== 0) {
            $this->consens1 = $this->relHomogeneity(exp($hBeta), $altNum);
        }
        // calculate consensus as average of all leafs
        foreach ($this->leafs as $leaf) {
            if (!isset($this->pwcEmpty[$leaf]) ||
        (isset($this->pwcEmpty[$leaf]) && $pc - count($this->pwcEmpty[$leaf]) > 1)) {
                $this->consens += $ahpH->pGlb[$leaf] * $this->getConsensus($leaf, $iScale);
            }
        }
        $this->wrn = array_merge($this->wrn, $ahpH->wrn);
        return;
    }


    /*
     * Common part for hierarchy and alternative consolidation
     * calculates results and stores them in $this->prio
     */
    private function ahpConsolidate($iScale, $ahpH, $hierMode)
    {
        //	global $ahpH;
        $text="";
        $pwcs = array();
        $partCnt = count($this->pSel);
        $this->leafs = $ahpH->leafs;
        $this->cr[0]['pTot'] = 0.;

        if ($hierMode) {
            $text = $ahpH->hText;
            $nodes = $this->nodes;
        } else {
            $nodes = $this->leafs;
            $branch = $ahpH->alt;
        }
        // loop through all leafs, get pairwise comparisons and calculate priorities
        foreach ($nodes as $node) {
            $this->pwcCnt[$node] = $partCnt;
            if ($hierMode) {
                $branch = $ahpH->getTreeNode($ahpH->hierarchy, $node);
            }
            $n = count($branch);
            $npc = $n*($n-1)/2;
            $i = 0;
            // loop through all participants (1 to $partCnt) 0 reserved for consolidation
            foreach ($this->pSel as $k=>$name) {
                $this->cr[$i+1][$node]  = 0.;
                // get pwc for node node$ and participant with name $name
                $tmp = $this->ahpDb->getPwcArray($this->sessionCode, $name, $node);
                // we check for inconsistencies in pwc table
                $dbE = !empty($tmp[$node]) && count($tmp[$node]['A']) != $npc;
                if (!empty($tmp[$node]) && !$dbE) {
                    // pwc is used as a temporary array for the calculation of geometric mean
                    $this->pwcN[$node][$k]=$name;
                    $pwc[$i] = $tmp[$node];
                    $ahp = new AhpCalc($n);
                    // here we have the judgment $pwc[$i] and can apply different scales
                    $pwcs[$i] = $this->calcScale($pwc[$i], $iScale);
                    $ahp->set_evm($pwcs[$i]);
                    $resPrio = $ahp->get_evm();
                    // Set result and cr for participant i
                    $this->prio[$i+1][$node] = array_combine($branch, $resPrio['evm_evec']);
                    $this->cr[$i+1][$node] = $resPrio['cr'];
                    $this->cr[$i+1]['pTot'] = max($resPrio['cr'], $this->cr[$i+1]['pTot']);
                    // here we can set uncertainties based on measurement error theory
                    // $resmax[$node][$branch[$k]] and $resmin[$node][$branch[$k]]
                    unset($ahp);
                    if ($hierMode) {
                        // calculate global priorities - for $hierMode
                        $ahpH->setPlocAll();
                        $ahpH->setPglb();
                    }
                } else { // no pwc for $name and $node
                    if ($dbE) {
                        $this->err[] = sprintf($this->ahpGroupTxt->err['dbE'], $name, $node);
                        trigger_error(
                            $this->sessionCode .
                            " inconsistent PWC: $name node: $node",
                            E_USER_WARNING
                        );
                    }
                    $this->pwcEmpty[$node][] = $name;
                    $this->pwcCnt[$node] -= 1;
                    if ($hierMode) {
                        $this->prio[$i+1][$node] = $ahpH->priorities[$node];
                        $this->cr[$i+1][$node] = 0;
                    } else {
                        $this->prio[$i+1][$node] = array_combine($branch, array_fill(0, $n, 1/$n));
                    }
                }
                $i++; // next participant
            }
            // consolidate: calculate geometric mean (aggregation of individual judgments - index 0)
            if ($pwcs != null) {
                // --- calculate consolidated priorities in prio[0]
                // --- $pwc unscaled, pwcs scaled
                $this->pwcCons[$node] = $this->pwcGeoMean($n, array_values($pwc));
                $pwcsCons = $this->calcScale($this->pwcCons[$node], $iScale);
                $ahp = new AhpCalc($n);
                // --- Keep consolidated matrix for each node
                $this->dmCons[$node] = $ahp->getMatrixFromPwc($pwcsCons);
                $ahp->set_evm($pwcsCons);
                $resPrio = $ahp->get_evm();
                $this->prio[0][$node] = array_combine($branch, $resPrio['evm_evec']);
                $this->cr[0][$node] = $resPrio['cr'];
                $this->cr[0]['pTot'] = max($resPrio['cr'], $this->cr[0]['pTot']);
                // --- here we can set uncertainties based on measurement error theory
                // --- $resmax[$node][$branch[$k]] and $resmin[$node][$branch[$k]]
                unset($ahp);

                if ($hierMode) {
                    // --- set priorities in hierarchy text - hierMode only
                    $nodeTxt = $node . ": ";
                    for ($j = 0; $j<$n; $j++) {
                        $nodeTxt .= $branch[$j] . "=" . round($resPrio['evm_evec'][$j], 8) . ", ";
                    }
                    $nodeTxt = rtrim($nodeTxt, ", ") . ";";
                    $txtNa = explode(":", $nodeTxt);
                    $text = $ahpH->setNewText($text, $node, $txtNa[1]);
                }
            } else { // --- no pwc for $name and $node
                if ($hierMode) {
                    for ($j = 0; $j<$n; $j++) {
                        $this->prio[0][$node][$branch[$j]]= $ahpH->priorities[$node][$branch[$j]];
                    }
                    $this->cr[0][$node] = 0;
                }
            }
            // --- clear pwc for next node
        $pwc = array(); // reset
        $pwcs = array();
        } // --- end foreach $node

        // ---sort missing pwc inputs by nodes
        $ndl = "";
        foreach ($nodes as $node) {
            if (isset($this->pwcEmpty[$node]) && $this->pwcCnt[$node] == 0) {
                $ndl .= $node .", ";
            }
        }
        if ($ndl <> "") {
            $this->wrn[] = sprintf($this->ahpGroupTxt->wrn['noPwc'], $ndl);
        }
        // --- $text will be empty for alternative mode
        return $text;
    }


    /*
     * Converts the judgment $pwc to a selected $scale
     * M <> 9 for scale 6 and 7 in getConsensus()
     */
    private function calcScale($pwc, $iScale)
    {
        $n = (int) (0.5 + sqrt(2 * count($pwc['A']) +0.25));
        foreach ($pwc['Intense'] as $i => $x) {
            switch ($iScale) {
            case 0: $c = $x; break; // 0 - linear scale (original AHP 1 to 9 scale)
            case 1: $c = log($x+1, 2);  break; // 1 - Logarithmic scale
            case 2: $c = sqrt($x);  break;    // 2 - Root square scale
            case 3: $c = 9/(10-$x);	break;    // 3 - Inverse Linear
            case 4: $c = (9+$x)/(11-$x); break; // 4 - Balanced scale @todo: take out
            case 5: $wb = 1/$n + (9/($n+8)-1/$n)/8*($x-1);
                            $c = $wb * (1-$n)/($wb-1); break;  // 5 - Balanced-n scale
            case 6: $wb = 1/$n + (0.9-1/$n)/8*($x-1);  // 6 - Adaptive-bal scale
                            $c = $wb*(1-$n)/($wb-1); break;    // 7 - Adaptive scale
            case 7: $c = pow($x, 1+ log($n-1)/log(9)); break;
            case 8: $c = $x * $x; 	break;    // 8 - Power scale
            case 9: $c = pow(2, $x-1); break; // 9 - Geometric scale
            default: $c = $x; break;
            // case 10: $c = 1 + ($x - 1)/(9 - 1); break; Kocz scale
        }
            $pwc['Intense']	[$i] = $c;
        }
        return $pwc;
    }


    /*
     * Calculates geometric mean for array of pairwise comparisons $pwcArray
     * Instead of calculating the geo mean of the whole matrix array, it is
     * calculated for the pairwise comparisons. From there the consolidated
     * decision matrix can easily be filled.
     * @return array $pwcCons consolidated judgment
     * ['A'] (0,1) and ['Intense'] 0 - 9 ['SD'] standard deviation
     **/
    private function pwcGeoMean($n, $pwcArray)
    {
        $pval = array();
        $pvalEx = array();
        $pvalEx2 = array();
        $pwcCons = array();
        $pk = count($pwcArray);
        $npc = ($n * $n - $n)/2;
        $pvalEx =  array_fill(0, $npc, 0.);
        $pvalEx2 = array_fill(0, $npc, 0.);
        for ($i=0; $i < $npc; $i++) {
            for ($k=0; $k < $pk; $k++) {
                $pval = ($pwcArray[$k]['A'][$i] == 0 ?
                $pwcArray[$k]['Intense'][$i] : 1./$pwcArray[$k]['Intense'][$i]);
                $x = log($pval);
                $pvalEx[$i] += $x;
                $pvalEx2[$i] += $x * $x;
            }
        }
        for ($i=0; $i < $npc; $i++) {
            // standard deviation
            if ($pk > 1) {
                $t = ($pvalEx2[$i] - $pvalEx[$i]*$pvalEx[$i]/$pk)/($pk-1);
            } else {
                $t = 1.;
            }
            $pwcCons['SD'][] = exp(sqrt(round($t, 9)));
            $pvalEx[$i] = exp($pvalEx[$i]/$pk);
            $pwcCons['A'][] = ($pvalEx[$i] < 1. ? 1 : 0);
            $pwcCons['Intense'][] = ($pvalEx[$i] < 1. ? 1./$pvalEx[$i] : $pvalEx[$i]);
        }
        return $pwcCons;
    }


    /*
     * Calculate relative homogeneity index 0 - 1
     * This is the uncorrected AHP consensus indicator
     * shown for the global priorities
     */
    private function relHomogeneity($dBeta, $dBetaMin)
    {
        if ($dBeta < 1.E-10 || $dBetaMin < 1.E-10) {
            return 0;
        }
        $s = 1/$dBetaMin;
        return (1./$dBeta - $s) / (1. - $s);
    }


    /*
     * Calculates Shannon beta entropy as gamma - alpha entropy
     * $ppal = alpha entropy, $pgam = gamma entropy, $pbet = beta entropy
     * @param float array $prio contains AHP priorities for participants [1] to [k]
     * $prio[participant][node][branch]=>priority
     * @return float $pbet Shannon beta entropy (ln)
     */
    private function getShannonBeta($node)
    {
        if ($this->pwcCnt[$node] <= 1) {
            return 0;
        }
        $cCnt = count($this->prio[0][$node]);
        $ppAvg = array_fill_keys(array_keys($this->prio[0][$node]), 0.);
        $ppal = 0.;
        // loop through participants - leave out 0 (consolidated)
        foreach ($this->pwcN[$node] as $k => $part) {
            $ppLn = 0.;
            // loop through criteria
            foreach ($this->prio[$k+1][$node] as $crit=>$p) {
                $ppLn += -1. * $p * log($p);
                $ppAvg[$crit] += $p;
            }
            $ppal += $ppLn;
        }
        $ppal /= $this->pwcCnt[$node];
        $pgam = 0.;
        foreach ($this->prio[0][$node] as $crit=>$v) {
            $pavg = $ppAvg[$crit]/$this->pwcCnt[$node];
            $pgam += -1. * $pavg * log($pavg);
        }
        $pbet = $pgam - $ppal;
        return $pbet;
    }


    /*
     * Calculates AHP consensus indicator
     * S = (M - 1/cor)/(1 - 1/cor)
     * with M = 1/exp(Hbeta) and
     * cor = exp(Hgmax)/exp(Hamin)
     * @uses getShannonBeta
     * @uses ahpShannonCor
     * @return float if more than 1 participant, "n/a" otherwise
     */
    public function getConsensus($node, $iScale = 0)
    {
        // $m depends on m for adaptive, adaptive-bal scales!
        switch ($iScale) {
        case 6: // adaptive
        case 7: // adaptive-bal
            $n = count($this->prio[0][$node])-1;
            $m = $n * 9;
            break;
        default:
            $m = $this->ahpScale[$iScale][1] ;
    }
        $pbet = $this->getShannonBeta($node);
        if (is_float($pbet)) {
            // calculate correction factor for AHP
            $cor = $this->ahpShannonCor(
                count($this->prio[0][$node]),
                $m,
                $this->pwcCnt[$node]
            );
            $consensus = ((1./exp($pbet) - 1./$cor))/(1. - 1./$cor);
            return $consensus;
        } else {
            return($pbet);
        }
    }


    /*
     * checks whether $node has a predecessor. If yes, returns predecessor
     * node name and priority
     */
    private function predecessor($node)
    {
        foreach ($this->prio[0] as $key => $branch) {
            $tmp = array_keys($branch);
            if (in_array($node, $tmp)) {
                $val = $this->prio[0][$key][$node];
                return array( $key => $val);
            }
        }
        return array();
    }


    /*
     * Calculates global consensus for the hierarchy using
     * weighted average. Each level sums up to 1.
     * divided by number of levels
     */
    private function getGlbConsensus($iScale = 0)
    {
        $this->consens = 0.;
        $lvn = 0;
        foreach ($this->nodes as $nod) {
            $nd = $nod;
            $pn = 1.;
            $i=0;
            $p=array();
            do {
                $p = $this->predecessor($nd);
                if (!empty($p)) {
                    $nd = key($p);
                    $pn *= $p[$nd];
                }
            } while (!empty($p) && ++$i<20);
            $this->consens += $pn * $this->getConsensus($nod, $iScale);
            $lvn += $pn;
        }
        $this->consens /= $lvn;
        return;
    }


    /*
     * Calculates the correction factor for the AHP consensus indicator
     * cor = exp(Hgmax)/exp(Hamin)
     * This function combines the functions hamin($nCrit, $mScale = 9) and
     * hgmax($nCrit, $mScale = 9, $kPart)
     * @param int $nCrit number of criteria
     * @param int $mScale max. points on the AHP scale (= 9 for AHP and BAL)
     * @param int $kPart number of participants ("samples")
     * @return float $cor correction based on gamma max and beta min entropy
     */
    private function ahpShannonCor($nCrit, $mScale = 9, $kPart)
    {
        $halmin = $mScale/($nCrit + $mScale - 1.);
        $halmin *= -log($halmin);
        $tmp = 1./($nCrit + $mScale - 1.);
        $tmp *= -log($tmp);
        $halmin += ($nCrit - 1) * $tmp;
        // up to here same as function hamin($nCrit, $mScale = 9) below
        $hgamax = ($nCrit - $kPart) * $tmp;
        $tmp =  ($kPart + $mScale - 1.)/($nCrit + $mScale - 1.)/$kPart;
        $tmp *= -log($tmp);
        $hgamax = log($nCrit);
        $cor = exp($hgamax-$halmin);
        return $cor;
    }


    /*
     * halmin is the minimum Shannon alpha entropy and depends
     * on the AHP scale  maximum value ($mScale) and the number
     * of criteria $nCrit
     */
    private function hamin($nCrit, $mScale = 9)
    {
        $halmin = $mScale/($nCrit + $mScale - 1.);
        $halmin *= -log($halmin);
        $tmp = 1./($nCrit + $mScale - 1.);
        $tmp *= -log($tmp);
        $halmin += ($nCrit - 1) * $tmp;
        return $halmin;
    }


    /*
     * Generates normal distributed random numbers mu=0 annd sigma=1
     */
    private function normd()
    {
        $i = 0;
        do {
            $i++;
            $u = (2*mt_rand(0, 100000)/100000.) - 1.;
            $v = (2*mt_rand(0, 100000)/100000.) - 1.;
            $q = $u * $u + $v * $v;
        } while (($q > 1 || $q < 0));
        $p = sqrt(-2. * log($q)/$q);
        return $u * $p ;
    }


    /*
     * Generates random $n pwcs with variations of +/- 0.5
     * pwc = array(2){['A']=>array(npc){} ['Intense']=>array(npc){}
     * when $dj=0 standard deviation should be given under pwc['SD']
     * @uses normd()
     * @return $pwcr[0] ...[n] with [A] and [Intense]
     *
     */
    public function generateRandPwc($nv, $dj)
    {
        $pwcr= array();
        foreach ($this->pwcCons as $nod => $pwc) {
            $npc = count($pwc["A"]);
            if ($dj == 0) {
                if (empty($pwc['SD'])) {
                    return array();
                }
                $sdflg = true;
            } else {
                $djm = 2 * $dj;
                $sdflg = false;
            }
            for ($i = 0; $i < $nv; $i++) { // generate n pwcs
                $tmpv = array();
                $tmpa = array();
                for ($j = 0; $j < $npc; $j++) {
                    // randomize
                    if ($sdflg) {
                        $sd = log($pwc['SD'][$j]);
                        $vr = $pwc["Intense"][$j] * exp($sd/2 * $this->normd());
                    } else {
                        $vr = $pwc["Intense"][$j] + $djm * mt_rand(-1, 1)/2.;
                    }
                    if (($vr > 9.5)) { // then we allow only entries < 9.5
                        $vr = 9.5;
                    }
                    if (($vr < 1) && !$sdflg) {
                        $vr = 2 - $vr;
                        $a = ($pwc['A'][$j] + 1) % 2;
                    // $vr = 1;
                    // $a = $pwc['A'][$j];
                    } else {
                        $a =  $pwc['A'][$j];
                    }
                    $tmpv[] = $vr;
                    $tmpa[] = $a;
                }
                $pwcr[$nod][] = array( "A" => $tmpa, "Intense" => $tmpv);
            }
        }
        return $pwcr;
    }


    /*
     * calculates weight uncertainties based on randomized variations of pwcs
     */
    public function uncertainty($pwcr, $ahpH, $iScale, $sdFlg=0, $hierMode)
    {
        if ($hierMode) {
            $ahpH = new AhpHier($this->pj['project_hText']);
            $ahpH->setPglb();
        } else {
            $n = $ahpH->altNum;
            $branch = $ahpH->alt;
        }
        // go through all nodes
        foreach ($pwcr as $node => $pwcs) {
            if ($hierMode) {
                $branch = $ahpH->getTreeNode($ahpH->hierarchy, $node);
                $npc = count($pwcs[0]['A']);
                $n = (int)(0.5+sqrt(2*$npc+0.25));
            }
            $pc = count($pwcs); // number of simulations
            // fill max/min result priorities with 0 resp 1
            foreach ($branch as $k) {
                $resmax[$node][$k] = 0.;
                $resmin[$node][$k] = 1.;
                $resex [$node][$k] = 0.;
                $resex2[$node][$k] = 0.;
            }
            foreach ($pwcs as $pwc) {
                $res = array();
                $ahp = new AhpCalc($n);
                $pwc = $this->calcScale($pwc, $iScale);
                $ahp->set_evm($pwc);
                $res = $ahp->get_evm();
                unset($ahp);
                // check for consistency, if exceeding CRLMT don't use.
                if ($res['cr'] > self::CRLMT) {
                    for ($i=0; $i< $n; $i++) {
                        $resmax[$node][$branch[$i]]
                        = $this->prio[0][$node][$branch[$i]];
                        $resmin[$node][$branch[$i]]
                        = $this->prio[0][$node][$branch[$i]];
                    }
                    $pc -=1;
                } else {
                    $this->simCnt +=1;
                    for ($i=0; $i< $n; $i++) {
                        $resmax[$node][$branch[$i]]
                        = max($resmax[$node][$branch[$i]], $res["evm_evec"][$i]);
                        $resmin[$node][$branch[$i]]
                        = min($resmin[$node][$branch[$i]], $res["evm_evec"][$i]);
                        $resex [$node][$branch[$i]]
                        += $res["evm_evec"][$i];
                        $resex2[$node][$branch[$i]]
                        += $res["evm_evec"][$i] * $res["evm_evec"][$i];
                    }
                }
            }
            if ($pc < 100 && $pc>0) {
                $this->wrn[] = sprintf($this->ahpGroupTxt->wrn['fUncEst'], $node, $pc);
            } elseif ($pc == 0) {
                $this->wrn[] = sprintf($this->ahpGroupTxt->wrn['nUncEst1'], $node);
            }

            if ($sdFlg) {
                for ($i=0; $i< $n; $i++) {
                    // standard deviation
                    $resex2[$node][$branch[$i]]
                    = sqrt(($resex2[$node][$branch[$i]]
                    - $resex[$node][$branch[$i]]
                    * $resex[$node][$branch[$i]] / $pc)/($pc-1));
                    $resex[$node][$branch[$i]]  /= $pc;
                    // set res with +/- standard deviation
                    $resmax[$node][$branch[$i]]
                    = $this->prio[0][$node][$branch[$i]]
                    + $resex2[$node][$branch[$i]];
                    $resmin[$node][$branch[$i]]
                    = $this->prio[0][$node][$branch[$i]]
                    - $resex2[$node][$branch[$i]];
                    if ($resmin[$node][$branch[$i]] < 0.) {
                        $resmin[$node][$branch[$i]] = 0.;
                    }
                }
            }

            // nodes without pairwise comparisons
            if ($hierMode) {
                $ta = array_diff_key($ahpH->priorities, $pwcr);
            } else {
                $ta = array_diff_key($ahpH->prioAlt, $pwcr);
            }
            foreach ($ta as $node =>$branch) {
                $resmax[$node] = $this->prio[0][$node];
                $resmin[$node] = $this->prio[0][$node];
            }
        } // loop through pwcr

        if (!empty($ta)) {
            $this->wrn[] = sprintf(
                $this->ahpGroupTxt->wrn['nUncEst2'],
                implode(", ", array_unique(array_keys($ta)))
            );
        }
        if ($hierMode) {
            // calculate consolidated global priorities
            $ahpH->priorities = $resmax;
            $ahpH->pLoc = array();
            $ahpH->setPlocAll();
            $ahpH->setPglb();
            $resmax['pTot'] = $ahpH->pGlb;

            $ahpH->priorities = $resmin;
            $ahpH->pLoc = array();
            $ahpH->setPlocAll();
            $ahpH->setPglb();
            $resmin['pTot'] = $ahpH->pGlb;
        } else {
            foreach ($ahpH->leafs as $node) {
                for ($i=0; $i< $n; $i++) {
                    $ahpH->prioAlt[$node][$i] = $resmax[$node][$branch[$i]];
                }
            } // max
            $ahpH->pLoc = array();
            $ahpH->setPlocAll();
            $ahpH->setPglb();
            $resmax['pTot'] = array_combine(
                array_values($ahpH->alt),
                array_values($ahpH->setPrioTot())
            );
            foreach ($ahpH->leafs as $node) {
                for ($i=0; $i< $n; $i++) {
                    $ahpH->prioAlt[$node][$i] = $resmin[$node][$branch[$i]];
                }
            } // min
            $ahpH->pLoc = array();
            $ahpH->setPlocAll();
            $ahpH->setPglb();
            $resmin['pTot'] = array_combine(
                array_values($ahpH->alt),
                array_values($ahpH->setPrioTot())
            );

            // set back to nominal
            foreach ($this->leafs as $node) {
                $ahpH->prioAlt[$node] = array_values($this->prio[0][$node]);
            } // nom
            $ahpH->prioTot = array_values($this->prio[0]['pTot']);
        }
        $this->prioVar['max'] = $resmax;
        $this->prioVar['min'] = $resmin;
        return array( "min" => $this->prioVar['min'], "max" => $this->prioVar['max']);
    }


    /*
     * Calculates overlap of priorities based on uncertainty estimation
     * @uses $this->prio[0]['pGlb/pTot'] and
     * @uses $this->prioVar['min']['pGlb/pTot'],$this->prioVar['ax']['pGlb/pTot']
     */
    public function getOverlap($hflg=true)
    {
        if ($hflg) {
            $idx = 'pTot';
            $p = $this->prio[0][$idx];
        } else {
            $idx = 'pTot';
            $p = $this->prio[0][$idx];
        }
        arsort($p);
        $criteria = array_keys($p);
        $n = count($criteria);
        $ol = array();
        for ($i=0; $i< $n; $i++) {
            $ol[$i][] = $criteria[$i];
            for ($j=$i+1; $j< $n; $j++) {
                $tmp =   $this->prioVar['min'][$idx][$criteria[$i]]
            - $this->prioVar['max'][$idx][$criteria[$j]];
                if ($tmp <0) {
                    $ol[$i][] = $criteria[$j];
                }
            }
        }
        $oli[0] = $ol[0];
        for ($i=0; $i < $n - 1; $i++) {
            if (count(array_unique(array_merge($ol[$i+1], $ol[$i]))) != count($ol[$i])) {
                $oli[$i+1] = $ol[$i+1];
            }
        }
        return $oli;
    }


    /** HTML Vector printout as table Rows = participants:
     * no, criteria, rank, weight participant 1,2, ...
     * @param string $node node name of priority vector
     * @param boolean $tflg w or w/o tolerance
     */
    public function printAhpGrpResult($node, $tflg)
    {
        $this->colors = new AhpColors();
        $rgbBaseColor = self::RGBBASE;
        $rgbEndColor =  self::RGBEND;
        $pctBfmt = "<span class='res'>%02.1f%%</span>";
        $pCnt = count($this->pSel); // number of participants
    $bCnt = count($this->prio[0][$node]); // number of branches
    $branch = array_keys($this->prio[0][$node]);
        echo "\n<!-- AHP GROUP RESULT -->";
        $tbCnt = ceil($bCnt/self::MAXCOL);
        for ($tb = 0; $tb < $tbCnt; $tb++) {
            echo($tb>0 ? $this->ahpGroupTxt->info['cont'] : "");
            echo "\n<div class='ofl'><table id='grTbl'>";
            // Header row
            echo $this->ahpGroupTxt->tbl['grTblTh'];
            // all branches
            for ($i = $tb*self::MAXCOL; $i < min($bCnt, ($tb+1)*self::MAXCOL); $i++) {
                echo "<th>", $branch[$i], "</th>";
            }
            // Consistency
            echo "<th>CR<sub>max</sub></th>";
            echo "</tr></thead>\n";

            echo "<tbody>";
            // first row: consolidated result
            $csc[0] = $this->colors->hueMap($this->prio[0][$node], $rgbBaseColor, $rgbEndColor);
            echo "\n<tr>";
            echo $this->ahpGroupTxt->tbl['grTblTd1'];
            for ($j = $tb*self::MAXCOL; $j < min($bCnt, ($tb+1)*self::MAXCOL); $j++) {
                $style = $csc[0][$j];
                echo "<td class='ca' style='background-color:$style;'><strong>";
                printf($pctBfmt, 100 * $this->prio[0][$node][$branch[$j]]);
                echo "</strong></td>";
            }
            echo "<td class='ca'><strong>";
            printf($pctBfmt, 100 * $this->cr[0][$node]);
            echo "</strong></td></tr>";
            // uncertainty (+) and (-)
            if ($tflg && isset($this->prioVar['max'])) {
                echo "\n<tr>";
                echo "<td>(+)</td>";
                for ($j = $tb*self::MAXCOL; $j < min($bCnt, ($tb+1)*self::MAXCOL); $j++) {
                    echo "<td class='ca'>";
                    printf($pctBfmt, 100 * ($this->prioVar['max'][$node][$branch[$j]]
                                - $this->prio[0][$node][$branch[$j]]));
                    echo "</td>";
                }
                echo "<td class='ca sm'>n/a</td>";
                echo "\n<tr>";
                echo "<td>(-)</td>";
                for ($j = $tb*self::MAXCOL; $j < min($bCnt, ($tb+1)*self::MAXCOL); $j++) {
                    echo "<td class='ca'>";
                    printf($pctBfmt, 100 * ($this->prio[0][$node][$branch[$j]]
                                - $this->prioVar['min'][$node][$branch[$j]]));
                    echo "</td>";
                }
                echo "<td class='ca sm'>n/a</td>";
            }
            // further rows: participants - only participants with pwc input
            foreach ($this->pwcN[$node] as $i=>$pName) {
                $csc[$i+1] = $this->colors->hueMap($this->prio[$i+1][$node], $rgbBaseColor, $rgbEndColor);
                echo "\n<tr>";
                echo "<td>", $pName,"</td>";
                // nodes = columns
                for ($j = $tb*self::MAXCOL; $j < min($bCnt, ($tb+1)*self::MAXCOL); $j++) {
                    $style = $csc[$i+1][$j];
                    echo "<td class='ca' style='background-color:$style;'>";
                    printf($pctBfmt, 100 * $this->prio[$i+1][$node][$branch[$j]]);
                    echo "</td>";
                }
                echo "<td class='ca sm'>";
                printf($pctBfmt, 100 * $this->cr[$i+1][$node]);
                echo "</td>\n</tr>";
            }
            echo "</tbody>\n</table></div>\n";
        }
        return;
    }


    /*
     * CSV export of group result
     * @return csv text
     */
    public function exportGroupResult($hierMode, $ds, $iScale = 0)
    {
        global $ahpDb;
        global $ahpH;
        $textout = array();
        $line="";
        $fs = ($ds == ',' ? ';' : ',');
        if (isset($_SESSION['ipart'])) {
            $partCnt = count($_SESSION['ipart']);
        } else {
            $partCnt = count($this->part);
        }
        // --- first line tells Excel the character used as field seperator
        $textout[] = "sep=" . $fs . self::NEWL;
        // --- Title
        $textout[] = self::ENCL . "Session Code" . self::ENCL .$fs . self::ENCL
                            . $this->sessionCode . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Project" . self::ENCL .$fs . self::ENCL
                            . $this->pj['project_name'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Description" . self::ENCL .$fs . self::ENCL
                            . $this->pj['project_description'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Author" . self::ENCL . $fs . self::ENCL
                            . $this->pj['project_author'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Created" . self::ENCL . $fs . self::ENCL
                            . substr($this->pj['project_datetime'], 0, 10) . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Evaluation" . self::ENCL . $fs . self::ENCL
                            . ($hierMode ? "Hiearchy" : "Alternatives") . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "No of Participants" . self::ENCL . $fs
                            . $partCnt . self::NEWL;
        $textout[] = self::ENCL . "Scale: " . self::ENCL . $fs . self::ENCL
                            . $this->ahpScale[$iScale][0] . self::ENCL . self::NEWL;

        if ($hierMode) {
            $textout[] = self::NEWL . self::ENCL
                    . "Global weights by nodes and participants"
                    . self::ENCL . self::NEWL;
            $nodes = array_keys($this->prio[0]);
            $nodCnt = count($nodes)-1;
            foreach ($nodes as $node) {
                if ($nodCnt>1 || $node != "pGlb") {
                    $textout[] = self::NEWL . self::ENCL . "Node" . self::ENCL
                    . $fs . self::ENCL . $node . self::ENCL . self::NEWL
                    . self::ENCL . "Weights" . self::ENCL . $fs . self::ENCL
                    . implode(self::ENCL . $fs . self::ENCL, array_keys($this->prio[0][$node]))
                    . self::ENCL . $fs . self::ENCL . "CR" . self::ENCL
                    . self::NEWL;
                    $line = self::ENCL . "Group result" . self::ENCL;
                    // consol. priorities
                    foreach ($this->prio[0][$node] as $k=>$val) {
                        $line .= $fs . number_format($val, ROUND, $ds, "");
                    }
                    $line .= $fs . number_format($this->cr[0][$node], ROUND, $ds, "");
                    $textout[] = $line . self::NEWL;
                    // variations, line = min, line1 = max
                    $textout = array_merge($textout, $this->exportVarH($node, $ds));
                    $i=1;
                    // breakdown by participants
                    $textout[] = self::ENCL . "by participants:" . self::ENCL . self::NEWL;
                    foreach ($this->pwcN[$node] as $part) {
                        $line = self::ENCL . $part . self::ENCL;
                        foreach ($this->prio[$i][$node] as $k=>$val) {
                            $line .= $fs . number_format($val, ROUND, $ds, "");
                        }
                        $line .= $fs . number_format($this->cr[$i++][$node], ROUND, $ds, "");
                        $textout[] = $line . self::NEWL;
                    }
                    // consens for nodes
                    if ($node != 'pTot') {
                        $consens = $this->getConsensus($node, $iScale);
                    } else {
                        $consens = $this->consens;
                    }
                }
                if ($partCnt > 1) {
                    $textout[] = self::ENCL . "AHP Consensus" . self::ENCL . $fs
                . number_format($consens, ROUND, $ds, "") . self::NEWL;
                }
                if ($node != 'pTot') {
                    if (is_array($this->dmCons[$node][0])) {
                        $n = sizeof($this->dmCons[$node][0]);
                    } else {
                        $n = 0;
                    }

                    // Consolidated decision matrix
                    $buf= self::ENCL . "Consolidated Decision Matrix" . self::ENCL . self::NEWL;
                    for ($i=0; $i< $n; $i++) {
                        $buf .= $fs;
                        for ($j=0; $j< $n; $j++) {
                            $buf .= self::ENCL
                        . number_format($this->dmCons[$node][$i][$j], ROUND, $ds, "")
                        . self::ENCL . $fs;
                        }
                        $buf .= self::NEWL;
                    }
                    $textout[] = $buf;
                }
            }
        } else { // Alternatives
            if ($partCnt > 1) {
                $textout[] = self::ENCL . "AHP Consensus"
            . self::ENCL . $fs . number_format($this->consens, ROUND, $ds, "") . self::NEWL;
            }
            $textout[] = self::NEWL;
            // complete alternative table
            $textout = array_merge($textout, $ahpH->exportAltTable($ds));
            // uncertainties
            $textout = array_merge($textout, $this->exportVarA($ds));
            // alternatives by participants
            $textout[] = self::NEWL;
            $textout[] = self::ENCL . "2. Alternatives by participant" . self::ENCL . self::NEWL;
            $textout[] = self::ENCL . "" . self::ENCL . $fs . self::ENCL
        . "Name" . self::ENCL . $fs . self::ENCL
        . implode(self::ENCL . $fs . self::ENCL, $this->pj['project_alt']) . self::ENCL
        . $fs . self::ENCL . "CR max" . self::ENCL . self::NEWL;

            $line = self::ENCL . '' . self::ENCL . $fs . self::ENCL . "Group" . self::ENCL;
            foreach ($this->prio[0]['pTot'] as $alternative=>$val) {
                $line .= $fs . number_format($val, ROUND, $ds, "");
            }
            $line .= $fs . number_format($this->cr[0]['pTot'], ROUND, $ds, "");
            $textout[] = $line . self::NEWL;

            $i = 1;
            foreach ($this->pSel as $part) {
                $line = self::ENCL . '' . self::ENCL . $fs . self::ENCL . $part . self::ENCL;
                foreach ($this->prio[$i]['pTot'] as $alternative=>$val) {
                    $line .= $fs . number_format($val, ROUND, $ds, "");
                }
                $line .= $fs . number_format($this->cr[$i++]['pTot'], ROUND, $ds, "");
                $textout[] = $line . self::NEWL;
            }
            // decision matrix
            $buf= self::NEWL . self::ENCL . "3. Consolidated Decision Matrix"
                . self::ENCL . self::NEWL;
            foreach ($this->dmCons as $lf => $dma) {
                $buf .= self::ENCL . $lf . self::ENCL
            . $fs . self::ENCL . "CR" . self::ENCL
            . $fs . self::ENCL . number_format($this->cr[0][$lf], ROUND, $ds, "")
            . self::ENCL . self::NEWL;
                $n = count($dma);
                for ($i=0; $i<$n; $i++) {
                    $buf .= $fs;
                    for ($j=0; $j<$n; $j++) {
                        $buf .= self::ENCL . number_format($dma[$i][$j], ROUND, $ds, "")
                    . self::ENCL . $fs;
                    }
                    $buf .= self::NEWL;
                }
            }
            $textout[] = $buf;
        }
        return implode($textout);
    }


    /*
     * Adds +/- uncertainty lines to hierarchy
     */
    private function exportVarH($node, $ds)
    {
        if (!empty($this->prioVar)) {
            $fs = ($ds == ',' ? ';' : ',');
            $line[0] = self::ENCL . "(-)" . self::ENCL;
            $line[1] = self::ENCL . "(+)" . self::ENCL;
            foreach ($this->prioVar['min'][$node] as $k=>$val) {
                $line[0] .=
             $fs . number_format(
                 $this->prio[0][$node][$k]- $val,
                 ROUND,
                 $ds,
                 ""
             );
                $line[1] .=
            $fs . number_format(
                $this->prioVar['max'][$node][$k]-$this->prio[0][$node][$k],
                ROUND,
                $ds,
                ""
            );
            }
            $line[0] = $line[0] . self::NEWL;
            $line[1] = $line[1] . self::NEWL;
            return($line);
        }
        return array();
    }


    /*
     * Adds +/- uncertainty lines to alternative table below the total priorities
     */
    private function exportVarA($ds)
    {
        if (!empty($this->prioVar)) {
            $fs = ($ds == ',' ? ';' : ',');
            $line[0] =  self::ENCL . '' . self::ENCL . $fs . self::ENCL . "(-)" . self::ENCL;
            $line[1] = self::ENCL . '' . self::ENCL . $fs . self::ENCL . "(+)" . self::ENCL;
            foreach ($this->prio[0]['pTot'] as $k=>$val) {
                $line[0] .= $fs
            . number_format(
                $this->prio[0]['pTot'][$k]- $this->prioVar['min']['pTot'][$k],
                ROUND,
                $ds,
                ""
            );
                $line[1] .= $fs
            . number_format(
                $this->prioVar['max']['pTot'][$k]- $this->prio[0]['pTot'][$k],
                ROUND,
                $ds,
                ""
            );
            }
            $line[0] = $line[0] . self::NEWL;
            $line[1] = $line[1] . self::NEWL;
            return $line;
        }
        return array();
    }
} // end class ahpGroupSession
