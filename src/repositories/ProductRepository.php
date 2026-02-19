<?php
declare(strict_types=1);

final class ProductRepository
{
    private const TABLE = 'product'; // <-- pas aan naar 'products' als jouw tabel zo heet

    public function __construct(private PDO $pdo)
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $sql = "SELECT product_id, naam, prijs, beschrijving, categorie_id
                FROM " . self::TABLE . "
                ORDER BY product_id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT product_id, naam, prijs, beschrijving, categorie_id
             FROM " . self::TABLE . " WHERE product_id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param int[] $ids
     * @return array<int, array<string, mixed>> keyed by product_id
     */
    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT product_id, naam, prijs, beschrijving, categorie_id
             FROM " . self::TABLE . "
             WHERE product_id IN ($placeholders)"
        );
        $stmt->execute(array_values($ids));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['product_id']] = $r;
        }
        return $map;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO " . self::TABLE . " (naam, prijs, beschrijving, categorie_id)
            VALUES (:naam, :prijs, :beschrijving, :categorie_id)
        ");
        $stmt->execute([
            ':naam' => $data['naam'],
            ':prijs' => $data['prijs'],
            ':beschrijving' => $data['beschrijving'],
            ':categorie_id' => $data['categorie_id'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE " . self::TABLE . "
            SET naam = :naam,
                prijs = :prijs,
                beschrijving = :beschrijving,
                categorie_id = :categorie_id
            WHERE product_id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':naam' => $data['naam'],
            ':prijs' => $data['prijs'],
            ':beschrijving' => $data['beschrijving'],
            ':categorie_id' => $data['categorie_id'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE product_id = :id");
        $stmt->execute([':id' => $id]);
    }
}
