(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.mapCreation = {
        attach: function attach(context,settings) {
            //Use of Leaflet API with the Leaflet Drupal module (see gbifstats.libraries.yml)
            let mapgbif = L.map('gbifstatsmap');
            mapgbif.setView([0.0, 0.0], 2);
            let country_code = drupalSettings.gbifstats.gbifstats.country_code;

            L.tileLayer('https://tile.gbif.org/3857/omt/{z}/{x}/{y}@2x.png?style=gbif-classic', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 8,
                minZoom : 1,
                tileSize: 512,
                zoomOffset: -1
            }).addTo(mapgbif);

            L.tileLayer('https://api.gbif.org/v2/map/occurrence/adhoc/{z}/{x}/{y}@2x.png?style=purpleYellow-noborder.poly&squareSize=16&country='+country_code+'&hasCoordinate=true&hasGeospatialIssue=false&advanced=false&srs=EPSG%3A3857', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 8,
                minZoom : 1,
                tileSize: 512,
                zoomOffset: -1
            }).addTo(mapgbif);
        }
    };
})(jQuery, Drupal, drupalSettings);