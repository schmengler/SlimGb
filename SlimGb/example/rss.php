<?php
// with rss plugin
require_once '../SlimGb.inc.php';
$gb = new SlimGb();
$gb = new SlimGb();
$gb->initGuestbook();
$gb->rss_entries(); // changes view parameters, injects new view in controller, returns controller->showEntries()