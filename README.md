# webDMS
New repo for 0.4b


# Notes
## Access levels 
* 4 = read only
* 3 = 4 + upload documents
* 2 = 3 + delete documents, folders and categories
* 1 = 2 + create and delete users
* 0 = 1 + master admin, cannot be deleted


# Documents folder
* Create a folder outside web root such as /home/name/docs
* chmod the folder as 0777 to enable write access 
* ensure www-data group has read/write access to the folder
* Update SITE_DOCS in cfg.php to reflect the new folder


# Features left to implement
* New file
* Edit file
* Recycling bin
* Delete file
* Delete folder
* Delete category
* Manage details
* Email notifications
* Reset password via email
* Add Access level checks to the required pages - i.e. disallow delete file from level 3 or 4
* Remove links depending on access level - i.e. hide file delete link for level 3 or 4
* User management
* User creation
* Installation script
* Documentation

# Feature list done
* Edit folder