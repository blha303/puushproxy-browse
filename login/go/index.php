<?php
if ($_GET) {
  $query = "?".http_build_query($_GET);
} else {
  $query = "";
}
header("Location: " . (empty($_SERVER['HTTPS']) || strcmp($_SERVER['HTTPS'], "off")) ? "http" : "https" . "://".$_SERVER['HTTP_HOST']."/".$query);
