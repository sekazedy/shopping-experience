CREATE DATABASE IF NOT EXISTS shopping_experience;

USE shopping_experience;

CREATE TABLE stock_products (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL ,
  available INT UNSIGNED DEFAULT 0,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  vat_rate DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT PK_id PRIMARY KEY (id),
  UNIQUE (name)
);

CREATE TABLE cart (
    id int NOT NULL AUTO_INCREMENT,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    vat_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT PK_id PRIMARY KEY (id)
);

CREATE TABLE cart_stock_products (
    cart_id int NOT NULL,
    stock_product_id int NOT NULL,
    quantity int NOT NULL DEFAULT 0
);