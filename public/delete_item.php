<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];
$pdo->prepare("DELETE FROM items WHERE item_id=?")->execute([$id]);

header("Location: manage_items.php");
exit;
