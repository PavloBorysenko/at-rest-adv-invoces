<?php

namespace Supernova\AtRestAdvInvoices;

class AdvertisementOrderManager {

    public function createOrder(int $postId, string $date, int $advertiserId, int $productId): int {
        $order = new \WC_Order();
        $order->set_customer_id(0);
        $order->set_status('pending');

        $itemId = $order->add_product(wc_get_product($productId), 1);
        
        $item = $order->get_item($itemId);
        $monthName = $this->getMonthName($this->getMonth($date));
        $item->add_meta_data( 'Payment for', $monthName . ' ' . $this->getYear($date), true );

        $item->save();


        $billingData = $this->getBillingData($advertiserId);
        $order->set_billing_company($billingData['company']);
        //$order->set_billing_first_name($billingData['first_name']);
        //$order->set_billing_last_name($billingData['last_name']);
        $order->set_billing_email($billingData['email']);
        $order->set_billing_phone($billingData['phone']);
        $order->set_billing_address_1($billingData['address']);

        $order->add_meta_data('_advertisement_id', $postId);
        $order->add_meta_data('_advertisement_date', $date);
        $order->add_meta_data('_advertisement_year', $this->getYear($date));
        $order->add_meta_data('_advertisement_month', $this->getMonth($date));
        $order->add_meta_data('_advertiser_id', $advertiserId);
        
        $order->calculate_totals();
        $order->save();
        return (int)$order->get_id();
    }
    private function getYear(string $date): int {
        return date('Y', strtotime($date));
    }
    private function getMonth(string $date): int {
        return date('m', strtotime($date));
    }
    private function getMonthName(int $month): string {
        return date('F', strtotime('2026-' . $month . '-01'));
    }
    public function getBillingData(int $advertiserId): array {

        $name = get_post_meta($advertiserId, 'contact_name', true);
        $name = explode(' ', $name);
        $first_name = $name[0];
        $last_name = $name[1];

        return [
            'company' => get_post_meta($advertiserId, 'company_name', true),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => get_post_meta($advertiserId, 'email', true),
            'phone' => get_post_meta($advertiserId, 'phone', true),
            'address' => get_post_meta($advertiserId, 'address', true),
        ];
    }
    
    public function getAllOrdersOfAdvertisement(int $postId, int $limit = 10) {
        $orders = wc_get_orders([
            'limit' => $limit,
            'meta_query' => [
                [
                    'key'   => '_advertisement_id',
                    'value' => $postId,
                ],
            ],
            'orderby'  => 'meta_value',
            'order'    => 'DESC',
            'meta_key' => '_advertisement_date',
        ]);
        return $orders;
    }
}