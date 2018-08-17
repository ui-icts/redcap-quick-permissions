<?php

require_once APP_PATH_DOCROOT . 'ControlCenter/header.php';

$quickPermissions = new \UIOWA\QuickPermissions\QuickPermissions();
$quickPermissions->displayPermissionsPage();