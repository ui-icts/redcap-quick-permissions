## Quick Permissions

### Description
The Quick Permissions module allows for the quick assignment of User Rights to a REDCap project. Common configurations can be saved as presets for even faster assignment.

### Basic Usage
After downloading and enabling this module on your REDCap instance, a link to Quick Permissions will appear at the bottom of the Control Center sidebar.

The "Select Project" dropdown will default to the most recently created project (by the currently logged in user), with the 9 most recent after that available for selection. If you would like to update an older project, the "Other" option will allow a PID to be manually entered.

The username whose rights should be updated can be manually entered in the "Enter Username" field or, if the user already has access to this project, can be selected from the "Load Existing User" dropdown and their current User Rights will be populated for reference (NOTE: This feature only works for the 10 most recent projects. Existing users will not be listed if an "Other" PID is provided).

After clicking the "Add/Update User" button, the module will display an alert message to report success or display an error message if the add/update failed. Successful operations are recorded in the project's logging (similar to normal User Rights modifications).

### Permissions Presets
There are 3 built-in presets that can be used for quick rights assignment:

* **None:** All boxes unchecked, Data Exports set to "No access", and Lock/Unlock Records set to "Disabled"
* **All (No Mobile App/API):** All boxes checked (except those pertaining to Mobile App and API), Data Exports set to "Full data set", and Lock/Unlock Records set to "Locking/Unlocking with E-signature authority"
* **REDCap Default:** Mirrors default options selected when using the "Add with custom rights" button on REDCap's built-in User Rights page

Custom presets can be saved by entering a name and clicking the "Save Custom Preset" button. This preset name will be added to the saved list and automatically selected. A "Delete" button will appear if a custom preset is selected (built-in presets can not be deleted).