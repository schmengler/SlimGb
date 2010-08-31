<?php
header('Content-type:text/html;charset=utf-8');
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);
date_default_timezone_set('Europe/Berlin');

require_once '../SlimGb.inc.php';
$gb = new SlimGb();
$gb->initGuestbook();
?>
<html>
<head>
<title>SlimGb Demo</title>
<link rel="stylesheet" href="../static/SlimGb.css">
</head>
<body>

<?php echo $gb->include_messages(); ?>

<?php echo $gb->include_form(); ?>

<?php echo $gb->include_pagination(); ?>

<?php echo $gb->include_entries(); ?>

<?php echo $gb->include_pagination(); ?>

</body>
</html>

