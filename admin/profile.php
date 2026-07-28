<?php
require_once __DIR__ . '/auth_check.php';
include './header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="./src/images/profile_picture/<?= htmlspecialchars($_SESSION['admin_profile_pic'] ?? 'default.png') ?>"
                         alt="Profile Picture"
                         onerror="this.src='./src/images/profile_picture/default.png'"
                         style="width:120px;height:120px;object-fit:cover;">
                </div>
                <h3 class="profile-username text-center mt-3"><?= htmlspecialchars($_SESSION['admin_name']) ?></h3>
                <p class="text-muted text-center"><?= htmlspecialchars($_SESSION['admin_email']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Admin Details</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Name</th><td><?= htmlspecialchars($_SESSION['admin_name']) ?></td></tr>
                    <tr><th>Username</th><td><?= htmlspecialchars($_SESSION['admin_username']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($_SESSION['admin_email']) ?></td></tr>
                    <tr><th>Mobile</th><td><?= htmlspecialchars($_SESSION['admin_mobile']) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>