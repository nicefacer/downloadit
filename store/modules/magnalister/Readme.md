Installation instructions magnalister
for Prestashop >= 1.5.3.0
System requirements

To verify, that all requirements are met by your server, copy the file magnalister_compatibility_check.php from the directory tools/ directory into the main directory of your shop. Then load the script in your browser (http://www.example.org/meinshop/magnalister_compatibility_check.php).

    Minimum requirements
        PHP Version 5
        MySQL Version 5
        Connecting to external Servers via PHP
    For optimal support:
        cURL-Library
        PHP Safe Mode disabled

Installation

    Copy magnalister.zip somewhere on your local computer.
    Go to your prestashop admin.
    Open your modules management from the admin top menu.
    On top right click "Add New Module".
    Upload magnalister.zip, then magnalister will appear in your modules list and you can install it.
    If you cannot upload the zip file, extract it on your local computer and upload the "magnalister" folder with all files in it into your <prestashop root>/modules/ folder.
    Then you will have magnalister in list of modules and you can install it.
    Give write permission to <prestashop root>/modules/magnalister/ (chmod 777 for linux servers).
    After installing, you should have an item "magnalister" in your admin menu.

