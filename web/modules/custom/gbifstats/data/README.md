Ce dossier sert au stockages des fichiers de données.

Assurez-vous que le dossier soit ecrivable par Drupal.

Le dossier `data` doit avoir comme groupe `www-data:www-data`. 

Si ce n'est pas le cas, lancez la commande suivante :
`chown -R www-data: [URL_vers_le dossier_racine_du_module]/data`

---
---

This folder save the data file use by the module

It need to be writable by Drupal.

The `data` folder need to have the `www-data:www-data` group.

If it that ot the case, launch the following command :
`chown -R www-data: [URL_to_the_root_folder_of_the_module]/data`