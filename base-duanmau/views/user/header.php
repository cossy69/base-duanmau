<div class="sum_clause">
    <div class="policy d-flex gap-2">
        <div class="info d-flex align-items-center gap-1">
            <i class="bxr bxs-location"></i>
            <p>Đường Võ Nguyên Giáp, TP Thanh Hóa</p>
        </div>
        <div class="info d-flex align-items-center gap-1">
            <i class="bxr bxs-envelope"></i>
            <p>quanganhlast@gmail.com</p>
        </div>
    </div>
    <div class="clause d-flex align-items-center gap-2 me-4">
        <a href="index.php?ctl=user&class=guarantee&act=guarantee">Bảo hành</a>
        <p style="color: white" class="p_bottom mb-0">/</p>

        <a href="index.php?ctl=user&class=guarantee&act=return_policy">Hoàn tiền</a>
        <p style="color: white" class="p_bottom mb-0">/</p>

        <a href="index.php?ctl=user&class=clause&act=clause">Điều khoản</a>
    </div>
</div>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand custom-brand" href="index.php?ctl=user&class=home&act=home">Tech Hub</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2">
                    <li class="nav-item">
                        <a
                            class="nav-link active fw-bold text-primary"
                            aria-current="page"
                            href="index.php?ctl=user&class=home&act=home">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=product&act=product">Sản Phẩm</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">Danh Mục</a>
                        <ul class="dropdown-menu">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <?php if ($category['product_count'] > 0): ?>
                                        <li>
                                            <a class="dropdown-item"
                                                href="index.php?ctl=user&class=product&act=product&category[]=<?php echo $category['category_id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="#">Không có danh mục</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=news&act=news">Tin Tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=introduce&act=introduce">Về Tech Hub</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=contact&act=contact">Phản Hồi</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <div class="search" style="position: relative; width: 100%; max-width: 400px;">
                        <form class="d-flex w-100" role="search" action="index.php" method="GET" autocomplete="off"> <input type="hidden" name="class" value="search">
                            <input type="hidden" name="act" value="search">

                            <input
                                id="search-input-header"
                                class="form-control me-2 live-search-input"
                                type="search"
                                name="keyword"
                                value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>"
                                placeholder="Tìm kiếm sản phẩm..."
                                aria-label="Search" />

                            <button class="btn btn-outline-primary d-flex align-items-center" type="submit">
                                <i class="bx bx-search fs-5"></i>
                            </button>
                        </form>

                        <div class="search-suggestions" id="suggestions-header"></div>
                    </div>
                    <div class="c-u d-flex gap-3 align-items-center">
                        <a href="index.php?ctl=user&class=favorite&act=favorite" class="text-secondary icon-with-badge">
                            <i class="bx bxs-heart fs-4"></i>
                            <?php
                            $favCount = $favoriteCount ?? 0;
                            $style = ($favCount <= 0) ? 'style="display: none;"' : '';
                            ?>
                            <span class="badge rounded-pill bg-danger favorite-count-badge" <?php echo $style; ?>>
                                <?php echo $favCount; ?>
                            </span>
                        </a>

                        <a href="index.php?class=cart&act=cart" class="text-secondary icon-with-badge" id="header-cart-icon">
                            <i class="bx bxs-cart fs-4"></i>
                            <?php if (isset($cartItemCount) && $cartItemCount > 0): ?>
                                <span class="badge rounded-pill bg-danger" id="header-cart-count">
                                    <?php echo $cartItemCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <a href="#" class="text-secondary d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bxs-user fs-4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <?php 
                                    // Kiểm tra quyền admin từ database
                                    $showAdminLink = false;
                                    if (isset($_SESSION['user_id'])) {
                                        global $pdo;
                                        $stmt = $pdo->prepare("SELECT role FROM user WHERE user_id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $userRole = $stmt->fetchColumn();
                                        $showAdminLink = ($userRole === 'admin' || $userRole === 'super_admin');
                                    }
                                    ?>
                                    <?php if ($showAdminLink): ?>
                                        <li>
                                            <a class="dropdown-item text-primary fw-bold" href="index.php?class=admin&act=dashboard">
                                                <i class='bx bxs-dashboard me-2'></i>Trang quản trị
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                    <?php endif; ?>

                                    <li>
                                        <a class="dropdown-item" href="index.php?ctl=user&class=account&act=account">
                                            <i class='bx bxs-user-detail me-2'></i>Tài khoản của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="index.php?class=login&act=logout">
                                            <i class='bx bx-log-out me-2'></i>Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="index.php?class=login&act=login" class="text-secondary" title="Đăng nhập">
                                <i class="bx bxs-user fs-4"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<?php
// Logic tạo breadcrumbs đơn giản dựa trên tham số URL
$currentClass = $_GET['class'] ?? 'home';
$currentAct = $_GET['act'] ?? 'home';
$breadcrumbs = [];

// Luôn có trang chủ
$breadcrumbs[] = ['name' => 'Trang chủ', 'url' => 'index.php?ctl=user&class=home&act=home'];

switch ($currentClass) {
    case 'product':
        $breadcrumbs[] = ['name' => 'Sản phẩm', 'url' => 'index.php?ctl=user&class=product&act=product'];
        if ($currentAct == 'product_detail') {
            // Nếu có biến $product từ controller thì hiện tên, không thì hiện "Chi tiết"
            $prodName = isset($product['name']) ? $product['name'] : 'Chi tiết sản phẩm';
            $breadcrumbs[] = ['name' => $prodName, 'url' => '#'];
        }
        break;
    case 'news':
        $breadcrumbs[] = ['name' => 'Tin tức', 'url' => 'index.php?ctl=user&class=news&act=news'];
        if ($currentAct == 'new_detail') {
            $breadcrumbs[] = ['name' => 'Chi tiết tin tức', 'url' => '#'];
        }
        break;
    case 'contact':
        $breadcrumbs[] = ['name' => 'Liên hệ', 'url' => '#'];
        break;
    case 'introduce':
        $breadcrumbs[] = ['name' => 'Giới thiệu', 'url' => '#'];
        break;
    case 'cart':
        $breadcrumbs[] = ['name' => 'Giỏ hàng', 'url' => '#'];
        break;
    case 'search':
        $breadcrumbs[] = ['name' => 'Tìm kiếm', 'url' => '#'];
        break;
    case 'account':
        $breadcrumbs[] = ['name' => 'Tài khoản', 'url' => '#'];
        break;
    case 'favorite':
        $breadcrumbs[] = ['name' => 'Yêu thích', 'url' => '#'];
        break;
    case 'compare':
        $breadcrumbs[] = ['name' => 'So sánh', 'url' => '#'];
        break;
}
?>

<?php if ($currentClass != 'home'): // Không hiện ở trang chủ 
?>
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light p-2 rounded">
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <?php if ($index == count($breadcrumbs) - 1): ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo htmlspecialchars($crumb['name']); ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="<?php echo $crumb['url']; ?>" class="text-decoration-none text-primary">
                                <?php echo htmlspecialchars($crumb['name']); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
<?php endif; ?>