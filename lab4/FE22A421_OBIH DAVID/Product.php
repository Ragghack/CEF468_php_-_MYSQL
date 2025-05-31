<?php
abstract class Product {
    public string $product_name;
    public float $product_price;

    public function __construct(string $name, float $price) {
        $this->product_name = $name;
        $this->product_price = $price;
    }

    abstract public function getDiscount(): float;
}
?>
