<?php

namespace Drupal\gbifstats\Controller;

use Drupal\Core\Url;
// Change following https://www.drupal.org/node/2457593
// See https://www.drupal.org/node/2549395 for deprecate methods information
use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Controller routines for GBIF Stats pages.
 */
class GBIFStatsController {

    /**
     * Create 3 files with GBIF data on the chosen country
     * This callback is mapped to the path 'gbifstats/generate/{country}'.
     * @param $country  the country code (two letter in uppercase)
     */
    public function generate($country) {

        // Get Default settings
        $config         = \Drupal::config('gbifstats.settings');
        // Page title and source text.
        $page_title     = $config->get('gbifstats.page_title');

        //Path of the module
        $module_handler = \Drupal::service('module_handler');
        $module_path    = $module_handler->getModule('gbifstats')->getPath();

        /*  Getting the custom country   */
        $country_custom_txt  = file_get_contents($module_path . '/data/country_custom.txt');
        $country_custom_tab  = explode(PHP_EOL, $country_custom_txt);
        $list_country_custom = array();

        foreach ($country_custom_tab as $ligne_fichier){
            $ligne_custom = explode("-----", $ligne_fichier);
            $list_country_custom[$ligne_custom[0]] = $ligne_custom[1];
        }

        /*  Test the validity of the country code   */
        $countryCode = ["AD", "AE", "AF", "AG", "AI", "AL", "AM", "AO", "AQ", "AR", "AS", "AT", "AU", "AW", "AX", "AZ", "BA", "BB", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BL", "BM", "BN", "BO", "BQ", "BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA", "CC", "CD", "CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR", "CU", "CV", "CW", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO", "DZ", "EC", "EE", "EG", "EH", "ER", "ES", "ET", "FI", "FJ", "FK", "FM", "FO", "FR", "GA", "GB", "GD", "GE", "GF", "GG", "GH", "GI", "GL", "GM", "GN", "GP", "GQ", "GR", "GS", "GT", "GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU", "ID", "IE", "IL", "IM", "IN", "IO", "IQ", "IR", "IS", "IT", "JE", "JM", "JO", "JP", "KE", "KG", "KH", "KI", "KM", "KN", "KP", "KR", "KW", "KY", "KZ", "LA", "LB", "LC", "LI", "LK", "LR", "LS", "LT", "LU", "LV", "LY", "MA", "MC", "MD", "ME", "MF", "MG", "MH", "MK", "ML", "MM", "MN", "MO", "MP", "MQ", "MR", "MS", "MT", "MU", "MV", "MW", "MX", "MY", "MZ", "NA", "NC", "NE", "NF", "NG", "NI", "NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA", "PE", "PF", "PG", "PH", "PK", "PL", "PM", "PN", "PR", "PS", "PT", "PW", "PY", "QA", "RE", "RO", "RS", "RU", "RW", "SA", "SB", "SC", "SD", "SE", "SG", "SH", "SI", "SJ", "SK", "SL", "SM", "SN", "SO", "SR", "SS", "ST", "SV", "SX", "SY", "SZ", "TC", "TD", "TF", "TG", "TH", "TJ", "TK", "TL", "TM", "TN", "TO", "TR", "TT", "TV", "TW", "TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE", "VG", "VI", "VN", "VU", "WF", "WS", "YE", "YT", "ZA", "ZM", "ZW"];

        $element['#message_erreur'] = "NoError";

        if(! in_array($country, $countryCode) && ! array_key_exists($country, $list_country_custom)){
            $element['#message_erreur'] = Html::escape("Code pays invalide dans votre URL");
        }else {

            /*  Getting the true country param for the API request   */

            if(array_key_exists($country, $list_country_custom)){
                $country_param  = $list_country_custom[$country];
            }else{
                $country_param = $country;
            }

            /*  Getting the number of publishers   */

            //Get informations
            $curl_publishers    = curl_init();
            curl_setopt_array($curl_publishers, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => 'https://api.gbif.org/v1/occurrence/search?country=' . $country_param.'&limit=0&facet=publishingOrg&facetLimit=1000'
            ]);

            if (!curl_exec($curl_publishers)) {
                die('Error: "' . curl_error($curl_publishers) . '" - Code: ' . curl_errno($curl_publishers));
            } else {
                $publishers_json = curl_exec($curl_publishers);
                curl_close($curl_publishers);
            }

            //Extract informations
            $publishers_object  = json_decode($publishers_json);
            $facet_publishers   = $publishers_object->{"facets"};
            $nb_publishers      = count($facet_publishers[0]->{"counts"});

            //Save informations
            file_put_contents($module_path . '/data/' . $country . '-nb_publishers.txt', json_encode($nb_publishers));

            /*  Getting the occurrences number */

            //Get informations
            $curl_occurrences   = curl_init();
            curl_setopt_array($curl_occurrences, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_URL             => 'http://api.gbif.org/v1/occurrence/search?country=' . $country_param
            ]);

            if (!curl_exec($curl_occurrences)) {
                die('Error: "' . curl_error($curl_occurrences) . '" - Code: ' . curl_errno($curl_occurrences));
            } else {
                $occurrences_json = curl_exec($curl_occurrences);
                curl_close($curl_occurrences);
            }

            //Extract informations
            $occurrences_object = json_decode($occurrences_json);
            $nb_occurrences     = $occurrences_object->{"count"};

            //Save informations
            file_put_contents($module_path . '/data/' . $country . '-nb_occurrences.txt', $nb_occurrences);

            /*  Getting the last datasets  */

            //Get informations
            $curl_datasets      = curl_init();
            curl_setopt_array($curl_datasets, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_URL             => 'https://api.gbif.org/v1/dataset?country=' . $country_param
            ]);

            if (!curl_exec($curl_datasets)) {
                die('Error: "' . curl_error($curl_datasets) . '" - Code: ' . curl_errno($curl_datasets));
            } else {
                $datasets_json  = curl_exec($curl_datasets);
                curl_close($curl_datasets);
            }

            //Extract informations
            $datasets_object    = json_decode($datasets_json);
            $last_datasets      = $datasets_object->{"results"};

            //Save informations
            file_put_contents($module_path . '/data/' . $country . '-last_datasets.json', json_encode($last_datasets));
        }

        /*  Data for the displaying of information  */

        $element['#country_code']   = Html::escape($country);
        $element['#title']          = Html::escape($page_title);

        // Theme function.
        $element['#theme']  = 'gbifstatsgenerate';

        /*  End : Data for the displaying of information  */

        return $element;
    }

    /**
     * Displaying GBIF data on one country
     * @param $country  the country code (two letter in uppercase)
     * @return mixed    html displaying the GBIF data on one country
     */
    public function display($country) {
        // Get Default settings
        $config = \Drupal::config('gbifstats.settings');
        // Getting module parameters
        $page_title         = $config->get('gbifstats.page_title');
        $node_name          = $config->get('gbifstats.node_name');
        $website            = $config->get('gbifstats.website');
        $head_delegation    = $config->get('gbifstats.head_delegation');
        $node_manager       = $config->get('gbifstats.node_manager');
        $link_page_GBIF     = $config->get('gbifstats.link_page_GBIF');
        $nb_publishers      = $config->get('gbifstats.nb_publishers');
        $nb_occurrences     = $config->get('gbifstats.nb_occurrences');
        $categories         = $config->get('gbifstats.categories');
        $display_map        = $config->get('gbifstats.display_map');

        //Path of the module
        $module_handler     = \Drupal::service('module_handler');
        $module_path        = $module_handler->getModule('gbifstats')->getPath();

        //Initialing variables
        $last_datasets_json         = $nb_publishers_txt = $nb_occurrences_txt = "";
        $element['#last_datasets']  = array();
        $element['#nb_publishers']  = Html::escape($nb_publishers);
        $element['#nb_occurrences'] = Html::escape($nb_occurrences);
        $element['#display_map']    = Html::escape($display_map);

        /*  Getting the custom country   */
        $country_custom_txt  = file_get_contents($module_path . '/data/country_custom.txt');
        $country_custom_tab  = explode(PHP_EOL, $country_custom_txt);
        $list_country_custom = array();

        foreach ($country_custom_tab as $ligne_fichier){
            $ligne_custom = explode("-----", $ligne_fichier);
            $list_country_custom[$ligne_custom[0]] = $ligne_custom[1];
        }

        /*  Test the validity of the country code   */
        $countryCode = ["AD", "AE", "AF", "AG", "AI", "AL", "AM", "AO", "AQ", "AR", "AS", "AT", "AU", "AW", "AX", "AZ", "BA", "BB", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BL", "BM", "BN", "BO", "BQ", "BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA", "CC", "CD", "CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR", "CU", "CV", "CW", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO", "DZ", "EC", "EE", "EG", "EH", "ER", "ES", "ET", "FI", "FJ", "FK", "FM", "FO", "FR", "GA", "GB", "GD", "GE", "GF", "GG", "GH", "GI", "GL", "GM", "GN", "GP", "GQ", "GR", "GS", "GT", "GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU", "ID", "IE", "IL", "IM", "IN", "IO", "IQ", "IR", "IS", "IT", "JE", "JM", "JO", "JP", "KE", "KG", "KH", "KI", "KM", "KN", "KP", "KR", "KW", "KY", "KZ", "LA", "LB", "LC", "LI", "LK", "LR", "LS", "LT", "LU", "LV", "LY", "MA", "MC", "MD", "ME", "MF", "MG", "MH", "MK", "ML", "MM", "MN", "MO", "MP", "MQ", "MR", "MS", "MT", "MU", "MV", "MW", "MX", "MY", "MZ", "NA", "NC", "NE", "NF", "NG", "NI", "NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA", "PE", "PF", "PG", "PH", "PK", "PL", "PM", "PN", "PR", "PS", "PT", "PW", "PY", "QA", "RE", "RO", "RS", "RU", "RW", "SA", "SB", "SC", "SD", "SE", "SG", "SH", "SI", "SJ", "SK", "SL", "SM", "SN", "SO", "SR", "SS", "ST", "SV", "SX", "SY", "SZ", "TC", "TD", "TF", "TG", "TH", "TJ", "TK", "TL", "TM", "TN", "TO", "TR", "TT", "TV", "TW", "TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE", "VG", "VI", "VN", "VU", "WF", "WS", "YE", "YT", "ZA", "ZM", "ZW"];

        $element['#message_erreur'] = "NoError";

        if(! in_array($country, $countryCode) && ! array_key_exists($country, $list_country_custom)){
            $element['#message_erreur'] = Html::escape("Code pays invalide dans votre URL");
        }else {

            /*  Getting the true country param for the API request   */

            if (array_key_exists($country, $list_country_custom)) {
                $element['#country_param'] = $list_country_custom[$country];
            } else {
                $element['#country_param'] = $country;
            }


            /*  Getting the number of publishers   */
            if ($categories["nb_publishers"] != "0") {
                $nb_publishers_txt = file_get_contents($module_path . '/data/' . $country . '-nb_publishers.txt');
                $element['#nb_publishers'] = Html::escape("" . number_format($nb_publishers_txt, 0, ',', ' '));
            } else {
                $element['#nb_publishers'] = Html::escape("NoSelect");
            }

            /*  Getting the occurrences number */
            if ($categories["nb_occurrences"] != "0") {
                $nb_occurrences_txt = file_get_contents($module_path . '/data/' . $country . '-nb_occurrences.txt');
                $element['#nb_occurrences'] = Html::escape("" . number_format($nb_occurrences_txt, 0, ',', ' '));
            } else {
                $element['#nb_occurrences'] = Html::escape("NoSelect");
            }

            /*  Getting the last datasets  */
            if ($categories["last_dataset"] != "0") {
                $last_datasets_json = file_get_contents($module_path . '/data/' . $country . '-last_datasets.json');
                $datasets_array = json_decode($last_datasets_json, true);

                for ($index = 0; $index < 5; $index++) {
                    $dataset = array();
                    $dataset['key_dataset'] = Html::escape("" . $datasets_array[$index]["key"]);
                    $dataset['title_dataset'] = Html::escape("" . $datasets_array[$index]["title"]);
                    array_push($element['#last_datasets'], $dataset);
                }
            } else {
                $element['#last_datasets'] = Html::escape("NoSelect");
            }

            /*  Displaying the map or not   */
            if ($display_map != "0") {
                $element['#display_map'] = Html::escape("oui");
            } else {
                $element['#display_map'] = Html::escape("non");
            }

            /*  Data for js function  */
            $element['#attached']['library'][] = 'gbifstats/gbifstats';
            $element['#attached']['drupalSettings']['gbifstats']['gbifstats']['country_code'] = $element['#country_param'];

        }

        /*  Data for the displaying of information  */
        $element['#node_name'] = Html::escape($node_name);
        $element['#website'] = Html::escape($website);
        $element['#head_delegation'] = Html::escape($head_delegation);
        $element['#node_manager'] = Html::escape($node_manager);
        $element['#link_page_GBIF'] = Html::escape($link_page_GBIF);
        $element['#country_code'] = Html::escape($country);
        $element['#title'] = Html::escape($page_title);

        // Theme function.
        $element['#theme'] = 'gbifstatsdisplay';

        return $element;
    }
}