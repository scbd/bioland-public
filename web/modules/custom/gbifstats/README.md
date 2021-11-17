GBIF Stats
==========

Générateur de statistiques GBIF pour Drupal 8.

Instructions
------------

1. Dézipper l'archive dans le dossier *modules/custom* de votre installation Drupal. Activez-le ensuite dans l'interface graphique sur la page d'administration des modules (`[URL_de_votre_page_d_accueil]/admin/modules`).

2. Générer les fichiers de données en visitant la page suivante : `[URL_de_votre_page_d_accueil]/gbifstats/generate/{country}` ; `{country}` étant le code en 2 lettres du pays voulu.

3. Pour voir les données du pays, visitez la page `[URL_of_Drupal]/gbifstats/display/{country}`

---

Il existe 3 types de permissions dans le module GBIF Stats : 
    *configure GBIF Stats* : pour configurer le module, 
    *generate GBIF Stats* : pour la generation de données (l'accès est restreint de base) 
    *view GBIF Stats* : pour l'affichage des stats.

Important
---------
Vérifiez que Drupal puisse écrire dans le dossier `data` du module. Si ce n'est pas le cas, suivez les instructions du fichier README.md du dossier `data` 

Alias de zone geographique
--------------------------
Des noms peuvent être donné à des zones géographiques qui regroupent 1 ou plusieurs pays. (Ex : France pour FR ; Amerique_Nord pour CA+US+MX) 
Pour cela, ajouter une ligne dans le fichier `data/country_custom.txt` au format `[NOM_DE_L_ALIAS]-----[SUITE_DE_CODE_PAYS]` avec `[SUITE_DE_CODE_PAYS]` de la forme `[CODE_PAYS_1]&country=[CODE_PAYS_1]&country=....`

Attention
---------
Ce module est en cours de travail. Reportez tout bugs et suggestions.

---
---

GBIF Stats
==========

GBIF Statistiques generator for Drupal 8.

Installation Instructions
------------

1. Option: direct install
   1. Unpack the archive in the *web/modules/custom* folder
   2. As Admin, Enable it in the administrative module page (`[URL_of_home_page]/admin/modules`).
2. Option: composer install
   1. Add package to the composer.json file repository section:
   ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/gbiffrance/Drupal8-gbifstats"
        }
    ],
   ```
   2. Install the package (pay attention to package name): 
   > $ composer require --prefer-dist "drupal/gbifstats"

   3. Enable it for the appropriate site:
   > $ drush @site-alias en gbifstats

3. As Admin, Configure permissions for installation-specific role mapping (`[URL_of_home_page]/admin/people/permissions`).
   
   3 permissions are availables on the GBIF Stats module : 
   - *configure GBIF Stats* : to configure the module, 
   - *generate GBIF Stats* : to the data file generation (acces restricted by default), 
   - *view GBIF Stats* : to watch the gbif stats

Configuring, generating and viewing
------------

1. Visit `[URL_of_home_page]/admin/config/development/gbifstats` to configure global parameters
2. Visit `[URL_of_home_page]/gbifstats/generate/{country}` to generate the files who containst information about the country;`{country}` is the two-letter country code.

3. Visit `[URL_of_Drupal]/gbifstats/display/{country}` to see your page displaying the information.

---


If you need the information for other countries, be advice than you will need *generate GBIF Stats* permission.

Important
---------
Drupal must have the right to write into the folder `data`. If that not the case, follow the instructions of the README.md of the `data` folder. 

Geographic Area Alias
----------------------
Alias cen be added to geographic area who contains 1 or more country. (Ex : France for FR ; North_America for CA+US+MX) 
To add Alias, add line into the `data/country_custom.txt` file with the following format `[NAME_OF_ALIAS]-----[COUNTRY_CODE]` avec `[COUNTRY_CODE]` somethig like this `[COUNTRY_CODE_1]&country=[COUNTRY_CODE_2]&country=....`


Attention
---------
This module is a work in progress. Please report bugs and suggestions.
