

<?php
// controllers/items.php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';
$itemModel = new ItemModel($pdo);


// guard
if(!isset($_SESSION['user_id'])){ header('Location: ../views/auth/login.php'); exit; }
$user_id = $_SESSION['user_id'];


$action = $_GET['action'] ?? 'list';


if($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST'){
// validate inputs
$type = $_POST['type'];
$category_id = (int)$_POST['category_id'];
$item_date = $_POST['item_date'];
$location = trim($_POST['location']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);


// File upload handling
$imageName = null;
if(!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
finfo_close($finfo);
$allowed = ['image/jpeg','image/png','image/gif'];
if(!in_array($mime,$allowed)){
$_SESSION['error'] = 'Invalid image type'; header('Location: ../views/items/form.php'); exit;
}
if($_FILES['image']['size'] > 2 * 1024 * 1024){ $_SESSION['error'] = 'Image too large'; header('Location: ../views/items/form.php'); exit; }
$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$imageName = uniqid('img_') . '.' . $ext;
move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/uploads/' . $imageName);
}


$itemModel->create($user_id,$type,$category_id,$item_date,$location,$title,$description,$imageName);
$_SESSION['success'] = 'Item reported successfully';
header('Location: ../public/index.php'); exit;
}


if($action === 'delete' && isset($_GET['id'])){
$id = (int)$_GET['id'];
// delete only own item unless admin
if($_SESSION['role'] === 'admin'){
$itemModel->delete($id);
} else {
$itemModel->delete($id,$user_id);
}
$_SESSION['success'] = 'Item deleted';
header('Location: ../public/index.php'); exit;
}
```