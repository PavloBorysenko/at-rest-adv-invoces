<?php

namespace Supernova\AtRestAdvInvoices;

use Supernova\AtRestAdvInvoices\AdvertisementOrderManager;

class AdvertisenemtCustomColumn {

    private AdvertisementOrderManager $orderManager;
    public function __construct(AdvertisementOrderManager $orderManager) {
        $this->orderManager = $orderManager;
        add_filter('manage_edit-advertisement_columns', [$this, 'addColumn']);
        add_action('manage_advertisement_posts_custom_column', [$this, 'renderColumn'], 10, 2);
        }
    public function addColumn($columns) {

        $new_columns = [];

        foreach ( $columns as $key => $label ) {
            $new_columns[ $key ] = $label;
            if ( $key === 'banner_publication' ) {
                $new_columns['last_order'] = 'Last Invoice';
            }
        }
    
        return $new_columns;        
    }
    public function renderColumn($column, $post_id) {
        if ($column === 'last_order') {
            $orders = $this->orderManager->getAllOrdersOfAdvertisement($post_id, 1);
            $order = $orders[0] ?? null;
            $status = '';
            $statusColor = '';
            if ($order) {
                $status = $order->get_status();
                $statusColor = $this->getOrderStatusColor($status);
            }

            include AT_REST_ADV_INVOICES_DIR . 'views/advertisenent-custom-column.php';
        }
    }
    private function getOrderStatusColor($status) {
        switch ($status) {
            case 'pending':
                return '#ff0000';
            case 'processing':
                return '#00ff00';
            case 'completed':
                return '#0000ff';
            case 'cancelled':
                return '#ff0000';
            case 'refunded':
                return '#0000ff';
            case 'failed':
                return '#ff0000';
            case 'trash':
                return '#0000ff';
        }
    }
}