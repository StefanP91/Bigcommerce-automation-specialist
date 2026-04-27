# BigCommerce Webhook Automation: Last Verified Date

This project automates the process of updating a "Last Verified" date on a BigCommerce product whenever the product is updated in the Admin panel.

## Features
- **Real-time Updates**: Uses BigCommerce Webhooks (`store/product/updated`) to trigger the script.
- **Storefront Visibility**: Automatically updates the product description to display the last verification timestamp to customers.
- **Loop Protection**: Built-in mechanism to prevent infinite webhook loops during self-updates.
- **Logging**: Detailed activity log for monitoring API requests and responses.

## Technical Details
- **Backend**: PHP
- **API**: BigCommerce V3 Catalog API
- **Authentication**: X-Auth-Token

## Setup Instructions
1. Upload `webhook.php` to your server (e.g., via ngrok or live hosting).
2. Create a Webhook in BigCommerce pointing to your URL:
   - Scope: `store/product/updated`
   - Destination: `https://your-domain.com/webhook.php`
3. Ensure your API Token has `modify` permissions for **Products**.

## Verification
To verify the functionality:
1. Edit any product in the BigCommerce Admin.
2. Visit the product page on the Storefront.
3. Observe the "Last Verified: YYYY-MM-DD HH:MM:SS" text at the top of the description.
