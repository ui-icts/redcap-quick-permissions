<?php
namespace UIOWA\QuickPermissions;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

class QuickPermissions extends AbstractExternalModule {
    public static $apiUrl = APP_PATH_WEBROOT_FULL . 'api/';

    public function updateUserRights()
    {
        if (isset($_POST['submit'])) {

            $pid = (isset($_POST['otherPid']) ? $_POST['otherPid'] : $_POST['pid']);
            $username = $_POST['username'];

            $design = (isset($_POST['design']) == '1' ? 1 : 0);
            $user_rights = (isset($_POST['user_rights']) == '1' ? 1 : 0);
            $data_access_groups = (isset($_POST['data_access_groups']) == '1' ? 1 : 0);

            $data_export = (isset($_POST['data_export']) == '1' ? 1 : 0);
            $reports = (isset($_POST['reports']) == '1' ? 1 : 0);
            $stats_and_charts = (isset($_POST['stats_and_charts']) == '1' ? 1 : 0);

            $calendar = (isset($_POST['calendar']) == '1' ? 1 : 0);
            $data_import_tool = (isset($_POST['data_import_tool']) == '1' ? 1 : 0);
            $data_comparison_tool = (isset($_POST['data_comparison_tool']) == '1' ? 1 : 0);
            $logging = (isset($_POST['logging']) == '1' ? 1 : 0);
            $file_repository = (isset($_POST['file_repository']) == '1' ? 1 : 0);

            $data_quality_create = (isset($_POST['data_quality_create']) == '1' ? 1 : 0);
            $data_quality_execute = (isset($_POST['data_quality_execute']) == '1' ? 1 : 0);

            $record_create = (isset($_POST['record_create']) == '1' ? 1 : 0);
            $record_rename = (isset($_POST['record_rename']) == '1' ? 1 : 0);
            $record_delete = (isset($_POST['record_delete']) == '1' ? 1 : 0);

            $lock_record_customize = (isset($_POST['lock_records_customization']) == '1' ? 1 : 0);
            $lock_record = (isset($_POST['lock_records']) == '1' ? 1 : 0);
            $lock_record_multiform = (isset($_POST['lock_records_all_forms']) == '1' ? 1 : 0);

            $api_export = (isset($_POST['api_export']) == '1' ? 1 : 0);
            $api_import = (isset($_POST['api_import']) == '1' ? 1 : 0);

            $mobile_app = (isset($_POST['mobile_app']) == '1' ? 1 : 0);
            $mobile_app_download_data = (isset($_POST['mobile_app_download_data']) == '1' ? 1 : 0);

            $sql = "INSERT INTO redcap_user_rights (
           project_id, username, design, user_rights, data_access_groups, data_export_tool, reports, graphical, calendar, data_import_tool, data_comparison_tool,
           data_logging, file_repository, data_quality_design, data_quality_execute, record_create, record_rename, record_delete, lock_record_customize, lock_record, lock_record_multiform, api_export, api_import, mobile_app, mobile_app_download_data)
           VALUES ($pid, '$username', $design, $user_rights, $data_access_groups, $data_export, $reports, $stats_and_charts, $calendar, $data_import_tool, $data_comparison_tool, $logging, $file_repository, $data_quality_create, $data_quality_execute, $record_create, $record_rename, $record_delete, $lock_record_customize, $lock_record, $lock_record_multiform, $api_export, $api_import, $mobile_app, $mobile_app_download_data)";

            $sql_Insert = db_query($sql);

            $urlString =
                sprintf("https://%s%sUserRights/index.php?pid=%d",  // User Rights page
                    SERVER_NAME,
                    APP_PATH_WEBROOT,
                    $pid);

            if ($sql_Insert) {
                \REDCap::logEvent("Created User\n<font color=\"#000066\">(Quick Permissions)</font>",'user = \'' . $username . '\'',NULL,NULL,NULL,$pid);

                $this->returnResultMessage("User " . $_POST['username'] . " successfully added to project.", $urlString);
            }
            elseif (db_error()) {
                $this->returnResultMessage("Failed to add user! Make sure your PID is valid and this user hasn't already been added to the project.", null);
            }
            else {
                $this->returnResultMessage("UNKNOWN ERROR! Please report this issue to the module's developer.", null);
            }
        }

    }

    public function displayPermissionsPage()
    {
        session_start();

        $defaultPresetsJson = "{\"none\":{\"title\":\"None\",\"data\":{\"design\":\"0\",\"user_rights\":\"0\",\"data_access_groups\":\"0\",\"data_export\":\"0\",\"reports\":\"0\",\"stats_and_charts\":\"0\",\"calendar\":\"0\",\"data_import_tool\":\"0\",\"data_comparison_tool\":\"0\",\"logging\":\"0\",\"file_repository\":\"0\",\"data_quality_create\":\"0\",\"data_quality_execute\":\"0\",\"record_create\":\"0\",\"record_rename\":\"0\",\"record_delete\":\"0\",\"lock_records_all_forms\":\"0\",\"lock_records\":\"0\",\"lock_records_customization\":\"0\"}},\"all\":{\"title\":\"All (No Mobile App/API)\",\"data\":{\"design\":\"1\",\"user_rights\":\"1\",\"data_access_groups\":\"1\",\"data_export\":\"1\",\"reports\":\"1\",\"stats_and_charts\":\"1\",\"calendar\":\"1\",\"data_import_tool\":\"1\",\"data_comparison_tool\":\"1\",\"logging\":\"1\",\"file_repository\":\"1\",\"data_quality_create\":\"1\",\"data_quality_execute\":\"1\",\"record_create\":\"1\",\"record_rename\":\"1\",\"record_delete\":\"1\",\"lock_records_all_forms\":\"1\",\"lock_records\":\"2\",\"lock_records_customization\":\"1\"}},\"minimal-data-entry\":{\"title\":\"Minimal Data Entry\",\"data\":{\"design\":\"0\",\"user_rights\":\"0\",\"data_access_groups\":\"0\",\"data_export\":\"0\",\"reports\":\"0\",\"stats_and_charts\":\"0\",\"calendar\":\"0\",\"data_import_tool\":\"0\",\"data_comparison_tool\":\"0\",\"logging\":\"0\",\"file_repository\":\"0\",\"data_quality_create\":\"0\",\"data_quality_execute\":\"0\",\"record_create\":\"1\",\"record_rename\":\"0\",\"record_delete\":\"0\",\"lock_records_all_forms\":\"0\",\"lock_records\":\"0\",\"lock_records_customization\":\"0\"}}}";

        if (!self::getSystemSetting('presets')) {
            self::setSystemSetting('presets', $defaultPresetsJson);
        }

        $presets = json_decode(self::getSystemSetting('presets'), true);

        $sql = "SELECT project_id, app_title FROM redcap_projects ORDER BY project_id DESC LIMIT 10";

        $sql = db_query($sql);
        $recentProjects = [];

        while ($row = db_fetch_assoc($sql)) {
            array_push($recentProjects, $row);
        }

        ?>
        <script>
            var permissionsLookup = <?= self::getSystemSetting('presets') ?>;
            var defaultPermissionsLookup = <?= $defaultPresetsJson ?>;
            var presetNames = Object.keys(permissionsLookup);
            var defaultPresetNames = Object.keys(defaultPermissionsLookup);
            var savePresetUrl = "<?= $this->getUrl("savePresets.php") ?>";
        </script>
        <script src="<?= $this->getUrl("QuickPermissions.js") ?>"></script>


        <h4>Quick Permissions</h4>

        <form name="permissions" action="<?= $this->getUrl("requestHandler.php") ?>" method="POST">

            <br/>
            <label for="pid">Select Project: </label>
            <select id="pid" name="pid" onchange="showOtherPid(this.value)">
                <?php
                foreach($recentProjects as $key => $value):
                    echo '<option value="' . $value['project_id'] . '">' . $value['project_id'] . ' - ' . $value['app_title'] . '</option>';
                endforeach;
                ?>
                <option value="other">Other</option>
            </select>
            <label id="otherPidLabel" for="otherPid" style="display:none">Enter PID: </label>
            <input id="otherPid" name="otherPid" style="display:none" disabled="disabled" required>
            <br />
            <label for="username">Enter Username: </label>
            <input id="username" type="text" name="username" value="" required><br/>
            <label for="quickPermissions">Permissions Preset:</label>
            <select id="quickPermissions" name="ignore" onchange="loadPermissionPreset(this)">
                <?php
                foreach($presets as $key => $value):
                    echo '<option value="' . $key . '">' . $value['title'] . '</option>';
                endforeach;
                ?>
            </select><button id="deletePreset" style="display: none" type="button" onclick="removePreset()">Delete</button>
            <br />
            <input id="newPresetTitle" name="ignore" type="text" onchange="setSavePresetButtonState(this)">
            <button id='addPreset' type="button" onclick="savePreset(this)">Save Custom Preset</button>
            <br/>
            <br/>

            <b><u>Highest Level Privileges:</u></b><br/>
            <input id="design" type="checkbox" name="design" value="1" > Project Design and Setup

            <input id="user_rights" type="checkbox" name="user_rights" value="1" > User Rights

            <input id="data_access_groups" type="checkbox" name="data_access_groups" value="1" > Data Access
            Groups<br/>

            <b><u>Data Exports:</u></b><br/>
            <input id="no_access" type="radio" name="data_export" checked="checked" value="0" > No access
            <input id="de-identified" type="radio" name="data_export" value="2" > De-identified
            <input id="remove_tagged_id_fields" type="radio" name="data_export" value="3" > Remove all
            tagged identifier fields
            <input id="full_data_set" type="radio" name="data_export" value="1" > Full data set
            <br/>

            <input id="reports" type="checkbox" name="reports" value="1" > Add/Edit Reports<br/>
            <input id="stats_and_charts" type="checkbox" name="stats_and_charts" value="1" > Stats and charts<br/>

            <b><u>Other Privileges:</u></b><br/>
            <input id="calendar" type="checkbox" name="calendar" value="1" > Calendar
            <input id="data_import_tool" type="checkbox" name="data_import_tool" value="1" > Data import
            tool
            <input id="data_comparison_tool" type="checkbox" name="data_comparison_tool" value="1" > Data
            comparison tool
            <input id="logging" type="checkbox" name="logging" value="1" > Logging
            <input id="file_repository" type="checkbox" name="file_repository" value="1" > File
            Repository<br/>

            <b><u>Data Quality:</u></b><br/>
            <input id="data_quality_create" type="checkbox" name="data_quality_create" value="1" > Data
            Quality Create
            <input id="data_quality_execute" type="checkbox" name="data_quality_execute" value="1" > Data
            Quality Execute<br/>

            <b><u>Project record settings</u></b><br/>
            <input id="record_create" type="checkbox" name="record_create" value="1" > Create records
            <input id="record_rename" type="checkbox" name="record_rename" value="1" > Rename records
            <input id="record_delete" type="checkbox" name="record_delete" value="1" > Delete records
            <br/>

            <b><u>Record locking and E-signatures:</u></b><br/>
            <input id="lock_records_all_forms" type="checkbox" name="lock_records_all_forms" value="1" > Record
            locking customization <br/>
            <input id="disabled" type="radio" name="lock_records"  checked="checked" value="0" > Disabled
            <input id="lock_records" type="radio" name="lock_records" value="1" > Locking/Unlocking
            <input id="lock_records_esig" type="radio" name="lock_records" value="2" > Locking/Unlocking with
            E-signature authority<br/>
            <input id="lock_records_customization" type="checkbox" name="lock_records_customization" value="1" > Allow
            locking of all forms at once for a given record<br/>
            <br/>
             <b><u>Mobile App:</u></b><br/>
             <input id="mobile_app" type="checkbox" name="mobile_app" > Data collection in mobile app
             <input id="mobile_app_download_data" type="checkbox" name="mobile_app_download_data" > Download data<br/>
             <b><u>API:</u></b><br/>
             <input id="api_export" type="checkbox" name="api_export" > API Export
             <input id="api_import" type="checkbox" name="api_import" > API Import/Update<br/>
            <br/>
            <br/>

            <input type="hidden" id="redirect" name="redirect" value=" ">
            <button id="submit" type="submit" name="submit" value="submit">Add User</button>
        </form>

        <script>
            document.getElementById("redirect").value = window.location.href;

            <?php
            if(isset($_SESSION['result'])){ //check if form was submitted
                $returnValue = $_SESSION['result'];
                $redirectUrl = $_SESSION['redirectUrl'];
                unset($_SESSION['result']);
                unset($_SESSION['redirectUrl']);

                echo "confirmRedirect(\"$returnValue\", \"$redirectUrl\");";
            }
            ?>
        </script>
        <?php
    }

    public function savePresets($newPresetJson) {
        self::setSystemSetting('presets', $newPresetJson);
    }

    public function returnResultMessage($message, $url) {

        if ($_POST['redirect']) {
            $_SESSION['result'] = $message;
            $_SESSION['redirectUrl'] = $url;
            $redirect = $_POST['redirect'];
            unset($_POST['redirect']);

            header("Location: $redirect");
        }
    }

    public function redcapApiCall($data, $outputFlag) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        $output = curl_exec($ch);

        curl_close($ch);

        if ($outputFlag) {
            echo $output;
        }
        else {
            return $output;
        }
    }
}

































