<?php
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';

$pdo = getDBConnection();
$errors = [];
$old = $_POST;

function generateSlug($string) {
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim(preg_replace('/[^a-zA-Z0-9\s-]/', '', str_replace(' ', '-', $string)))));
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

$basePath = realpath(__DIR__ . '/../..');

function uploadFile($file, $targetDir, $allowedTypes = [], $maxSize = 5242880) {
    global $basePath;
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'Upload error';
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) $errorMsg = 'File exceeds maximum allowed size';
        elseif ($file['error'] === UPLOAD_ERR_NO_FILE) $errorMsg = 'No file was uploaded';
        elseif ($file['error'] === UPLOAD_ERR_PARTIAL) $errorMsg = 'File was only partially uploaded';
        elseif ($file['error'] === UPLOAD_ERR_NO_TMP_DIR) $errorMsg = 'Server temporary folder is missing';
        elseif ($file['error'] === UPLOAD_ERR_CANT_WRITE) $errorMsg = 'Failed to write file to disk';
        elseif ($file['error'] === UPLOAD_ERR_EXTENSION) $errorMsg = 'File upload stopped by extension';
        return ['success' => false, 'error' => $errorMsg . ' (code ' . $file['error'] . ')'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowedTypes) && !in_array($ext, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type: .' . $ext];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large (max ' . ($maxSize / 1048576) . 'MB).'];
    }
    $targetDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $targetDir);
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }
    if (!is_dir($targetDir) || !is_writable($targetDir)) {
        return ['success' => false, 'error' => 'Upload directory is not accessible or writable.'];
    }
    $targetDir = realpath($targetDir);
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $dest = $targetDir . DIRECTORY_SEPARATOR . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $relative = $basePath . DIRECTORY_SEPARATOR;
        $relativePath = str_replace($relative, '', $targetDir . DIRECTORY_SEPARATOR) . $filename;
        return ['success' => true, 'path' => str_replace(DIRECTORY_SEPARATOR, '/', $relativePath)];
    }
    return ['success' => false, 'error' => 'Failed to save file. Check directory permissions.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_product'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $description = $_POST['description'] ?? '';
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $metaKeywords = trim($_POST['meta_keywords'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    if (!$name) $errors[] = 'Product name is required.';
    if (!$slug) $slug = generateSlug($name);
    if (!$slug) $errors[] = 'Could not generate a valid slug.';

    $existingSlug = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
    $existingSlug->execute([$slug]);
    if ($existingSlug->fetch()) $errors[] = 'This slug is already in use. Please choose another.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $featuredImage = '';
            if (!empty($_FILES['featured_image']['name'])) {
                $dir = __DIR__ . '/../../uploads/products/featured';
                $result = uploadFile($_FILES['featured_image'], $dir, ['jpg','jpeg','png','webp','gif'], 2097152);
                if ($result['success']) $featuredImage = $result['path'];
                else $errors[] = 'Featured image: ' . $result['error'];
            }

            $brochure = '';
            if (!empty($_FILES['brochure']['name'])) {
                $dir = __DIR__ . '/../../uploads/products/brochures';
                $result = uploadFile($_FILES['brochure'], $dir, ['pdf'], 10485760);
                if ($result['success']) $brochure = $result['path'];
                else $errors[] = 'Brochure: ' . $result['error'];
            }

            if (empty($errors)) {
                $ogTitle = trim($_POST['og_title'] ?? '') ?: $name;
                $ogDescription = trim($_POST['og_description'] ?? '') ?: strip_tags($shortDescription ?: $description);
                $ogImage = '';

                if (!empty($_FILES['og_image']['name'])) {
                    $dir = __DIR__ . '/../../uploads/products/featured';
                    $result = uploadFile($_FILES['og_image'], $dir, ['jpg','jpeg','png','webp','gif'], 2097152);
                    if ($result['success']) $ogImage = $result['path'];
                    else $errors[] = 'OG Image: ' . $result['error'];
                } elseif ($featuredImage) {
                    $ogImage = $featuredImage;
                }

                $schemaMarkup = trim($_POST['schema_markup'] ?? '');
                if (!$schemaMarkup) {
                    $schemaMarkup = json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'Product',
                        'name' => $name,
                        'description' => strip_tags($shortDescription ?: $description),
                        'image' => $featuredImage ? '../../' . $featuredImage : '',
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                }

                if (empty($errors)) {
                    $stmt = $pdo->prepare("INSERT INTO products (name, slug, short_description, description, featured_image, brochure, meta_title, meta_description, meta_keywords, schema_markup, og_title, og_description, og_image, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$name, $slug, $shortDescription, $description, $featuredImage, $brochure, $metaTitle, $metaDescription, $metaKeywords, $schemaMarkup, $ogTitle, $ogDescription, $ogImage, $status]);
                    $productId = $pdo->lastInsertId();

                    if (!empty($_POST['spec_label'])) {
                        $specStmt = $pdo->prepare("INSERT INTO product_specifications (product_id, label, value) VALUES (?,?,?)");
                        foreach ($_POST['spec_label'] as $i => $label) {
                            $label = trim($label ?? '');
                            $value = trim($_POST['spec_value'][$i] ?? '');
                            if ($label && $value) $specStmt->execute([$productId, $label, $value]);
                        }
                    }

                    if (!empty($_FILES['gallery']['name'][0])) {
                        $galleryStmt = $pdo->prepare("INSERT INTO product_gallery (product_id, image_path, sort_order) VALUES (?,?,?)");
                        $dir = __DIR__ . '/../../uploads/products/gallery';
                        $files = $_FILES['gallery'];
                        $fileCount = count($files['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                                $file = [
                                    'name' => $files['name'][$i],
                                    'type' => $files['type'][$i],
                                    'tmp_name' => $files['tmp_name'][$i],
                                    'error' => $files['error'][$i],
                                    'size' => $files['size'][$i]
                                ];
                                $result = uploadFile($file, $dir, ['jpg','jpeg','png','webp','gif'], 3145728);
                                if ($result['success']) {
                                    $galleryStmt->execute([$productId, $result['path'], $i]);
                                }
                            }
                        }
                    }

                    $pdo->commit();
                    $_SESSION['flash_message'] = 'Product created successfully!';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: index.php');
                    exit;
                }
            }

            if (!empty($errors)) $pdo->rollBack();
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../header.php';
?>

<style>
.spec-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: center; }
.spec-row input { flex: 1; }
.spec-row .btn-remove-spec { flex-shrink: 0; }
.gallery-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
.gallery-preview .gallery-item { position: relative; width: 120px; height: 120px; border: 2px solid #dee2e6; border-radius: 6px; overflow: hidden; cursor: default; }
.gallery-preview .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
.gallery-preview .gallery-item .remove-gallery { position: absolute; top: 2px; right: 2px; width: 22px; height: 22px; background: rgba(220,53,69,.85); color: #fff; border: none; border-radius: 50%; font-size: 12px; line-height: 22px; text-align: center; cursor: pointer; }
#featuredPreview { max-width: 200px; max-height: 200px; margin-top: 8px; border-radius: 6px; display: none; }
#ogImagePreview { max-width: 200px; max-height: 200px; margin-top: 8px; border-radius: 6px; display: none; }
.form-section { border-bottom: 1px solid #dee2e6; padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
.form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
</style>

<div class="row mb-3">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark" style="font-size:1.5rem;">Add New Product</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Products</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="productForm">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Product Details</h3></div>
                <div class="card-body">
                    <div class="form-section">
                        <div class="form-group">
                            <label>Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="productName" class="form-control" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Slug</label>
                            <div class="input-group">
                                <input type="text" name="slug" id="productSlug" class="form-control" value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                                <div class="input-group-append">
                                    <button type="button" id="generateSlugBtn" class="btn btn-outline-secondary" title="Auto-generate from name"><i class="fas fa-sync-alt"></i></button>
                                </div>
                            </div>
                            <small class="text-muted">Leave blank to auto-generate from name. Edit to customize.</small>
                        </div>
                        <div class="form-group">
                            <label>Short Description</label>
                            <textarea name="short_description" class="form-control" rows="3" maxlength="500"><?= htmlspecialchars($old['short_description'] ?? '') ?></textarea>
                            <small class="text-muted">Brief summary (max 500 characters).</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label>Full Description</label>
                            <textarea name="description" id="descriptionEditor"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="d-block mb-2">Specifications</label>
                        <div id="specsContainer">
                            <?php if (!empty($old['spec_label'])): ?>
                                <?php foreach ($old['spec_label'] as $i => $label): ?>
                                    <?php if (trim($label ?? '') || trim($old['spec_value'][$i] ?? '')): ?>
                                    <div class="spec-row">
                                        <input type="text" name="spec_label[]" class="form-control" placeholder="Label (e.g. Weight)" value="<?= htmlspecialchars($label) ?>">
                                        <input type="text" name="spec_value[]" class="form-control" placeholder="Value (e.g. 5 kg)" value="<?= htmlspecialchars($old['spec_value'][$i] ?? '') ?>">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-spec"><i class="fas fa-times"></i></button>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="spec-row">
                                    <input type="text" name="spec_label[]" class="form-control" placeholder="Label (e.g. Weight)">
                                    <input type="text" name="spec_value[]" class="form-control" placeholder="Value (e.g. 5 kg)">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-spec"><i class="fas fa-times"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addSpecBtn" class="btn btn-sm btn-outline-success mt-2"><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Media</h3></div>
                <div class="card-body">
                    <div class="form-section">
                        <div class="form-group">
                            <label>Featured Image</label>
                            <div class="custom-file">
                                <input type="file" name="featured_image" id="featuredImage" class="custom-file-input" accept="image/jpeg,image/png,image/webp,image/gif">
                                <label class="custom-file-label" for="featuredImage">Choose image</label>
                            </div>
                            <img id="featuredPreview" src="#" alt="Preview">
                            <small class="text-muted d-block mt-1">Recommended: 800x800px. Max 2MB. Formats: JPG, PNG, WebP, GIF.</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label>Media Gallery</label>
                            <div class="custom-file">
                                <input type="file" name="gallery[]" id="galleryInput" class="custom-file-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                                <label class="custom-file-label" for="galleryInput">Choose images</label>
                            </div>
                            <div id="galleryPreview" class="gallery-preview"></div>
                            <small class="text-muted d-block mt-1">You can select multiple images at once. Max 3MB per image.</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label>Brochure (PDF)</label>
                            <div class="custom-file">
                                <input type="file" name="brochure" id="brochureInput" class="custom-file-input" accept=".pdf">
                                <label class="custom-file-label" for="brochureInput">Choose PDF</label>
                            </div>
                            <small class="text-muted d-block mt-1">Optional. Max 10MB. PDF format only.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Publish</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" checked>
                            <label class="custom-control-label" for="statusSwitch">Active</label>
                        </div>
                    </div>
                    <button type="submit" name="submit_product" class="btn btn-success btn-block"><i class="fas fa-save"></i> Create Product</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Search Engine Optimization</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($old['meta_title'] ?? '') ?>" maxlength="70">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3" maxlength="160"><?= htmlspecialchars($old['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($old['meta_keywords'] ?? '') ?>" placeholder="Comma separated">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Open Graph</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>OG Title</label>
                        <input type="text" name="og_title" class="form-control" value="<?= htmlspecialchars($old['og_title'] ?? '') ?>">
                        <small class="text-muted">Leave blank to use product name.</small>
                    </div>
                    <div class="form-group">
                        <label>OG Description</label>
                        <textarea name="og_description" class="form-control" rows="2"><?= htmlspecialchars($old['og_description'] ?? '') ?></textarea>
                        <small class="text-muted">Leave blank to use short description.</small>
                    </div>
                    <div class="form-group">
                        <label>OG Image</label>
                        <div class="custom-file">
                            <input type="file" name="og_image" id="ogImage" class="custom-file-input" accept="image/jpeg,image/png,image/webp,image/gif">
                            <label class="custom-file-label" for="ogImage">Choose image</label>
                        </div>
                        <img id="ogImagePreview" src="#" alt="OG Preview">
                        <small class="text-muted d-block mt-1">Leave blank to use featured image. Recommended: 1200x630px.</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Schema Markup</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Custom Schema (JSON-LD)</label>
                        <textarea name="schema_markup" class="form-control" rows="6" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($old['schema_markup'] ?? '') ?></textarea>
                        <small class="text-muted">Auto-generated if left blank. You can customize it.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/ui/trumbowyg.min.css">
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/trumbowyg.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/plugins/upload/trumbowyg.upload.min.js"></script>
<script>
$('#descriptionEditor').trumbowyg({
    btns: [
        ['viewHTML'],
        ['undo', 'redo'],
        ['formatting'],
        ['strong', 'em', 'del'],
        ['foreColor', 'backColor'],
        ['link'],
        ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen']
    ],
    plugins: {
        upload: {
            serverPath: '../../includes/upload-editor-image.php',
            fileFieldName: 'file',
            urlPropertyName: 'location'
        }
    }
});

function generateSlug(text) {
    return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .replace(/-+/g, '-');
}

document.getElementById('productName')?.addEventListener('input', function() {
    const slugField = document.getElementById('productSlug');
    if (!slugField.dataset.modified) {
        slugField.value = generateSlug(this.value);
    }
});

document.getElementById('productSlug')?.addEventListener('input', function() {
    this.dataset.modified = this.value !== generateSlug(document.getElementById('productName')?.value || '');
});

document.getElementById('generateSlugBtn')?.addEventListener('click', function() {
    const name = document.getElementById('productName')?.value || '';
    document.getElementById('productSlug').value = generateSlug(name);
    document.getElementById('productSlug').dataset.modified = '';
});

document.getElementById('addSpecBtn')?.addEventListener('click', function() {
    const container = document.getElementById('specsContainer');
    const div = document.createElement('div');
    div.className = 'spec-row';
    div.innerHTML = '<input type="text" name="spec_label[]" class="form-control" placeholder="Label (e.g. Weight)">' +
        '<input type="text" name="spec_value[]" class="form-control" placeholder="Value (e.g. 5 kg)">' +
        '<button type="button" class="btn btn-sm btn-danger btn-remove-spec"><i class="fas fa-times"></i></button>';
    container.appendChild(div);
});

document.getElementById('specsContainer')?.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-spec')) {
        const row = e.target.closest('.spec-row');
        if (document.querySelectorAll('.spec-row').length > 1) {
            row.remove();
        }
    }
});

document.getElementById('featuredImage')?.addEventListener('change', function(e) {
    const preview = document.getElementById('featuredPreview');
    const label = this.nextElementSibling;
    if (this.files && this.files[0]) {
        label.textContent = this.files[0].name;
        const reader = new FileReader();
        reader.onload = function(e) { preview.style.display = 'block'; preview.src = e.target.result; };
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('ogImage')?.addEventListener('change', function(e) {
    const preview = document.getElementById('ogImagePreview');
    const label = this.nextElementSibling;
    if (this.files && this.files[0]) {
        label.textContent = this.files[0].name;
        const reader = new FileReader();
        reader.onload = function(e) { preview.style.display = 'block'; preview.src = e.target.result; };
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('galleryInput')?.addEventListener('change', function(e) {
    const container = document.getElementById('galleryPreview');
    const label = this.nextElementSibling;
    container.innerHTML = '';
    if (this.files) {
        const count = this.files.length;
        label.textContent = count + ' image(s) selected';
        Array.from(this.files).forEach((file, idx) => {
            const reader = new FileReader();
            const div = document.createElement('div');
            div.className = 'gallery-item';
            const img = document.createElement('img');
            const remover = document.createElement('button');
            remover.type = 'button';
            remover.className = 'remove-gallery';
            remover.innerHTML = '&times;';
            remover.addEventListener('click', function() { div.remove(); });
            reader.onload = function(e) { img.src = e.target.result; };
            reader.readAsDataURL(file);
            div.appendChild(img);
            div.appendChild(remover);
            container.appendChild(div);
        });
    }
});

document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function(e) {
        if (this.id === 'featuredImage' || this.id === 'ogImage') return;
        if (this.id !== 'galleryInput') {
            const label = this.nextElementSibling;
            if (this.files && this.files[0]) label.textContent = this.files[0].name;
        }
    });
});
</script>

<?php include __DIR__ . '/../footer.php'; ?>
