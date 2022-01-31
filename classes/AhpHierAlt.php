<?php
/**
* Analytic Hierarchy Process Alternative Evaluation class 2014-01-06
* extends ahpHierClass
*
* $LastChangedDate$
* $Rev$
*
*
* @package AHP-OS
* @author Klaus D. Goepel
* @copyright 2014 Klaus D. Goepel
*
* @version 2014-01-27
* @version 2017-10-05 last version w/o SVN
*
* @uses array $_SESSION['prioAlt']) priorities for alternatives
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
*
* public function calcPrioTotal($k, $pGlb)
* public function setPrioTot()
* public function setPrioAltDef()
* public function getAlternatives()
*
* public function robTest1()
* public function robTest2()
*
* public function displayAlternativesTable($ahp = true)
* public function exportAltTable($ds)
* public function exportRobTest($ds, $rt1, $rt2)
* 
* private function getPostParameter()
*
* static function abscmp($a,$b)
*
* displayAlternativesMenu($loggedIn)
*/
class AhpHierAlt extends AhpHier
{

/** Properties */
public $altNum = 2;
public $alt = array();
public $prioAlt = array(); // priority of alternatives, index = criteria
public $prioTot = array(); // total priority of alternatives
public $pwcaDone;
//public $pwcaDoneFlg = false; is defined in ahpHierClass
public $wpm = false; // weighted product method


/** Methods */

public function __construct(){
	parent::__construct();
	mb_internal_encoding('UTF-8');	
//	$this->ahpHierTxt = new $class;
	return;
}


/* Calculates weight for alternative k for wsm or wpm
 * @param int $k index of alternative
 * @param float array $pGlb
 * @uses flatarray
 * @return float $res
 * is called from ahpHierClass!
 */
public function calcPrioTotal($k, $pGlb){
	$res = ($this->wpm ? 1. : 0.);
	if(is_array($this->flatarray)){
		foreach ($this->flatarray as $el){
			// Weighted Product Method
			if($this->wpm) {
				if(isset($this->prioAlt[$el][$k]))
					$res *= pow($this->prioAlt[$el][$k], $pGlb[$el]);
				else // no judgment
					$res *= pow(1./$this->altNum,$pGlb[$el]);
			} elseif (isset($this->prioAlt[$el][$k]))
				// Weighted Sum Method
				$res += $pGlb[$el] * $this->prioAlt[$el][$k];
		}			
	}
	return $res;
}


/* Calculates total weights for all alternatives
 * sets prioTot
 * @return array float $prioTot 
 * @used by ahpGroupClass
 */
public function setPrioTot(){
	for ($i=0; $i < $this->altNum; $i++){
		$this->prioTot[$i] = $this->calcPrioTotal( $i, $this->pGlb);
	}
	return $this->prioTot;
}


/* @called by ahp-alt.php */
public function getAlternatives(){
	return $this->alt;
}


/* set pairwise comparison input and scale from $_POST parameters 
 * @return array pwc (A, Intensity, scale) or int 1 (start pwc), int 2 (error in parameters)
 */
private function getPostParameter() {
	$para=array();
	$args = array(
		'prioAlt' => array('filter' => FILTER_VALIDATE_FLOAT,
		'flags' => FILTER_REQUIRE_ARRAY ),
		);
	$para = filter_input_array(INPUT_POST, $args);
	if($para == NULL ){
		// variables not set
		return 1;
	} else {
		// ok - 
		$this->prioAlt = $para['prioAlt'];
		return 0;
	}
}


/* checks for completion of pairwise comparisons for alternatives
 * @uses $_SESSION['prioAlt']
 * @return array $pwcA contains leafs (criteria) for which alternative evaluation was done
 */
private function pwcADone(){
	if(isset($_SESSION['prioAlt']) && is_array($_SESSION['prioAlt']))
		$pwcA = array_keys($_SESSION['prioAlt']);
	else
		$pwcA = array();
	return $pwcA;
}


/* ALTERNATIVE HTML TABLE - displays html Table with Alternatives
 * called from ahp-alt.php
 * @return void
 */
public function displayAlternativesTable($ahp = true){
	$cellFmt =  "style='background-color:White;'";
	$tblColWdth = round(70/($this->level),0); //width in percent for level columns
	$pwcA = $this->pwcADone();
	$pwcCnt = 0;
	$errMsg = "";
	$pwcTot = count($this->pGlb);
	echo "<form method='POST'>";
	echo "\n<!-- DISPLAY HIERARCHY -->\n";
	echo "<div class='ofl'><table id='aTbl'>";
	echo $this->ahpHierTxt->tbl['aTblCp'];
	// --- Header
	echo "<tr><thead>";
	echo $this->ahpHierTxt->tbl['aTblTh'];
	for ($i=0; $i<$this->altNum; $i++)
		echo "<th>". $this->alt[$i] . "</th>";
	echo "</thead><tbody>";
	foreach ($this->flatarray as $node=>$el){
		$nda = explode(self::FLAT_DELIM,$node);
		$ktxt[] = $nda[count($nda)-2];
	}
	$r_sp = array_values(array_count_values($ktxt));
	$nda = array_keys(array_count_values($ktxt));
	// --- Rows
	$row=0; $l=0; // index fur r_sp array
	$k=0;
	foreach ($this->flatarray as $node=>$el){
		if( in_array( $el, $pwcA) ){
			$inpfmt = "<td class='resbox done ca sm'>%01.3f</td>";
			$pwcCnt += 1;
		} else {
			$inpfmt = "<td class='resbox ca sm'>%01.3f</td>";
		}
		echo "\n<tr>";
		echo "<td class='ca var' >", ++$row, ". </td>";
		if($k == 0){
			$k = $r_sp[$l];
			echo "<td class='hier sm' rowspan='", $k, "'>",$nda[$l++], "</td>";
		}
		$k--; 
		echo "<td class='hier var'>", $el, "</td>";
		echo "<td class='ca sm res'>", round(100 *$this->pGlb[$el],1), "% </td>";
		echo "\n<td class='ca'>";
		// AHP button
		if( $ahp)
			if(isset($_SESSION['pwc']) && in_array($el,array_keys($_SESSION['pwc'])))
				echo "<input class='btng' type='submit' value='AHP' lenght='3' name='AHP[",$el, "]' />\n";
			else
				echo "<input class='btnr' type='submit' value='AHP' lenght='3' name='AHP[",$el, "]' />\n";

		// --- Alternatives
		$altSum = 0.;
		for ($j=0; $j<$this->altNum; $j++){
			if(isset($this->prioAlt[$el][$j])){
				$val = $this->prioAlt[$el][$j];
				$altSum += $val;
			}
			printf( $inpfmt,round($val,3));
		}
		echo "</tr>";
		if ( abs($altSum-1) > self::CALC_TOL ){
			$errmsg = sprintf($this->ahpHierTxt->err['unkn'],$el);
		}
	} // next row

	echo "<tr><td colspan='5' align='right' >" , $this->ahpHierTxt->tbl['aTblTd1'], "</td>";
	$inpfmt = ($pwcCnt < $pwcTot ? "class='ca resbox sm'" : "class='ca resbox sm done' style='font-weight:bold;'");
	for($j=0; $j<$this->altNum; $j++){
		$prioTot = $this->calcPrioTotal( $j, $this->pGlb, 0);
		echo "<td $inpfmt >", round($prioTot,3), "</td>";
	}
	echo "</tr>";

	// --- Show completion of comparisons
	echo "<tr><td colspan='" . (5+$this->altNum) . "'>";
	if($pwcCnt < $pwcTot){
		$this->pwcaDoneFlg = false;
		 printf($this->ahpHierTxt->msg['aPwcCmplN'],$pwcCnt,$pwcTot);
	} elseif ($errMsg != "")
		echo "<span class='err sm'> $errMsg</span>";
	else {
		$this->pwcaDoneFlg = true;
		echo $this->ahpHierTxt->msg['aPwcCmplA'];
	}
	echo "</td></tr>";
	echo "</tbody></table></div>";
	echo "</form>";
	return;
}


/* set default values for alternative judgments 
 * called from ahp-alt.php
 */
public function setPrioAltDef(){
	reset($this->flatarray);
	foreach ($this->flatarray as $el){
		$this->prioAlt[$el] = array_fill(0,$this->altNum,(1./$this->altNum));
	}
	return;
}


/* compare function for absolute sort */
static function abscmp($a,$b){
	if(abs($a) <= abs($b))
		return -1;
	else
		return +1;
}


/* Calculate robustness of alternatives for variation of criteria
 * @para $this->wpm = true weighted product model, weighted sum otherwise
 * @return $rb1[0]: relative-top $rb1[1] relative-any
 * $rb1[2]: complete table
 */
public function robTest1(){
	$rb1 = array();		// result
	$alt = array_values($this->prioTot);
	arsort($alt);
	$top = key($alt);
	// table header	
	$rb1[2][0][] = "Criteria";
	foreach($this->leafs as $crit)
		$rb1[2][0][] = $crit;
	$rb1[2][1][] = "weights";
	foreach($this->leafs as $crit)
		$rb1[2][1][] = $this->pGlb[$crit];
	$k = 2;
	// compare each alternative with all others
	for($i=0; $i< $this->altNum; $i++){
		for($j=$i+1; $j< $this->altNum; $j++){
			$rb1[2][$k][] = "A" . ($i+1) . ($i == $top ? "*" : "") . " - A" . ($j+1) . ($j == $top ? "*" : "");
			foreach($this->leafs as $crit){
				$key = ($i) . "~" . ($j) . "~" . $crit;
				if($this->wpm){
					// --- Weighted Product Method ---
					$t = 1.;
					foreach($this->leafs as $crt){
						if($this->prioAlt[$crt][$j] > 0)
							$t *= pow($this->prioAlt[$crt][$i]/$this->prioAlt[$crt][$j],$this->pGlb[$crt]);	
					}
					$t =log($t);
					if($this->prioAlt[$crit][$j] > 0.) // avoid divide by zero
						$t1 = $this->prioAlt[$crit][$i]/$this->prioAlt[$crit][$j];
					$t1 = ( $t1 == 1 ? 0 : log($t1)); // avoid divide by zero
				} else{
					// --- Weighted Sum Method (default) ---
					$t = $this->prioTot[$i] - $this->prioTot[$j];
					$t1 = $this->prioAlt[$crit][$i] - $this->prioAlt[$crit][$j];
				}
				if( round($t1,4) != 0 && abs($t/$t1) < $this->pGlb[$crit]){
					// RT and RA (divided by $this->pGlb[$crit])
					$rb1[1][$key] = $t/$t1/$this->pGlb[$crit];
					if($i == $top || $j == $top)
						$rb1[0][$key] = $t/$t1/$this->pGlb[$crit]; //percent
					$rb1[2][$k][]= $t/$t1;    //absolute
				} else {
					$rb1[2][$k][]= "n/a";
				}
			}
			$k++;
		}
	}
	// table footer
	$rb1[2][$k][] = $this->ahpHierTxt->wrd['alt'];
	for($i=0; $i< $this->altNum; $i++){
		$rb1[2][$k][] = "A" . ($i+1) . ": " . $this->alt[$i];
	}
	if(!empty($rb1[0]))
		uasort($rb1[0], array($this, "abscmp")); // only the smallest value
	if(!empty($rb1[1]))
		uasort($rb1[1], array($this, "abscmp"));
	return $rb1;
}


/* Calculate robustness of alternatives
 * for variation of alternative assessment
 * @para $this->wpm = true weighted product model, weighted sum otherwise
 * rbTab[0] smallest relative rbTab[2] complete table absolute
 */
public function robTest2(){
	$rbTab = array(null,null,null);
	// table header
	$rbTab[2][0][] = "Ai";
	foreach($this->leafs as $crit)
		$rbTab[2][0][] = $crit;
	$rbTab[2][0][] = "Ak";
	$jr = 1;
	// compare for all alternatives
	for($i=0; $i< $this->altNum; $i++){
		for($k=0; $k< $this->altNum; $k++){
			if($i <> $k){
				$rbTab[2][$jr][] = "A" . ($i+1);
				foreach($this->leafs as $crit){
					$key = $i . "~" . $k . "~" . $crit;
					if($this->wpm){
					// --- Weighted Product Model ---
						$t1 =  (1 - pow($this->prioTot[$k]/$this->prioTot[$i],1./$this->pGlb[$crit]));
						if(abs($t1)< 1 ){
							$rbTab[0][$key] = $t1;  // percent
							$rbTab[2][$jr][] = $t1 * $this->prioAlt[$crit][$i] ; // absolute
						} else
							$rbTab[2][$jr][] = "n/a";
					} else {
					// --- Weighted Sum Model ---
						$t1 = $this->prioTot[$i] - $this->prioTot[$k];
						if( round($t1,6) != 0){
							$t = $this->pGlb[$crit] * (1.+ $this->prioAlt[$crit][$k] - $this->prioAlt[$crit][$i]);
							$t = 1/(1 + $t/$t1);
							if(abs($t) < $this->prioAlt[$crit][$i]){
								$rbTab[0][$key] = $t/$this->prioAlt[$crit][$i]; // percent
								$rbTab[2][$jr][] = $t; // absolute
							} else
								$rbTab[2][$jr][] = "n/a";
						} else
							$rbTab[2][$jr][] = "n/a";
					}
				}
				$rbTab[2][$jr][] = "A" . ($k+1);
				$jr++;
			}
		}
	}
	if(is_array($rbTab[0]))
		uasort($rbTab[0], array($this, "abscmp")); // only the smallest value
	return $rbTab;
}


/*
 * csv export of complete alternative table 
 * @return array $textout, each element one line
 */
public function exportAltTable($ds){
	$fs = ($ds == ',' ? ';' : ',');
	$altnum = $this->altNum;
	$textout[] = self::ENCL . "1. Alternatives with local weights " 
			. ( $this->wpm ? "(Weighted product method) " : "(Weighted sum method) " ) . self::ENCL . self::NEWL;
	$textout[] = self::ENCL . 'Crit/Alt' . self::ENCL . $fs . self::ENCL . "pGlb" . self::ENCL . $fs . self::ENCL .
		implode( self::ENCL . $fs . self::ENCL, $this->alt) . self::ENCL . self::NEWL ;
	foreach($this->leafs as $crit){
		$line = self::ENCL . $crit . self::ENCL . $fs . self::ENCL . number_format($this->pGlb[$crit], ROUND, $ds, "")
		 . self::ENCL . $fs;
		for( $i=0; $i< $altnum; $i++){
			$line .= self::ENCL . number_format($this->prioAlt[$crit][$i], ROUND, $ds, "") . self::ENCL . $fs;
		}
		$textout[] = $line . self::NEWL;
	}
	$line = self::ENCL . "Group Result" . self::ENCL . $fs . self::ENCL . '' . self::ENCL;
	foreach( $this->prioTot as $alternative=>$val )
		$line .= $fs . number_format($val,ROUND, $ds, "");
	$textout[] = $line . self::NEWL;
	return $textout;
}


/* export robustness tables 
 */
public function exportRobTest($ds, $rt1, $rt2){
	global $ahpH;
	$textout = array();
	$fs = ($ds == ',' ? ';' : ',');
//	$textout[] = "sep=" . $fs . self::NEWL;

	$textout[] = self::NEWL . self::ENCL . "4. Robustness (sensitivity analysis, " 
	. ($this->wpm ? "WPM" : "WSM") . ")" . self::ENCL . self::NEWL;
	// table 1: critical criteria
	$textout[] = self::ENCL . "4.1 Absolute critical criteria" . self::ENCL . self::NEWL;
	
	if(is_array($rt1[2])){
		foreach($rt1[2] as $row){
			$line="";
			foreach($row as $cell){
				$line .= self::ENCL;
				$line .= ( is_numeric($cell) ? number_format($cell,ROUND, $ds, "") : $cell);
				$line .= self::ENCL . $fs;
			}
			$textout[] = $line . self::NEWL;
		}
	}
	
	// table 2: critical alternatives
	$textout[] = self::NEWL . self::ENCL . "4.2 Absolute critical alternatives" . self::ENCL . self::NEWL;
	if(is_array($rt2[2])){
		foreach($rt2[2] as $row){
			$line = "";
			foreach($row as $cell){
				$line .= self::ENCL; 
				$line .= ( is_numeric($cell) ? number_format($cell,ROUND, $ds, "") : $cell);
				$line .= self::ENCL . $fs;
			}
			$textout[] = $line . self::NEWL;
		}
	}
	return implode($textout);
}

} // end class ahpAlternatives
