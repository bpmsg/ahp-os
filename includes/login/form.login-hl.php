<?php
// --- User is logged in
if ($login->isUserLoggedIn() === true) { ?>
	<div style="display:block;float:left">
		<a href='<?php echo SITE_URL; ?>'><?php echo APP; ?></a>&nbsp;&nbsp;<a href='<?php echo BASE."ahp-news.php"; ?>'>Latest News</a></div>
		<div style='display:block;float:right'>
			<?php echo $login->lgTxt->wrd['hlWlc'] . $_SESSION['user_name']; ?>!
			&nbsp;<a href='<?php echo BASE . "includes/login/do/do-edit.php"; ?>'><?php echo $login->lgTxt->wrd['hlAc']; ?></a>
			&nbsp;<small>(<a href='<?php echo $myUrl . "?logout"; ?> '>
				<?php echo $login->lgTxt->wrd['hlLo']; ?></a>)</small>
		</div>
	<div style="clear:both;"></div>
<?php } else { ?>
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" name="loginform">
	<div style="display:block;float:left" ><?php echo $loginHeaderText; ?></div>
	<div style="display:block;float:right">
		<label><span class="hide-if-placeholder" >Username or Email Address</span>
		<input id="login_input_name" type="text" name="user_name" 
			placeholder="<?php echo $login->lgTxt->wrd['hlUn']; ?>" size="13" maxlength="40" value="" ></label>
		<label><span class="hide-if-placeholder">Password</span>
		<input id="login_input_password" type="password" name="user_password" 
			placeholder="<?php echo $login->lgTxt->wrd['hlPw']; ?>" size="13" maxlength="100" ></label>
		<input type="checkbox" id="user_rememberme" name="user_rememberme" title="Remember me" value="1" tabindex="3" >
		<input type="submit"  name="login" value="Log in" >
		&nbsp;<small>(<a href="<?php echo BASE . 'includes/login/do/do-reset-pw.php'; ?>"><?php echo $login->lgTxt->wrd['hlFg']; ?></a>)</small>
    &nbsp;<a href="<?php echo BASE . 'includes/login/do/do-register.php'; ?>"><?php echo $login->lgTxt->wrd['hlReg']; ?></a>
	</div>
	</form>
<?php } ?>
<script>
/* <![CDATA[ */
	(function(){
		if( document.getElementsByClassName ) {
			var hideThese = document.getElementsByClassName( "hide-if-placeholder" )
			for ( var i = 0; i < hideThese.length; i++ ) {
				hideThese[i].style.display = "none";
			}
		}
	})();
/* ]]> */
</script>
<div style="clear:both;"></div>
