<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <nav class="buyora-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Home</a><span>/</span>
        <a href="<?= LANG_URL . '/blog' ?>"><?= htmlspecialchars(lang('latest_blog'), ENT_QUOTES, 'UTF-8') ?></a>
        <span>/</span><span><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></span>
    </nav>
    <article class="buyora-panel">
        <h1><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <small><i class="fa fa-clock-o" aria-hidden="true"></i> <?= date('M d, y', $article['time']) ?></small>
        <?php if (!empty($article['image'])) { ?>
            <img src="<?= base_url('attachments/blog_images/' . rawurlencode($article['image'])) ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>" style="width:100%;max-height:420px;object-fit:cover;border-radius:12px;margin:16px 0;">
        <?php } ?>
        <div class="buyora-copy"><?= $article['description'] ?></div>
        <p><a class="buyora-text-link" href="<?= LANG_URL . '/blog' ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?= htmlspecialchars(lang('go_back'), ENT_QUOTES, 'UTF-8') ?></a></p>
    </article>
</main>
