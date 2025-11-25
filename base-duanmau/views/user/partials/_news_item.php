<div class="col-12 mb-4 news-item-anim">
    <a href="index.php?class=news&act=new_detail&id=<?php echo $post['post_id']; ?>"
        class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden text-decoration-none text-dark hover-lift">
        <div class="row g-0 h-100">
            <div class="col-md-4 overflow-hidden position-relative">
                <img src="<?php echo htmlspecialchars($post['thumbnail_url']); ?>"
                    class="img-fluid h-100 w-100 object-fit-cover transition-zoom"
                    alt="<?php echo htmlspecialchars($post['title']); ?>"
                    style="min-height: 200px;">
                <div class="position-absolute top-0 start-0 bg-primary text-white px-3 py-1 rounded-end-3 mt-3 small fw-bold">
                    Mới
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-body p-4 d-flex flex-column h-100 justify-content-center">
                    <h5 class="card-title fw-bold mb-2 lh-base text-hover-primary">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h5>
                    <p class="card-text text-muted small mb-3 line-clamp-2">
                        <?php echo htmlspecialchars(strip_tags(html_entity_decode($post['content']))); ?>
                    </p>
                    <div class="mt-auto d-flex align-items-center text-secondary small">
                        <span class="me-3"><i class="bx bx-calendar me-1"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                        <span><i class="bx bx-user me-1"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>