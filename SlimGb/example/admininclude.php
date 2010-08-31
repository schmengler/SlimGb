<?php
require_once '../SlimGb.inc.php';
$gb = new SlimGb();
$gb->initAdmin();

?>
<html>
<body>

<?php echo $gb->showAdmin(); ?>

</body>
</html>
