-- ============================================
-- Insert Blog Posts into Database
-- ============================================
-- Run this after creating the blog/FAQ structure
-- ============================================

-- Insert Blog Post 1: How to Take Attendance in Large Classes Efficiently (In-Depth Guide)
INSERT INTO blog_posts (
    slug,
    title,
    meta_description,
    author,
    featured_image,
    content,
    excerpt,
    category,
    tags,
    word_count,
    read_time,
    is_published,
    is_featured,
    published_at
) VALUES (
    'how-to-take-attendance-in-large-classes-efficiently',
    'How to Take Attendance in Large Classes Efficiently',
    'Discover proven strategies to take attendance in large classes efficiently using class check systems. Learn how to reduce attendance time from 15+ minutes to just 30 seconds with modern QR code technology.',
    'Class Check Team',
    'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200',
    'Comprehensive guide covering traditional attendance problems, modern class check solutions, QR code implementation, best practices, common challenges, and ROI analysis. Includes detailed step-by-step implementation guide.',
    'Taking attendance in large lecture halls with 100+ students can consume up to 20 minutes per class. Learn modern class check techniques that reduce this to under 30 seconds while improving accuracy.',
    'Guides & Tutorials',
    'class check, large classes, QR code attendance, efficient attendance, university attendance',
    2800,
    12,
    1,
    1,
    NOW()
);

-- Insert Blog Post 2: How to Reduce Class Attendance Time by 95% (Quick Read)
INSERT INTO blog_posts (
    slug,
    title,
    meta_description,
    author,
    featured_image,
    content,
    excerpt,
    category,
    tags,
    word_count,
    read_time,
    is_published,
    is_featured,
    published_at
) VALUES (
    'how-to-reduce-class-attendance-time-by-95-percent',
    'How to Reduce Class Attendance Time by 95%',
    'Discover the exact method to reduce class check attendance time from 15 minutes to just 30 seconds. Save 95% of attendance time with modern QR code systems.',
    'Class Check Team',
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1200',
    'Quick actionable guide showing the 3-step formula to reduce attendance time by 95%. Includes time savings calculator and real-world examples by class size.',
    'The average professor spends 15-20 minutes per class on attendance. Here\'s how to reduce that to 30 seconds using class check QR systems – a 95% time reduction.',
    'Tips & Tricks',
    'class check, time savings, QR attendance, fast attendance, efficiency',
    1100,
    6,
    1,
    0,
    NOW()
);

-- Insert Blog Post 3: How to Prevent Attendance Fraud (Quick Read)
INSERT INTO blog_posts (
    slug,
    title,
    meta_description,
    author,
    featured_image,
    content,
    excerpt,
    category,
    tags,
    word_count,
    read_time,
    is_published,
    is_featured,
    published_at
) VALUES (
    'how-to-prevent-attendance-fraud-in-university-classes',
    'How to Prevent Attendance Fraud in University Classes',
    'Stop students from faking attendance with these proven class check security strategies. Learn how to prevent proxy attendance, QR code sharing, and other fraud tactics.',
    'Class Check Team',
    'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1200',
    'Complete guide to attendance fraud prevention covering the 5 most common fraud tactics and how modern class check systems use layered security to prevent cheating.',
    'Attendance fraud costs universities millions and undermines academic integrity. Discover how modern class check systems prevent cheating better than traditional methods.',
    'Best Practices',
    'class check security, attendance fraud, prevent cheating, university security, QR code security',
    1200,
    7,
    1,
    0,
    NOW()
);

-- ============================================
-- Verify Blog Posts Were Inserted
-- ============================================

-- Check all blog posts
-- SELECT id, title, category, read_time, views, is_published, is_featured
-- FROM blog_posts
-- ORDER BY published_at DESC;

-- ============================================
-- Update Category Post Counts
-- ============================================

UPDATE blog_categories
SET post_count = (
    SELECT COUNT(*)
    FROM blog_posts
    WHERE category = 'Guides & Tutorials' AND is_published = 1
)
WHERE slug = 'guides-tutorials';

UPDATE blog_categories
SET post_count = (
    SELECT COUNT(*)
    FROM blog_posts
    WHERE category = 'Tips & Tricks' AND is_published = 1
)
WHERE slug = 'tips-tricks';

UPDATE blog_categories
SET post_count = (
    SELECT COUNT(*)
    FROM blog_posts
    WHERE category = 'Best Practices' AND is_published = 1
)
WHERE slug = 'best-practices';

-- ============================================
-- Success Message
-- ============================================

-- SELECT CONCAT('Successfully inserted ', COUNT(*), ' blog posts!') AS message
-- FROM blog_posts;
