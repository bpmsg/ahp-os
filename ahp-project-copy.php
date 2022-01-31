<?php
/* AHP-OS
 * Last Change: $LastChangedDate$
 * Revision: $Rev$
 */
include 'includes/config.Ahp.php';

$login = new Login();
// reset in case back from edit form
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);

$ahpAdmin = new LoginAdmin();
$ahpDb = new AhpDb();

/* Put here the project session code of the project to copy */
$sc = "repYhe";

$pageTitle ='AHP Project Copy';
$title = "AHP Copy Projects";
$subTitle = "Copying individual projects from sqlite to mysql database";
$version ="2019-07-05";

// get all pairwise comparisons for project with session code $sc
function readAllPwc($sc){
	global $ahpDb;
	$query = $ahpDb->db_connection->prepare("SELECT * FROM `pwc` WHERE `project_sc` = :sc");
	$query->bindValue(':sc', $sc, PDO::PARAM_STR);
	$query->execute();
	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function writeProject($sc, $p, $pwc){
	global $ahpDb;
	$p['project_author'] = "Klaus";
	// write projects            
	if(is_array($p)){
		try {
			$sql = 'INSERT INTO projects (
			project_sc, project_name, project_description, project_hText, project_datetime, project_author
			) VALUES ( :project_sc, :project_name, :project_description, :project_hText, :project_datetime, :project_author
			);';
			$queryInsert = $ahpDb->db_connection->prepare($sql);
		} catch (PDOException $e){
				$err[] = DB_PROJECT_WRITE_ERROR . $e;
		}
	  if(is_object($queryInsert)){
				// write project data
  			$queryInsert->bindValue(':project_sc', $sc, PDO::PARAM_STR);
				$queryInsert->bindValue(':project_name', $p['project_name'], PDO::PARAM_STR);
				$queryInsert->bindValue(':project_description', $p['project_description'], PDO::PARAM_STR);
				$queryInsert->bindValue(':project_hText', $p['project_hText'], PDO::PARAM_STR);
				$queryInsert->bindValue(':project_datetime', $p['project_datetime'], PDO::PARAM_STR);
				$queryInsert->bindValue(':project_author', $p['project_author'], PDO::PARAM_STR);
				$queryInsert->execute();
				if (!$queryInsert){
					$err[] = DB_PROJECT_WRITE_ERROR;
					return false;
				}
		} 
	} else {
		echo "<p class='err'>Invalid project</p>";
	}
	// write Alternatives
	if(is_array($p['project_alt']) && (count($p['project_alt'])>0){
		try {
			$ahpDb->db_connection->exec( 'PRAGMA foreign_keys = ON;' );
			$sql = 'INSERT INTO alternatives ( project_sc, alt) VALUES ( :project_sc, :alt);';
			$queryIns = $ahpDb->db_connection->prepare($sql);
		} catch (PDOException $e){
			$err[] = DB_PROJECT_WRITE_ERROR . $e;
		}
	  if(is_object($queryIns)){
			foreach ($p['project_alt'] as $a){
				$queryIns->bindValue(':project_sc', $sc, PDO::PARAM_STR);
				$queryIns->bindValue(':alt', $a, PDO::PARAM_STR);
				$queryIns->execute();
			}
			if(!$queryIns){
				echo "<p>alt insert failed</p>";
				return false;
			} 
		} 
	}
// write pwc
	if(count($pwc)>0){
		try {
			$sql = 'INSERT INTO pwc ( project_sc, pwc_part, pwc_timestamp, pwc_node, pwc_ab, pwc_intense ) 
			VALUES ( :project_sc, :pwc_part, :pwc_timestamp, :pwc_node, :pwc_ab, :pwc_intense );';
			$queryIns = $ahpDb->db_connection->prepare($sql);
		} catch (PDOException $e){
			$err[] = DB_PROJECT_WRITE_ERROR . $e;
		}
	  if(is_object($queryIns)){
			foreach($pwc as $c){
				$queryIns->bindValue(':project_sc', $sc, PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_part', $c['pwc_part'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_timestamp', $c['pwc_timestamp'], PDO::PARAM_INT);
				$queryIns->bindValue(':pwc_node', $c['pwc_node'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_ab', $c['pwc_ab'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_intense', $c['pwc_intense'], PDO::PARAM_STR);
				$queryIns->execute();
			}
			if(!$queryIns){
				echo "<p>pwc insert failed</p>";
				return false;
			}
		}
	}
	return true;
}

$project = $ahpDb->readProjectData($sc);
$pwc = readAllPwc($sc);

$ahpDb->db_connection = null;
$ahpDb->db_type = "mysql"; // MariaDB
$dbc = $ahpDb->databaseConnection();

// generate a new session code
$sc = $ahpDb->generateSessionCode(6);
$project['project_sc'] = $sc;

if(!$ahpDb->checkSessionCode($sc))
	$rsf = writeProject($sc, $project, $pwc);

/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml($pageTitle);
	include 'includes/login/form.login-hl.php';
	if (!empty($login->errors)) 
		echo $login->getErrors();
	echo "<h1>$title</h1>";
	echo "<h2>$subTitle</h2>";
	if($dbc)
		echo "<p>Connected to MariaDb</p>";
	echo "<p class='msg'>New session code: $sc</p>";
	if($rsf)
		echo "<p>Project written</p>";
	echo "<p></p>";
$webHtml->webHtmlFooter($version);