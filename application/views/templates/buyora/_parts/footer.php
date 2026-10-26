            <footer class="buyora-footer">
                <div class="buyora-container footer-main">
                    <a class="footer-brand" href="<?= base_url() ?>" aria-label="BUYORA home">
                        <img src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA — Shop More, Live Better" width="138" height="38" loading="lazy" decoding="async">
                    </a>
                    <div class="footer-column">
                        <h2>Quick Links</h2>
                        <a href="<?= base_url() ?>">Home</a>
                        <a href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Shop</a>
                        <a href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">Deals</a>
                        <a href="<?= htmlspecialchars(LANG_URL . '/shop', ENT_QUOTES, 'UTF-8') ?>">New Arrivals</a>
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
                            <a href="<?= base_url('category/' . (int) $footerCategory['id'] . '/' . url_title($footerCategory['name'], 'dash', true)) ?>"><?= htmlspecialchars($footerCategory['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php } ?>
                    </div>
                    <div class="footer-column">
                        <h2>Customer Service</h2>
                        <a href="<?= base_url('contacts') ?>">Help Center</a>
                        <a href="<?= base_url('checkout') ?>" rel="nofollow">Track Order</a>
                        <a href="<?= base_url('contacts') ?>">Returns &amp; Refunds</a>
                        <a href="<?= base_url('contacts') ?>">Shipping Policy</a>
                        <a href="<?= base_url('contacts') ?>">Terms &amp; Conditions</a>
                    </div>
                    <div class="footer-column footer-social">
                        <h2>Follow Us</h2>
                        <div class="social-icons" aria-label="Social media">
                            <a href="<?= !empty($footerSocialFacebook) ? htmlspecialchars($footerSocialFacebook, ENT_QUOTES, 'UTF-8') : 'https://www.facebook.com' ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href="<?= !empty($footerSocialTwitter) ? htmlspecialchars($footerSocialTwitter, ENT_QUOTES, 'UTF-8') : 'https://www.instagram.com' ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href="<?= !empty($footerSocialYoutube) ? htmlspecialchars($footerSocialYoutube, ENT_QUOTES, 'UTF-8') : 'https://www.youtube.com' ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a>
                            <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <a href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa fa-music" aria-hidden="true"></i></a>
                        </div>
                        <label for="footer-email">Subscribe to our newsletter</label>
                        <form class="newsletter-form" method="post" id="subscribeForm">
                            <input id="footer-email" type="email" name="subscribeEmail" placeholder="Your email address" required>
                            <button type="button" onclick="checkEmailField()">Subscribe</button>
                        </form>
                    </div>
                    <div class="footer-column footer-contact">
                        <h2>Contact Us</h2>
                        <?php
                        $footerPhones = array_filter(array_map('trim', explode(',', isset($footerContactPhone) && $footerContactPhone !== '' ? $footerContactPhone : '0333-6108869, 0308-8503190')));
                        foreach ($footerPhones as $fp) {
                            $cleanFp = preg_replace('/[^0-9+]/', '', $fp);
                        ?>
                            <a href="tel:<?= htmlspecialchars($cleanFp, ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-phone" aria-hidden="true"></i> Cell. <?= htmlspecialchars($fp, ENT_QUOTES, 'UTF-8') ?></a>
                        <?php } ?>
                        <a href="mailto:<?= htmlspecialchars($footerContactEmail ?: 'biolabtraders@gmail.com', ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Email. <?= htmlspecialchars($footerContactEmail ?: 'biolabtraders@gmail.com', ENT_QUOTES, 'UTF-8') ?></a>
                        <span><i class="fa fa-map-marker" aria-hidden="true"></i> <?= htmlspecialchars($footerContactAddr ?: 'Office No. 5, 1st Floor, Karim Centre, 16-Edward Road, Lahore, Pakistan', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="buyora-container footer-seo-copy" style="padding: 16px 0 10px 0; border-top: 1px solid rgba(255,255,255,0.08); font-size: 13px; line-height: 1.6; color: #9ca3af;">
                        <p style="margin: 0 0 6px 0;"><strong>BUYORA Online Marketplace</strong> is Pakistan's premier shopping platform connecting smart consumers with verified sellers across Karachi, Lahore, Islamabad, Rawalpindi, and nationwide. Browse thousands of authentic products across mobile electronics, trendy apparel, beauty essentials, home and living gadgets, sports gear, and daily essentials with transparent ratings and competitive pricing.</p>
                        <p style="margin: 0;">Shop with complete peace of mind through Safepay SSL-encrypted card transactions, dependable Cash on Delivery (COD), rapid doorstep courier shipping, and comprehensive buyer protection including 7-day hassle-free returns. Experience modern, seamless, and customer-first digital commerce on BUYORA.</p>
                    </div>
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
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/buyora/lang.js') ?>"></script>
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
