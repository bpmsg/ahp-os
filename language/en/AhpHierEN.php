<?php
class AhpHierEN {
	
public $wrd = array(
	"lvl"				=>	"Level",
	"nd"				=>	"Node",
	"lvls"			=>	"hierarchy level(s)",
	"lfs"				=>	"hierarchy leafs",
	"nds"				=>	"hierarchy node(s)",
	"chr"				=>	"hierarchy characters",
	"glbP"			=>	"Glb Prio.",
	"alt"				=>	"Alternatives"
);

public $wrn = array(
	"glbPrioS"	=> "Sum of global priorities not 100%. Check hierarchy! ",
	"prioSum"		=>	"Warning! Sum of priorities not 100% under category: %s"
);

public $err  = array(
	"hLmt"			=>	"Program limits exeeded. ",
	"hLmtLv"		=>	"Too many hierarchy levels. ",
	"hLmtLf"		=>	"Too many hierarchy leafs. ",
	"hLmtNd"		=>	"Too many hierarchy nodes. ",
	"hEmpty"		=>	"Hierarchy empty or w/o node, please define Hierarchy. ",
	"hSemicol"	=>	"Missing semicolon at the end ",
	"hTxtlen"		=>	"Max. length of input text exeeded! ",
	"hNoNum"		=>	"Name of categories/sub-categories must not be numbers; found: ",
	"hEmptyCat"	=>	"Empty category name ",
	"hEmptySub"	=>	"Empty sub-category name ",
	"hSubDup"		=>	"Duplicate subcategory name(s): ",
	"hNoSub"		=>	"Less than 2 sub-categories in category ",
	"hCatDup"		=>	"Duplicate category name(s): ",
	"hColSemi"	=>	"Unequal number of <i>colons</i> and <i>semicolons</i>, check hierarchy definition",
	"hHier"			=>	"Error in hierarchy, please check text. ",
	"hMnod"			=>	"Hierarchy starts with more than one node - ",
	"unkn"			=>	"<span class='err'>Unknown Error - Please repeat evaluation %s </span>"
);

public $msg = array(
	"sbmPwc1"		=>	"<small><span class='msg'>Please complete pairwise comparisons (Click on \"AHP\")</span></small>",
	"sbmPwc2"		=>	"<small><span class='msg'>OK. Submit for group eval or alternative eval.</span></small>",
	"aPwcCmplN"	=>	"<small><span class='msg'>%g out of %g comparisons completed</span></small>",
	"aPwcCmplA"	=>	"<small><span class='msg'>All evaluations are completed.</span></small>"
);

public $tbl	= array(
	"hTblCp"		=>	"<caption>Decision Hierarchy</caption>",
	"aTblCp"		=>	"<caption>Hierarchy with Alternatives</caption>",
	"aTblTh"		=>	"<th>No</th><th>Node</th><th>Criterion</th><th>Glb Prio.</th><th>Compare</th>",
	"aTblTd1"		=>	"Total weight of alternatives: "
);

}