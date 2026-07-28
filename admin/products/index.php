<?php
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';

$pdo = getDBConnection();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = '';
$params = [];
if ($search) {
    $where = "WHERE p.name LIKE :search OR p.slug LIKE :search2";
    $params[':search'] = "%{$search}%";
    $params[':search2'] = "%{$search}%";
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p {$where}");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

$stmt = $pdo->prepare("SELECT p.*, 
    (SELECT COUNT(*) FROM product_gallery pg WHERE pg.product_id = p.id) as gallery_count
    FROM products p {$where} ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$products = $stmt->fetchAll();

include __DIR__ . '/../header.php';
?>

<div class="row mb-3">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark" style="font-size:1.5rem;">Products</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="add-product.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <div class="input-group input-group-sm" style="width:300px;">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary ml-2"><i class="fas fa-times"></i> Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th style="width:80px;">Gallery</th>
                    <th style="width:70px;">Status</th>
                    <th style="width:160px;">Created</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if ($product['featured_image']): ?>
                                    <img src="../../<?= htmlspecialchars($product['featured_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <div style="width:50px;height:50px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="font-weight-bold"><?= htmlspecialchars($product['name']) ?></td>
                            <td><code><?= htmlspecialchars($product['slug']) ?></code></td>
                            <td><span class="badge badge-info"><?= (int)$product['gallery_count'] ?></span></td>
                            <td>
                                <?php if ($product['status']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y h:i A', strtotime($product['created_at'])) ?></td>
                            <td>
                                <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo;</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">&raquo;</a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<form id="deleteForm" method="POST" action="delete-product.php" style="display:none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="confirm" value="1">
</form>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        Swal.fire({
            title: 'Delete Product?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        });
    });
});

<?php if (isset($_SESSION['flash_message'])): ?>
Swal.fire({
    icon: '<?= $_SESSION['flash_type'] ?? 'success' ?>',
    title: '<?= addslashes($_SESSION['flash_message']) ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
endif; ?>
</script>

<?php include __DIR__ . '/../footer.php'; ?>
