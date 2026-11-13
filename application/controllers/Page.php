<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->Model('Public_model');
    }

    public function index($page = null)
    {
        $this->goOut($page);
        $page = $this->Public_model->getOnePage($page);
        $this->goOut($page);
        $data = array();
        $head = array();
        $head['title'] = $page['name'];
        $head['description'] = character_limiter(strip_tags(trim($page['content'])), 120);
        $head['keywords'] = str_replace(" ", ",", $page['name']);
        $data['content'] = $page['content'];
        $this->render('dynPage', $head, $data);
    }

    public function accountDeletion()
    {
        $submitted = false;
        $message = '';
        if ($this->input->post('email')) {
            $email = trim($this->input->post('email'));
            $reason = trim($this->input->post('reason'));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `account_deletions` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `reason` TEXT NULL,
                    `status` VARCHAR(50) DEFAULT 'requested',
                    `created_at` INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

                $user = $this->db->where('email', $email)->get('users_public')->row_array();
                $userId = $user ? (int) $user['id'] : null;

                $this->db->insert('account_deletions', array(
                    'user_id' => $userId,
                    'email' => $email,
                    'reason' => $reason,
                    'status' => 'requested',
                    'created_at' => time()
                ));

                if ($user) {
                    $this->db->where('id', $userId)->update('users_public', array(
                        'name' => 'Deactivated Customer',
                        'phone' => '',
                        'email' => 'deleted_' . $userId . '_' . time() . '@anonymized.buyoramarketplace.com',
                        'password' => ''
                    ));
                }

                $submitted = true;
                $message = 'Your deletion request has been submitted and processed. Applicable personal data has been anonymized.';
            } else {
                $message = 'Please provide a valid email address.';
            }
        }

        $head = array(
            'title' => 'Request Account & Data Deletion - BUYORA Marketplace',
            'description' => 'Submit a request to delete your BUYORA Marketplace account and associated personal data in accordance with Google Play and privacy guidelines.',
            'keywords' => 'delete account, data privacy, buyora marketplace'
        );

        $html = '<div class="container" style="max-width: 720px; margin: 40px auto; padding: 20px; font-family: Inter, sans-serif;">';
        $html .= '<h2 style="color: #0F172A; font-weight: 700;">Account & Data Deletion Request</h2>';
        $html .= '<p style="color: #475569; font-size: 14px; line-height: 1.6;">At BUYORA Marketplace, we respect your privacy and provide full transparency over your personal data. In compliance with Google Play Store User Data policies and privacy regulations, you may request the deletion of your BUYORA customer account and personal information at any time.</p>';
        
        if ($submitted) {
            $html .= '<div style="background-color: #ECFDF5; border: 1px solid #10B981; padding: 16px; border-radius: 8px; color: #065F46; font-size: 14px; margin: 20px 0;">' . htmlspecialchars($message) . '</div>';
        } else {
            if (!empty($message)) {
                $html .= '<div style="background-color: #FEF2F2; border: 1px solid #EF4444; padding: 16px; border-radius: 8px; color: #991B1B; font-size: 14px; margin: 20px 0;">' . htmlspecialchars($message) . '</div>';
            }
            $html .= '<form method="post" action="' . base_url('account-deletion') . '" style="background: #F8FAFC; padding: 24px; border-radius: 12px; border: 1px solid #E2E8F0; margin-top: 20px;">';
            $html .= '<div style="margin-bottom: 16px;"><label style="display:block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Registered Email Address *</label><input type="email" name="email" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 6px; box-sizing: border-box;" placeholder="your.email@example.com"></div>';
            $html .= '<div style="margin-bottom: 16px;"><label style="display:block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Reason for Deletion (Optional)</label><textarea name="reason" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 6px; box-sizing: border-box;" placeholder="Let us know why you are leaving..."></textarea></div>';
            $html .= '<button type="submit" style="background-color: #DC2626; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 14px;">Confirm Account Deletion</button>';
            $html .= '</form>';
        }

        $html .= '<div style="margin-top: 32px; border-top: 1px solid #E2E8F0; padding-top: 20px; font-size: 12px; color: #64748B;">';
        $html .= '<h4 style="color: #1E293B; font-size: 14px; margin-bottom: 8px;">What data is deleted?</h4>';
        $html .= '<ul style="padding-left: 20px; line-height: 1.6;">';
        $html .= '<li>Your name, phone number, saved delivery addresses, and login credentials are permanently anonymized and purged.</li>';
        $html .= '<li>Your wishlist, preferences, and saved shopping cart sessions are deleted.</li>';
        $html .= '<li>Completed transaction receipts and historical financial audit records are retained exclusively for legal and tax compliance in accordance with applicable ecommerce regulations.</li>';
        $html .= '</ul>';
        $html .= '</div></div>';

        $data = array('content' => $html);
        $this->render('dynPage', $head, $data);
    }

    public function privacyPolicy()
    {
        $head = array(
            'title' => 'Privacy Policy - BUYORA Marketplace',
            'description' => 'Official Privacy Policy for BUYORA Marketplace application and website.',
            'keywords' => 'privacy policy, buyora marketplace, user data'
        );

        $html = '<div class="container" style="max-width: 760px; margin: 40px auto; padding: 20px; font-family: Inter, sans-serif; line-height: 1.7; color: #334155;">';
        $html .= '<h2 style="color: #0F172A; font-weight: 800; margin-bottom: 8px;">BUYORA Marketplace Privacy Policy</h2>';
        $html .= '<p style="font-size: 12px; color: #64748B; margin-bottom: 24px;">Last updated: November 2026</p>';
        $html .= '<p>BUYORA Marketplace operates the BUYORA mobile application and online marketplace at <strong>buyoramarketplace.com</strong>. This page informs users of our policies regarding the collection, use, and disclosure of personal data when using our Service.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">1. Information We Collect</h3>';
        $html .= '<p>When using BUYORA, we collect information that you provide directly, including name, email address, phone number, and physical shipping address required strictly to process orders and coordinate doorstep delivery across Pakistan.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">2. How Information is Used</h3>';
        $html .= '<p>We use collected data to create user accounts, verify checkout transactions, dispatch courier riders (e.g. TCS / Leopards Logistics), and provide customer care support.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">3. Data Sharing and Third Parties</h3>';
        $html .= '<p>We do not sell personal data to third parties. Data is shared exclusively with verified payment gateways (Bank Alfalah Alfa Payment Gateway) for transaction processing and courier logistics partners for package fulfillment.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">4. Data Security & Encryption in Transit</h3>';
        $html .= '<p>All communications between the BUYORA application and our servers are encrypted in transit using industry-standard Transport Layer Security (TLS/HTTPS). Payment details are handled directly via verified PCI-DSS compliant banking channels.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">5. Data Retention Period</h3>';
        $html .= '<p>We retain personal information for as long as your account remains active or as needed to provide you with order fulfillment, warranty support, and to comply with tax and legal requirements.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">6. Children\'s Privacy</h3>';
        $html .= '<p>Our services are not directed to individuals under the age of 13. We do not knowingly collect personal identifiable information from children. If we become aware that a child has provided us with personal data, we take steps to delete such information promptly.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">7. Account & Data Deletion</h3>';
        $html .= '<p>Users can delete their account at any time through the in-app Profile Settings or via our web portal at <a href="' . base_url('account-deletion') . '" style="color: #FF6B00; font-weight: 600;">' . base_url('account-deletion') . '</a>.</p>';
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">8. Contact Us</h3>';
        $html .= '<p>For privacy inquiries, contact our Data Protection Officer at: <a href="mailto:buyoramarketplace@gmail.com" style="color: #FF6B00;">buyoramarketplace@gmail.com</a>.</p>';
        $html .= '</div>';

        $data = array('content' => $html);
        $this->render('dynPage', $head, $data);
    }

    public function termsOfService()
    {
        $head = array(
            'title' => 'Terms of Service & Trader Information - BUYORA Marketplace',
            'description' => 'Terms and Conditions, commercial trader information, and customer rights for BUYORA Marketplace.',
            'keywords' => 'terms of service, trader information, buyora marketplace, legal terms'
        );

        $html = '<div class="container" style="max-width: 760px; margin: 40px auto; padding: 20px; font-family: Inter, sans-serif; line-height: 1.7; color: #334155;">';
        $html .= '<h2 style="color: #0F172A; font-weight: 800; margin-bottom: 8px;">Terms of Service & Commercial Trader Information</h2>';
        $html .= '<p style="font-size: 12px; color: #64748B; margin-bottom: 24px;">Last updated: November 2026</p>';
        
        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">1. Commercial Trader Identification</h3>';
        $html .= '<p>This e-commerce service is operated by <strong>BUYORA Marketplace</strong> (Trader: Muhammad Rashid Khan).</p>';
        $html .= '<ul style="padding-left: 20px; line-height: 1.8;">';
        $html .= '<li><strong>Registered Address:</strong> Office No. 5, 1st Floor, Karim Centre, 16-Edward Road, Lahore, Pakistan</li>';
        $html .= '<li><strong>Customer Support Phone:</strong> +92 333 6108869, +92 308 8503190</li>';
        $html .= '<li><strong>Official Business Email:</strong> <a href="mailto:buyoramarketplace@gmail.com" style="color: #FF6B00;">buyoramarketplace@gmail.com</a></li>';
        $html .= '<li><strong>Online Portal:</strong> <a href="https://buyoramarketplace.com" style="color: #FF6B00;">https://buyoramarketplace.com</a></li>';
        $html .= '</ul>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">2. Nature of Service</h3>';
        $html .= '<p>BUYORA is a multi-vendor online marketplace providing consumers with access to physical consumer goods (electronics, fashion, jewelry, lifestyle, and home goods). All goods are physical products fulfilled through verified merchant hubs and national courier logistics.</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">3. Transparent Pricing & Taxes</h3>';
        $html .= '<p>All product prices are quoted in Pakistani Rupees (PKR) and clearly indicate product totals, any applicable discounts, and standard courier shipping charges at checkout prior to order confirmation. There are no hidden fees or automatic recurring subscription charges.</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">4. Order Delivery & Logistics</h3>';
        $html .= '<p>Orders are dispatched with verified domestic courier partners (TCS, Leopards Logistics) with real-time tracking IDs. Standard transit time is 2 to 4 business days for metropolitan centers and 3 to 6 business days for regional locations nationwide.</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">5. Consumer Rights & Dispute Resolution</h3>';
        $html .= '<p>Customers are protected by our Buyer Protection program. In the event of damaged, defective, or incorrectly dispatched products, customers have the right to request a replacement or full refund in accordance with our <a href="' . base_url('return-policy') . '" style="color: #FF6B00; font-weight: 600;">Return & Refund Policy</a>.</p>';

        $html .= '</div>';

        $data = array('content' => $html);
        $this->render('dynPage', $head, $data);
    }

    public function returnPolicy()
    {
        $head = array(
            'title' => 'Return & Refund Policy - BUYORA Marketplace',
            'description' => 'Official 7-day Return and Refund Policy for BUYORA Marketplace customers.',
            'keywords' => 'return policy, refund policy, buyer protection, buyora marketplace'
        );

        $html = '<div class="container" style="max-width: 760px; margin: 40px auto; padding: 20px; font-family: Inter, sans-serif; line-height: 1.7; color: #334155;">';
        $html .= '<h2 style="color: #0F172A; font-weight: 800; margin-bottom: 8px;">BUYORA 7-Day Return & Refund Policy</h2>';
        $html .= '<p style="font-size: 12px; color: #64748B; margin-bottom: 24px;">Last updated: November 2026</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">1. 7-Day Return Window</h3>';
        $html .= '<p>We want you to shop with complete peace of mind. You may request a return within <strong>7 calendar days</strong> from the date your package was marked delivered by our courier.</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">2. Return Eligibility</h3>';
        $html .= '<p>An item is eligible for return if:</p>';
        $html .= '<ul style="padding-left: 20px; line-height: 1.8;">';
        $html .= '<li>The product arrived physically damaged, cracked, or non-functional.</li>';
        $html .= '<li>The product is materially different from the merchant listing (incorrect color, model, or size).</li>';
        $html .= '<li>Key parts, accessories, or package components are missing.</li>';
        $html .= '<li>The item is returned in its original product packaging with intact tags.</li>';
        $html .= '</ul>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">3. Return Pickup & Shipping</h3>';
        $html .= '<p>For defective or incorrectly fulfilled orders, BUYORA arranges doorstep courier pickup at <strong>no additional shipping charge</strong> to the consumer.</p>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">4. Refund Process & Timelines</h3>';
        $html .= '<p>Once the returned parcel is received at our fulfillment center and verified, refunds are released within <strong>3 to 5 business days</strong>:</p>';
        $html .= '<ul style="padding-left: 20px; line-height: 1.8;">';
        $html .= '<li><strong>Online Card / Alfa / Safepay:</strong> Recredited to the original card or bank account.</li>';
        $html .= '<li><strong>Cash on Delivery (COD):</strong> Refunded directly to your bank account, JazzCash, or EasyPaisa wallet.</li>';
        $html .= '</ul>';

        $html .= '<h3 style="color: #0F172A; margin-top: 20px;">5. How to Initiate a Return</h3>';
        $html .= '<p>Initiate a return directly through the BUYORA mobile app under <strong>Profile &gt; Orders &gt; Start Return</strong>, or email us at <a href="mailto:buyoramarketplace@gmail.com" style="color: #FF6B00; font-weight: 600;">buyoramarketplace@gmail.com</a> with your Order ID and photo proof.</p>';

        $html .= '</div>';

        $data = array('content' => $html);
        $this->render('dynPage', $head, $data);
    }

    private function goOut($page)
    {
        if ($page == null) {
            redirect();
        }
    }

}
