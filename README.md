# At Rest Adv Invoices

WordPress plugin for creating WooCommerce invoices from advertisement posts.

---

## 1. Main Functionality

This plugin automatically generates WooCommerce orders (invoices) for advertisement posts. It adds a meta box to the advertisement post editor with the following features:

- **Date Picker**: Select the billing date for the invoice (past dates only)
- **Auto-filled Fields**: Year and month are automatically populated based on the selected date
- **Create Invoice Button**: Generates a WooCommerce order with advertiser billing information
- **Orders List**: Displays all previously created orders for the current advertisement with status and direct links
- **Custom Column**: Shows order information in the advertisements list table

The plugin retrieves advertiser billing data (company name, email, phone, address, VAT number) from the advertisement post meta fields and creates a properly formatted WooCommerce order.

---

## 2. Settings

### Requirements
- WooCommerce must be activated
- Advertisement custom post type must exist
- ACF field `ad_type` must be configured with advertisement type choices
- WooCommerce products in the "advertisement" category must be created

### Configuration
1. Navigate to **Adv Invoices Settings** page (hidden menu, direct URL: `/wp-admin/admin.php?page=adv-invoices-settings`)
2. Map each advertisement type to a corresponding WooCommerce product
3. Save settings

### Required Post Meta Fields
Each advertisement post must have:
- `advertiser` - Advertiser post ID
- `ad_type` - Advertisement type (matches ACF field choices)

Each advertiser post must have:
- `company_name` - Company name
- `contact_name` - Contact person name
- `email` - Email address
- `phone` - Phone number
- `address` - Billing address
- `vat_number` - VAT number (optional)

---

## 3. Troubleshooting

**Plugin won't activate**
- Ensure WooCommerce is installed and activated first

**"Product not found for ad type" error**
- Check that advertisement types are mapped to products in settings
- Verify the advertisement has a valid `ad_type` meta field

**"Advertiser not found" error**
- Ensure the advertisement post has a valid `advertiser` meta field with an existing post ID

**Orders not showing in meta box**
- Check that orders were created successfully
- Verify orders have the `_advertisement_id` meta field

**Billing data missing in order**
- Verify advertiser post has all required meta fields populated
- Check meta field names match exactly

---

## 4. Testing Notes

If the plugin is disabled, the old invoice system will work.

Verify invoice creation on the banner page (for different advertisers, with complete and incomplete advertiser data).

On the order page, verify all advertiser data.

Verify how PDFs are generated for advertisements and for monthly payments.

Verify email sending from the order page for advertisements and for monthly payments.

Verify the payment link in the email.

---

## 5. Changelog

**v1.0.1 - January 15, 2026**
- Added custom column to advertisements list showing invoice information
- Fixed date formatting in orders list
- Improved error handling for missing advertiser data

**v1.0.0 - January 13, 2026**
- Initial release
- Invoice meta box with date picker
- WooCommerce order creation from advertisement data
- Settings page for ad type to product mapping
- Orders list display in meta box
- AJAX-based order creation with loading states
