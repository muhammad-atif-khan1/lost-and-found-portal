<?php
// config/config.php


define('BASE_URL', '/lost-and-found/public'); // adjust for your server
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/../assets/uploads/');


// file upload limits
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
$allowed_mime = ['image/jpeg','image/png','image/gif'];
```