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

## Pages
### Page 1
* Database name
* Database user
* Database pass
* Database host

#### Process
* Check settings work
* Write Database details, Create a connection to cfg
* Generate and write Security settings to cfg
* Generate and write permissions to cfg
* Generate and write user types
* Write debug false to cfg
* Import blank database

### Page 2
* Site URL - Automatic
* Site Root - Automatic
* Site Docs

#### Process
* Check Site Docs folder exists and is writeable
* Append Structure to cfg

### Page 3
* Mail server 
* Mail from
* Mail host
* Mail Port
* Mail user
* Mail pass

#### Process
* Check mail settings
* Append mail settings to cfg

### Page 4
* Create the master user account

#### Process
* Check account details are valid
* Add account to database
* Send activation email

### Page 5
* Show completion message 
* Add link to delete the install file and direct to homepage

#### Process
* unset install file
* direct to SITE_URL