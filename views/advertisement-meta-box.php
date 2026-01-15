<div class="at-rest-invoice-meta-box">
    <?php if ($advertiserId <= 0) { ?>
        <p>
            Please set advertiser for this advertisement first and update this advertisement.
        </p>

    <?php
        return;
    } ?>
    <p>
        <label for="invoice-date">Select billing date:</label>
        <input type="text" id="invoice-date" class="regular-text" readonly>
    </p>
    
    <div class="invoice-date-container">
        <label for="invoice-year"> Billing Month:</label>    
        <input type="text" id="invoice-year" class="regular-text" readonly>
        <input type="text" id="invoice-month" class="regular-text" readonly>
    </div>

    <p>
        <button type="button" 
            id="create-invoice" 
            class="button button-primary" 
            data-post-id="<?php echo $id; ?>">
            Create Invoice
        </button>
        <span class="spinner"></span>
    </p>

    <div id="invoice-ansver-text"></div>

    <h3> 10 latest orders list for this advertisement:</h3>
    <div id="orders-list">
        <?php foreach ($ordersData as $order) : ?>
            <div class="order-item">
                <a target="_blank" href="<?php echo $order['link']; ?>"><?php echo '#' . $order['id']; ?></a>
                <span class="order-date"><?php echo $order['month'] . ' ' . $order['year']; ?></span>
                <span class="order-status order-status-<?php echo sanitize_title($order['status']); ?>"><?php echo $order['status']; ?></span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($ordersData)) : ?>
            <div class="no-orders-message">No orders found</div>
        <?php endif; ?>
    </div>