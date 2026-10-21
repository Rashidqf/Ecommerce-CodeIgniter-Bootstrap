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

    <section class="buyora-blog-guide" style="margin-top: 36px; padding: 28px 24px; background: #ffffff; border-radius: 14px; border: 1px solid #e5e7eb; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 10px;">About the BUYORA Shopping &amp; Lifestyle Blog</h2>
        <p style="font-size: 14px; color: #4b5563; line-height: 1.7; margin-bottom: 12px;">
            The official BUYORA Blog is your ultimate guide to smart online shopping in Pakistan. Discover comprehensive product reviews, technology buying guides, seasonal fashion roundups, beauty skincare routines, and home styling advice written by our in-house retail specialists.
        </p>
        <p style="font-size: 14px; color: #4b5563; line-height: 1.7; margin: 0;">
            Stay ahead of seasonal discount sales, Black Friday mega drops, Ramadan &amp; Eid festivals, and exclusive vendor promo code releases. Have a question or want to see a specific gadget reviewed? Reach out to our editorial team anytime via our contact page.
        </p>
    </section>
</main>
