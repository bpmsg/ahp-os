<?php
/**
* Class for color modifications
*
* @author Klaus D. Goepel
* @copyright 2014 Klaus D. Goepel
* @version 2014-02-14
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
class AhpColors {

/** Methods */

	public function __construct(){
		mb_internal_encoding('UTF-8');
	}
/** Function to get the rank of an array
*
* @param array of values
* @return array $rk rank of values
*/
	function getRank($v) {
		$rk = array();
		foreach ($v as $number){
    	$larger = 1;
		foreach ($v as $number2)
			if ($number2 > $number)
				$larger++;
      $rk[] = $larger;
		}
		return $rk;
	}

/* converts array with rgb values into html color string
* @param array $rgb (0-255, 0-255, 0-255)
* @return string "#RRGGBB"
*/
function rgbToHex($rgb){
	return sprintf("#%02X%02X%02X", $rgb[0],$rgb[1],$rgb[2] );
}

/* converts rgb hex string into hsl values
* @param $rgb as "#RRGGBB"
* @return array hue (0 - 360), saturation (0 - 1), luminance (0 - 1)
*/
function rgbToHsl( $rgb ) {
	if( strlen($rgb) != 7 || substr($rgb,0,1) != '#' )
		return array(0, 0, 0);
	$r = hexdec(substr($rgb,1,2))/255.;
	$g = hexdec(substr($rgb,3,2))/255.;
	$b = hexdec(substr($rgb,5,2))/255.;
  $max = max( $r, $g, $b );
	$min = min( $r, $g, $b );
	$h;	$s;	$l = ( $max + $min ) / 2.;
	$d = $max - $min;
	if( $d == 0 ){
  	$h = $s = 0; // achromatic
  } else {
  	$s = $d / ( 1. - abs( 2. * $l - 1 ) );

		switch( $max ){
			case $r:
				$h = 60. * fmod( ( ( $g - $b ) / $d ), 6. ); 
      				if ($b > $g) {
					$h += 360.;
	    			}
	    			break;
    			case $g: 
    				$h = 60. * ( ( $b - $r ) / $d + 2. ); 
    				break;
			case $b: 
				$h = 60. * ( ( $r - $g ) / $d + 4. ); 
				break;
	  }			        	        
	}
	return array( $h, $s, $l);
}

/* converts hsl array into hex RGB string
* @param $hsl array hue (0 - 360), saturation (0 - 1), luminance (0 - 1)
* @return string $rgb as "#RRGGBB"
*/
function hslToRgb( $hsl ){
	$r; $g; $b;
	$h = $hsl[0]; $s = $hsl[1]; $l = $hsl[2];

	// ensure positive values	
	$c = abs( ( 1. - abs( 2 * $l - 1. ) ) * $s );
	$x = abs( $c * ( 1. - abs( fmod( ( $h / 60. ), 2 ) - 1. ) ) );
	$m = abs($l - ( $c / 2 ));

	if ( $h < 60 ) {
		$r = $c; $g = $x; $b = 0;
	} elseif ( $h < 120 ) {
		$r = $x; $g = $c; $b = 0;			
	} elseif ( $h < 180 ) {
		$r = 0; $g = $c; $b = $x;					
	} elseif ( $h < 240 ) {
		$r = 0; $g = $x; $b = $c;
	} elseif ( $h < 300 ) {
		$r = $x; 	$g = 0; $b = $c; 
	} else {
		$r = $c; $g = 0; $b = $x;
	}
	$r = (int) round(( $r + $m ) * 255., 0);
	$g = (int) round(( $g + $m ) * 255., 0);
	$b = (int) round(( $b + $m ) * 255., 0);
  return sprintf("#%02X%02X%02X", $r, $g, $b);
}

/* modify luminance of RGB color by factor
* @param $rgb string "#RRGGBB"
* @return string $rgb modified color as "#RRGGBB"
*/
function modifyLum($rgb,$fac){
	$hsl = array(0,0,0);
	$hsl = rgbToHsl($rgb);
	$hsl[2] = min(max($hsl[2] * $fac, 0),1);
	return hslToRgb($hsl);
}

/* change RGB color by delta hue, sat, lum
* @param $rgb string "#RRGGBB"
* @param float $dh, ds, dl delta to change
* @return string $rgb modified color as "#RRGGBB"
*/
function setColor($rgb,$dh,$ds,$dl){
	$hsl = array(0,0,0);
	$hsl = $this->rgbToHsl($rgb);
	$hsl[0] = min(max( $hsl[0] + $dh, 0),360);
	$hsl[1] = min(max( $hsl[1] + $ds, 0),1);
	$hsl[2] = min(max( $hsl[2] + $dl, 0),1);
	return $this->hslToRgb($hsl);
}

/* change RGB color by delta hue, factor sat & lum
* @param $rgb string "#RRGGBB"
* @param float $dh, 
* @param float $ds, $dl factor for change of sat and lum
* @return string $rgb modified color as "#RRGGBB"
*/
function modifyColor($rgb,$dh,$ps,$pl){
	$hsl = array(0,0,0);
	$hsl = $this->rgbToHsl($rgb);
	$hsl[0] = min(max( $hsl[0] + $dh, 0),360);
	$hsl[1] = min(max($hsl[1] * $ps, 0),1);
	$hsl[2] = min(max($hsl[2] * $pl, 0),1);
	return $this->hslToRgb($hsl);
}
/* Generates an array with $steps html rgb colors
* with changing luminance
* add: -steps to darken!
*/
function lumScale($rgbBaseColor, $steps){
$lumSc = array(); 
$hslBase = $this->rgbToHsl($rgbBaseColor);
$lstep = (1.- $hslBase[2])/($steps+1);
	if($lstep < 0.01){
		$lumSc = array_fill(0,$steps,$rgbBaseColor);
		return $lumSc;
	}
	$hslBase[2] -= $lstep;
	for($i=0; $i<$steps; $i++){
		$hslBase[2] += $lstep;
		$lumSc[] = $this->hslToRgb($hslBase);
	}
	return $lumSc;
}

/* Generates an array with $steps html rgb colors
* with changing hue from base to end color
* add: -steps to darken!
*/
function hueScale($rgbBaseColor, $rgbEndColor, $steps){
	$hueSc = array(); 
	$hslBase= array(0,0,0);
	$hslEnd = array(0,0,0);
	if ($steps <2) 
		return $rgbBaseColor;

	$hslBase = $this->rgbToHsl($rgbBaseColor);
	$hslEnd  = $this->rgbToHsl($rgbEndColor);

	if(abs($hslBase[0]-$hslEnd[0])> 10.){
		$hstep = ($hslEnd[0]-$hslBase[0])/$steps;
	} else {
		$hstep = 0.;
	}
	if(abs($hslBase[2]-$hslEnd[2])> 10.){
		$lum = ($hslEnd[2]+$hslBase[2])/2.;
	} else {
		$lum = $hslBase[2];
	}
	for($i=0; $i<$steps; $i++){
		$hueSc[] = $this->modifyColor($rgbBaseColor, $i * $hstep, 1, 1);
	}
	return $hueSc;
}

// mapping of colors according to values
function lumMap($v, $rgbBaseColor){
	$steps = count($v);
	if ($steps <2) return $rgbBaseColor;
	$lumSc = array(); 
	$range = max($v) - min($v);
	if( 2 * $range /(max($v) + min($v)) < 1.E-2){
		$lumSc = array_fill(0, $steps, $rgbBaseColor);
		return $lumSc;
	}		
	if($range < 1.E-3) $range = 1.E-3;
	$hslBase = $this->rgbToHsl($rgbBaseColor);
	$hsl = $hslBase;
	$lstep = (1.- $hslBase[2])/($steps + 1);
	$reso = $steps/$range;
	foreach($v as $val){
		$i = floor( $steps + 1 - (($val - min($v)) * $reso));
		$hsl[2] = $hslBase[2] + $i * $lstep;
		$rgb = $this->hslToRgb($hsl);
		$lumSc[] = ($rgb == "#FFFFFF" ? "" : $rgb);		
	}
	return $lumSc;
}
/* Generates an array of html rgb colors
* with changing hue from base to end color
* according to the relative value of vector $v
*/
function hueMap($v, $rgbBaseColor, $rgbEndColor){
	$steps = count($v);
	if ($steps >1 && is_numeric(max($v)) && is_numeric(min($v))){
		$range = max($v) - min($v);
		$hueSc = array(); 
		$hslBase= array(0,0,0);
		$hslEnd = array(0,0,0);
		if( abs($range) < 1.E-8 || 2 * $range /(max($v) + min($v)) < 1.E-2){
			$hueSc = array_fill(0, $steps, $rgbBaseColor);
			return $hueSc;
		}		
		$hslBase = $this->rgbToHsl($rgbBaseColor);
		$hsl = $hslBase;
		$hslEnd = $this->rgbToHsl($rgbEndColor);
		$lstep = (0.95 - $hslBase[2])/($steps + 1);
		$hstep = ($hslEnd[0] - $hslBase[0])/($steps + 1);
		$reso = $steps/$range;
		foreach($v as $val){
			$i = floor( $steps + 1 - (($val - min($v)) * $reso));
			$hsl[0] = $hslBase[0] + $i * $hstep;
			$hsl[2] = $hslBase[2] + $i * $lstep;
			$rgb = $this->hslToRgb($hsl);
			$hueSc[] = ($rgb == "#FFFFFF" ? "" : $rgb);		
		}
		return $hueSc;
	}
	return $rgbBaseColor;
}

} // end class colors

