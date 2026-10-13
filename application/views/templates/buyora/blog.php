<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <h1><?= htmlspecialchars(lang('latest_blog'), ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if (!empty($posts)) { ?>
        <div class="buyora-blog-grid">
            <?php foreach ($posts as $post) { ?>
                <article class="buyora-blog-card">
                    <a href="<?= LANG_URL . '/blog/' . $post['url'] ?>">
                        <img src="<?= base_url('attachments/blog_images/' . rawurlencode($post['image'])) ?>" alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>">
                    </a>
                    <div>
                        <h2><?= htmlspecialchars(character_limiter($post['title'], 85), ENT_QUOTES, 'UTF-8') ?></h2>
                        <small><i class="fa fa-clock-o" aria-hidden="true"></i> <?= date('M d, y', $post['time']) ?></small>
                        <p><?= htmlspecialchars(character_limiter(strip_tags($post['description']), 180), ENT_QUOTES, 'UTF-8') ?></p>
                        <a class="buyora-text-link" href="<?= LANG_URL . '/blog/' . $post['url'] ?>"><?= htmlspecialchars(lang('read_mode'), ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                </article>
            <?php } ?>
        </div>
        <?php if (!empty($links_pagination)) { ?><div class="buyora-pagination"><?= $links_pagination ?></div><?php } ?>
    <?php } else { ?>
        <p class="buyora-empty-state"><?= htmlspecialchars(lang('no_posts'), ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>
</main>
