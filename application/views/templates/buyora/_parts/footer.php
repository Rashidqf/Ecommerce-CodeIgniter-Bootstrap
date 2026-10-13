            <footer class="buyora-footer">
                <div class="buyora-container footer-main">
                    <a class="footer-brand" href="<?= base_url() ?>" aria-label="BUYORA home">
                        <img src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
                    </a>
                    <div class="footer-column">
                        <h2>Quick Links</h2>
                        <a href="<?= base_url() ?>">Home</a>
                        <a href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Shop</a>
                        <a href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">Deals</a>
                        <a href="<?= htmlspecialchars(base_url() . '#featured', ENT_QUOTES, 'UTF-8') ?>">New Arrivals</a>
                        <a href="<?= LANG_URL . '/contacts' ?>">Contact</a>
                    </div>
                    <div class="footer-column">
                        <h2>Categories</h2>
                        <?php
                        $footerCategories = isset($buyora_footer_categories) && is_array($buyora_footer_categories) ? $buyora_footer_categories : array();
                        if (empty($footerCategories)) { ?>
                            <span>No categories pinned</span>
                        <?php }
                        foreach ($footerCategories as $footerCategory) { ?>
                            <a href="<?= htmlspecialchars(LANG_URL . '?category=' . (int) $footerCategory['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($footerCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php } ?>
                    </div>
                    <div class="footer-column">
                        <h2>Customer Service</h2>
                        <a href="<?= LANG_URL . '/contacts' ?>">Help Center</a>
                        <a href="<?= LANG_URL . '/checkout' ?>">Track Order</a>
                        <a href="<?= LANG_URL . '/contacts' ?>">Returns &amp; Refunds</a>
                        <a href="<?= LANG_URL . '/contacts' ?>">Shipping Policy</a>
                        <a href="<?= LANG_URL . '/contacts' ?>">Terms &amp; Conditions</a>
                    </div>
                    <div class="footer-column footer-social">
                        <h2>Follow Us</h2>
                        <div class="social-icons" aria-label="Social media">
                            <a href="<?= $footerSocialFacebook ?: '#' ?>" aria-label="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href="<?= $footerSocialTwitter ?: '#' ?>" aria-label="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href="<?= $footerSocialYoutube ?: '#' ?>" aria-label="YouTube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <a href="#" aria-label="TikTok"><i class="fa fa-music" aria-hidden="true"></i></a>
                        </div>
                        <label for="footer-email">Subscribe to our newsletter</label>
                        <form class="newsletter-form" method="post" id="subscribeForm">
                            <input id="footer-email" type="email" name="subscribeEmail" placeholder="Your email address" required>
                            <button type="button" onclick="checkEmailField()">Subscribe</button>
                        </form>
                    </div>
                    <div class="footer-column footer-contact">
                        <h2>Contact Us</h2>
                        <a href="tel:<?= htmlspecialchars($footerContactPhone, ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-phone" aria-hidden="true"></i> <?= htmlspecialchars($footerContactPhone ?: '+92 300 1234567', ENT_QUOTES, 'UTF-8') ?></a>
                        <a href="mailto:<?= htmlspecialchars($footerContactEmail, ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-envelope" aria-hidden="true"></i> <?= htmlspecialchars($footerContactEmail ?: 'info@buyora.com', ENT_QUOTES, 'UTF-8') ?></a>
                        <span><i class="fa fa-map-marker" aria-hidden="true"></i> <?= htmlspecialchars($footerContactAddr ?: 'Karachi, Pakistan', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="buyora-container footer-legal">
                        <span>&copy; 2026 Buyora. All rights reserved.</span>
                        <span>Secure Payments&nbsp; | &nbsp;100% Original Products&nbsp; | &nbsp;Fast &amp; Reliable Delivery</span>
                    </div>
                </div>
            </footer>
            <?php echo $addJs; ?>
        </div>
    </div>
    <div id="notificator" class="alert"></div>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
    <script>
        var variable = {
            clearShoppingCartUrl: "<?= base_url('clearShoppingCart') ?>",
            manageShoppingCartUrl: "<?= base_url('manageShoppingCart') ?>",
            discountCodeChecker: "<?= base_url('discountCodeChecker') ?>"
        };
    </script>
    <script src="<?= base_url('assets/js/system.js') ?>"></script>
    <script src="<?= base_url('assets/buyora/buyora.js') ?>"></script>
</body>
</html>
