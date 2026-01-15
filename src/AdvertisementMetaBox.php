<?php

namespace Supernova\AtRestAdvInvoices;

use Supernova\AtRestAdvInvoices\SettingsPage;
use Supernova\AtRestAdvInvoices\AdvertisementOrderManager;

class AdvertisementMetaBox {
    private $settingsPage;
    private $orderManager;
    
    public function __construct( SettingsPage $settingsPage, AdvertisementOrderManager $orderManager ) {
        $this->orderManager = $orderManager;
        $this->settingsPage = $settingsPage;
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_create_advertisement_invoice', [$this, 'handleCreateInvoice']);
    }

    public function register() {
        add_meta_box(
            'at_rest_adv_invoices',
            'Invoice Generator',
            [$this, 'render'],
            'advertisement',
            'side',
            'default'
        );
    }

    public function render($post) {
        $id = $post->ID;
        $ordersData = $this->getOrdersData($id);
        $advertiserId = (int)get_post_meta($id, 'advertiser', true);
        include AT_REST_ADV_INVOICES_DIR . 'views/advertisement-meta-box.php';
    }

    public function enqueueAssets($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        global $post;
        if (!$post || $post->post_type !== 'advertisement') {
            return;
        }

        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_style(
            'at-rest-adv-invoices-meta-box',
            AT_REST_ADV_INVOICES_URL . 'assets/css/advertisement-meta-box.css',
            [],
            '1.0.0'
        );
        
        wp_enqueue_script(
            'at-rest-adv-invoices-meta-box',
            AT_REST_ADV_INVOICES_URL . 'assets/js/advertisement-meta-box.js',
            ['jquery', 'jquery-ui-datepicker'],
            '1.0.0',
            true
        );

        wp_localize_script('at-rest-adv-invoices-meta-box', 'atRestInvoiceData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('create_invoice_nonce')
        ]);
    }

    public function handleCreateInvoice() {
        check_ajax_referer('create_invoice_nonce', 'nonce');

        $postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
        //$year = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
        //$month = isset($_POST['month']) ? sanitize_text_field($_POST['month']) : '';

        if (!$postId || !$date) {
            wp_send_json_error(['message' => 'Invalid data']);
        }
        $advertiserId = (int)get_post_meta($postId, 'advertiser', true);
        if ($advertiserId <= 0) {
            wp_send_json_error(['message' => 'Advertiser not found']);
        }
        $adType = $this->getPostAdType($postId);
        $productId = $this->settingsPage->getProductByAdType($adType);
        if (!$productId) {
            wp_send_json_error(['message' => 'Product not found for ad type: ' . $adType]);
        }

        $orderId = $this->orderManager->createOrder($postId, $date, $advertiserId, $productId);
        if ($orderId <= 0) {
            wp_send_json_error(['message' => 'Failed to create order']);
        }

        wp_send_json_success(['message' => 'Invoice created successfully']);
    }
    private function getPostAdType($postId) {
        $adType = get_post_meta($postId, 'ad_type', true);
        return $adType;
    }

    private function getOrdersData($postId) {
        $orders = $this->orderManager->getAllOrdersOfAdvertisement($postId);
        $ordersData = [];
        foreach ($orders as $order) {
            $monthNum = $order->get_meta('_advertisement_month');
            $ordersData[] = [
                'title' => $order->get_title(),
                'link' => $order->get_edit_order_url(),
                'id' => $order->get_id(),
                'year' => $order->get_meta('_advertisement_year'),
                'month' => $monthNum ? date('F', mktime(0, 0, 0, $monthNum, 1)) : '',
                'status' => $order->get_status(),
            ];
        }
        return $ordersData;
    }

}