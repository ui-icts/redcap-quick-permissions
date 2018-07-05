<?php
namespace UIOWA\QuickPermissions;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

class QuickPermissions extends AbstractExternalModule {
    public static $apiUrl = APP_PATH_WEBROOT_FULL . 'api/';

    public function updateUserRights()
    {
        global $conn;
        if (!isset($conn))
        {
            db_connect(false);
        }

        if (isset($_POST['submit'])) {

            $pid = (isset($_POST['pid']) ? $_POST['pid'] : $_POST['pid']);
            $username = db_real_escape_string($_POST['username']);

            if(preg_match('~[^A-Za-z0-9\-._]~', $username)) {
                $this->returnResultMessage("ERROR: User names can only contain letters, numbers, underscores, hyphens, and periods.", null);
                exit;
            }

            $design = (isset($_POST['design']) == '1' ? 1 : 0);
            $user_rights = (isset($_POST['user_rights']) == '1' ? 1 : 0);
            $data_access_groups = (isset($_POST['data_access_groups']) == '1' ? 1 : 0);

            $data_export_tool = (isset($_POST['data_export_tool']) == '1' ? $_POST['data_export_tool'] : 0);
            $reports = (isset($_POST['reports']) == '1' ? 1 : 0);
            $graphical = (isset($_POST['graphical']) == '1' ? 1 : 0);

            $calendar = (isset($_POST['calendar']) == '1' ? 1 : 0);
            $data_import_tool = (isset($_POST['data_import_tool']) == '1' ? 1 : 0);
            $data_comparison_tool = (isset($_POST['data_comparison_tool']) == '1' ? 1 : 0);
            $data_logging = (isset($_POST['data_logging']) == '1' ? 1 : 0);
            $file_repository = (isset($_POST['file_repository']) == '1' ? 1 : 0);

            $data_quality_design = (isset($_POST['data_quality_design']) == '1' ? 1 : 0);
            $data_quality_execute = (isset($_POST['data_quality_execute']) == '1' ? 1 : 0);

            $record_create = (isset($_POST['record_create']) == '1' ? 1 : 0);
            $record_rename = (isset($_POST['record_rename']) == '1' ? 1 : 0);
            $record_delete = (isset($_POST['record_delete']) == '1' ? 1 : 0);

            $lock_record_customize = (isset($_POST['lock_record_customize']) == '1' ? 1 : 0);
            $lock_record = (isset($_POST['lock_record']) == '1' ? $_POST['lock_record'] : 0);
            $lock_record_multiform = (isset($_POST['lock_record_multiform']) == '1' ? 1 : 0);

            $api_export = (isset($_POST['api_export']) == '1' ? 1 : 0);
            $api_import = (isset($_POST['api_import']) == '1' ? 1 : 0);

            $mobile_app = (isset($_POST['mobile_app']) == '1' ? 1 : 0);
            $mobile_app_download_data = (isset($_POST['mobile_app_download_data']) == '1' ? 1 : 0);

            $sql = "SELECT COUNT(username) AS 'count' FROM redcap_user_rights WHERE project_id = ? AND username = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is',
                $pid,
                $username);

            $stmt->execute();
            $userExists = $stmt->get_result();
            $stmt->close();

            $userExists = db_fetch_assoc($userExists)['count'];

            if ($userExists == 1) {
                $sql = "UPDATE redcap_user_rights SET
               design = ?,
               user_rights = ?,
               data_access_groups = ?,
               data_export_tool = ?,
               reports = ?,
               graphical = ?,
               calendar = ?,
               data_import_tool = ?,
               data_comparison_tool = ?,
               data_logging = ?,
               file_repository = ?,
               data_quality_design = ?,
               data_quality_execute = ?,
               record_create = ?,
               record_rename = ?,
               record_delete = ?,
               lock_record_customize = ?,
               lock_record = ?,
               lock_record_multiform = ?,
               api_export = ?,
               api_import = ?,
               mobile_app = ?,
               mobile_app_download_data = ?
               WHERE project_id = ? AND username = ?";
            }
            else if ($userExists == 0) {
                $sql = "INSERT INTO redcap_user_rights (
               design,
               user_rights,
               data_access_groups,
               data_export_tool,
               reports,
               graphical,
               calendar,
               data_import_tool,
               data_comparison_tool,
               data_logging,
               file_repository,
               data_quality_design,
               data_quality_execute,
               record_create,
               record_rename,
               record_delete,
               lock_record_customize,
               lock_record,
               lock_record_multiform,
               api_export,
               api_import,
               mobile_app,
               mobile_app_download_data,
               project_id,
               username)
           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiiiiiiiiiiiiiiiiiiiiiiis',
                $design,
                $user_rights,
                $data_access_groups,
                $data_export_tool,
                $reports,
                $graphical,
                $calendar,
                $data_import_tool,
                $data_comparison_tool,
                $data_logging,
                $file_repository,
                $data_quality_design,
                $data_quality_execute,
                $record_create,
                $record_rename,
                $record_delete,
                $lock_record_customize,
                $lock_record,
                $lock_record_multiform,
                $api_export,
                $api_import,
                $mobile_app,
                $mobile_app_download_data,
                $pid,
                $username);

            $stmt->execute();
            $success = $stmt->affected_rows;
            $stmt->close();

            $urlString =
                sprintf("https://%s%sUserRights/index.php?pid=%d",  // User Rights page
                    SERVER_NAME,
                    APP_PATH_WEBROOT,
                    $pid);

            if ($success >= 1) {
                if ($userExists == 0) {
                    \REDCap::logEvent("Created User\n<font color=\"#000066\">(Quick Permissions)</font>",'user = \'' . $username . '\'',NULL,NULL,NULL,$pid);

                    $this->returnResultMessage("User " . $_POST['username'] . " successfully added to project.", $urlString);
                }
                else if ($userExists == 1) {
                    \REDCap::logEvent("Updated User\n<font color=\"#000066\">(Quick Permissions)</font>",'user = \'' . $username . '\'',NULL,NULL,NULL,$pid);

                    $this->returnResultMessage("Existing user rights for " . $_POST['username'] . " successfully updated.", $urlString);
                }

            }
            elseif ($success == 0) {
                $this->returnResultMessage("User already has identical rights! No changes were applied.", null);
            }
            else {
                $this->returnResultMessage("ERROR! Please check that your PID is valid. If the problem persists, please report this issue to the module's developer.", null);
            }
        }

    }

    public function getExistingUsers($projectId)
    {
        global $conn;
        if (!isset($conn))
        {
            db_connect(false);
        }

        $sql = "SELECT
          rights.username,
          info.user_firstname,
          info.user_lastname
        FROM redcap_user_rights AS rights
        LEFT JOIN redcap_user_information AS info on rights.username = info.username
        WHERE rights.project_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $projectId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $users = [];

        while ($row = db_fetch_assoc($result)) {
            array_push($users, $row);
        }

        echo json_encode($users);
    }

    public function getExistingUserRights($projectId, $username)
    {
        global $conn;
        if (!isset($conn))
        {
            db_connect(false);
        }

        $sql = "SELECT * FROM redcap_user_rights WHERE redcap_user_rights.project_id = ? AND redcap_user_rights.username = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('is', $projectId, $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        echo json_encode(db_fetch_assoc($result));
    }

    public function displayPermissionsPage()
    {
        session_start();

        $defaultPresetsJson = "{\"none\":{\"title\":\"None\",\"data\":{\"design\":\"0\",\"user_rights\":\"0\",\"data_access_groups\":\"0\",\"data_export_tool\":\"0\",\"reports\":\"0\",\"graphical\":\"0\",\"calendar\":\"0\",\"data_import_tool\":\"0\",\"data_comparison_tool\":\"0\",\"data_logging\":\"0\",\"file_repository\":\"0\",\"data_quality_design\":\"0\",\"data_quality_execute\":\"0\",\"record_create\":\"0\",\"record_rename\":\"0\",\"record_delete\":\"0\",\"lock_record_multiform\":\"0\",\"lock_record\":\"0\",\"lock_record_customize\":\"0\",\"mobile_app\":\"0\",\"mobile_app_download_data\":\"0\",\"api_export\":\"0\",\"api_import\":\"0\"}},\"all\":{\"title\":\"All (No Mobile App/API)\",\"data\":{\"design\":\"1\",\"user_rights\":\"1\",\"data_access_groups\":\"1\",\"data_export_tool\":\"1\",\"reports\":\"1\",\"graphical\":\"1\",\"calendar\":\"1\",\"data_import_tool\":\"1\",\"data_comparison_tool\":\"1\",\"data_logging\":\"1\",\"file_repository\":\"1\",\"data_quality_design\":\"1\",\"data_quality_execute\":\"1\",\"record_create\":\"1\",\"record_rename\":\"1\",\"record_delete\":\"1\",\"lock_record_multiform\":\"1\",\"lock_record\":\"2\",\"lock_record_customize\":\"1\",\"mobile_app\":\"0\",\"mobile_app_download_data\":\"0\",\"api_export\":\"0\",\"api_import\":\"0\"}},\"minimal-data-entry\":{\"title\":\"REDCap Default\",\"data\":{\"design\":\"0\",\"user_rights\":\"0\",\"data_access_groups\":\"0\",\"data_export_tool\":\"2\",\"reports\":\"1\",\"graphical\":\"1\",\"calendar\":\"1\",\"data_import_tool\":\"0\",\"data_comparison_tool\":\"0\",\"data_logging\":\"0\",\"file_repository\":\"1\",\"data_quality_design\":\"0\",\"data_quality_execute\":\"0\",\"record_create\":\"1\",\"record_rename\":\"0\",\"record_delete\":\"0\",\"lock_record_multiform\":\"0\",\"lock_record\":\"0\",\"lock_record_customize\":\"0\",\"mobile_app\":\"0\",\"mobile_app_download_data\":\"0\",\"api_export\":\"0\",\"api_import\":\"0\"}}}";

        if (!self::getSystemSetting('presets')) {
            self::setSystemSetting('presets', $defaultPresetsJson);
        }

        $presets = json_decode(self::getSystemSetting('presets'), true);

        $sql = "SELECT
          project_id,
          app_title
        FROM redcap_projects AS projects
        LEFT JOIN redcap_user_information AS info ON projects.created_by = info.ui_id
        WHERE projects.status != 3 AND
          projects.date_deleted IS NULL AND
          info.username = '" . USERID . "'
        ORDER BY project_id DESC LIMIT 10";

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
            var requestHandlerUrl = "<?= $this->getUrl("requestHandler.php") ?>";
        </script>
        <script src="<?= $this->getUrl("QuickPermissions.js") ?>"></script>

        <h4>Quick Permissions</h4>

        <form name="permissions" action="<?= $this->getUrl("requestHandler.php?type=updateUser") ?>" method="POST">

            <br/>
            <label for="pidSelect">Select Project: </label>
            <select id="pidSelect" name="pidSelect" onchange="UIOWA_QuickPermissions.updatePid('')">
                <?php
                foreach($recentProjects as $key => $value):
                    echo '<option value="' . $value['project_id'] . '">' . $value['project_id'] . ' - ' . $value['app_title'] . '</option>';
                endforeach;
                ?>
                <option value="other">Other</option>
            </select>
            <label id="pidLabel" for="pid" style="display:none">Enter PID: </label>
            <input id="pid" name="pid" style="display:none" required>
            <br />
            <br />
            <label for="username">Enter Username: </label>
            <input id="username" type="text" name="username" value="" required><br/>
            -- OR --<br />
            <label for="existingUser">Load Existing User: </label>
            <select id="existingUser" name="ignore" onchange="document.getElementById('username').value =  this.value; UIOWA_QuickPermissions.getUserRights(document.getElementById('pid').value, this.value);"></select><br/><br/>
            <label for="quickPermissions">Permissions Preset:</label>
            <select id="quickPermissions" name="ignore" onchange="UIOWA_QuickPermissions.loadPermissions(null)">
                <option value="">---Select---</option>
                <?php
                foreach($presets as $key => $value):
                    echo '<option value="' . $key . '">' . $value['title'] . '</option>';
                endforeach;
                ?>
            </select><button id="deletePreset" style="display: none" type="button" onclick="UIOWA_QuickPermissions.removePreset()">Delete</button>
            <br />
            <input id="newPresetTitle" name="ignore" type="text" oninput="UIOWA_QuickPermissions.setSavePresetButtonState(this)">
            <button id='addPreset' type="button" onclick="UIOWA_QuickPermissions.savePreset(this)">Save Custom Preset</button>
            <br/>
            <br/>

            <b><u>Highest Level Privileges:</u></b><br/>
            <input id="design" type="checkbox" name="design" value="1" > Project Design and Setup

            <input id="user_rights" type="checkbox" name="user_rights" value="1" > User Rights

            <input id="data_access_groups" type="checkbox" name="data_access_groups" value="1" > Data Access
            Groups<br/>

            <b><u>Data Exports:</u></b><br/>
            <input id="no_access" type="radio" name="data_export_tool" checked="checked" value="0" > No access
            <input id="de-identified" type="radio" name="data_export_tool" value="2" > De-identified
            <input id="remove_tagged_id_fields" type="radio" name="data_export_tool" value="3" > Remove all
            tagged identifier fields
            <input id="full_data_set" type="radio" name="data_export_tool" value="1" > Full data set
            <br/>

            <input id="reports" type="checkbox" name="reports" value="1" > Add/Edit Reports<br/>
            <input id="graphical" type="checkbox" name="graphical" value="1" > Stats and charts<br/>

            <b><u>Other Privileges:</u></b><br/>
            <input id="calendar" type="checkbox" name="calendar" value="1" > Calendar
            <input id="data_import_tool" type="checkbox" name="data_import_tool" value="1" > Data import
            tool
            <input id="data_comparison_tool" type="checkbox" name="data_comparison_tool" value="1" > Data
            comparison tool
            <input id="data_logging" type="checkbox" name="data_logging" value="1" > Logging
            <input id="file_repository" type="checkbox" name="file_repository" value="1" > File
            Repository<br/>

            <b><u>Data Quality:</u></b><br/>
            <input id="data_quality_design" type="checkbox" name="data_quality_design" value="1" > Data
            Quality Create
            <input id="data_quality_execute" type="checkbox" name="data_quality_execute" value="1" > Data
            Quality Execute<br/>

            <b><u>Project record settings</u></b><br/>
            <input id="record_create" type="checkbox" name="record_create" value="1" > Create records
            <input id="record_rename" type="checkbox" name="record_rename" value="1" > Rename records
            <input id="record_delete" type="checkbox" name="record_delete" value="1" > Delete records
            <br/>

            <b><u>Record locking and E-signatures:</u></b><br/>
            <input id="lock_record_customize" type="checkbox" name="lock_record_customize" value="1" > Record
            locking customization <br/>
            <input id="disabled" type="radio" name="lock_record"  checked="checked" value="0" > Disabled
            <input id="lock_record" type="radio" name="lock_record" value="1" > Locking/Unlocking
            <input id="lock_record_esig" type="radio" name="lock_record" value="2" > Locking/Unlocking with
            E-signature authority<br/>
            <input id="lock_record_multiform" type="checkbox" name="lock_record_multiform" value="1" > Allow
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
            <button id="submit" type="submit" name="submit" value="submit">Add/Update User</button>
        </form>

        <script>
            document.getElementById("redirect").value = window.location.href;

            UIOWA_QuickPermissions.setSavePresetButtonState(document.getElementById('newPresetTitle'));
            UIOWA_QuickPermissions.updatePid(document.getElementById('pidSelect').value);
            UIOWA_QuickPermissions.getExistingUsers(document.getElementById('pid').value);

            <?php
            if(isset($_SESSION['result'])){ //check if form was submitted
                $returnValue = $_SESSION['result'];
                $redirectUrl = $_SESSION['redirectUrl'];
                unset($_SESSION['result']);
                unset($_SESSION['redirectUrl']);

                echo "UIOWA_QuickPermissions.confirmRedirect(\"$returnValue\", \"$redirectUrl\");";
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

































