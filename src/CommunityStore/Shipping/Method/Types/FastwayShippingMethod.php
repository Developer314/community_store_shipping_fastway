<?php

namespace Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore\Shipping\Method\Types;

use Core;
use Database;
use Concrete\Package\CommunityStore\Src\CommunityStore\Shipping\Method\ShippingMethodTypeMethod;
use Concrete\Package\CommunityStore\Src\CommunityStore\Shipping\Method\ShippingMethodOffer as StoreShippingMethodOffer;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore\FastwayApiCalls;
use Concrete\Core\Support\Facade\Session;

/**
 * @ORM\Entity
 * @ORM\Table(name="CommunityStoreFastwayMethods")
 */
class FastwayShippingMethod extends ShippingMethodTypeMethod
{
    public function getShippingMethodTypeName()
    {
        return t('Fastway Method');
    }

    /**
     * @ORM\Column(type="text")
     */
    protected $country_code;


    /**
     * @ORM\Column(type="text")
     */
    protected $fastway_api_key;


    /**
     * @ORM\Column(type="text")
     */
    protected $franchisee_code;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $surcharge;

    /**
     * @ORM\Column(type="text")
     */
    protected $fastway_satchel_name;

    /**
     * @ORM\Column(type="text")
     */
    protected $fastway_satchel_description;

    /**
     * @ORM\Column(type="text")
     */
    protected $fastway_parcel_name;

    /**
     * @ORM\Column(type="text")
     */
    protected $fastway_parcel_description;


    public function getAPIKey()
    {
        return $this->fastway_api_key;
    }

    public function setAPIKey($fastway_api_key)
    {
        $this->fastway_api_key = $fastway_api_key;
    }

    public function getCountryCode()
    {
        return $this->country_code;
    }

    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    public function getFranchiseeCode()
    {
        return $this->franchisee_code;
    }

    public function setFranchiseeCode($franchisee_code)
    {
        $this->franchisee_code = $franchisee_code;
    }

    public function getSurcharge()
    {
        return $this->surcharge;
    }

    public function setSurcharge($surcharge)
    {
        $this->surcharge = $surcharge;
    }

    public function getSatchelName()
    {
        return $this->fastway_satchel_name;
    }

    public function setSatchelName($fastway_satchel_name)
    {
        $this->fastway_satchel_name = $fastway_satchel_name;
    }

    public function getSatchelDescription()
    {
        return $this->fastway_satchel_description;
    }

    public function setSatchelDescription($fastway_satchel_description)
    {
        $this->fastway_satchel_description = $fastway_satchel_description;
    }

    public function getParcelName()
    {
        return $this->fastway_parcel_name;
    }

    public function setParcelName($fastway_parcel_name)
    {
        $this->fastway_parcel_name = $fastway_parcel_name;
    }

    public function getParcelDescription()
    {
        return $this->fastway_parcel_description;
    }

    public function setParcelDescription($fastway_parcel_description)
    {
        $this->fastway_parcel_description = $fastway_parcel_description;
    }

    public function addMethodTypeMethod($data)
    {
        return $this->addOrUpdate('add', $data);
    }

    public function update($data)
    {
        return $this->addOrUpdate('update', $data);
    }

    private function addOrUpdate($type, $data)
    {
        if ($type == "update") {
            $sm = $this;
        } else {
            $sm = new self();
        }
        // do any saves here
        $sm->setAPIKey($data['fastway_api_key']);
        $sm->setCountryCode($data['country_code']);
        $sm->setFranchiseeCode($data['franchisee_code']);
        $sm->setSurcharge($data['surcharge']);
        $sm->setSatchelName($data['fastway_satchel_name']);
        $sm->setSatchelDescription($data['fastway_satchel_description']);
        $sm->setParcelName($data['fastway_parcel_name']);
        $sm->setParcelDescription($data['fastway_parcel_description']);
        $em = Database::connection()->getEntityManager();
        $em->persist($sm);
        $em->flush();
        return $sm;
    }

    public function dashboardForm($shippingMethod = null)
    {
        $this->set('form', Core::make("helper/form"));
        $this->set('smt', $this);
        if (is_object($shippingMethod)) {
            $smtm = $shippingMethod->getShippingMethodTypeMethod();
        } else {
            $smtm = new self();
        }
        $this->set("smtm", $smtm);
        /*if ($smtm->getAPIKey() != '' || $smtm->getCountryCode() != '') {
            $fw_api = new FastwayApiCalls($smtm->getAPIKey());
            $franchisees = $fw_api->getFranchiseeByCountryCode($smtm->getCountryCode());
            if (isset($franchisees['result'])) {
                $this->set("franchisees", $franchisees['result']);
            }

        }*/

        $countries = array('' => 'Select Country', 1 => 'Australia', 6 => 'New Zealand', 11 => 'Ireland & N.Ireland', 24 => 'South Africa');
        $this->set("countries", $countries);
    }

    public function get_franchisee()
    {
        $fastway_api_key = $_POST['fastway_api_key'];
        $country_code = $_POST['country_code'];
        $fw_api = new FastwayApiCalls($fastway_api_key);
        $franchisees = $fw_api->getFranchiseeByCountryCode($country_code);
        if (isset($franchisees['result'])) {
            $default = array('' => 'Select Franchisee');
            echo Core::make('helper/form')->select('franchisee_code', $default + $franchisees['result']);
        } else {
            echo '<div class="alert alert-warning">' . $franchisees['error'] . '</div>';
        }

        die;
    }

    public function isEligible()
    {
        /*$subtotal = StoreCalculator::getSubTotal();
        $totalWeight = StoreCart::getCartWeight();
        $customer = new StoreCustomer();*/

        $address = Session::get('shipping_address');
        if (in_array($address['country'], array('AU', 'NZ', 'IE', 'ZA'))) {
            return true;
        } else {
            return false;
        }


    }

    public function getOffers()
    {

        /* $offers = array();
         $offer = new StoreShippingMethodOffer();
         $offer->setRate('15');
         $offer->setOfferLabel('Fastway Parcel');
         $offers[] = $offer;
         return $offers;*/
        $shipping_method_token = Session::get('shipping_method_token');
        $shipping_methods = Session::get('fastway_shipping_methods');
        $sessionOffers = $shipping_methods[$shipping_method_token];
        if (is_array($sessionOffers) && sizeof($sessionOffers) > 0) {
            return $sessionOffers;
        } else if (isset($this->fastway_api_key) && isset($this->country_code) && isset($this->franchisee_code)) {
            \Log::addInfo('API Call getOffers Called' . $_SERVER['REMOTE_ADDR']);
            $fw_api = new FastwayApiCalls($this->fastway_api_key, $this->franchisee_code);
            $result = $fw_api->getAvailableServices($this->country_code);
            $parcel_price = 999999;
            $satchel_price = 999999;
            $excess_package = 0;
            $offers = array();
            if (is_array($result->result->services) && count($result->result->services) > 0) {

                foreach ($result->result->services as $k => $r) {

                    if ($r->type == "Parcel") {


                        $tmp_price = "";
                        $exc_price = $this->custom_parcel_excess_price;

                        if ($r->name == "Local") {
                            $tmp_price = $this->custom_local_parcel_price;
                        } else {
                            if ($r->labelcolour == "LIME") {
                                $tmp_price = $this->custom_lime_parcel_price;
                            } else if ($r->labelcolour == "PINK") {
                                $tmp_price = $this->custom_pink_parcel_price;
                            } else if ($r->labelcolour == "RED") {
                                $tmp_price = $this->custom_red_zone_parcel_price;
                            } else if ($r->labelcolour == "ORANGE") {
                                $tmp_price = $this->custom_orange_zone_parcel_price;
                            } else if ($r->labelcolour == "GREEN") {
                                $tmp_price = $this->custom_green_zone_parcel_price;
                            } else if ($r->labelcolour == "WHITE") {
                                $tmp_price = $this->custom_white_zone_parcel_price;
                            } else if ($r->labelcolour == "GREY") {
                                $tmp_price = $this->custom_grey_zone_parcel_price;
                            }
                        }

                        if (is_numeric($tmp_price)) {
                            $exc = $r->excess_labels_required;

                            if ($exc > 0) {
                                if (is_numeric($exc_price) && !empty($exc_price)) {
                                    $tmp_price = $tmp_price + ($exc_price * $exc);
                                } else {
                                    $tmp_price = $tmp_price + $r->excess_label_price_normal;
                                }
                            }

                            if ($parcel_price > $tmp_price) {
                                $parcel_price = $tmp_price;
                            }
                        }


                        if ($parcel_price > $r->totalprice_normal && !is_numeric($tmp_price)) {
                            $parcel_price = $r->totalprice_normal;
                        }
                    }
                    if ($r->type == "Satchel") {

                        $tmp_price = "";
                        if ($r->labelcolour == "SAT-LOC-A3") {
                            $tmp_price = $this->custom_local_satchel_price;
                        } else
                            if ($r->labelcolour == "SAT-NAT-A2") {
                                $tmp_price = $this->custom_nat_a2_satchel_price;
                            } else
                                if ($r->labelcolour == "SAT-NAT-A3") {
                                    $tmp_price = $this->custom_nat_a3_satchel_price;
                                } else
                                    if ($r->labelcolour == "SAT-NAT-A4") {
                                        $tmp_price = $this->custom_nat_a4_satchel_price;
                                    } else
                                        if ($r->labelcolour == "SAT-NAT-A5") {
                                            $tmp_price = $this->custom_nat_a5_satchel_price;
                                        }


                        if (is_numeric($tmp_price)) {
                            if ($satchel_price > $tmp_price) {
                                $satchel_price = $tmp_price;
                            }
                        }


                        if ($satchel_price > $r->totalprice_normal && !is_numeric($tmp_price)) {
                            $satchel_price = $r->totalprice_normal;
                        }
                    }
                }
                if ($satchel_price != '') {
                    $offer = new StoreShippingMethodOffer();
                    $offer->setRate(round($satchel_price + $this->surcharge));
                    $offer->setOfferLabel($this->fastway_satchel_name ? $this->fastway_satchel_name : 'Fastway Satchel');
                    if ($this->fastway_satchel_description) {
                        $offer->setOfferDetails($this->fastway_satchel_description);
                    }
                    $offers[] = $offer;
                }
                if ($parcel_price != '') {
                    $offer = new StoreShippingMethodOffer();
                    $offer->setRate(round($parcel_price + $this->surcharge));
                    $offer->setOfferLabel($this->fastway_parcel_name ? $this->fastway_parcel_name : 'Fastway Parcel');
                    if ($this->fastway_parcel_description) {
                        $offer->setOfferDetails($this->fastway_parcel_description);
                    }
                    $offers[] = $offer;
                }
                $extra_fee = 0;
            }
            $shipping_method_token = Session::get('shipping_method_token');
            Session::remove('fastway_shipping_methods');
            Session::set('fastway_shipping_methods', array($shipping_method_token => $offers));
            return $offers;
        }

    }

    public function validate($data, $e)
    {

        $vt = Core::make('helper/validation/strings');
        $vn = Core::make('helper/validation/numbers');

        if (!$vn->integer($this->post('country_code'))) {
            $e->add(t('Country Code is Required'));
        }

        if (!$vt->notempty($this->post('fastway_api_key'))) {
            $e->add(t('API Key is required'));
        }

        if (!$vt->notempty($this->post('franchisee_code'))) {
            $e->add(t('Franchisee Code is required'));
        }

        return $e;

    }


}
