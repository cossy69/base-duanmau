<div class="container my-5">
    <div class="row">
        <div class="col-lg-9 col-md-12">
            <article>
                <h1 class="display-5 fw-bold text-dark mb-3">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                <p class="text-muted border-bottom pb-3 mb-4">
                    <i class="bx bx-calendar"></i> Ngày đăng: <?php echo date('d/m/Y', strtotime($post['created_at'])); ?> |
                    <i class="bx bx-user"></i> Tác giả: <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?>
                </p>

                <figure class="figure w-100 text-center">
                    <img src="<?php echo htmlspecialchars($post['thumbnail_url']); ?>"
                        class="article-image img-fluid rounded shadow-sm"
                        alt="<?php echo htmlspecialchars($post['title']); ?>"
                        style="max-height: 500px; object-fit: cover;" />
                </figure>

                <div class="content-body mt-4">
                    <?php echo $post['content']; ?>
                </div>
            </article>
        </div>

        <div class="col-lg-3 col-md-12 d-none d-lg-block">
            <div class="toc-sidebar p-3 bg-white rounded shadow-sm">
                <h5 class="fw-bold text-dark mb-3">Bài viết mới</h5>
                <div class="list-group small shadow-sm">
                    <?php foreach ($relatedPosts as $rp): ?>
                        <a href="index.php?class=news&act=new_detail&id=<?php echo $rp['post_id']; ?>" class="list-group-item list-group-item-action">
                            <?php echo htmlspecialchars($rp['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>