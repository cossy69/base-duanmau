<div class="quick-buttons">
    <a href="index.php?ctl=user&class=compare&act=compare" class="quick-btn compare-btn" title="So sánh">
        <i class="bx bx-swap-horizontal"></i>
        <span class="badge-compare">2</span>
    </a>

    <a href="index.php?ctl=user&class=discound&act=discound" class="quick-btn compare-btn" title="Voucher">
        <i class="bxr bxs-tickets"></i>
    </a>
</div>
<div class="container my-5">
    <h2 class="mb-4 text-primary">Tất cả Sản phẩm</h2>

    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold border-bottom">
                    <i class="bx bx-filter me-2"></i> Bộ lọc
                </div>
                <div class="card-body">
                    <h6 class="text-primary mt-2">Danh mục</h6>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="category1" />
                        <label class="form-check-label" for="category1">Điện thoại (12)</label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="category2" />
                        <label class="form-check-label" for="category2">Laptop (8)</label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="category3" />
                        <label class="form-check-label" for="category3">Phụ kiện (25)</label>
                    </div>

                    <hr />

                    <h6 class="text-primary">Khoảng giá</h6>
                    <div class="mb-3">
                        <input
                            type="range"
                            class="form-range"
                            min="0"
                            max="100"
                            step="5"
                            id="priceRange" />
                        <div class="d-flex justify-content-between small">
                            <span>0 VNĐ</span>
                            <span id="priceValue">50.000.000 VNĐ</span>
                        </div>
                    </div>

                    <hr />

                    <h6 class="text-primary">Thương hiệu</h6>
                    <select class="form-select form-select-sm">
                        <option selected>Tất cả</option>
                        <option value="1">Apple</option>
                        <option value="2">Samsung</option>
                        <option value="3">Xiaomi</option>
                    </select>

                    <button class="btn btn-primary w-100 mt-3">Áp dụng</button>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div
                class="d-flex justify-content-between align-items-center mb-3 p-3 rounded shadow-sm border">
                <p class="mb-0 fw-bold">Tìm thấy 45 kết quả</p>
                <div class="d-flex align-items-center gap-2">
                    <label for="sortOrder" class="form-label mb-0 small text-muted">Sắp xếp theo:</label>
                    <select
                        class="form-select form-select-sm"
                        id="sortOrder"
                        style="width: 150px">
                        <option selected>Mới nhất</option>
                        <option value="price_asc">Giá tăng dần</option>
                        <option value="price_desc">Giá giảm dần</option>
                        <option value="best_seller">Bán chạy nhất</option>
                    </select>
                </div>
            </div>

            <div class="row_product">
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?ctl=user&class=product&act=product_detail">
                        <img
                            src="image/gtWwywQMwUjALlIYGqwK.png"
                            class="card-img-top"
                            alt="ten anh" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title">Card title</h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p
                                    style="
                        font-size: 22px;
                        font-weight: 500;
                        color: rgb(255, 18, 18);
                      "
                                    class="p_bottom gia gia_cu">
                                    1400000
                                </p>
                                <p
                                    style="
                        font-size: 17px;
                        font-weight: 400;
                        color: rgb(59, 59, 59);
                        text-decoration: line-through;
                      "
                                    class="p_bottom gia gia_moi">
                                    1000000
                                </p>
                            </div>
                            <p
                                style="color: black; margin-bottom: 10px"
                                class="card-text">
                                Some quick example text to build on the card title and make
                                up the bulk of the card’s content.
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <p class="p_bottom giam_gia">-400K</p>
                        </div>
                    </a>
                </div>
            </div>

            <nav class="mt-5" aria-label="Product pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active" aria-current="page">
                        <a class="page-link bg-primary border-primary" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link text-primary" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link text-primary" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link text-primary" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
    <div
        id="tb_Toast"
        class="toast align-items-center text-white border-0 bg-primary"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastTb"></div>
            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>