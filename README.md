# sync-api-sde-fr-to-woocommerce

Integrate api.sde.fr API to Woocommerce (Products API, Orders API, Order Status Sync)

# API Endpoints:

1. Sync all Products to products table: {{WEBSITE_URL}}/wp-json/sde/v1/products
2. Add Synced Products to woocommerce: {{WEBSITE_URL}}/wp-json/sde/v1/add-product
3. Sync orders from api to woocommerce {{WEBSITE_URL}}/wp-json/sde/v1/create-order
4. Sync all order status from API to woocommerce {{WEBSITE_URL}}/wp-json/sde/v1/order-status-update
