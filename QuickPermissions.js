$(document).ready(function () {
    $('#username').autocomplete({
        source: app_path_webroot + "UserRights/search_user.php?ignoreExistingUsers=1&pid=" + document.getElementById('pid').value,
        minLength: 2,
        delay: 150,
        html: true,
        select: function (event, ui) {
            $(this).val(ui.item.value);
            return false;
        }
    });

    $[ "ui" ][ "autocomplete" ].prototype["_renderItem"] = function( ul, item) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( $( "<a></a>" ).html( item.label ) )
            .appendTo( ul );
    };
});

var UIOWA_QuickPermissions = {};

UIOWA_QuickPermissions.savePreset = function() {
    var elements = document.forms["permissions"].getElementsByTagName("input");
    var rights = {};
    var radioName = '';

    for (var i in elements) {
        var el = elements[i];

        if (el.value != undefined && el.value != '' && el.type != 'hidden' && el.name != radioName && el.name != 'ignore') {
            rights[el.name] = '0';

            if (el.checked) {
                rights[el.name] = el.value;

                if (el.type == 'radio') {
                    radioName = el.name;
                }
            }
        }
    }

    var input = document.getElementById('newPresetTitle');
    var strippedTitle = input.value.replace(/\W+/g, '-').toLowerCase();

    if (!presetNames.includes(strippedTitle) && strippedTitle != '') {
        presetNames.push(strippedTitle);

        permissionsLookup[strippedTitle] = {
            title: input.value,
            data: rights
        };

        var dropdown = document.getElementById('quickPermissions');
        var newOption = document.createElement("option");
        newOption.text = input.value;
        newOption.value = strippedTitle;
        dropdown.appendChild(newOption);
        dropdown.value = strippedTitle;
        input.value = '';
        document.getElementById("deletePreset").style.display = '';

        this.savePresets(permissionsLookup);
    }
    else {
        alert("This preset title is too similar to an existing preset. Please change it to a unique title before saving.");
    }
};

UIOWA_QuickPermissions.removePreset = function() {
    var dropdown = document.getElementById('quickPermissions');
    var valueToRemove = dropdown.value;

    var confirmDelete = confirm("Are you sure you want to delete this preset?");

    if (confirmDelete) {
        presetNames.splice(presetNames.indexOf(valueToRemove), 1);
        delete permissionsLookup[valueToRemove];
        dropdown.remove(dropdown.selectedIndex);

        this.loadPermissions(null);

        this.savePresets(permissionsLookup);
    }
};

UIOWA_QuickPermissions.loadPermissions = function(data) {
    if (data === null) {
        var select = document.getElementById('quickPermissions');
        var selectedValue = select.options[select.selectedIndex].value;

        if (selectedValue == '') {
            document.getElementById("deletePreset").style.display = 'none';
            return;
        }

        data = permissionsLookup[selectedValue]['data'];

        if (!defaultPresetNames.includes(selectedValue))
        {
            document.getElementById("deletePreset").style.display = '';
        }
        else
        {
            document.getElementById("deletePreset").style.display = 'none';
        }
    }

    var keys = Object.keys(data);

    for (var i in keys) {
        var key = keys[i];
        var el = document.getElementsByName(key);

        if (el[0].type == 'checkbox') {
            el[0].checked = data[key] == "1";
        }
        if (el[0].type == 'radio') {
            for (var j in el) {
                if (el[j].value == data[key]) {
                    el[j].checked = true;
                }
            }
        }
    }
};

UIOWA_QuickPermissions.savePresets = function(presetsData) {
    presetsData = JSON.stringify(presetsData);

    var request = new XMLHttpRequest();
    request.open("POST", savePresetUrl, true);
    request.setRequestHeader("Content-type", "application/json");
    request.send(presetsData);
};

UIOWA_QuickPermissions.getExistingUsers = function(pid) {
    var request = new XMLHttpRequest();
    request.open("POST", requestHandlerUrl, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send('type=getProjectUsers&pid=' + pid);

    request.onreadystatechange = function() {
        if (request.readyState === 4) {
            var usersDropdown = document.getElementById('existingUser');
            var users = JSON.parse(request.response);

            for (var i = usersDropdown.options.length - 1 ; i >= 0 ; i--)
            {
                usersDropdown.remove(i);
            }

            var initialOption = document.createElement("option");
            initialOption.text = '---Select---';
            initialOption.value = '';
            usersDropdown.appendChild(initialOption);

            for (var j in users) {
                var newOption = document.createElement("option");
                var labelStr = users[j]['username'];

                if (users[j]['user_firstname'] != null && users[j]['user_lastname'] != null) {
                    labelStr +=  ' (' + users[j]['user_firstname'] + ' ' + users[j]['user_lastname'] + ')';
                }

                newOption.text = labelStr;
                newOption.value = users[j]['username'];
                usersDropdown.appendChild(newOption);
                existingUsers.push(users[j]['username']);
            }
        }
    }
};

UIOWA_QuickPermissions.getUserList = function() {
    var request = new XMLHttpRequest();
    request.open("POST", requestHandlerUrl, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send('type=getUserList');

    request.onreadystatechange = function() {
        if (request.readyState === 4) {
            var userInfo = JSON.parse(request.response);

            $( function() {
                $( "#username" ).autocomplete({
                    source: userInfo
                });
            } );
            }
    }
};

UIOWA_QuickPermissions.getUserRights = function(pid, username) {
    var request = new XMLHttpRequest();
    request.open("POST", requestHandlerUrl, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send('type=getUserRights&pid=' + pid + '&username=' + username);

    request.onreadystatechange = function() {
        if (request.readyState === 4) {
            var userRights = JSON.parse(request.response);

            var supportedPermissions = [
                'design',
                'user_rights',
                'data_access_groups',
                'data_export_tool',
                'reports',
                'graphical',
                'calendar',
                'data_import_tool',
                'data_comparison_tool',
                'data_logging',
                'file_repository',
                'data_quality_design',
                'data_quality_execute',
                'record_create',
                'record_rename',
                'record_delete',
                'lock_record_customize',
                'lock_record',
                'lock_record_multiform',
                'api_export',
                'api_import',
                'mobile_app',
                'mobile_app_download_data',
                'email'
            ];

            for (var property in userRights) {
                if (supportedPermissions.indexOf(property) == -1) {
                    delete userRights[property];
                }
            }

            document.getElementById("email").checked = '';
            document.getElementById("email").disabled = 'disabled';

            UIOWA_QuickPermissions.loadPermissions(userRights);
        }
    }
};

UIOWA_QuickPermissions.newUserCheck = function (input) {
    var username = input.value;
    var emailCheckbox = document.getElementById("email");

    if (existingUsers.indexOf(username) != -1) {
        emailCheckbox.checked = '';
        emailCheckbox.disabled = 'disabled';
    }
    else {
        emailCheckbox.disabled = '';
    }


};

UIOWA_QuickPermissions.updatePid = function(value) {
    var pidInput = document.getElementById('pid');
    var pidLabel = document.getElementById('pidLabel');

    if (document.getElementById('pidSelect').value != 'other') {
        pidInput.style.display = 'none';
        pidLabel.style.display = 'none';

        document.getElementById('pid').value = document.getElementById('pidSelect').value;
    }
    else {
        pidInput.style.display = '';
        pidLabel.style.display = '';

        pidInput.disabled = '';

        document.getElementById('pid').value = value;
    }

    UIOWA_QuickPermissions.getExistingUsers(document.getElementById('pid').value);
};

UIOWA_QuickPermissions.confirmRedirect = function(message, url) {
    if (url != '') {
        var confirmed = confirm(message + '\n\nClick OK to view User Rights page or Cancel to stay on this page.');
    }
    else {
        alert(message);
    }

    if (confirmed) {
        window.location.href = url;
    }
};

UIOWA_QuickPermissions.setSavePresetButtonState = function(input) {
    var button = document.getElementById('addPreset');

    if (input.value == '') {
        button.disabled = 'disabled'
    }
    else {
        button.disabled = ''
    }
};