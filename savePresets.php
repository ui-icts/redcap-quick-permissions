<?php

$module = new UIOWA\QuickPermissions\QuickPermissions();
$newPresetJson = file_get_contents('php://input');
$module->savePresets($newPresetJson);