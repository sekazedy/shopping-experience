# Shopping experience

## Description
Small project is related to online e-commerce where product stock is managed so that user can add products to the shopping cart, manage stock, see cart subtotal, vat and total.

## Installation
In order to make the code work, you will need to:
- Have a server running PHP and MySQL
- Using composer, install the dependencies for the project
```bash
composer install
```
and then
```bash
composer dump-autoload
```
- Using the npm package manager, run
```bash
npm install
```
to install front-end dependencies
- Copy the .env.example file, rename it to .env and change the configuration for your database connection

## Configuration for the server included
I was developing under the Apache XAMPP server and included .htaccess file with the configuration.
I also included a nginx.conf file for a Nginx server to give main idea for the configuration, but I have not tested it.

After that everything should be working.

## Available API routes
```bash
GET /test #test that routes are working
```
```bash
GET /stock/get-products #get all products in stock
```
```bash
POST /stock/add-product #add product to the stock
#Example input data:
# {
#    "name": "Android",
#    "available": 4,
#    "price": 240.11,
#    "vat_rate": 0.13
# }
```
```bash
DELETE /stock/remove-product #remove product from the stock
#Example input data:
# {
#    "name": "Android"
# }
```
```bash
POST /cart/create #create new cart and obtain cart_id to work with
```
```bash
POST /cart/add-product #add existing product to the cart
#Example input data:
# {
#    "name": "Android",
#    "cart_id": 37
# }
```
```bash
POST /cart/remove-product #remove existing product from the cart
#Example input data:
# {
#    "name": "iPhone",
#    "cart_id": 37
# }
```
```bash
GET /cart/{id}/get-subtotal #get subtotal of the cart with id = {id}
```
```bash
GET /cart/{id}/get-vat-amount #get subtotal of the cart with id = {id}
```
```bash
GET /cart/{id}/get-total #get subtotal of the cart with id = {id}
```