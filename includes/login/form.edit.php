<!-- show potential errors / feedback (from login object) -->
<p><?php echo $login->getErrors(); ?></p>
<!-- account details and latest activities -->
<h2><?php echo $login->lgTxt->titles['h2info']; ?></h2>
<table id='tbEd'>
<?php
    if (is_object($account)) {
        echo "<tr><td>",  $login->lgTxt->tbl['tbEdTd1'], "</td><td><span class='var'>", $account->user_id, "</span></td></tr>";
        echo "\n<tr><td>",$login->lgTxt->tbl['tbEdTd2'], "</td><td><span class='var'>", $account->user_name, "</span></td></tr>";
        echo "\n<tr><td>",$login->lgTxt->tbl['tbEdTd3'], "</td><td><span class='var'>", $account->user_email, "</span></td></tr>";
        echo "\n<tr><td>",$login->lgTxt->tbl['tbEdTd4'], "</td><td><span class='var'>", $account->user_registration_datetime, "</span></td></tr>";
        echo "\n<tr><td>",$login->lgTxt->tbl['tbEdTd5'], "</td><td><span class='var'>",($account->user_rememberme_token ? "yes" : "no"), "</span></td></tr>";
    } else {
        $login->errors[] = "Cannot retrieve user information.";
    }
?>
</table>
<h2><?php echo $login->lgTxt->titles['h2act']; ?></h2>
<?php $userDb->displayLogTable(htmlspecialchars($_SESSION['user_id'])); ?>

<div style='clear:both;'></div>
<!-- backlink -->
<form method="post" action="<?php echo $url; ?>" >
	<div class='ca'>
    <input type="submit" name="return" value="<?php echo $login->lgTxt->wrd['done']; ?>" />
	</div>
</form>
<hr>
<h2><?php echo $login->lgTxt->wrd['crC']; ?></h2>
<!-- edit form for username / this form uses HTML5 attributes, like "required" and type="email" -->
	<h3><?php echo $login->lgTxt->wrd['unC']; ?></h3>
<form method="post" action="do-edit.php" name="user_edit_form_name">
    <label for="user_name"><?php echo $login->lgTxt->wrd['unN']; ?></label>
  <input id="user_name" type="text" name="user_name" pattern="[a-zA-Z0-9]{2,64}" required />
    <p><input type="submit" name="user_edit_submit_name" value="<?php echo $login->lgTxt->wrd['unC'];  ?>" /></p>
</form><hr/>

<!-- edit form for user email / this form uses HTML5 attributes, like "required" and type="email" -->
<h3><?php echo $login->lgTxt->wrd['emlC']; ?></h3>
<form method="post" action="do-edit.php" name="user_edit_form_email">
  	<label for="user_email"><?php echo $login->lgTxt->wrd['emlN']; ?></label>
    <input id="user_email" type="email" name="user_email" required /> 
    &nbsp;<?php echo $login->lgTxt->msg['emlN']; ?>
    <p><input type="submit" name="user_edit_submit_email" value="<?php echo $login->lgTxt->wrd['emlC']; ?>" /></p>
</form><hr/>

<!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
<h3><?php echo $login->lgTxt->wrd['pwC']; ?></h3>
<form method="post" action="do-edit.php" name="user_edit_form_password">
    <label for="user_password_old"><?php echo $login->lgTxt->wrd['pwO']; ?></label>
    <input id="user_password_old" type="password" name="user_password_old" autocomplete="off" />
    <p><label for="user_password_new"><?php echo $login->lgTxt->wrd['pwN']; ?></label>
  	<input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" autocomplete="off" />
  	&nbsp;
    <label for="user_password_repeat"><?php echo $login->lgTxt->wrd['pwNr']; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" autocomplete="off" /></p>

    <p><input type="submit" name="user_edit_submit_password" value="<?php echo $login->lgTxt->wrd['pwC']; ?>" /></p>
</form><hr/>

<!-- Delete account -->
<h3><?php echo $login->lgTxt->wrd['delA']; ?></h3>
<form method="post" action="<?php echo BASE . 'includes/login/do/do-user-admin.php'; ?>" name="user_edit_form_delete" >
 	<?php echo $login->lgTxt->info['delA']; ?>
	<input type='hidden' name='formToken' value='<?php echo $formToken; ?>' />
 	<input type="submit" name="user_edit_form_delete" value='<?php echo $login->lgTxt->wrd['delA']; ?>' onclick="return deletconfig()" />
	<script type='text/javascript'> 
		function deletconfig(){ var del=confirm("Are you sure to delete your account?");
		return del;} 
	</script>
</form>
<hr>
<!-- backlink -->
<form method="post" action="<?php echo $url; ?>" >
	<div class='ca'>
    <input type="submit" name="return" value="<?php echo $login->lgTxt->wrd['done']; ?>" />
	</div>
</form>
