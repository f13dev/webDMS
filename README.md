# webDMS
A simple Document Management System designed for home users who do not need the advanced features found on bloated commercial sollutions.

## webDMS 0.4 requires:
1. A Linux web server (local or remote, a Windows server would reqire some modification)
2. PHP 7
3. MySQL/MariaDB or similar database system with availability for 1 database
4. libreoffice-headless is required for generating PDFs
  * sudo apt-get install libreoffice-headless (Debian/Ubuntu/Mint)
  * sudo yum install libreoffice-headless (Red Hat/Centos/Fedora)
5. Mod Rewrite if you wish to have tidier URLs

# Installation
1. Clone the repository to your server 
  * git clone https://github.com/f13dev/webDMS /path/to/webDMS
2. Create a folder outside the web root for docuuments (not accessible externally)
  * Ensure the folder is owned by www-root 
    * chown -R www-data:www-data /path/to/documents
  * Ensure the folder is writeable 
    * chmod -R 0644 /path/to/documents
3. Create a temporary directory outside the web root
  * Ensure the temporary folder is owned by www-data and is writeable
4. Until the install script has been created:
  * Create a database and dump webDMS.sql to it
  * Copy cfg.example.php to cfg.php
  * Edit cfg.php to enter your database, site settings and generate some random strings
  * Manually reate a user (via PHPMyAdmin or MySQL terminal)
    * Enter first and last name in plain text
    * Edit temp.secureEmail.php to add your email address, visit the page and copy the content to the email field (delete the file afterwards)
    * Leave the password blank for now, this will be reset later 
    * Generate a random user salt (this is unique to each user)
    * Set type as 0
  * Visit https://domain.com/webDMS and click "Reset your password"
  * Once the master account has been created, you can create new accounts from the admin panel

# Notes
## Default access levels 
| ID | Level       |
|----|-------------|
| 0  | Super admin |
| 1  | Organiser   |
| 2  | Editor      |
| 3  | Uploader    |
| 4  | Browser     |

## Default permissions 
|          | User     | Category | Folder   | Document |
|----------|----------|----------|----------|----------|
| Create   | 0        | 1        | 1        | 3        |
| Edit     | 0        | ----     | 1        | 2        |
| Delete   | 0        | 1        | 1        | 2        |
| View     | 1        | 4        | 4        | 4        |

# Documents folder
* Create a folder outside web root such as /home/name/docs
* chmod the folder as 0777 to enable write access 
* ensure www-data group has read/write access to the folder
* Update SITE_DOCS in cfg.php to reflect the new folder


# Features left to implement
* Installation script
* Documentation

# Feature list done
* User edit
* User creation
* User deletion
* User table
* Add Access level checks to the required pages - i.e. disallow delete file from level 3 or 4
* Remove links depending on access level - i.e. hide file delete link for level 3 or 4
* Reset password via email
* Manage details
* Delete folder
* Delete category
* Permenently delete from recycle bin
* Restore from recycle bin
* Recycling bin
* Edit folder
* New file
* Edit file

# To do
* check if session CSRF token isn't set, if so direct to login with error message rather than die
* Code tidying
* Commenting
* Move reusable code to classes or functions