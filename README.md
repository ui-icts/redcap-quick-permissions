## Quick Permissions

### Description
The Quick Permissions module allows for the quick assignment of User Rights to a REDCap project. Common configurations can be saved as presets for even faster assignment.

### Basic Usage
After downloading and enabling this module on your REDCap instance, a link to Quick Permissions will appear at the bottom of the Control Center sidebar.

The "Select Project" dropdown will default to the most recently created project, with the 9 most recent after that available for selection. If you would like to update an older project, the "Other" option will allow a PID to be manually entered.

No validation is done on the "Enter Username" field, so be sure it is correct before submitting.

### Permissions Presets
There are 3 built-in presets that can be used for quick rights assignment:

* **None:** All boxes unchecked, Data Exports set to "No access", and Lock/Unlock Records set to "Disabled"
* **All (No Mobile App/API):** All boxes checked (except those pertaining to Mobile App and API), Data Exports set to "Full data set", and Lock/Unlock Records set to "Locking/Unlocking with E-signature authority"
* **Minimal Data Entry:** "Create records" checked, Data Exports set to "No access", and Lock/Unlock Records set to "Disabled"

Custom presets can be saved by entering a name and clicking the "Save Custom Preset" button. This preset name will be added to the saved list and automatically selected. A "Delete" button will appear if a custom preset is selected (built-in presets can not be deleted).

If the Quick Projects module is also installed, any custom user rights presets configured in this module will be available for use in Quick Projects as well.