<?php
    if ($order) {
    ?>
    <a href="<?php echo get_edit_post_link($order->get_id()); ?>">
        #<?php echo $order->get_id(); ?>
    </a> | 
    <span class="status <?php echo $status; ?>"
        style="color: <?php echo $statusColor; ?>;" >
        <?php echo wc_get_order_status_name($status); ?>
    </span>
<?php
    } else {
        echo '—';
    }