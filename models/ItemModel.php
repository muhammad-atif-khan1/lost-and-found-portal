<?php
// models/ItemModel.php

class ItemModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE ITEM
    public function create($user_id, $type, $category_id, $item_date, $location, $title, $description, $image)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO items (user_id, type, category_id, item_date, location, title, description, image, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'approved')"
        );

        return $stmt->execute([
            $user_id,
            $type,
            $category_id,
            $item_date,
            $location,
            $title,
            $description,
            $image
        ]);
    }

    // UPDATE ITEM
    public function update($item_id, $type, $category_id, $item_date, $location, $title, $description, $image = null)
    {
        if ($image !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE items
                 SET type = ?, category_id = ?, item_date = ?, location = ?, title = ?, description = ?, image = ?
                 WHERE item_id = ?"
            );
            return $stmt->execute([$type, $category_id, $item_date, $location, $title, $description, $image, $item_id]);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE items
             SET type = ?, category_id = ?, item_date = ?, location = ?, title = ?, description = ?
             WHERE item_id = ?"
        );

        return $stmt->execute([$type, $category_id, $item_date, $location, $title, $description, $item_id]);
    }

    // DELETE ITEM
    public function delete($item_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE item_id = ?");
        return $stmt->execute([$item_id]);
    }

    // GET ITEM BY ID
    public function getById($item_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT i.*, c.category_name, u.name
             FROM items i
             JOIN categories c ON i.category_id = c.category_id
             LEFT JOIN users u ON i.user_id = u.id
             WHERE i.item_id = ?"
        );

        $stmt->execute([$item_id]);
        return $stmt->fetch();
    }

    // MARK ITEM AS RETURNED (UPDATED → saves who received it)
    public function markReturned($item_id, $claimant_name, $phone)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE items 
             SET status = 'returned',
                 returned_to = ?, 
                 returned_contact = ?, 
                 returned_at = NOW()
             WHERE item_id = ?"
        );

        return $stmt->execute([$claimant_name, $phone, $item_id]);
    }

    // CREATE CLAIM
    public function createClaim($item_id, $claimant_name, $phone, $message, $proof_image = null)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO claims (item_id, claimant_name, phone, message, proof_image)
             VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([$item_id, $claimant_name, $phone, $message, $proof_image]);
    }

    // CHECK IF ITEM IS CLAIMABLE
    public function isClaimableById($item_id)
    {
        $stmt = $this->pdo->prepare("SELECT type, status FROM items WHERE item_id = ?");
        $stmt->execute([$item_id]);
        $row = $stmt->fetch();
        if (!$row) return false;
        return ($row['type'] === 'found' && $row['status'] !== 'returned');
    }

    // SEARCH ITEMS
    public function search($filters = [], $limit = 20, $offset = 0)
    {
        $sql = "
            SELECT i.*, c.category_name, u.name
            FROM items i
            JOIN categories c ON i.category_id = c.category_id
            LEFT JOIN users u ON i.user_id = u.id
            WHERE i.status IN ('approved', 'returned')
        ";

        $params = [];

        if (!empty($filters['type'])) {
            $sql .= " AND i.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND i.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND i.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND i.item_date >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND i.item_date <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
