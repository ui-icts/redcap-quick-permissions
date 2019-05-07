$(document).ready(function(){
    var presets = UIOWA_QuickPermissions.presetsJson;

    $(
        '<select id=\'quickPermissions\'>' +
        '<option value=\'\'>---Select Preset---</option>' +
        '</select>'
    )
        .insertAfter( $('#new_username') );

    $.each(presets, function(key, value) {
        console.log(value);

        if (!value['data']['assign_manual_role']) {
            $('#quickPermissions').append('<option value=\'' + key + '\'>' + value['title'] + '</option>')
        }
    });

    $(
        $('#addUserBtn')
            .clone()
            .prop({ id: 'quickAddBtn' })
            .html('Add with Quick Permissions')
            .hide()
    )
        .insertAfter( $('#addUserBtn') );

    $('#quickPermissions').change(function () {
        var addUserBtn = $('#addUserBtn');
        var quickAddBtn = $('#quickAddBtn');

        if ($(this).val() != '') {
            addUserBtn.hide();
            quickAddBtn.show();
        }
        else {
            addUserBtn.show();
            quickAddBtn.hide();
        }
    });

    $('#quickAddBtn').click(function () {
        var data = presets[$('#quickPermissions').val()]['data'];

        data['username'] = $('#new_username').val();
        data['pid'] = UIOWA_QuickPermissions.pid;
        data['submit'] = 'UserRights';

        $.ajax({
            method: 'POST',
            url: UIOWA_QuickPermissions.requestUrl,
            dataType: 'json',
            data: data,
            success: function() {
                location = location;
            }
        })

    });

    // todo quick delete column?
    //var header = $('#user_rights_roles_table').find('th').first();
    //var newHeader = header.clone();
    //newHeader.insertBefore(header);
    //
    //$('#table-user_rights_roles_table > tbody > tr').each(function(index, value) {
    //    newHeader.insertBefore(value);
    //})
});