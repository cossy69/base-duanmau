<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel | Tech Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #81beff;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --bg-light: #f8f9fa;
            --text-dark: #1a1a1a;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: sans-serif;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            padding: 30px 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: white;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid transparent;
            transition: 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary-color);
        }

        .sidebar .nav-link i {
            font-size: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-top: 4px solid var(--primary-color);
        }

        .stat-box h6 {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-box .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <?php
    // Lấy action hiện tại để active menu
    $act = $_GET['act'] ?? 'dashboard';
    ?>

    <div class="sidebar">
        <div class="ps-3 mb-4 text-white">
            <h4 style="font-weight: 700; margin: 0;">Tech Hub</h4>
            <small style="opacity: 0.7;">Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($act == 'dashboard') ? 'active' : ''; ?>" href="index.php?class=admin&act=dashboard">
                <i class="bx bxs-dashboard"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo ($act == 'products' || $act == 'add_product') ? 'active' : ''; ?>" href="index.php?class=admin&act=products">
                <i class="bx bxs-package"></i> <span>Sản phẩm</span>
            </a>
            <a class="nav-link <?php echo ($act == 'attributes') ? 'active' : ''; ?>" href="index.php?class=admin&act=attributes">
                <i class='bx  bx-categories'></i> <span>Thuộc tính</span>
            </a>
            <a class="nav-link <?php echo ($act == 'users') ? 'active' : ''; ?>" href="index.php?class=admin&act=users">
                <i class='bx  bx-community'></i> <span>Người dùng</span>
            </a>
            <a class="nav-link <?php echo ($act == 'coupons' || $act == 'add_coupon') ? 'active' : ''; ?>" href="index.php?class=admin&act=coupons">
                <i class='bx bxs-discount'></i> <span>Mã giảm giá</span>
            </a>
            <a class="nav-link <?php echo ($act == 'posts' || $act == 'add_post' || $act == 'edit_post') ? 'active' : ''; ?>" href="index.php?class=admin&act=posts">
                <i class='bx bx-news'></i> <span>Tin tức</span>
            </a>
            <a class="nav-link <?php echo ($act == 'reviews') ? 'active' : ''; ?>" href="index.php?class=admin&act=reviews">
                <i class='bx bx-star'></i> <span>Đánh giá</span>
            </a>
            <a class="nav-link <?php echo ($act == 'feedbacks') ? 'active' : ''; ?>" href="index.php?class=admin&act=feedbacks">
                <i class='bx  bx-message-reply'></i> <span>Phản hồi</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="index.php">
                <i class='bx bx-home-alt'></i> <span>Về trang chủ</span>
            </a>
            <a class="nav-link" href="index.php?class=login&act=logout">
                <i class='bx bx-log-out'></i> <span>Đăng xuất</span>
            </a>
        </nav>
    </div>