<?php
/*
 * Analytic Hierarchy Process Consensus cluster analysis
 * Contains functions for group decision cluster analysis
 * Extends AHPGroup class
 *
 * $LastChangedDate: 2022-04-30 12:26:26 +0800 (Sa, 30 Apr 2022) $
 * $Rev: 207 $
 *
 * @package AHP
 * @author Klaus D. Goepel
 * @copyright 2022 Klaus D. Goepel
 * @uses AhpGroup.php
 * @uses colorClass.php
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
 * Public Methods:
 *
 * public function construct($priorities, $fct="S",$iScale=0)
 * public function findThreshold()
 * public function cluster($thrsh = 0.8)
 * public function calcGroupSim($cluster)
 * public function printColorPalette()
 * public function printThrhTable()
 * public function printBetaMatrix()
 *
 *
 * Method/Variable from AhpGroup:
 * $ahpScale
 * ahpShannonCor()
 * consensusWording()
 *
 */

class AhpCluster extends AhpGroup
{
    private const CLMAX = 40;     // Max cluster iterations in cluster()
    private const THMIN = 0.6875; // Minimum threshold to search for clusters
    private const FULLMAT = 40;   // Max dimension of matrix fully displayed

    private $colors;

    private $catCnt;            // Number of categories
    private $iScale;            // AHP scale integer (used for S*)
    public $sampleCnt;          // Number of samples
    public $samples = array();  // Each sample is one participant
    public $cat = array();      // Categories

    private $distr = array();   // Priority distributions normalized
    private $bMat = array();    // Similarity Matrix
    public $bmin;               // Minimum of bMat
    public $bmax;               // Max of bMat
    private $clMat = array();   // Similarity Matrix after clustering

    private $csc = array();     // color palette for similarity matrix
    private $srt = array();     // Indices for clustered similarity matrix
    private $border = array();  // Cluster borders
    private $bm;                // Minimum of similarity matrix in percent


    /*
     * Correction factor for AHP consensus depends on scale
     * called from calcSim() - specific for AHP-OS
     */
    private function getMfromScale(int $iScale)
    {
        // $m depends on m and n for adaptive, adaptive-bal scales!
        switch ($iScale) {
            case 6: // adaptive
            case 7: // adaptive-bal
                $m = ($this->catCnt - 1) * 9;
                break;
            default:
                $m = $this->ahpScale[$iScale][1];
        }
        return $m;
    }


    /* Initiate class
     * $this->sampleCnt (Number of samples = participants)
     * $this->samples Name of samples (participants)
     * $this->distr relative priority distribution normalized to one
     * $this->catCnt Number of categories
     * $this->cat Names of categories
     * $this->fct (S or S*) rel. homogeneity or AHP consensus
     * $this->iScale (int, AHP scale used, 0 AHP 1-9)
     *
     * @para $priorities: array of samples (participants)
     * with categories as key and priorities as values
     * @para $fct: "S" for relative homogeneity, S* for AHP consensus
     * @para int $iScale: 0 = AHP 1-9 only used for S*
     */
    public function __construct($priorities, $fct="S", $iScale=0)
    {
        //parent::__construct($priorities);
        array_shift($priorities); // group result [0] disregarded
        mb_internal_encoding('UTF-8');
        $this->fct = $fct;
        $this->iScale = $iScale;
        $this->sampleCnt = sizeof($priorities);
        $this->samples = array_keys($priorities);
        foreach ($this->samples as $i => $sample) {
            $ps = round(array_sum($priorities[$sample]), 5);
            if ($ps != 1. && $ps != 0.) {
                // normalize
                foreach( $priorities[$sample] as $k => $c)
                    $priorities[$sample][$k] /= $ps;
            } 
            $this->distr[] = $priorities[$sample];
        }
        $this->catCnt = sizeof($this->distr[0]);
        $this->cat = array_keys($this->distr[0]);
        $this->betaMatrix();
        $this->srt = range(0, $this->sampleCnt-1, 1);
        $this->clMatrix();
    }


    /*
     * Fill Similarity Matrix bMat
     * S* = AHP consensus
     * S  = Relative Homogeneity (for pTot)
     * Setting
     * $this->bMat Similarity matrix unclustered
     * $this->bm min value of matrix
     * $this->csc Color palette
     *
     * @uses $this->setColorPalette()
     * @uses $this->calcGroupSim()
     * @uses $this->calcSim()
     */
    private function betaMatrix()
    {
        $this->bmin = 1.;
        $this->bmax = 0.;
        for ($i = 0; $i < $this->sampleCnt; $i++) {
            for ($j = $i; $j < $this->sampleCnt; $j++) {
                $res = $this->calcGroupSim(array($i,$j));
                $sim = $res['sim'];
                $this->bmin = min($this->bmin, $sim);
                if($i <> $j)
                    $this->bmax = max($this->bmax, $sim);
                $this->bMat[$i][$j] = $sim;
                $this->bMat[$j][$i] = $sim;
            }
        }
        $this->setColorPalette();
        return;
    }


    /*
     * Aggregation function
     * Calculate average and sd of categories for selected samples
     */
    public function calcAvgDistr(array $smpSel)
    {
        $n = sizeof($smpSel);
        $sd = array();
        $tmpx = array_fill(0, $this->catCnt, 0.);
        $tmpx = array_combine($this->cat, $tmpx);
        $tmpxx = $tmpx;
        foreach ($smpSel as $smp) {
            foreach ($this->distr[$smp] as $cat=>$val) {
                $tmpx[$cat]  += $val;
                $tmpxx[$cat] += $val * $val;
            }
        }
        if ($n > 1) {
            foreach ($tmpxx as $cat => $sxx) {
                $sd[$cat] = sqrt(($sxx - $tmpx[$cat] * $tmpx[$cat]/$n)/($n-1));
                $tmpx[$cat] /= $n;
            }
        }
        return array('avg' => $tmpx,
                     'sd'  => $sd);
    }


    /*
     * Set color palette
     * called by betaMatrix()
     */
    private function setColorPalette()
    {
        $bmn = floor(round(100 * $this->bmin, -1) - 5);
        $this->bm = ($bmn > 90) ? 70 : $bmn;
        $bs = (int) (100 - $this->bm)/19;
        $this->colors = new AhpColors();
        $this->csc = $this->colors->hueMap(
            range($this->bm, 100, $bs),
            self::RGBBASE,
            self::RGBEND
        );
        /* Grayscale
        $this->csc = array(
            "#F8F8F8", "#F5F5F5","#F0F0F0","#E0E0E0","#E8E8E8", 
            "#D8D8D8","#D3D3D3","#D0D0D0","#C8C8C8","#C0C0C0",
            "#BEBEBE", "#B8B8B8","#B0B0B0","#A9A9A9","#A8A8A8", 
            "#A0A0A0","#989898","#909090","#888888","#808080");
            */
    }


    /*
     * Fill clustered Similarity Matrix clMat
     * clMat is the clustered and sorted similarity matrix
     * called from cluster()
     */
    private function clMatrix()
    {
        for ($i = 0; $i < $this->sampleCnt; $i++) {
            for ($j = $i; $j < $this->sampleCnt; $j++) {
                $this->clMat[$i][$j]
                    = $this->bMat[$this->srt[$i]][$this->srt[$j]];
                $this->clMat[$j][$i]
                    = $this->bMat[$this->srt[$j]][$this->srt[$i]];
            }
        }
    }


    /*
     * Find optimal threshold value:
     * Minimum number of clusters and unclustered AND
     * Consensus of first cluster > 67.5% AND
     * At least two cluster and less than three unclustered
     *
     * @return threshold value if successful or NULL if not
     */
    public function findThreshold()
    {
        $thf = $this->calcThreshold();
        $sm =  999; // minimum sum of clustered + unclustered
        $i1m = 999; // minimum index for 1 unclustered
        foreach ($thf['th'] as $i => $th) { 
            // --- Sum of clustered and unclustered samples
            $s = $thf['cl'][$i] + $thf['uc'][$i];
            // Only threshold with S > Smin
            if ($thf['cs'][$i] > self::THMIN){
                // --- One unclustered member and 1 cluster
                if ($thf['uc'][$i] <= 2 && $thf['cl'][$i] == 1) {
                    $i1m = min($i1m, $i);
                }
                // --- Two cluster or more and 1 or 2 unclustered
                if ($s < $sm
                  && $thf['cl'][$i] > 1
                  && $thf['uc'][$i] < 3) {
                    $sm =  min($sm, $s);
                    $si = $i;
                }
            }
        }
        // var_dump($i1m, $si);
        if (($si === null && $i1m >= 0)
            || ($si >0 && $i1m < $si)) {
            $si = $i1m;
        }
        // TODO: print output in calling program
        if ($thf['th'][$si] == null) {
            // --- no solution
            echo
            "<p class='err'>Clustering is not possible, 
            try manual threshold input.</p>";
        } else {
            echo "<p class='msg'>Consensus threshold for clustering is 
            determined as <span class='res'>",$thf['th'][$si];
            echo "</span></p>";
        }
        return $thf['th'][$si];
    }


    /*
     * Calculate number of cluster and unclustered as function of threshold
     * called from findThreshold() and printThrhTable()
     */
    private function calcThreshold()
    {
        $thf = array();
        for ($th = 0.975; $th > self::THMIN; $th -= 0.025) {
            $brnk = $this->cluster($th);
            $cons = $this->calcGroupSim($brnk['cluster'][0])['sim'];
            $clCnt = sizeof($brnk['cluster'])-1;
            $ucCnt = sizeof($brnk['unclust']);
            $thf['th'][] = $th;
            $thf['cl'][] = $clCnt;
            $thf['uc'][] = $ucCnt;
            $thf['cs'][] = $cons;
        }
        return $thf;
    }


    /*
     *  Callback comparison function for usort in cluster()
     */
    private function cmp($a, $b)
    {
        $asum = array_sum($this->bMat[$a]);
        $bsum = array_sum($this->bMat[$b]);
        if ($asum == $bsum) {
            return 0;
        }
        return ($asum < $bsum) ? 1 : -1;
    }


    /*
     * Main Cluster Algorithm
     * @para thrsh can be NULL
     */
    public function cluster($thrsh = 0.8)
    {
        $els = array();                 // clustered samples
        $elu = array();                 // unclustered samples
        $this->srt = array();           // element indices of clustered matrix
        $this->border = array();        // absolute border indices
        $brd = array();                 // sample border indices
        $cMat = $this->bMat;            // temporary matrix for clustering
        $clDone = $this->sampleCnt;     // w/o group result priorities
        $elAll = range(0, $clDone-1);   // to track unclustered elements
        $cl = 0;                        // number of cluster
        if ($thrsh != null) { // Threshold NULL returned by findThreshold()
            do {
                $els[$cl] = $this->getRowCnt($cMat, $thrsh);
                $clCnt = sizeof($els[$cl]);
                if ($clCnt < 2) {
                    // --- unclustered
                    $elu = array_merge($elu, $elAll);
                    $this->srt = array_merge($this->srt, $elAll);
                    break;
                }
                // --- sort $els cluster from high to low similarity
                usort($els[$cl], array($this, 'cmp'));

                $this->srt = array_merge($this->srt, $els[$cl]);
                $elAll = array_diff($elAll, $els[$cl]);
                // --- remove clustered elements from matrix
                for ($i = 0; $i < $this->sampleCnt; $i++) {
                    for ($j = 0; $j < $this->sampleCnt; $j++) {
                        if (in_array($j, $els[$cl]) && $i != $j) {
                            $cMat[$i][$j] = 0.;
                            $cMat[$j][$i] = 0.;
                        }
                    }
                }
                $brd[] = end($els[$cl]);
            } while ($cl++ < self::CLMAX);
        } else {
            // unsorted/unclustered matrix
            $this->srt = range(0, $this->sampleCnt-1, 1);
        }
        // --- fill cluster similarity matrix and absolute border indices
        $this->clMatrix();
        $this->border[] = -1;
        foreach ($brd as $pb) {
            $this->border[] = (array_search($pb, $this->srt));
        }
        $this->border[] = $clDone;
        return array(
            'cluster' => $els,
            'unclust' => $elu
        );
    }


    /*
     * Find row in similarity matrix with highest number of elements
     * having S or S* > threshold
     */
    private function getRowCnt(array &$cMat, float $thrsh = 0.8)
    {
        $rEl = array();
        $rMax = 0;
        $iMax = 0;
        for ($i = 0; $i < $this->sampleCnt; $i++) {
            $rEl[$i] = array();
            for ($j = 0; $j < $this->sampleCnt; $j++) {
                if ($cMat[$i][$j] > $thrsh) {
                    $rEl[$i][] = $j;
                }
            }
            $rCnt = sizeof($rEl[$i]);
            if ($rCnt > $rMax) {
                $rMax = $rCnt;
                $iMax = $i;
            } else {
                unset($rEl[$i]);
            }
        }
        return($rEl[$iMax]);
    }


    /*
     * Calculate AHP Consensus S* or Smilarity S or Horn index H
     * based on Beta diversity
     * Similarity S has to be used for pTot instead of S*
     */
    private function calcSim(float $beta, int $p)
    {
        if ($this->fct == "S") {
            return (1./$beta - 1./$this->catCnt)/(1. - 1./$this->catCnt);
        } elseif ($this->fct == "H") {
            return log(2/$beta,2);
        } else {
            $m = $this->getMfromScale($this->iScale);
            $cor = $this->ahpShannonCor($this->catCnt, $m, $p);
            return (1/$beta - 1/$cor)/(1 - 1/$cor);
        }
    }


    /*
     * Calculates Shannon beta entropy as gamma - alpha entropy
     * for cluster. $cluster contains the indices of the samples
     * $this->distr[] is the priority distribution
     * $ppal = alpha entropy,
     * $pgam = gamma entropy,
     * $pbet = beta entropy
     *
     * @uses calcSim()
     * @return alpha, beta, gamma entropy and
     * rel. homogeneity or consensus
     * null when cluster has 1 element only
     */
    public function calcGroupSim(array $cluster)
    {
        $cCnt = $this->catCnt;      // number of categories
        $pCnt = sizeof($cluster);   // number of samples
        if($pCnt <2)
            return null;
        $ppAvg = array_fill_keys($this->cat, 0.);
        $ppal = 0.;
        // loop through participants
        foreach ($cluster as $k) {
            $ppLn = 0.;
            // loop through criteria
            foreach ($this->distr[$k] as $cat=>$p) {
                $ps += $p;
                if ($p > 0.) {
                    $ppLn -= $p * log($p);
                }
                $ppAvg[$cat] += $p;
            }
            $ppal += $ppLn;
        }
        $ppal /= $pCnt;
        $pgam = 0.;
        foreach ($this->cat as $c) {
            $pavg = $ppAvg[$c]/$pCnt;
            if ($pavg > 0.) {
                $pgam -= $pavg * log($pavg);
            }
        }
        $pbet = $pgam - $ppal;
        $beta = exp($pbet);
        $gam = exp($pgam);
        $s = $this->calcSim($beta, $pCnt);
        return array('alpha'=> $ppal,
                     'beta' => $pbet,
                     'gamma'=> $pgam,
                     'sim'  => $s
                    );
    }


    /*
     * Print color palette as scale
     */
    public function printColorPalette()
    {
        $pctBfmt = "<span class='sm res'>%02.0f%%</span>";
        echo "\n<!-- Threshold Table -->";
        echo "\n<div class='ofl'>
        <table id='thrhTbl' style='border-collapse:collapse;'>";
        // -no header
        echo "<tbody><tr>";
        $stp = (100 - (int) $this->bm)/19;
        echo "<th>Scale</th>";
        foreach ($this->csc as $i=>$col) {
            $style = $this->csc[$i];
            echo "<td class='ca sm' 
                style='padding:2px;background-color:$style;'>";
            printf($pctBfmt, round($this->bm + $i * $stp));
            echo "</td>";
        }
        echo "</tr></tbody>";

        echo "</tbody>\n</table></div>\n";
    }


    /*
     * Display Threshold Table
     */
    public function printThrhTable()
    {
        $thf = $this->calcThreshold();
        echo "\n<!-- Threshold Table -->";
        echo "\n<div class='ofl'><table id='thrhTbl'>";
        // --- Table Header
        echo "<thead><tr>
            <th class='ra' style='width:15%;'>Threshold</th>";
        foreach ($thf['th'] as $th) {
            echo "<th>",$th,"</th>";
        }
        echo "</tr></thead><tbody>\n";
        // --- Table body
        echo "<tr><th class='ra' >Cluster</th>";
        foreach ($thf['cl'] as $cl) {
            echo "<td class='res ca'>",$cl,"</td>";
        }
        echo "</tr>\n<tr>
            <th class='ra' style='white-space:nowrap;'>Unclustered</th>";
        foreach ($thf['uc'] as $uc) {
            echo "<td class='res ca'   >",$uc,"</td>";
        }
        echo "</tbody>\n</table></div>\n";
    }


    /*
     *  Display similarity matrix
     *  Note: indices i,j run from 1 ... sampleCnt
     */
    public function printBetaMatrix()
    {
        $pctBfmt = "<span class='sm res'>%02.0f%%</span>";
        $lh =  ($this->sampleCnt > self::FULLMAT ? "2px" : "14px");
        $pad = ($this->sampleCnt > self::FULLMAT ? "4px" : "2px");
        $bdl = "2px";   // --- Cluster border thickness
        $ibd = 0;       // --- Index for border rows
        $jbd = 0;       // --- Index for border columns
        echo "\n<!-- Similarity Matrix -->";
        echo "\n<div class='ofl'>
            <table id='bMatTbl' style='border-collapse:collapse;'>";
        // --- Header row for full matrix only
        if ($this->sampleCnt < self::FULLMAT) {
            echo "<thead><tr><th style='padding:$pad;'>0</th>";
            for ($i=0; $i < $this->sampleCnt; $i++) {
                echo "<th style='padding:2px;'>",$this->srt[$i]+1, "</th>";
            }
            echo "</tr></thead>\n";
        }
        // --- Table body
        echo "<tbody>\n";
        $stp = (100 - (int) $this->bm)/19;
        // --- Rows
        for ($i=0; $i < $this->sampleCnt; $i++) {
            echo "<tr class='nb' style='line-height:$lh;'>";
            // --- Columns
            if ($this->sampleCnt < self::FULLMAT) {
                echo "<td class='sm' style='padding:$pad;'>",$this->srt[$i]+1,"</td>";
            }

            for ($j=0; $j < $this->sampleCnt; $j++) {
                $style ="";
                // --- Cluster borders
                if ($i-1 == $this->border[$ibd]
                      && (($j <= $this->border[$ibd+1] && $j  > $this->border[$ibd])
                      || ($j >  $this->border[$ibd-1] && $j <= $this->border[$ibd]))) {
                    $style = "border-top:solid $bdl green;";
                } elseif ($i >$this->border[$ibd] && $j>$this->border[$ibd]) {
                    $ibd++;
                }
                if (($j-1 == $this->border[$jbd] && $i <= $this->border[$jbd+1]
                        && $i  > $this->border[$jbd])
                        || ($j-1 == $this->border[$ibd] && $i >  $this->border[$ibd-1]
                        && $i  <= $this->border[$ibd])) {
                    $style .= "border-left:solid $bdl green;";
                } elseif ($i > $this->border[$jbd+1] && $j > $this->border[$jbd]) {
                    $jbd++;
                }

                $val = 100 * $this->clMat[$i][$j];
                $jv = (int) ($val - $this->bm) / $stp;
                $style .= "background-color:" . $this->csc[$jv] . ";padding:$pad;";
                echo "<td class='ca sm' style='$style;'>";

                if ($this->sampleCnt < self::FULLMAT) {
                    printf($pctBfmt, $val);
                } else {
                    echo "<span class='sm res'> </span>";
                }
                echo "</td>";
            }
            echo "</tr>\n";
        }
        echo "</tbody>\n</table></div>\n";
    }
}
