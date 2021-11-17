#!/bin/sh 


echo "\nEnabling gbifstats module for $1"
drush -y @$1 en gbifstats


echo "\nChecking INITIAL permissions"
drush @$1 role:list |grep ":$\|GBIF"

echo "\nRemoving permissions"
drush @$1 role-remove-perm anonymous       'configure GBIF Stats'

echo "\nAdding permissions"
drush @$1 role-add-perm anonymous       'view GBIF Stats'
drush @$1 role-add-perm authenticated   'view GBIF Stats'
drush @$1 role-add-perm site_manager    'configure GBIF Stats'
drush @$1 role-add-perm site_manager    'generate GBIF Stats'
drush @$1 role-add-perm content_manager 'generate GBIF Stats'

echo "\n\nChecking FINAL permissions"
drush @$1 role:list |grep ":$\|GBIF"

echo "\n\nALL SET for $1"
