# Install script process
## Gather information

### User input
* Database name
* Database user
* Database pass
* Database host
* email from
* date format
* mail server
* mail host
* mail port
* mail user
* mail pass

### Automatic
* site url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
* site root = getcwd();
* site docs = getcwd() . 'documents/'; (default, should be changed outside webroot)
* office app - https://stackoverflow.com/questions/12424787/how-to-check-if-a-shell-command-exists-from-php
* rewrite - in_array('mod_rewrite', apache_get_modules())
* file_types - (create default list)
* account permissions - (create default list)
* user types - (create default list)
* debug - false