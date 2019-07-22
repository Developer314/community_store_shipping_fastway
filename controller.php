<?php

namespace Concrete\Package\CommunityStoreShippingFastway;

use Package;
use Whoops\Exception\ErrorException;
use Concrete\Package\CommunityStore\Src\CommunityStore\Shipping\Method\ShippingMethodType as StoreShippingMethodType;

class Controller extends Package
{
    protected $pkgHandle = 'community_store_shipping_fastway';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '0.0.9';


    protected $pkgAutoloaderRegistries = array(
        'src/CommunityStore' => 'Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore',
    );

    public function getPackageDescription()
    {
        return t("Fastway API");
    }

    public function getPackageName()
    {
        return t("Fastway Shipping Method Type");
    }

    public function install()
    {
        $installed = Package::getInstalledHandles();
        if (!(is_array($installed) && in_array('community_store', $installed))) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();
            StoreShippingMethodType::add('fastway', 'Fastway Shipping', $pkg);
        }

    }

    public function on_start()
    {
        $fsm = new \Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore\Shipping\Method\Types\FastwayShippingMethod();
        /*echo $fsm->get_franchisee();*/
        \Route::register('/get_franchisee', '\Concrete\Package\CommunityStoreShippingFastway\Src\CommunityStore\Shipping\Method\Types\FastwayShippingMethod::get_franchisee');
    }

    public function uninstall()
    {
        $pm = StoreShippingMethodType::getByHandle('fastway');
        if ($pm) {
            $pm->delete();
        }
        parent::uninstall();
    }

}

?>