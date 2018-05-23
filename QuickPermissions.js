function savePreset() {
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

        savePresets(permissionsLookup);
    }
    else {
        alert("This preset title is blank or too similar to an existing preset. Please change it to a unique title before saving.");
    }
}

function removePreset() {
    var dropdown = document.getElementById('quickPermissions');
    var valueToRemove = dropdown.value;

    var confirmDelete = confirm("Are you sure you want to delete this preset?");

    if (confirmDelete) {
        presetNames.splice(presetNames.indexOf(valueToRemove), 1);
        delete permissionsLookup[valueToRemove];
        dropdown.remove(dropdown.selectedIndex);

        loadPermissionPreset(dropdown);

        savePresets(permissionsLookup);
    }
}

function loadPermissionPreset(select) {
    var selectedValue = select.options[select.selectedIndex].value;

    var preset = permissionsLookup[selectedValue]['data'];
    var keys = Object.keys(preset);

    for (var i in keys) {
        var key = keys[i];
        var el = document.getElementsByName(key);

        if (el[0].type == 'checkbox') {
            el[0].checked = preset[key] == "1";
        }
        if (el[0].type == 'radio') {
            for (var j in el) {
                if (el[j].value == preset[key]) {
                    el[j].checked = true;
                }
            }
        }
    }

    if (!defaultPresetNames.includes(selectedValue))
    {
        document.getElementById("deletePreset").style.display = '';
    }
    else
    {
        document.getElementById("deletePreset").style.display = 'none';
    }
}

function savePresets(presetsData) {
    presetsData = JSON.stringify(presetsData);

    var request = new XMLHttpRequest();
    request.open("POST", savePresetUrl, true);
    request.setRequestHeader("Content-type", "application/json");
    request.send(presetsData);
}

function showOtherPid(value) {
    var pidInput = document.getElementById('otherPid');
    var pidLabel = document.getElementById('otherPidLabel');

    if (value == 'other') {
        pidInput.style.display = '';
        pidLabel.style.display = '';

        pidInput.disabled = '';
    }
    else {
        pidInput.style.display = 'none';
        pidLabel.style.display = 'none';

        pidInput.disabled = 'disabled';
    }
}

function confirmRedirect(message, url) {
    var confirmed = confirm(message + '\n\nClick OK to view User Rights page or Cancel to stay on this page.');

    if (confirmed) {
        window.location.href = url;
    }
}

function setSavePresetButtonState(input) {
    var button = document.getElementById('addPreset');

    if (input.value == '') {
        button.disabled = 'disabled'
    }
    else {
        button.disabled = ''
    }
}