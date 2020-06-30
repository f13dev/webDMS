# webDMS
New repo for 0.4b

# Notes
## Default access levels 
* 0 = Super Admin
* 1 = Organiser
* 2 = Editor
* 3 = Uploader
* 4 = Browser
## Default permissions 
### User 
* Create = 0
* Delete = 0
* Edit = 0
* View = 1
### Category
* Create = 1
* Delete = 1
### Folder
* Create = 1
* Delete = 1
* Edit = 1
### Document
* Create = 3
* Delete = 2
* Edit = 2

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