<?php

$module = new UIOWA\QuickPermissions\QuickPermissions();

if ($_REQUEST['type'] == 'updateUser') {
    $module->updateUserRights();
}
elseif ($_REQUEST['type'] == 'getUsers') {
    $module->getExistingUsers($_REQUEST['pid']);
}
elseif ($_REQUEST['type'] == 'getUserRights') {
    $module->getExistingUserRights($_REQUEST['pid'], $_REQUEST['username']);
}