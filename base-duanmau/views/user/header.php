<div class="sum_clause">
    <div class="policy d-flex gap-2">
        <div class="info d-flex align-items-center gap-1">
            <i class="bxr bxs-location"></i>
            <p>Vo Nguyen Giap Street, Thanh Hoa</p>
        </div>
        <div class="info d-flex align-items-center gap-1">
            <i class="bxr bxs-envelope"></i>
            <p>nhom5@gmail.com</p>
        </div>
    </div>
    <div class="clause d-flex align-items-center gap-2 me-4">
        <a href="index.php?ctl=user&class=guarantee&act=guarantee">Privacy Policy</a>
        <p style="color: white" class="p_bottom">/</p>
        <a href="index.php?ctl=user&class=clause&act=clause">Terms of Use</a>
        <p style="color: white" class="p_bottom">/</p>
        <a href="index.php?ctl=user&class=refund&act=refund">Sales and Refunds</a>
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
                            href="index.php?ctl=user&class=home&act=home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=product&act=product">Product</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">Category</a>
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
                        <a class="nav-link" href="index.php?ctl=user&class=news&act=news">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=news&act=news">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=introduce&act=introduce">Introduce</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?ctl=user&class=contact&act=contact">Contact</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?class=login&act=logout">Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?class=login&act=login">Đăng nhập</a>
                        </li>
                    <?php endif; ?>

                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <div class="search">
                        <form class="d-flex" role="search">
                            <input
                                class="form-control me-2"
                                type="search"
                                placeholder="Search"
                                aria-label="Search" />
                            <button
                                class="btn btn-outline-primary d-flex align-items-center"
                                type="submit">
                                <i class="bx bx-search fs-5"></i>
                            </button>
                        </form>
                    </div>
                    <div class="c-u d-flex gap-3">
                        <a href="index.php?ctl=user&class=favorite&act=favorite" class="text-secondary icon-with-badge">
                            <i class="bx bxs-heart fs-4"></i>
                            <?php if (isset($favoriteCount) && $favoriteCount > 0): ?>
                                <span class="badge rounded-pill bg-danger favorite-count-badge">
                                    <?php echo $favoriteCount; ?>
                                </span>
                            <?php endif; ?>
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
                            <a href="index.php?ctl=user&class=account&act=account" class="text-secondary" title="Tài khoản của tôi">
                                <i class="bx bxs-user fs-4"></i>
                            </a>
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