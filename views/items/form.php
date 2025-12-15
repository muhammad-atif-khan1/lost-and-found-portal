`php
<?php session_start(); if(!isset($_SESSION['user_id'])){ header('Location: /lost-and-found/views/auth/login.php'); exit; }
require_once __DIR__ . '/../../config/db.php';
$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<form action="/lost-and-found/controllers/items.php?action=create" method="post" enctype="multipart/form-data" id="itemForm">
<label>Type</label>
<select name="type" required>
<option value="lost">Lost</option>
<option value="found">Found</option>
</select>
<label>Category</label>
<select name="category_id" required>
<?php foreach($cats as $c): ?><option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option><?php endforeach; ?>
</select>
<label>Date</label>
<input type="date" name="item_date" required>
<label>Location</label>
<input type="text" name="location" required>
<label>Title</label>
<input type="text" name="title" required>
<label>Description</label>
<textarea name="description"></textarea>
<label>Image (jpg/png/gif)</label>
<input type="file" name="image" accept="image/*">
<button type="submit">Submit</button>
</form>


<script src="/lost-and-found/assets/js/main.js"></script>
```