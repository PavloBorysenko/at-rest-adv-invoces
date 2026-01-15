<div class="wrap">
    <h1>Adv Invoices Settings</h1>   
        <form method="post" action="options.php">
            <?php 
                settings_fields($optionGroup);
                do_settings_sections($optionGroup);
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="ad_type">Products for ad types</label></th>
                    <td>
                        <ul>
                        <?php foreach ($adTypes as $adTypeKey => $adTypeLabel) { ?>
                            <li>
                                <label for="ad_type_<?php echo $adTypeKey; ?>">               
                                    <select name="<?php echo $optionName; ?>[ad_type_products][<?php echo $adTypeKey; ?>]" 
                                            id="ad_type_<?php echo $adTypeKey; ?>">
                                        <option value="">Select product</option>
                                        <?php foreach ($products as $product) { ?>
                                            <option <?php selected($product->get_id(), $adTypeProducts[$adTypeKey] ?? ''); ?> 
                                                value="<?php echo $product->get_id(); ?>">
                                                <?php echo $product->get_name() . ' (' .wc_price($product->get_price(), ['html' => false]) . ')'; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <strong><?php echo $adTypeLabel; ?></strong>
                                </label>
                                <hr>
                            </li>
                        <?php } ?>
                        </ul>

                    </td>
                </tr>

            </table>
            <?php submit_button('Save settings'); ?>
        </form>

    </div>    
</div>