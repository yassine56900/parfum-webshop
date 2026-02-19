<?php
declare(strict_types=1);

final class CartService
{
    private const KEY = 'cart_items';

    /** @return array<int, int> productId => aantal */
    public function items(): array
    {
        $items = Session::get(self::KEY, []);
        return is_array($items) ? $items : [];
    }

    public function add(int $productId, int $qty = 1): void
    {
        $qty = max(1, $qty);
        $items = $this->items();

        $items[$productId] = ($items[$productId] ?? 0) + $qty;
        Session::set(self::KEY, $items);
    }

    public function update(int $productId, int $qty): void
    {
        $items = $this->items();

        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $qty;
        }

        Session::set(self::KEY, $items);
    }

    public function remove(int $productId): void
    {
        $items = $this->items();
        unset($items[$productId]);
        Session::set(self::KEY, $items);
    }

    public function clear(): void
    {
        Session::set(self::KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }
}
