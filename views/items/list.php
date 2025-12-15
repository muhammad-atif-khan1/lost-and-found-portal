<h2 class="mb-4">Recent Lost & Found Items</h2>

<!-- Search Form -->
<form method="GET" class="row g-3 mb-4">

    <!-- Item Type -->
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
            <option value="">Any</option>
            <option value="lost" <?= (isset($_GET['type']) && $_GET['type']=='lost') ? 'selected' : '' ?>>Lost</option>
            <option value="found" <?= (isset($_GET['type']) && $_GET['type']=='found') ? 'selected' : '' ?>>Found</option>
        </select>
    </div>

    <!-- Category -->
    <div class="col-md-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select">
            <option value="">Any</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= (isset($_GET['category_id']) && $_GET['category_id']==$cat['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Location -->
    <div class="col-md-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
               placeholder="Enter location"
               value="<?= isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '' ?>">
    </div>

    <!-- Search Button -->
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100">🔍 Search</button>
    </div>

</form>

<!-- Item List -->
<div class="row">

<?php if (empty($items)): ?>
    <p class="text-center text-muted fs-5">😕 No items found.</p>
<?php else: ?>

    <?php foreach ($items as $item): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">

                <!-- Image -->
                <?php if (!empty($item['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" 
                         class="card-img-top" style="height:200px; object-fit:cover;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/400x200?text=No+Image"
                         class="card-img-top">
                <?php endif; ?>

                <div class="card-body">

                    <!-- Title -->
                    <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>

                    <!-- Badge for Lost / Found -->
                    <span class="badge <?= $item['type']=='lost' ? 'bg-danger' : 'bg-success' ?>">
                        <?= ucfirst($item['type']) ?>
                    </span>

                    <!-- Category -->
                    <p class="mt-2 mb-1">
                        <strong>Category:</strong> <?= htmlspecialchars($item['category_name']) ?>
                    </p>

                    <!-- Location -->
                    <p class="mb-1">
                        <strong>Location:</strong> <?= htmlspecialchars($item['location']) ?>
                    </p>

                    <!-- Date -->
                    <p class="text-muted">
                        <small><?= htmlspecialchars($item['item_date']) ?></small>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</div>
