<?php
if ($login->passwordResetWasSuccessful() == true && $login->passwordResetLinkIsValid() != true) {
	// the user has just successfully entered a new password
	// we show the return link to the calling website
	echo '<div class="ca"><a href="', SITE_URL,'" >Continue</a></div>';
} else {
    // show the request-a-password-reset or type-your-new-password form

	if ($login->passwordResetLinkIsValid() == true) { 
	?>
		<form method="post" action="do-reset-pw.php" name="new_password_form">
		<fieldset>
	   <input type='hidden' name='user_name' value='<?php echo $_GET['user_name']; ?>' />
	   <input type='hidden' name='user_password_reset_hash' value='<?php echo $_GET['verification_code']; ?>' />
	   <label for="user_password_new"><?php echo $login->lgTxt->wrd['pw']; ?></label><br>
	   <input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" /><br>
	   <label for="user_password_repeat"><?php echo $login->lgTxt->wrd['pwr']; ?></label><br>
	   <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
	  	<p><input type="submit" name="submit_new_password" value="<?php echo $login->lgTxt->wrd['pwSbm']; ?>" ></p>
		</fieldset>
		</form>
		<!-- no data from a password-reset-mail has been provided, so we simply show the request-a-password-reset form -->
	<?php } elseif($login->passwordResetSet == true) { ?>
	   <p class='ca'>
	   	<a href='<?php echo SITE_URL; ?>' ><?php echo $login->lgTxt->wrd['cont']; ?></a>
	   </p>
	<?php } else { ?>
		<form method="post" action="do-reset-pw.php" name="password_reset_form">
  		<p><label for="user_email"><?php echo $login->lgTxt->info['pwRes']; ?></label></p>
  		<input id="user_email" type="email" name="user_email" required />
    	<input type="submit" name="request_password_reset" value="<?php echo $login->lgTxt->wrd['pwRes']; ?>" />
    	<p class='ca'>
    		<a href='<?php echo SITE_URL; ?>'><?php echo $login->lgTxt->wrd['cont']; ?></a>
    	</p>
		</form>
	<?php } 
}?>

