<?php
/*

PHPGraphLib Graphing Library

The first version PHPGraphLib was written in 2007 by Elliott Brueggeman to
deliver PHP generated graphs quickly and easily. It has grown in both features
and maturity since its inception, but remains PHP 4.04+ compatible. Originally
available only for paid commerial use, PHPGraphLib was open-sourced in 2013 
under the MIT License. Please visit http://www.ebrueggeman.com/phpgraphlib 
for more information.

---

The MIT License (MIT)

Copyright (c) 2013 Elliott Brueggeman

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
class PHPGraphLibPie extends PHPGraphLib 
{
	const PIE_3D_HEIGHT_PERCENT = 4;
	const PIE_LEGEND_TEXT_WIDTH = 6;
	const PIE_LEGEND_TEXT_HEIGHT = 12;
	const PIE_LABEL_TEXT_WIDTH = 6;
	const PIE_LABEL_TEXT_HEIGHT = 12;
	const PIE_LEGEND_PADDING = 5; 
	const PIE_CENTER_Y_OFFSET = 50; //in %
	const PIE_CENTER_X_OFFSET = 50; //in %
	const PIE_CENTER_LEGEND_SCALE = 55; //offset in % of existing coords when legend
	const PIE_WIDTH_PERCENT = 75; //default width % of total width
	const PIE_HEIGHT_PERCENT = 28; 
	const PIE_LABEL_SCALE = 90; //in % scale width/height if data labels
	const PIE_LEGEND_SCALE = 64;//in % scale width/height if legend

	//internals - do not change
	protected $pie_width;
	protected $pie_height;
	protected $pie_center_x;
	protected $pie_center_y;
	protected $pie_legend_x;
	protected $pie_legend_y;
	protected $pie_data_label_space;
	protected $pie_3D_height;

	protected $pie_data_max_length = 0;
	protected $pie_color_pointer = 0;
	protected $pie_data_array_percents = array();
	protected $bool_x_axis = false;
	protected $bool_y_axis = false;
	protected $bool_data_points = false;
	protected $pie_precision = 0; //number of significant digits in label %
	protected $bool_legend = true;
	protected $bool_data_labels = true;

	//default colors, in order of display on graph
	protected $pie_avail_colors = array(
		'pastel_orange_1', 'pastel_orange_2', 'pastel_blue_1', 'pastel_green_1',
		'clay', 'pastel_blue_2', 'pastel_yellow', 'silver', 'pastel_green_2',
		'brown', 'gray', 'pastel_purple', 'olive', 'aqua', 'yellow', 'teal', 'lime'
	);

	protected function calcCoords() 
	{
		//calc coords of pie center and width/height
		$this->pie_width = $this->width * (self::PIE_WIDTH_PERCENT / 100);
		$this->pie_height = $this->width * (self::PIE_HEIGHT_PERCENT / 100);
		$this->pie_center_y = $this->height * (self::PIE_CENTER_Y_OFFSET / 100);
		$this->pie_center_x = $this->width * (self::PIE_CENTER_X_OFFSET / 100);

		//set data label spacing 
		if ($this->bool_data_labels) {
			//set to number of pixels that are equal to text width
			//7 is a base spacer that all labels get
			$this->pie_data_label_space = 7 + $this->width / 30;
			$this->pie_width *= self::PIE_LABEL_SCALE / 100;
			$this->pie_height *= self::PIE_LABEL_SCALE / 100;
		}

		if ($this->bool_legend) {
			//compensate for legend with lesser preset percent
			$this->pie_width *= self::PIE_LEGEND_SCALE / 100;
			$this->pie_height *= self::PIE_LEGEND_SCALE / 100;
			$this->pie_center_x *= self::PIE_CENTER_LEGEND_SCALE / 100;
		}
		$this->pie_3D_height = self::PIE_3D_HEIGHT_PERCENT * ($this->pie_width / 100);	
	}

	protected function setupData() 
	{
		//in the pie extension, this will calculate the total sum and the corresponding percentages
		if ($this->data_set_count == 1) {
			$sum = array_sum($this->data_array[0]);
			if ($sum > 0) {
				foreach ($this->data_array[0] as $dataText => $dataValue) {
					$this->pie_data_array_percents[] = $dataValue / $sum;
					//find data text length
					$len = strlen($dataText);
					if ($len > $this->pie_data_max_length) {
						$this->pie_data_max_length = $len; }
				}
				$this->bool_bars_generate = true;
			} else {
				$this->bool_bars_generate = false;
				$this->error[] = "Sum of data must be greater than 0.";
			}
		} else {
			$this->error[]="Multiple datasets not allowed with pie charts";
		}
	}

	protected function generateLegend() 
	{
		$maxChars = NULL;
		//calc height / width based on # of values
		$pie_legend_height = (self::PIE_LEGEND_TEXT_HEIGHT * $this->data_count) + (2 * self::PIE_LEGEND_PADDING);	
		$pie_legend_width = ($this->pie_data_max_length * self::PIE_LEGEND_TEXT_WIDTH) + (6 * self::PIE_LEGEND_PADDING);

		//allotted space does not include padding around legend (smaller)
		$allottedSpace = $this->width - $this->pie_center_x - ($this->pie_width / 2) - (2 * self::PIE_LEGEND_PADDING);
		if ($this->bool_data_labels) {
			//also compensate for displayed text data % on graph
			$allottedSpace -= ((4 + $this->pie_precision) * self::PIE_LABEL_TEXT_WIDTH) + $this->pie_data_label_space;
		}
		//check to make sure we are not > allotted space
		if ($pie_legend_width > $allottedSpace) {
			//if we are, adjust width and max length for data values
			//4 = padding | swatch(padding width) | padding | ...text... |padding
			$swatchAndPaddingWidth = 4 * self::PIE_LEGEND_PADDING;
			//MAX CHARS = ALOTTED SPACE - ENOUGH ROOM FOR SWATCHES / TEXT WIDTH
			$maxChars = floor(($allottedSpace - $swatchAndPaddingWidth) / self::PIE_LEGEND_TEXT_WIDTH);
			$pie_legend_width = ($maxChars * self::PIE_LEGEND_TEXT_WIDTH) + $swatchAndPaddingWidth;
		} else {
			//we didnt go over allotted space, so we should adjust the center of the pie chart now
			$equalSpacing = ($this->width - ($this->pie_width + $pie_legend_width)) / 3;
			//so now reposition center at spacing + 1/2 pie width
			$this->pie_center_x = ($this->pie_width / 2) + $equalSpacing;
		}
		//auto adjusting formula for position of pie_legend_x based on pie chart size
		$a = ($this->pie_center_x + $this->pie_width / 2);
		$b = $this->width - $a;
		$c = ($b - $pie_legend_width) / 2;
		//set pie x & y args
		$this->pie_legend_x = $a + $c;
		$this->pie_legend_y = ($this->height - $pie_legend_height) / 2;		
		//background
		imagefilledrectangle($this->image, $this->pie_legend_x, $this->pie_legend_y, $this->pie_legend_x + $pie_legend_width, 
			$this->pie_legend_y + $pie_legend_height, $this->legend_color);
		//border
		imagerectangle($this->image, $this->pie_legend_x, $this->pie_legend_y, $this->pie_legend_x + $pie_legend_width, 
			$this->pie_legend_y + $pie_legend_height, $this->legend_outline_color);
		$xValue = $this->pie_legend_x + self::PIE_LEGEND_PADDING;
		$count = 0;
		$this->resetColorPointer();
		$swatchToTextOffset = (self::PIE_LEGEND_TEXT_HEIGHT - 6) / 2;
		$swatchSize = self::PIE_LEGEND_TEXT_HEIGHT - (2 * $swatchToTextOffset);
		foreach ($this->data_array[0] as $dataText => $dataValue) {
			$yValue = $this->pie_legend_y + self::PIE_LEGEND_TEXT_HEIGHT * $count + self::PIE_LEGEND_PADDING;
			//draw color boxes
			$color = $this->generateNextColor();
			imagefilledrectangle($this->image, $xValue, $yValue + $swatchToTextOffset, $xValue + $swatchSize, $yValue + $swatchToTextOffset + $swatchSize, $color);
			imagerectangle($this->image, $xValue, $yValue + $swatchToTextOffset, $xValue + $swatchSize, $yValue + $swatchToTextOffset + $swatchSize, $this->legend_swatch_outline_color);	
			//if longer than our max, trim text
			if ($maxChars) { 
				$dataText = substr($dataText,0, $maxChars); 
			}
			imagestring($this->image, 2, $xValue + (2 * self::PIE_LEGEND_PADDING), $yValue, $dataText, $this->legend_text_color);
			$count++;
		}
	}

	protected function generateBars()
	{
		$this->resetColorPointer();
		//loop through and create shadaing
		for ($i = $this->pie_center_y + $this->pie_3D_height; $i > $this->pie_center_y; $i--) {
			$arcStart = 0;
			foreach ($this->pie_data_array_percents as $key => $value) {
				$color = $this->generateNextColor(true); 
				// generate a darker version of the indexed color
				// do not draw if the value is zero
				if (! $value == 0){
					imagefilledarc($this->image, $this->pie_center_x, $i, $this->pie_width, $this->pie_height, $arcStart, (360 * $value) + $arcStart, $color, IMG_ARC_PIE);
					$arcStart += 360*$value;
				}
			}
			$this->resetColorPointer();
		}
		$arcStart = 0;	
		foreach ($this->pie_data_array_percents as $key => $value) {
			$color = $this->generateNextColor();
			// do not draw if the value is zero
			if (! $value == 0){
				imagefilledarc($this->image, $this->pie_center_x, $this->pie_center_y, $this->pie_width, $this->pie_height, $arcStart, (360*$value)+$arcStart, $color, IMG_ARC_PIE);
				$arcStart += 360 * $value;
			}
			if ($this->bool_data_labels) { 
				$this->generateDataLabel($value, $arcStart); 
			}
		}
	}

	protected function generateDataLabel($value, $arcStart) 
	{
		//midway if the mid arc angle of the wedge we just drew
		$midway = ($arcStart - (180 * $value));
		//adjust for ellipse height/width ratio
		$skew = self::PIE_HEIGHT_PERCENT / self::PIE_WIDTH_PERCENT;
		$pi = atan(1.0) * 4.0;
		$theta = ($midway / 180) * $pi;
		$valueX = $this->pie_center_x + ($this->pie_width / 2 + $this->pie_data_label_space) * cos($theta);
		$valueY = $this->pie_center_y + ($this->pie_width / 2 + $this->pie_data_label_space) * sin($theta) * $skew;
		$displayValue = $this->formatPercent($value);
		$valueArray = $this->dataLabelHandicap($valueX, $valueY, $displayValue, $midway);
		$valueX = $valueArray[0];
		$valueY = $valueArray[1];	
		imagestring($this->image, 2, $valueX, $valueY, $displayValue, $this->label_text_color);
	}

	protected function formatPercent($input) 
	{	
		return number_format($input * 100, $this->pie_precision) . '%';
	}

	protected function dataLabelHandicap($x, $y, $value, $midway) 
	{
		//moves data label x/y based on quadrant and length of displayed data
		//and how text is displayed (upper left corner x/y)
		//extra 1 for % sign
		$lengthOffset = (strlen($value) * (self::PIE_LABEL_TEXT_WIDTH)) / 2;
		$vertOffset = self::PIE_LABEL_TEXT_HEIGHT / 2;
		if ($midway <= 30) {
			$newX = $x - (1.5 * $lengthOffset);
			$newY = $y - $vertOffset;
		} elseif ($midway > 30 && $midway <= 135) {
			$newX = $x - $lengthOffset;
			$newY = $y - $vertOffset + $this->pie_3D_height;
		} elseif ($midway > 135 && $midway <= 165) {
			$newX = $x - $lengthOffset;
			$newY = $y - $vertOffset;
		} elseif ($midway > 165 && $midway <= 200) {
			//value at risk for being out of bounds on smaller graphs
			$newX = $x - (1/3 * $lengthOffset);
			$newY = $y - $vertOffset;
		} elseif ($midway > 200 && $midway <= 330) {
			$newX = $x - $lengthOffset;
			$newY = $y - $vertOffset;
		} elseif ($midway > 330) {
			//value at risk for overlapping the legend on smaller graphs
			$newX = $x - (1.5 * $lengthOffset);
			$newY = $y - $vertOffset;
		} else {
			$newX = $x - $lengthOffset;
			$newY = $y - $vertOffset;
		}
		return array($newX, $newY);
	}

	protected function generateNextColor($dark = false) 
	{
		$array = $this->returnColorArray($this->pie_avail_colors[$this->pie_color_pointer]);
		if ($dark) {
			//we are trying to generate a darker version of the existing color
			$array[0] *= .8;
			$array[1] *= .8;
			$array[2] *= .8;
		}
		$color = imagecolorallocate($this->image, $array[0], $array[1], $array[2]);
		$this->pie_color_pointer++;
		if ($this->pie_color_pointer >= count($this->pie_avail_colors)) {
			$this->pie_color_pointer = 0;
		}
		return $color;
	}

	protected function resetColorPointer() 
	{
		$this->pie_color_pointer = 0;
	}

	protected function returnColorArray($color) 
	{
		//this function first checks exisitng colors in phpgraphlib
		//then if not found checks its own list
		//comes with various preset lighter pie chart friendly colors
		if ($resultColor = parent::returnColorArray($color)) {
			return $resultColor;
		} else {
			//remove last error generated (phpgraphlib::returncolorarray) sets only one error if false)
			array_pop($this->error);
			//check to see if numeric color passed through in form '128,128,128'
			if (strpos($color,',') !== false) {
				return explode(',', $color);
			}
			switch(strtolower($color)) {
				//named colors based on w3c's recommended html colors
				case 'pastel_orange_1': return array(238,197,145); break;
				case 'pastel_orange_2': return array(238,180,34); break;
				case 'pastel_blue_1':   return array(122,197,205); break;
				case 'pastel_green_1':  return array(102,205,0); break;
				case 'pastel_blue_2':   return array(125,167,217); break;
				case 'pastel_green_2':  return array(196,223,155); break;
				case 'clay':            return array(246,142,85); break;
				case 'pastel_yellow':   return array(255,247,153); break;
				case 'pastel_purple':   return array(135,129,189); break;
				case 'brown':           return array(166,124,81); break;	
			}
			$this->error[] = "Color name \"$color\" not recogized.";
			return false;
		}
	}
	protected function generateTitle()
	{
		//draws title b/t top of graph and edge of canvas
		$pieTop = $this->pie_center_y - ($this->pie_height / 2);
		if ($this->bool_legend) {
			$topElement = ($pieTop < $this->pie_legend_y) ? $pieTop : $this->pie_legend_y;
		} else {
			$topElement = $pieTop;
		}

		if ($topElement < 0) {
			$this->error[] = "Not enough room for a title. Increase graph height, or eliminate data values.";
		} else {
			$title_y = ($topElement / 2) - (self::TITLE_CHAR_HEIGHT / 2);
			$title_x = ($this->width / 2) - ((strlen($this->title_text) * self::TITLE_CHAR_WIDTH) / 2);
			imagestring($this->image, 2, $title_x , $title_y , $this->title_text,  $this->title_color);
		}
	}

	public function setLabelTextColor($color)
	{
		$this->setGenericColor($color, '$this->label_text_color', "Label text color not specified properly.");
	}

	public function setPrecision($digits)
	{
		if (is_int($digits)) { 
			$this->pie_precision = $digits;
			return;
		}

		$this->error[] = "Integer arg for setPrecision() not specified properly.";
	}

	public function setDataLabels($bool) 
	{
		if (is_bool($bool)) { 
			$this->bool_data_labels = $bool;
			return;
		}
		
		$this->error[] = "Boolean arg for setDataLabels() not specified properly."; 
	}

	//overwritten and unused PHPGraphLib functions
	function setDataPoints($bool) 
	{ 
		$this->error[] = __function__ . '() function not allowed in PHPGraphLib Stacked extension.'; 
	}

	function calcTopMargin() {}
	function calcRightMargin() {}
	function setupGrid() {}
}
