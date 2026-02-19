<?php
declare(strict_types=1);

final class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $sql = "SELECT product_id, naam, prijs FROM products ORDER BY product_id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $sql = "SELECT product_id, naam, prijs, beschrijving, categorie_id
                FROM products
                WHERE product_id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /** @param int[] $ids */
    public function findManyByIds(array $ids): array
    {
        $ids = array_values(array_filter($ids, fn($x) => is_int($x) && $x > 0));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT product_id, naam, prijs FROM products WHERE product_id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);

        $rows = $stmt->fetchAll();
        $out = [];

        foreach ($rows as $row) {
            $out[(int)$row['product_id']] = $row;
        }

        return $out;
    }
}
