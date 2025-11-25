<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .transition-zoom {
        transition: transform 0.5s ease;
    }

    .hover-lift:hover .transition-zoom {
        transform: scale(1.05);
    }

    .text-hover-primary:hover {
        color: var(--bs-primary) !important;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }

    /* Animation khi load AJAX */
    .news-item-anim {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<main class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bolder text-dark mb-2">Góc Công Nghệ</h1>
        <p class="text-muted">Cập nhật xu hướng mới nhất từ Tech Hub</p>
        <div class="mx-auto bg-primary rounded-pill" style="width: 60px; height: 4px;"></div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div id="news-container" class="row">
                <?php if (empty($posts)): ?>
                    <p class="text-center">Chưa có bài viết nào.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <?php include './views/user/partials/_news_item.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div id="loading" class="text-center d-none py-4">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Tìm kiếm</h5>
                        <div class="input-group">
                            <input type="text" id="search-input" class="form-control bg-light border-0 py-2" placeholder="Nhập từ khóa...">
                            <button class="btn btn-primary" type="button" id="btn-search">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Chủ đề hot</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="badge bg-light text-dark text-decoration-none px-3 py-2 border hot-topic-tag" data-keyword="Review">Review</a>
                            <a href="#" class="badge bg-light text-dark text-decoration-none px-3 py-2 border hot-topic-tag" data-keyword="iPhone">iPhone</a>
                            <a href="#" class="badge bg-light text-dark text-decoration-none px-3 py-2 border hot-topic-tag" data-keyword="Samsung">Samsung</a>
                            <a href="#" class="badge bg-light text-dark text-decoration-none px-3 py-2 border hot-topic-tag" data-keyword="Mẹo vặt">Mẹo vặt</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const newsContainer = document.getElementById('news-container');
        const loading = document.getElementById('loading');
        let timeout = null;

        // Hàm gọi AJAX lấy tin tức
        function fetchNews(keyword) {
            loading.classList.remove('d-none');
            newsContainer.style.opacity = '0.5';

            fetch(`index.php?class=news&act=filter&keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.text())
                .then(html => {
                    newsContainer.innerHTML = html;
                    newsContainer.style.opacity = '1';
                    loading.classList.add('d-none');
                })
                .catch(err => console.error(err));
        }

        // 1. Tìm kiếm khi gõ (debounce)
        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchNews(e.target.value);
            }, 500);
        });

        // 2. Tìm kiếm khi bấm nút Search icon
        document.getElementById('btn-search').addEventListener('click', function() {
            fetchNews(searchInput.value);
        });

        // 3. (MỚI) Xử lý click vào "Chủ đề hot"
        const tags = document.querySelectorAll('.hot-topic-tag');
        tags.forEach(tag => {
            tag.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn load lại trang

                const keyword = this.getAttribute('data-keyword'); // Lấy từ khóa

                // Điền từ khóa vào ô input cho người dùng thấy
                searchInput.value = keyword;

                // Gọi hàm tìm kiếm
                fetchNews(keyword);
            });
        });
    });
</script>