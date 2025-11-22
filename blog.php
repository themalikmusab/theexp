<?php
session_start();

// Check if user_id is set in the session to determine login status
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

// Include database connection
require_once __DIR__ . '/config/connection.php';

// SEO Meta Configuration
$page_title = "Class Check Blog - Attendance Management Guides & Tips";
$meta_description = "Expert guides, tutorials, and best practices for class check attendance systems. Learn how to efficiently manage attendance in large classes with QR codes and modern technology.";
$meta_keywords = "class check blog, attendance management, QR code attendance, educational technology, classroom management tips";
$canonical_url = "https://classcheck.me/blog.php";

// Pagination
$posts_per_page = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $posts_per_page;

// Filters
$selected_category = $_GET['category'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Build SQL query
$where_clauses = ["is_published = 1"];
$params = [];

if ($selected_category !== 'all') {
    $where_clauses[] = "category = :category";
    $params[':category'] = $selected_category;
}

if (!empty($search_query)) {
    $where_clauses[] = "(title LIKE :search OR excerpt LIKE :search OR content LIKE :search)";
    $params[':search'] = '%' . $search_query . '%';
}

$where_sql = implode(' AND ', $where_clauses);

// Get total posts for pagination
try {
    $count_sql = "SELECT COUNT(*) FROM blog_posts WHERE $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);
} catch (PDOException $e) {
    $total_posts = 0;
    $total_pages = 1;
}

// Get blog posts
try {
    $sql = "SELECT * FROM blog_posts
            WHERE $where_sql
            ORDER BY published_at DESC, created_at DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $blog_posts = [];
}

// Get categories for filter
try {
    $cat_stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Get featured post
try {
    $featured_stmt = $pdo->query("SELECT * FROM blog_posts WHERE is_published = 1 AND is_featured = 1 ORDER BY published_at DESC LIMIT 1");
    $featured_post = $featured_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_post = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <meta name="author" content="Class Check">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass Morphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Reading Progress Bar */
        #reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            z-index: 9999;
            transition: width 0.2s ease;
        }

        /* Blog Card Hover Effects */
        .blog-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .blog-card-image {
            transition: all 0.4s ease;
            overflow: hidden;
        }

        .blog-card:hover .blog-card-image img {
            transform: scale(1.1);
        }

        /* Search Input Animation */
        .search-input {
            transition: all 0.3s ease;
        }

        .search-input:focus {
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
        }

        /* Category Pills */
        .category-pill {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .category-pill.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Pagination */
        .pagination-btn {
            transition: all 0.3s ease;
        }

        .pagination-btn:hover:not(.active) {
            background: #667eea;
            color: white;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Featured Post Ribbon */
        .featured-ribbon {
            position: absolute;
            top: 20px;
            right: -35px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 8px 40px;
            transform: rotate(45deg);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-weight: 600;
            font-size: 12px;
            z-index: 10;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.8); }
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Reading Progress Bar -->
    <div id="reading-progress"></div>

    <!-- Navigation (Same as your index.php) -->
    <nav class="glass fixed w-full z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold gradient-text">Classcheck</a>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-indigo-600 transition">Home</a>
                    <a href="blog.php" class="text-indigo-600 font-semibold">Blog</a>
                    <a href="faqs.php" class="text-gray-700 hover:text-indigo-600 transition">FAQs</a>
                    <a href="class-check-pricing-guide.php" class="text-gray-700 hover:text-indigo-600 transition">Pricing</a>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <a href="pages/dashboard.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="pages/login.php" class="text-gray-700 hover:text-indigo-600 transition">Login</a>
                        <a href="pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-16 px-4">
        <div class="max-w-7xl mx-auto text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6">
                <span class="gradient-text">Class Check Blog</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 mb-8 max-w-3xl mx-auto">
                Expert guides, tutorials, and insights for modern attendance management with class check systems
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-12">
                <form method="GET" action="blog.php" class="relative">
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search_query); ?>"
                        placeholder="Search blog posts..."
                        class="search-input w-full px-6 py-4 rounded-full border-2 border-gray-300 focus:border-indigo-600 focus:outline-none text-lg"
                    >
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-2 rounded-full hover:shadow-lg transition">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Category Filter -->
    <section class="pb-8 px-4">
        <div class="max-w-7xl mx-auto" data-aos="fade-up">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="blog.php?category=all" class="category-pill px-6 py-3 rounded-full <?php echo $selected_category === 'all' ? 'active' : 'bg-white'; ?> shadow-md">
                    All Posts
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="blog.php?category=<?php echo urlencode($cat['slug']); ?>"
                       class="category-pill px-6 py-3 rounded-full <?php echo $selected_category === $cat['slug'] ? 'active' : 'bg-white'; ?> shadow-md">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Post -->
    <?php if ($featured_post && $page === 1 && $selected_category === 'all' && empty($search_query)): ?>
    <section class="pb-16 px-4">
        <div class="max-w-7xl mx-auto" data-aos="zoom-in">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden blog-card pulse-glow">
                <div class="featured-ribbon">FEATURED</div>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="blog-card-image h-64 md:h-full">
                        <img src="<?php echo htmlspecialchars($featured_post['featured_image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800'); ?>"
                             alt="<?php echo htmlspecialchars($featured_post['title']); ?>"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-4 py-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold">
                                <?php echo htmlspecialchars($featured_post['category'] ?? 'Featured'); ?>
                            </span>
                            <span class="text-gray-500 text-sm">
                                <?php echo $featured_post['read_time']; ?> min read
                            </span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold mb-4 gradient-text">
                            <?php echo htmlspecialchars($featured_post['title']); ?>
                        </h2>
                        <p class="text-gray-600 mb-6 text-lg">
                            <?php echo htmlspecialchars(substr($featured_post['excerpt'], 0, 200)); ?>...
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                <span>By <?php echo htmlspecialchars($featured_post['author']); ?></span>
                                <span>•</span>
                                <span><?php echo date('M d, Y', strtotime($featured_post['published_at'] ?? $featured_post['created_at'])); ?></span>
                            </div>
                            <a href="blog/<?php echo htmlspecialchars($featured_post['slug']); ?>.php"
                               class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-full hover:shadow-lg transition inline-block">
                                Read More →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Blog Posts Grid -->
    <section class="pb-16 px-4">
        <div class="max-w-7xl mx-auto">
            <?php if (empty($blog_posts)): ?>
                <div class="text-center py-16" data-aos="fade-up">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No posts found</h3>
                    <p class="text-gray-600">Try adjusting your search or filter criteria</p>
                    <a href="blog.php" class="inline-block mt-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-full hover:shadow-lg transition">
                        View All Posts
                    </a>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($blog_posts as $post): ?>
                        <article class="bg-white rounded-2xl shadow-lg overflow-hidden blog-card" data-aos="fade-up">
                            <div class="blog-card-image h-48">
                                <img src="<?php echo htmlspecialchars($post['featured_image'] ?? 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600'); ?>"
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold">
                                        <?php echo htmlspecialchars($post['category'] ?? 'General'); ?>
                                    </span>
                                    <span class="text-gray-500 text-xs">
                                        <?php echo $post['read_time']; ?> min read
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold mb-3 text-gray-800 line-clamp-2">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h3>
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars(substr($post['excerpt'], 0, 150)); ?>...
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 text-sm">
                                        <?php echo date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                    </span>
                                    <a href="blog/<?php echo htmlspecialchars($post['slug']); ?>.php"
                                       class="text-indigo-600 font-semibold hover:text-purple-600 transition">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <section class="pb-16 px-4">
        <div class="max-w-7xl mx-auto" data-aos="fade-up">
            <div class="flex justify-center items-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&category=<?php echo urlencode($selected_category); ?>&search=<?php echo urlencode($search_query); ?>"
                       class="pagination-btn px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">
                        ← Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($selected_category); ?>&search=<?php echo urlencode($search_query); ?>"
                       class="pagination-btn px-4 py-2 rounded-lg shadow hover:shadow-lg transition <?php echo $i === $page ? 'active' : 'bg-white'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&category=<?php echo urlencode($selected_category); ?>&search=<?php echo urlencode($search_query); ?>"
                       class="pagination-btn px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">
                        Next →
                    </a>
                <?php endif; ?>
            </div>
            <p class="text-center text-gray-600 mt-4">
                Showing <?php echo min($offset + 1, $total_posts); ?>-<?php echo min($offset + $posts_per_page, $total_posts); ?> of <?php echo $total_posts; ?> posts
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="py-16 px-4">
        <div class="max-w-4xl mx-auto text-center" data-aos="zoom-in">
            <div class="glass rounded-3xl p-12 shadow-2xl">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 gradient-text">
                    Ready to Transform Your Attendance?
                </h2>
                <p class="text-xl text-gray-700 mb-8">
                    Join thousands of educators using Class Check to save time and improve accuracy
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-full hover:shadow-lg transition text-lg font-semibold">
                        Start Free Trial
                    </a>
                    <a href="class-check-for-universities.php" class="bg-white text-indigo-600 px-8 py-4 rounded-full hover:shadow-lg transition text-lg font-semibold border-2 border-indigo-600">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 gradient-text">Classcheck</h3>
                    <p class="text-gray-400">Modern QR code-based attendance system for educational institutions.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Class Check Solutions</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="class-check-for-universities.php" class="hover:text-white transition">For Universities</a></li>
                        <li><a href="class-check-vs-traditional-attendance.php" class="hover:text-white transition">vs Traditional</a></li>
                        <li><a href="class-check-pricing-guide.php" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="class-check-security-features.php" class="hover:text-white transition">Security</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="blog.php" class="hover:text-white transition">Blog</a></li>
                        <li><a href="faqs.php" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="index.php" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Classcheck. All rights reserved. Built with ❤️ for educators.</p>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- JavaScript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Reading Progress Bar
        function updateReadingProgress() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;
            document.getElementById('reading-progress').style.width = scrollPercentage + '%';
        }

        window.addEventListener('scroll', updateReadingProgress);
        window.addEventListener('resize', updateReadingProgress);
        updateReadingProgress();

        // Track blog list views
        if (typeof gtag === 'function') {
            gtag('event', 'page_view', {
                'page_title': 'Class Check Blog',
                'page_location': window.location.href,
                'page_path': window.location.pathname
            });
        }

        // Search tracking
        const searchForm = document.querySelector('form[action="blog.php"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                const searchQuery = this.querySelector('input[name="search"]').value;
                if (typeof gtag === 'function' && searchQuery) {
                    gtag('event', 'search', {
                        'search_term': searchQuery
                    });
                }
            });
        }
    </script>
</body>
</html>
