-- ============================================
-- Class Check Blog & FAQ Database Structure
-- ============================================
-- Created: 2025-11-22
-- Purpose: Blog posts and searchable FAQ system
-- ============================================

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_description TEXT,
    author VARCHAR(100) DEFAULT 'Class Check Team',
    featured_image VARCHAR(255),
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    category VARCHAR(100),
    tags TEXT,
    word_count INT DEFAULT 0,
    read_time INT DEFAULT 5,
    views INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_published (is_published, published_at),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Categories Table
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    post_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ Table (Searchable)
CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    order_position INT DEFAULT 0,
    views INT DEFAULT 0,
    helpful_count INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_published (is_published),
    INDEX idx_order (order_position),
    FULLTEXT idx_search (question, answer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ Categories Table
CREATE TABLE IF NOT EXISTS faq_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(100),
    description TEXT,
    order_position INT DEFAULT 0,
    question_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_order (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Comments Table (Optional - for future use)
CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT,
    author_name VARCHAR(100),
    author_email VARCHAR(255),
    comment_text TEXT NOT NULL,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Initial Blog Categories
-- ============================================

INSERT INTO blog_categories (name, slug, description) VALUES
('Guides & Tutorials', 'guides-tutorials', 'Comprehensive guides for using class check systems'),
('Best Practices', 'best-practices', 'Industry best practices for attendance management'),
('Product Updates', 'product-updates', 'Latest features and improvements'),
('Case Studies', 'case-studies', 'Real-world success stories'),
('Tips & Tricks', 'tips-tricks', 'Quick tips for better attendance management'),
('Industry Insights', 'industry-insights', 'Educational technology trends and analysis');

-- ============================================
-- Insert Initial FAQ Categories
-- ============================================

INSERT INTO faq_categories (name, slug, icon, description, order_position) VALUES
('Getting Started', 'getting-started', '🚀', 'Learn the basics of Class Check', 1),
('Account & Billing', 'account-billing', '💳', 'Payment and subscription questions', 2),
('Features', 'features', '⚡', 'Understanding Class Check features', 3),
('Security & Privacy', 'security-privacy', '🔒', 'Data protection and security', 4),
('Technical Support', 'technical-support', '🛠️', 'Troubleshooting and technical help', 5),
('Mobile & Apps', 'mobile-apps', '📱', 'Mobile application questions', 6),
('Integration', 'integration', '🔗', 'Connect with other systems', 7),
('Best Practices', 'best-practices', '⭐', 'Tips for optimal usage', 8);

-- ============================================
-- Insert Sample FAQs (You can customize these)
-- ============================================

INSERT INTO faqs (question, answer, category, order_position, is_published) VALUES
-- Getting Started
('What is Class Check?', 'Class Check is a modern, QR code-based attendance system designed for educational institutions. It replaces traditional roll call with quick QR code scanning, reducing attendance time from 15+ minutes to just 30 seconds per class.', 'Getting Started', 1, 1),

('How do I get started with Class Check?', 'Getting started is easy! Simply register for a free account, create your first class, and generate a QR code. Students can then scan the code using their mobile devices to mark their attendance. The entire setup takes less than 5 minutes.', 'Getting Started', 2, 1),

('Do students need to download an app?', 'No app download is required! Students can scan QR codes directly from their mobile browser. However, we are developing a dedicated mobile app with additional features like AI tutors, flashcards, and audiobooks that will be available soon.', 'Getting Started', 3, 1),

-- Features
('What features does Class Check offer?', 'Class Check offers QR code attendance, real-time analytics, automated reports, AI-powered smart notes, absence tracking, export capabilities, and seamless integration options. We are constantly adding new features based on user feedback.', 'Features', 1, 1),

('Can I export attendance data?', 'Yes! You can export attendance data in multiple formats including CSV, Excel, and PDF. This makes it easy to integrate with your existing systems or share reports with administrators.', 'Features', 2, 1),

('How does the QR code system work?', 'Each class session gets a unique, time-limited QR code. Students scan it once to mark their attendance. The QR code expires after the class period and cannot be reused, ensuring security and preventing fraud.', 'Features', 3, 1),

-- Security & Privacy
('Is my data safe with Class Check?', 'Absolutely! We store all data on our own secure servers with encryption both in transit (HTTPS/TLS) and at rest (database encryption). We implement strict access controls, secure authentication, and regular security audits to protect your information.', 'Security & Privacy', 1, 1),

('Can students fake their attendance?', 'Class Check prevents attendance fraud through multiple security measures: unique QR codes per session, one-time scanning per student, time-limited codes, and planned location-based verification. This is far more secure than paper-based systems.', 'Security & Privacy', 2, 1),

('Do you comply with educational data privacy regulations?', 'We take data privacy seriously and follow best practices for educational data protection. We are working towards FERPA and GDPR compliance certifications as we scale. You have full control over your data and can delete it at any time.', 'Security & Privacy', 3, 1),

-- Account & Billing
('Is Class Check free to use?', 'Class Check offers a free plan for individual instructors with basic features. We also offer premium plans with advanced analytics, unlimited classes, and priority support. Our mobile app (coming soon) will use a freemium model.', 'Account & Billing', 1, 1),

('How do I upgrade my account?', 'You can upgrade your account anytime from your dashboard. Visit the pricing page to compare plans and select the one that best fits your needs. Upgrades are instant and you will have immediate access to premium features.', 'Account & Billing', 2, 1),

-- Technical Support
('What browsers are supported?', 'Class Check works on all modern browsers including Chrome, Firefox, Safari, Edge, and mobile browsers. For the best experience, we recommend using the latest version of your preferred browser.', 'Technical Support', 1, 1),

('What if the QR code does not scan?', 'If a QR code does not scan, try these steps: 1) Ensure good lighting, 2) Clean your camera lens, 3) Hold the phone steady, 4) Refresh the page to generate a new code. If issues persist, contact our support team.', 'Technical Support', 2, 1),

-- Mobile & Apps
('When will the mobile app be available?', 'We are actively developing native iOS and Android apps that will launch simultaneously once we establish strategic partnerships with universities. Join our waitlist to be notified when the app launches and get early access!', 'Mobile & Apps', 1, 1),

('What features will the mobile app have?', 'The mobile app will include all current web features plus AI tutors, voice cloning of favorite teachers, on-the-go flashcards, audiobooks, enhanced QR scanning, push notifications, and offline capabilities. Users can also submit feature requests!', 'Mobile & Apps', 2, 1);

-- ============================================
-- Useful Queries for Blog Management
-- ============================================

-- Get all published blog posts ordered by date
-- SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC;

-- Search FAQs
-- SELECT * FROM faqs WHERE MATCH(question, answer) AGAINST('attendance' IN NATURAL LANGUAGE MODE);

-- Get FAQs by category
-- SELECT f.*, c.name as category_name FROM faqs f
-- JOIN faq_categories c ON f.category = c.slug
-- WHERE c.slug = 'getting-started' ORDER BY f.order_position;

-- Update blog view count
-- UPDATE blog_posts SET views = views + 1 WHERE slug = 'blog-slug';

-- Get popular blog posts
-- SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY views DESC LIMIT 10;
