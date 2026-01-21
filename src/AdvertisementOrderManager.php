<?php

namespace Supernova\AtRestAdvInvoices;

class AdvertisementOrderManager {

    public function createOrder(int $postId, string $date, int $advertiserId, int $productId, float|null $customPrice): int {
        $order = new \WC_Order();
        $order->set_customer_id(0);
        $order->set_status('pending');

        $this->addProductToOrder($order, $productId, $date, $customPrice);

        $this->setBillingData($order, $advertiserId);

        $this->addMetaDataToOrder($order, $postId, $date, $advertiserId, $productId);
        
        $order->calculate_totals();
        $order->save();
        return (int)$order->get_id();
    }

    private function addProductToOrder(\WC_Order $order, int $productId, string $date, float|null $customPrice) {
        $product = wc_get_product($productId);
        if ($customPrice !== null) {
            $product->set_price( $customPrice );
        }
       
        $itemId = $order->add_product($product, 1);
        $item = $order->get_item($itemId);
        

        
        $monthName = $this->getMonthName($this->getMonth($date));
        $item->add_meta_data('Payment for', $monthName . ' ' . $this->getYear($date), true);
        
        $item->save();
    }
    private function setBillingData(\WC_Order $order, int $advertiserId) {
        $billingData = $this->getBillingData($advertiserId);
        //$order->set_billing_first_name($billingData['first_name']);
        //$order->set_billing_last_name($billingData['last_name']);
        $order->set_billing_company($billingData['company']);
        $order->set_billing_email($billingData['email']);
        $order->set_billing_phone($billingData['phone']);
        $order->set_billing_address_1($billingData['address']);
    }
    private function addMetaDataToOrder(\WC_Order $order, int $postId, string $date, int $advertiserId, int $productId) {
        $order->add_meta_data('_advertisement_id', $postId);
        $order->add_meta_data('_advertisement_date', $date);
        $order->add_meta_data('_advertisement_year', $this->getYear($date));
        $order->add_meta_data('_advertisement_month', $this->getMonth($date));
        $order->add_meta_data('_advertiser_id', $advertiserId);
        $order->add_meta_data('_advertiser_vat_number', $this->getVatNumber($advertiserId));
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
    private function getVatNumber(int $advertiserId): string {
        $vat_number = get_post_meta($advertiserId, 'vat_number', true);
        if ($vat_number) {
            return $vat_number;
        }
        $user = get_user_by('email', get_post_meta($advertiserId, 'email', true));
        if ($user) {
            return get_user_meta($user->ID, 'vat_number', true);
        }
        return '';
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
            'orderby' => [
                'meta_value' => 'DESC',
                'ID' => 'DESC'
            ],
            'meta_key' => '_advertisement_date',
        ]);
        return $orders;
    }
}