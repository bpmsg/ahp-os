<?php
/*
 * Analytic Hierarchy Process Consensus cluster analysis
 * Contains functions for group decision cluster analysis
 * Extends AHPGroup class
 *
 * $LastChangedDate: 2022-04-05 15:51:03 +0800 (Di, 05 Apr 2022) $
 * $Rev: 197 $
 *
 * @package AHP
 * @author Klaus D. Goepel
 * @copyright 2022 Klaus D. Goepel
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
 * public function construct($priorities)
 * public function betaMatrix($fct="S*",$iScale=0)
 * public function findThreshold()                  
 * public function cluster($thrsh = 0.8)
 * public function calcGroupSim($cluster)
 * public function printColorPalette()
 * public function printThrhTable()
 * public function printBetaMatrix()
 * 
 */

class AhpCluster extends AhpGroup
{
    private const CLMAX = 40;   // Max cluster iterations in cluster()
    private const THMIN = 0.675;// Minimum threshold to search for clusters
    private const FULLMAT = 40; // Max dimension of matrix fully displayed

    private $colors;

    public  $sampleCnt;         // Number of samples
    private $catCnt;            // Number of categories
    private $iScale;
    public  $samples = array(); // Each sample is one participant
    public  $cat = array();     // Categories
    private $distr = array();   // Priority distribution

    public  $bMat = array();    // Similarity Matrix
    private $csc = array();     // color palette for similarity matrix
    private $clMat = array();   // Similarity Matrix after clustering
    private $srt = array();     // Indices for clustered similarity matrix
    private $border = array();  // Cluster borders
    private $bm;                // Minimum of similarity matrix in percent


    /* Initiate class setting
     * $this->sampleCnt (Number of samples = participants)
     * $this->samples Name of samples (keys)
     * $this->distr relative priority distribution normalized to one
     * $this->catCnt Number of categories
     * $this->cat Names of categories
     */
    public function __construct($priorities)
    {
        parent::__construct($priorities['Project']);
        mb_internal_encoding('UTF-8');
        $this->sampleCnt = sizeof($priorities);
        $this->samples = array_keys($priorities);
        foreach ($this->samples as $i => $sample) {
            $this->distr[] = $priorities[$sample];
        }
        $this->catCnt = sizeof($this->distr[0]);
        $this->cat = array_keys($this->distr[0]);
    }


    /*
     * Fill Similarity Matrix bMat
     * S* = AHP consensus
     * S  = Relative Homogeneity (for pTot)
     * Setting
     * $this->fct (S or S*)
     * $this->iScale (int, AHP scale used)
     * $this->bMat Similarity matrix unclustered
     * $this->bm min value of matrix
     * $this->csc Color palette
     * 
     * @uses $this->setColorPalette()
     * @uses $this->calcBeta()
     * @uses $this->calcSim()
     */
    public function betaMatrix($fct="S*",$iScale=0)
    {
        $bmin = 1.;
        $this->fct = $fct;
        $this->iScale = $iScale;
        for ($i = 1; $i < $this->sampleCnt; $i++) {
            for ($j = $i; $j < $this->sampleCnt; $j++) {
                $beta = $this->calcBeta($i, $j);
                $sim = $this->calcSim($beta, 2);
                $bmin = min($bmin, $sim);
                $this->bMat[$i][$j] = $sim;
                $this->bMat[$j][$i] = $sim;
            }
        }
        $this->setColorPalette($bmin);
        return $this->bMat;
    }


    /*
     * Set color palette
     * called by betaMatrix()
     */
    private function setColorPalette($bmin=0, $bmax=100){
        $bm = round($bmin * 100);
        $bm = ($bm > 99 ? 0 : $bm);
        $bs = (int) ($bmax - $bm)/19;
        $this->colors = new AhpColors();
        $this->csc = $this->colors->hueMap(
            range($bm, 100., $bs),
            self::RGBBASE,
            self::RGBEND
        );
        $this->bm = $bm;         
    }
    
    
    /*
     * Correction factor for AHP consensus depends on scale
     * called from calcSim()
     */
    private function getMfromScale($iScale){
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


    /*
     * Fill clustered Similarity Matrix clMat
     * called from cluster()
     */
    private function clMatrix()
    {
        for ($i = 1; $i < $this->sampleCnt; $i++) {
            for ($j = $i; $j < $this->sampleCnt; $j++) {
                $this->clMat[$i][$j]
                    = $this->bMat[$this->srt[$i-1]][$this->srt[$j-1]];
                $this->clMat[$j][$i]
                    = $this->bMat[$this->srt[$j-1]][$this->srt[$i-1]];
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
            // --- One unclustered member
            if( $thf['uc'][$i] == 1 
              && $s < 4
              && $thf['cs'][$i] > self::THMIN)
                $i1m = min($i1m,$i);
            // --- Two cluster or more and < 3 unclustered
            if ($s < $sm
              && $thf['cs'][$i] > self::THMIN
              && $thf['cl'][$i] > 1
              && $thf['uc'][$i] < 3) {
                $sm =  min($sm, $s);
                $si = $i;
            }
        }
        // var_dump($i1m, $si);
        if( ($si === NULL && $i1m >= 0)
            || ($si >0 && $i1m < $si ))
            $si = $i1m;        
        // TODO: print output in calling program
        if ($thf['th'][$si] == NULL) {
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
            $cons = $this->calcGroupSim($brnk['cluster'][0]);
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
    private function cmp($a, $b){
        $asum = 0.;
        $bsum = 0.;
        $asum = array_sum($this->bMat[$a]);
        $bsum = array_sum($this->bMat[$b]);
        if($asum == $bsum)
            return 0;
        return ($asum < $bsum) ? 1 : -1;
    }


    /*
     * Main Cluster Algorithm
     */
    public function cluster($thrsh = 0.8)
    {
        $els = array();                 // clustered samples
        $elu = array();                 // unclustered samples
        $this->srt = array();
        $this->border = array();       
        $brd = array();
        $cMat = $this->bMat;            // temporary matrix for clustering
        $clDone = $this->sampleCnt - 1; // w/o group result priorities
        $elAll = range(1, $clDone);     // to track unclustered elements
        $cl = 0;
        if($thrsh != NULL){ // Threshold NULL returned by findThreshold()
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
                for ($i = 1; $i < $this->sampleCnt; $i++) {
                    for ($j = 1; $j < $this->sampleCnt; $j++) {
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
            $this->srt = range(1,$this->sampleCnt,1);
        }
        // --- fill cluster similarity matrix
        $this->clMatrix();
        $this->border[] = 0;
        foreach($brd as $pb){
            $this->border[] = (1+array_search($pb,$this->srt));
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
    private function getRowCnt(&$cMat, $thrsh = 0.8)
    {
        $rEl = array();
        $rMax = 0;
        $iMax = 0;
        for ($i = 1; $i < $this->sampleCnt; $i++) {
            for ($j=1; $j < $this->sampleCnt; $j++) {
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
     * Calculate AHP Consensus S* or Smilarity S based on Beta entropy
     * Similarity S has to be used for pTot instead of S*
     */
    private function calcSim($beta, $pCnt)
    {
        if ($this->fct == "S") {
            return (1./$beta - 1./$this->catCnt)/(1. - 1./$this->catCnt);
        } else {
            // ToDO: reflect correct scale!
            $m = $this->getMfromScale($this->iScale);
            $cor = $this->ahpShannonCor($this->catCnt, $m, $pCnt);
            return (1/$beta - 1/$cor)/(1 - 1/$cor);
        }
    }


    /*
     * Calculate Beta Hill Number based on Alpha and Gamma Entropy
     * for a pair $k, $l
     */
    private function calcBeta($k, $l)
    {
        return $this->calcGamma($k, $l)/$this->calcAlpha($k, $l);
    }


    /*
     *  Calculate Alpha entropy for a pair $k, $l
     */
    private function calcAlpha($k, $l)
    {
        $pk = 0.;
        $pl = 0.;
        foreach ($this->distr[$k] as $i => $pcat) {
            $pk -= $pcat * log($pcat);
            $pl -= $this->distr[$l][$i] * log($this->distr[$l][$i]);
        }
        return exp(($pk + $pl)/2.);
    }


    /*
     *  Calculate Gamma entropy for a pair $k, $l
     */
    private function calcGamma($k, $l)
    {
        $pm = 0.;
        foreach ($this->distr[$k] as $i => $pcat) {
            $pavg = ($pcat + $this->distr[$l][$i])/2.;
            $pm -= $pavg * log($pavg);
        }
        return exp($pm);
    }


    /*
     * Calculates Shannon beta entropy as gamma - alpha entropy
     * for cluster. $cluster contains the indices of the samples
     * $this->distr[] is the priority distribution
     * $ppal = alpha entropy, $pgam = gamma entropy, $pbet = beta entropy
     * @uses calcSim()
     */
    public function calcGroupSim($cluster)
    {
        $cCnt = $this->catCnt; // number of categories
        $pCnt = sizeof($cluster);
        $ppAvg = array_fill_keys($this->cat, 0.);
        $ppal = 0.;
        // loop through participants
        foreach ($cluster as $k) {
            $ppLn = 0.;
            // loop through criteria
            foreach ($this->distr[$k] as $cat=>$p) {
                $ps += $p;
                $ppLn -= $p * log($p);
                $ppAvg[$cat] += $p;
            }
            $ppal += $ppLn;
        }
        $ppal /= $pCnt;

        $pgam = 0.;
        foreach ($this->cat as $c) {
            $pavg = $ppAvg[$c]/$pCnt;
            $pgam -= $pavg * log($pavg);
        }
        $beta = exp($pgam - $ppal);
        $s = $this->calcSim($beta, $pCnt);
        return $s;
    }


    /* 
     * Print color palette as scale
     */
    public function printColorPalette(){
        $pctBfmt = "<span class='sm res'>%02.0f%%</span>";
        echo "\n<!-- Threshold Table -->";
        echo "\n<div class='ofl'><table id='thrhTbl'>";    
        echo "<thead><tr>";
        echo "<th></th>";
        foreach ($this->csc as $i=>$col){
            echo "<th style='padding:2px;'>", $i+1, "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody><tr>";
        $stp = (100 - (int) $this->bm)/19;
        echo "<th>Scale</th>";        
        foreach ($this->csc as $i=>$col){            
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
        $bdl = "2px";   // --- Border thickness
        $ibd = 0;       // --- Index for border rows
        $jbd = 0;       // --- Index for border columns
        echo "\n<!-- Similarity Matrix -->";
        echo "\n<div class='ofl'><table id='bMatTbl'>";
        // --- Header row for full matrix only
        if($this->sampleCnt < self::FULLMAT){
            echo "<thead><tr><th style='padding:$pad;'>0</th>";
            for ($i=1; $i< $this->sampleCnt; $i++)
                echo "<th style='padding:2px;'>",$this->srt[$i-1], "</th>";
            echo "</tr></thead>\n";
        }
        // --- Table body
        echo "<tbody>\n";
        $stp = (100 - (int) $this->bm)/19;
        // --- Rows
        for ($i=1; $i < $this->sampleCnt; $i++) {
            echo "<tr style='line-height:$lh;'>";
            // --- Columns
            if($this->sampleCnt < self::FULLMAT)
                echo "<td class='sm' style='padding:$pad;'>",$this->srt[$i-1],"</td>";
            
            for ($j=1; $j < $this->sampleCnt; $j++) {
                $style ="";
                // --- Cluster borders
                if($i-1 == $this->border[$ibd] 
                      && (($j <= $this->border[$ibd+1] && $j  > $this->border[$ibd])
                      || ($j >  $this->border[$ibd-1] && $j <= $this->border[$ibd])))
                    $style = "border-top:solid $bdl green;";
                elseif ($i >$this->border[$ibd] && $j>$this->border[$ibd])
                    $ibd++;                    
                if(($j-1 == $this->border[$jbd] && $i <= $this->border[$jbd+1] 
                        && $i  > $this->border[$jbd])
                        || ($j-1 == $this->border[$ibd] && $i >  $this->border[$ibd-1] 
                        && $i  <= $this->border[$ibd]))
                    $style .= "border-left:solid $bdl green;";
                elseif ($i > $this->border[$jbd+1] && $j > $this->border[$jbd])
                    $jbd++;

                $val = 100 * $this->clMat[$i][$j];
                $jv = (int) ($val - $this->bm) / $stp;
                $style .= "background-color:" . $this->csc[$jv] . ";padding:$pad;";
                echo "<td class='ca sm' style='$style'>";

                if($this->sampleCnt < self::FULLMAT)
                    printf($pctBfmt, $val);
                else
                    echo "<span class='sm res'> </span>";
                echo "</td>";
            }
            echo "</tr>\n";
        }
        echo "</tbody>\n</table></div>\n";
    }
}
