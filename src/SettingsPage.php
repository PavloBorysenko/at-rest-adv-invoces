<?php
namespace Supernova\AtRestAdvInvoices;

class SettingsPage
{
    private $optionGroup = 'adv_invoices_settings_group';
    private $optionName = 'adv_invoices_settings';
    private $pageSlug = 'adv-invoices-settings';

    private $options = [];
    public function __construct()
    {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'addSettingsPage']);

    }
    public function addSettingsPage() {
        add_submenu_page(
            null,
            'Adv Invoices Settings',
            'Adv Invoices Settings',
            'manage_options',
            $this->pageSlug,
            [$this, 'renderSettingsPage']
        );
    }
    public function renderSettingsPage() {
        $optionGroup = $this->optionGroup;
        $adTypeProducts = $this->getAdTypefProducts();
        $optionName = $this->optionName;
        $options = $this->getOptions();
        $products = $this->getAdsProducts();
        $adTypes = $this->getAdTypes();
        require AT_REST_ADV_INVOICES_DIR . 'views/adv-invoices-settings-page.php';
    }
    public function registerSettings() {
        register_setting($this->optionGroup, $this->optionName);
    }
    
    public function getAdTypefProducts() {
        $options = $this->getOptions();
        if (isset($options['ad_type_products'])) {
            return $options['ad_type_products'];
        }
        return [];
    }

    public function getOptions() : array {
        if (empty($this->options)) {
            $this->options = get_option($this->optionName);
            if (empty($this->options)) {
                $this->options = [];
            }
        }
        return $this->options;
    }

    private function getAdTypes() : array {
        $field = acf_get_field('ad_type');
        return $field['choices'];
    }

    private function getAdsProducts() : array {
        $products = wc_get_products([
            'status'   => 'publish',
            'category' => ['advertisement'],
            'limit'    => -1
        ]);
        return $products;
    }
    public function getProductByAdType($adType) {
        $products = $this->getAdTypefProducts();
        if (isset($products[$adType])) {
            return $products[$adType];
        }
        
        return null;

    }

}