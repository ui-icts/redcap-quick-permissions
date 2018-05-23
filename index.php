<?php

require_once APP_PATH_DOCROOT . 'ControlCenter/header.php';

$quickProjects = new \UIOWA\QuickPermissions\QuickPermissions();
$quickProjects->displayPermissionsPage();