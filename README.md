# WooCommerce Product Importer

This repository contains code to automatically import products into WooCommerce from an XML file. The import process is executed once a day via WordPress cron.

## Installation

1. Clone this repository onto your local server or hosting environment:

   git clone -b impot-products https://github.com/odokienko-sergio/storefront.git

2. Import the database dump woo_db.sql using phpMyAdmin or any other database management tool.

3. Edit the WordPress configuration file (wp-config.php) to match your database settings.

4. Notice the products.xml file located in the storefront theme directory. This file will be used for the daily product imports.

## Importing Products

To manually trigger the product import:

1. Log in to the WordPress admin dashboard.

2. Navigate to "Products" > "Import Products".

3. Click "Import" and follow the on-screen instructions.

Products will also be automatically imported once a day through the WordPress scheduled tasks.

## License

This project is licensed under the [MIT License](LICENSE).