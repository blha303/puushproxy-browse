<?php
include('config.php');
?>
<html>
  <head>
    <style>
      img.thumb {
        width: auto;
        max-height: 100;
      }
      body {
        background-color: #000000;
        color: #FFFFFF;
      }
      table {
        background-color: #cccccc;
      }
    </style>
<?php if (!isset($_POST['email'])) { ?>
    <script type="text/JavaScript">
      // http://stackoverflow.com/a/1038781/2746737
      function GetWidth()
      {
          var x = 0;
          if (self.innerHeight)
          {
                  x = self.innerWidth;
          }
          else if (document.documentElement && document.documentElement.clientHeight)
          {
                  x = document.documentElement.clientWidth;
          }
          else if (document.body)
          {
                  x = document.body.clientWidth;
          }
          return x - 100; // minus 100 to leave space on the side so it doesn't go off the screen.
      }
    </script>
<?php } ?>
    <title>puush browse</title>
  </head>
  <body<?php if (!isset($_POST['email'])) echo " onLoad='document.getElementById(\"width\").value = GetWidth(); document.getElementById(\"email\").focus();'"; ?>>
    <a href="https://github.com/blha303/puushproxy-browse">
      <img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub">
    </a>
<?php

if (isset($_POST['email']) && isset($_POST['pass'])) {

  if (!isset($_POST['width'])) {
    $width = 800 / 100;
  } else {
    $width = (int) ($_POST['width'] / 100);
  }

  $m = new MongoClient();

  $db = $m->puush;

  $userdb = $db->users;

  $users = $userdb->find(array("email" => $_POST['email']));
  foreach ($users as $user) {
    if (sha1($salt . $_POST['pass']) == $user['password']) {
      $theuser = $user;
    }
  }
  if (!$theuser) {
    die("Couldn't find a user matching that email/password combination.");
  }
// I have several puushproxy instances using one database with different urls, so
// I have to correct URLs manually here. If you can suggest a better solution,
// please open a ticket! https://github.com/blha303/puushproxy-browse/issues/new
  if ($theuser['email'] == "jophestus@jophest.us") {
    $domain = "i.jophest.us";
  } else {
    $domain = "with-you.pw";
  }

  $collection = $db->files;

  $cursor = $collection->find(array("owner" => $theuser['_id'])); ?>

  <table border=1>
    <tr>
<?php

  $x = 0;
  $newline = true;
  foreach($cursor as $document) {
    if ($x == $width) {
      echo "    </tr>".PHP_EOL."    <tr>".PHP_EOL;
      $x = 0;
    }
    if (substr($document['name'], 0, 2) == "ss" && file_exists("upload/".$document['name'])) { ?>
      <td>
        <a href='http://<?php echo $domain; ?>/<?php echo $document['shortname']; ?>'>
          <center><img src='imagethumb.php?s=upload/<?php echo $document['name']; ?>&w=100' class='thumb'></center>
        </a>
      </td>
<?php
      $x += 1;
    }
    echo $out;
  }
} else {
?><form method="POST">
  <input type='text' name='email' id='email'><br>
  <input type='password' name='pass' id='pass'><br>
  <input type='hidden' id='width' name='width' value=''>
  <input type='submit'>
</form><?php
} ?>
  </body>
</html>
