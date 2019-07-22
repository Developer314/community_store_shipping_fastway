<?php

namespace Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore;

use Session;
use Concrete\Package\CommunityStore\Src\CommunityStore\Cart\Cart as StoreCart;
use Config;

class FastwayApiCalls
{
    public $apikey;
    public $pickupFranchiseeCode;

    public function __construct($apikey = '', $pickupFranchiseeCode = '')
    {
        $this->apikey = $apikey;
        $this->pickupFranchiseeCode = $pickupFranchiseeCode;
    }

    public function getFranchiseeByCountryCode($country_code = 6)
    {
        $Url = 'https://sa.api.fastway.org/v2/psc/listrfs?CountryCode=' . $country_code . '&api_key=' . $this->apikey;
        $results = array();
        $result = $this->getReponse($Url);
        if ($result->error != '') {
            $results['error'] = $result->error;
        } else {
            $franchisee = $result->result;
            $franchiseee_arr = array();
            if (is_array($franchisee) && sizeof($franchisee)) {
                foreach ($franchisee as $franchise) {
                    $franchiseee_arr[$franchise->FranchiseCode] = $franchise->FranchiseName;
                }
            }
            $results['result'] = $franchiseee_arr;
        }

        return $results;

    }

    public function getAvailableServices($country_code = 6)
    {
        $address = Session::get('shipping_address');
        $city = $address['city'];
        $postal_code = $address['postal_code'];
        $cartWeight = StoreCart::getCartWeight();

        switch (Config::get('community_store.weightUnit')) {
            case 'lb':
                $cartWeightInKG = number_format($cartWeight / 2.205, 2, '.', '');
                break;
            case 'oz':
                $cartWeightInKG = number_format($cartWeight / 35.274, 2, '.', '');
                break;
            case 'g':
                $cartWeightInKG = number_format($cartWeight / 1000, 2, '.', '');
                break;
            default:
                $cartWeightInKG = $cartWeight;

        }

        $url = "https://sa.api.fastway.org/v2/psc/lookup/" . $this->pickupFranchiseeCode . "/" . rawurlencode($city) . "/" . $postal_code . "/" . $cartWeightInKG . "?api_key=" . $this->apikey;
        return $this->getReponse($url);

    }

    public function getReponse($Url)
    {
        \Log::addInfo('API Call'.$_SERVER['REMOTE_ADDR']);
        // is cURL installed yet?
        if (!function_exists('curl_init')) {
            die('Sorry cURL is not installed!');
        }

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Now set some options (most are optional)

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);

        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Should cURL return or print out the data? (true = retu	rn, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);
        return json_decode($output);


    }

}
