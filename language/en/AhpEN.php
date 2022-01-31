<?php
class AhpEN {

public $titles = array(
	"pageTitle"		=>	"AHP Online System - AHP-OS",
	"h1title"			=>	"<h1>AHP Online System - AHP-OS</h1>",
	"h2subTitle"	=>	"<h2>Multi-criteria Decision Making Using the Analytic Hierarchy Process</h2>",
	"h2contact"		=>	"<h2>Contact and Feedback</h2>"
);

public $msg = array(
	"tu"	=>	"Thank You!",
	"cont"	=>	"Continue"
);

public $info = array(
	"contact"	=>	"<p>
								Please feel free to leave a 
								<a href='%s'>comment</a> 
								or like/share this site.
								</p>",
	"intro11"	=>	"<div class='entry-content'><p style='text-align:justify;'>
								This free <b>web based AHP solution</b> is a supporting 
								tool for decision making processes.
								The programs can be helpful in your daily work for simple decision problems and also support complex 
								decision making problems. Participate in a group session and try a 
								<a href='https://bpmsg.com/participate-in-an-ahp-group-session-ahp-practical-example/'>practical example</a>. 
								Download the <a href='docs/BPMSG-AHP-OS-QuickReference.pdf' target='_blank'>quick reference guide</a> 
								or the <a href='docs/BPMSG-AHP-OS.pdf' target='_blank'>AHP-OS manual</a>. 
								For full functionality you need to login. Please <a href='includes/login/do/do-register.php'>register</a> 
								as new user, if you don't have an account yet. It's all free!
								</p></div>",
	"intro12"	=>	"<ol style='line-height:150%;'>
								<li><span style='cursor:help;' 
								title='Manage complete AHP projects and group sessions. You need to be a registered user and login.' >
									<a href='ahp-session-admin.php'>My AHP Projects</a></span></li>
								<li><span style='cursor:help;' 
								title='The AHP priority calculator calculates priorities or weights for a set of criteria based on pairwise comparisons.' >
									<a href='ahp-calc.php'>AHP Priority Calculator</a></span></li>
								<li><span style='cursor:help;' 
								title='Handle complete decision problems under AHP. Define a hierarchy of criteria and evaluate alternatives.' >
									<a href='ahp-hierarchy.php'>AHP Hierarchies</a></span></li>
								<li><span style='cursor:help;' 
								title='Participate in an AHP group sessions to evaluate criteria or alternatives as a member of a group' >
								<a href='ahp-hiergini.php'>AHP Group Session</a></span></li>
								</ol>",
	"intro13"	=>	"<p style='text-align:justify;'>
								For programs 2 and 3 you can export the results as csv files (comma separated values) for further 
								processing in excel. <b>For terms of use please see our </b> 
								<a href='https://bpmsg.com/about/user-agreement-and-privacy-policy/' >
								user agreement and privacy policy.</a> If you like the program, <span class='err'>please help and consider a 
								<a href='ahp-news.php'>donation</a> to maintain the website</span>.</p>",
	"intro14"	=>	"<p><b>For your work please cite:</b><br>
								<code>Goepel, K.D. (2018). Implementation of an Online Software Tool for the Analytic Hierarchy 
								Process (AHP-OS). <i>International Journal of the Analytic Hierarchy Process</i>, Vol. 10 Issue 3 2018, pp 469-487,
								<br><a href='https://doi.org/10.13033/ijahp.v10i3.590'>https://doi.org/10.13033/ijahp.v10i3.590</a>
								</code></p>",

	"intro21"	=> "<h3>Introduction</h3>
								<div style='display:inline;'>
								<img src='images/AHP-icon-150x150.png' alt='AHP' style='float: left; height:15%; width:15%; padding:5px;'>
								</div><div class='entry-summary'><p style='text-align:justify;'>
								AHP stands for <i>Analytic Hierarchy Process</i>. It is a method to support 
								multi-criteria decision making, and was originally developed by Prof. Thomas L. Saaty. AHP derives 
								<i>ratio scales</i> from paired comparisons of criteria, and allows for some small inconsistencies in 
								judgments. Inputs can be actual measurements, but also subjective opinions. As a result, 
								<i>priorities</i> (weightings) and a <i>consistency ratio</i> will be calculated.
 								Internationally AHP is used in a wide range of applications, for example for the evaluation 
 								of suppliers, in project management, in the hiring process or the evaluation of company performance. </p></div>",

	"intro22"	=>"	<div style='display:block;clear:both;'>
								<h3>Benefits of AHP</h3>
								<p style='text-align:justify;'>
								Using AHP as a supporting tool for decision making will help to 
								gain a <i>better insight in complex decision problems</i>. As you need to structure the problem as a 
								hierarchy, it forces you to think through the problem, consider possible decision criteria and select 
								the most significant criteria with respect to the decision objective. Using pairwise comparisons 
								helps to discover and correct logical inconsistencies. The method also allows to \"translate\" 
								subjective opinions, such as preferences or feelings, into measurable numeric relations. 
								AHP helps to makes decisions in a more rational way and to make them more transparent and 
								better understandable.
								</p>",

	"intro23"	=>"	<h3>Method</h3>
								<p style='text-align:justify;'>
								Mathematically the method is based on the solution of 
								an Eigen value problem. The results of the pair-wise comparisons are arranged in a matrix. 
								The first (dominant) normalized right Eigen vector of the matrix gives the ratio scale (weighting), the 
								Eigen value determines the consistency ratio.
								</p>",
	
	"intro24"	=>"	<h3>AHP Examples</h3>
								<p style='text-align:justify;'>
								In order to make the method easier to understand, and to show the 
								wide range of possible applications, we give some <a href='ahp-examples.php' >examples</a> 
								for different decision hierarchies.
								</p>
								<p style='text-align:justify;'>
								A simple introduction 
								to the method is given <a href='docs/AHP-articel.Goepel.en.pdf' target='_blank'>here</a>.
								</p></div>"
);

public $tbl	= array(
	"grTblTh"			=> 	"\n<thead><tr class='header'><th>Participant</th>",
	"grTblTd1"		=>	"<td><strong>Group result</strong></td>"

);
}
