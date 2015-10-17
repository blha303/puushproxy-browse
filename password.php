<?php
if (isset($_POST['pass'])) {
  include('config.php');
  echo sha1($salt . $_POST['pass']);
} else { ?>
<form method="POST"><input type="password" name="pass"><input type="submit"></form>
<?php }
