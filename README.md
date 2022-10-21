



# How to build local?

## Required modules
```sh
brew install ddev
brew install composer
```

## Run image
```sh
git clone https://github.com/scbd/bioland-base bioland-base
cd bioland-base
ddev composer install
ddev start
ddev launch
```

You can browser using: https://bioland.ddev.site/


## Known issues:
- CKEditor 5 - The editor version was upgrade from 4 which was ending support in Drupal 9.5.
- Google Maps API key - This configuration will be done whenever the site is moving into production.
- oEmbed content in a frame - Configuration will be done whenever the site is moving into production. (It will be used www.xx.chm-cbd.net).
- Image media type - No practical impact as it will just reduce the size of uploaded images.

# Pull updates on server
git fetch --all
git reset --hard origin/master