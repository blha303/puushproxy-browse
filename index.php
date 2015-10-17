<?php
include('config.php');
if (isset($_POST['clearcookies']) && isset($_COOKIE['apikey'])) {
  unset($_COOKIE['apikey']);
  setcookie('apikey', null, -1, '/');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
    <link rel="shortcut icon" href="http://<?php echo $domain; ?>/favicon.ico" />
    <style>
      img.thumb {
        width: auto;
        max-height: 100;
      }
      img.icon {
        width: 100;
        height: 100;
      }
      body {
        background-color: #000000;
        color: #FFFFFF;
      }
      table {
        background-color: #cccccc;
      }
      a.text {
        text-decoration:none;
        color: black;
        font-family: consolas, monospace;
        font-size: 12px;
      }
    </style>
    <title>puush browse</title>
  </head>
<?php
if ((isset($_POST['email']) && isset($_POST['password'])) || isset($_GET['k'])) {
  $m = new MongoClient();

  $db = $m->puush;

  $userdb = $db->users;

  if (isset($_POST['email'])) {
    $users = $userdb->find(array("email" => $_POST['email']));
    foreach ($users as $user) {
      if (sha1($salt . $_POST['password']) == $user['password']) {
        $theuser = $user;
      }
    }
  } else if (isset($_GET['k'])) {
    $users = $userdb->find(array("apiKey" => $_GET['k']));
    foreach ($users as $user) {
      $theuser = $user;
    }
  }
  if (!$theuser) {
    die("Couldn't find a user matching that email/password combination.");
  }
  foreach ($domains as $key => $value) {
    if ($theuser['apiKey'] == $key) {
      $domain = $value;
    }
  }
  if (!isset($domain)) $domain = $defaultpuushurl;

  setcookie("apikey", $theuser['apiKey'], time() + (10 * 365 * 24 * 60 * 60)); // add user's api key to cookies for ten years

  $collection = $db->files;

  $cursor = $collection->find(array("owner" => $theuser['_id']));
 ?>

<body>
  <a href="thumbs.php">Thumbnails</a>
  <form method="POST" action="<?php echo $defaultpuushurl; ?>/api/up" enctype="multipart/form-data" id="upload">
    <label for="f">Upload:</label><input type="file" name="f" id="f"><input type="button" value="Submit" onclick="uploadsubm()">
    <input type="hidden" name="z" value="poop"><input type="hidden" name="k" value="<?php echo $theuser['apiKey']; ?>">
    <span id="uploadout">Make sure you allow popups.</span>
  </form>
  <ul>
<?php

  $x = 0;
  $newline = true;
  foreach(array_reverse(iterator_to_array($cursor)) as $document) {
    $ext = pathinfo($document['name'], PATHINFO_EXTENSION);
    if (file_exists("upload/".$document['name'])) { ?>
      <li>
        <a href='<?php echo $domain; ?>/<?php echo $document['shortname'].".".$ext; ?>' target="_blank" class="fancybox">
          <?php echo $document['name']; ?>
        </a>
      </li>
<?php
    }
  } ?>
  </ul>
  <script type="text/javascript">
// http://hayageek.com/jquery-ajax-form-submit/
function getDoc(frame) {
     var doc = null;
 
     // IE8 cascading access check
     try {
         if (frame.contentWindow) {
             doc = frame.contentWindow.document;
         }
     } catch(err) {
     }
 
     if (doc) { // successful getting content
         return doc;
     }
 
     try { // simply checking may throw in ie8 under ssl or mismatched protocol
         doc = frame.contentDocument ? frame.contentDocument : frame.document;
     } catch(err) {
         // last attempt
         doc = frame.document;
     }
     return doc;
 }
function uploadsubm() {
$("#upload").submit(function(e) {
    var formObj = $(this);
    var formURL = formObj.attr("action");

    if(window.FormData !== undefined) {
        var formData = new FormData(this);
        $.ajax({
            url: formURL,
            type: 'POST',
            data:  formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            success: function(data, textStatus, jqXHR) {
		$("#uploadout").text(data.split(",")[1]);
                window.open(data.split(",")[1]);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#uploadout").text("Upload failed");
            }
       });
        e.preventDefault();
        e.unbind();
   }
   else {
        var  iframeId = 'unique' + (new Date().getTime());
        var iframe = $('<iframe src="javascript:false;" name="'+iframeId+'" />');
        iframe.hide();
        formObj.attr('target',iframeId);
        iframe.appendTo('body');
        iframe.load(function(e)
        {
            var doc = getDoc(iframe[0]);
            var docRoot = doc.body ? doc.body : doc.documentElement;
            var data = docRoot.innerHTML;
            $("#uploadout").text(data.split(",")[1]);
            window.open(data.split(",")[1]);
        });
    }
});
$("#upload").submit();
}

$("#register").on("click", function(e){
  $("#login").attr('action', '<?php echo $defaultpuushurl; ?>/register');
  $("#login").submit();
}
  </script>
  <script type="text/javascript" src="fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
  <link rel="stylesheet" href="fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  <script type="text/javascript" src="fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>

  <link rel="stylesheet" href="fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
  <script type="text/javascript" src="fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <script type="text/javascript" src="fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

  <link rel="stylesheet" href="fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
  <script type="text/javascript" src="fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
  <script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
  </script>
<?php } else {
?>
<body onLoad='document.getElementById("email").focus();'>
<a href="thumbs.php">Thumbnails</a>
<form id="login" method="POST">
  <label for="email">Email</label>
  <input type='text' name='email' id='email'><br>
  <label for="password">Password</label>
  <input type='password' name='password' id='password'><br>
  <input type='submit' value="Login">
  <input type='button' id="register" value="Register">
</form>
or
<form method="GET">
  <label for="apikey">API key</label>
  <input type='text' name='k' id="apikey" width=64<?php if (isset($_COOKIE['apikey'])) { echo " value='" . $_COOKIE['apikey'] . "'"; } ?>><br>
  <input type='submit'>
</form>
<br>
Upload:
<form method="POST" action="<?php echo $defaultpuushurl; ?>/api/up" enctype="multipart/form-data">
<input type="hidden" name="z" value="poop">
<input type="text" name="k" placeholder="API key"<?php if (isset($_COOKIE['apikey'])) { echo " value='" . $_COOKIE['apikey'] . "'"; } ?>><br>
<input type="file" name="f" id="f"><br>
<input type="submit">
</form>
Clear cookies:
<form method="POST">
<input type="submit" name="clearcookies" value="Clear API key">
</form>
Source: <a href="https://github.com/blha303/puushproxy-browse">github.com/blha303/puushproxy-browse</a><br>
Icons: <a href="https://github.com/teambox/Free-file-icons">Teambox Free icon set</a><?php
} ?>
  </body>
</html>
