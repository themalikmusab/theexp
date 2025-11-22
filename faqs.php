<?php
session_start();

// Check if user_id is set in the session to determine login status
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

// Include database connection
require_once __DIR__ . '/config/connection.php';

// SEO Meta Configuration
$page_title = "Class Check FAQs - Frequently Asked Questions About Attendance Systems";
$meta_description = "Find answers to all your questions about class check attendance systems. Searchable FAQ database covering setup, security, pricing, features, and troubleshooting.";
$meta_keywords = "class check faq, attendance system questions, QR code attendance help, classcheck support";
$canonical_url = "https://classcheck.me/faqs.php";

// Handle search and filters
$search_query = $_GET['search'] ?? '';
$selected_category = $_GET['category'] ?? 'all';

// Build SQL query
$where_clauses = ["is_published = 1"];
$params = [];

if ($selected_category !== 'all') {
    $where_clauses[] = "category = :category";
    $params[':category'] = $selected_category;
}

if (!empty($search_query)) {
    $where_clauses[] = "MATCH(question, answer) AGAINST(:search IN NATURAL LANGUAGE MODE)";
    $params[':search'] = $search_query;
}

$where_sql = implode(' AND ', $where_clauses);

// Get FAQs
try {
    $sql = "SELECT * FROM faqs WHERE $where_sql ORDER BY order_position ASC, id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $faqs = [];
}

// Get FAQ categories
try {
    $cat_stmt = $pdo->query("SELECT * FROM faq_categories ORDER BY order_position ASC");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Group FAQs by category
$faqs_by_category = [];
foreach ($faqs as $faq) {
    $cat = $faq['category'] ?? 'general';
    if (!isset($faqs_by_category[$cat])) {
        $faqs_by_category[$cat] = [];
    }
    $faqs_by_category[$cat][] = $faq;
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
    <meta name="twitter:card" content="summary">
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
            background: rgba(255, 255, 255, 0.9);
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

        /* Search Input */
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

        /* FAQ Item */
        .faq-item {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .faq-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .faq-question {
            cursor: pointer;
            transition: all 0.3s;
        }

        .faq-question:hover {
            color: #667eea;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .faq-answer.active {
            max-height: 2000px;
        }

        .faq-icon {
            transition: transform 0.3s ease;
        }

        .faq-icon.active {
            transform: rotate(180deg);
        }

        /* Category Card */
        .category-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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

        /* Helpful Button */
        .helpful-btn {
            transition: all 0.3s ease;
        }

        .helpful-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Reading Progress Bar -->
    <div id="reading-progress"></div>

    <!-- Navigation -->
    <nav class="glass fixed w-full z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold gradient-text">Classcheck</a>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-indigo-600 transition">Home</a>
                    <a href="blog.php" class="text-gray-700 hover:text-indigo-600 transition">Blog</a>
                    <a href="faqs.php" class="text-indigo-600 font-semibold">FAQs</a>
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
            <div class="text-6xl mb-6 float-animation">❓</div>
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6">
                <span class="gradient-text">Class Check FAQs</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 mb-8 max-w-3xl mx-auto">
                Find instant answers to all your questions about class check attendance systems
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-8">
                <form method="GET" action="faqs.php" class="relative" id="search-form">
                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="<?php echo htmlspecialchars($search_query); ?>"
                        placeholder="Search FAQs... (e.g., 'QR code security', 'pricing', 'how to start')"
                        class="search-input w-full px-6 py-4 rounded-full border-2 border-gray-300 focus:border-indigo-600 focus:outline-none text-lg"
                        autocomplete="off"
                    >
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-2 rounded-full hover:shadow-lg transition">
                        Search
                    </button>
                </form>
                <p class="text-sm text-gray-600 mt-3">
                    <?php if (!empty($search_query)): ?>
                        Showing results for "<?php echo htmlspecialchars($search_query); ?>" (<?php echo count($faqs); ?> results)
                        <a href="faqs.php" class="text-indigo-600 hover:underline ml-2">Clear search</a>
                    <?php else: ?>
                        Try searching: "QR code", "security", "pricing", "setup guide"
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="pb-12 px-4">
        <div class="max-w-5xl mx-auto" data-aos="fade-up">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                    <div class="text-4xl font-bold gradient-text mb-2"><?php echo count($faqs); ?>+</div>
                    <div class="text-gray-600">Questions Answered</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                    <div class="text-4xl font-bold gradient-text mb-2"><?php echo count($categories); ?></div>
                    <div class="text-gray-600">FAQ Categories</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">24/7</div>
                    <div class="text-gray-600">Instant Answers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Filter (Only show if not searching) -->
    <?php if (empty($search_query)): ?>
    <section class="pb-12 px-4">
        <div class="max-w-7xl mx-auto" data-aos="fade-up">
            <h2 class="text-2xl font-bold text-center mb-8 text-gray-900">Browse by Category</h2>
            <div class="grid md:grid-cols-4 gap-6">
                <?php foreach ($categories as $cat): ?>
                    <a href="faqs.php?category=<?php echo urlencode($cat['slug']); ?>"
                       class="category-card bg-white rounded-2xl p-6 shadow-lg text-center <?php echo $selected_category === $cat['slug'] ? 'ring-4 ring-indigo-600' : ''; ?>">
                        <div class="text-4xl mb-3"><?php echo htmlspecialchars($cat['icon']); ?></div>
                        <h3 class="font-bold text-lg mb-2 text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <span class="text-indigo-600 font-semibold text-sm">
                            View Questions →
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if ($selected_category !== 'all'): ?>
                <div class="text-center mt-6">
                    <a href="faqs.php" class="text-indigo-600 hover:underline font-semibold">← View All Categories</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQs Section -->
    <section class="pb-16 px-4">
        <div class="max-w-4xl mx-auto">
            <?php if (empty($faqs)): ?>
                <div class="text-center py-16 bg-white rounded-3xl shadow-lg" data-aos="fade-up">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No FAQs found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search or browse by category</p>
                    <a href="faqs.php" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-full hover:shadow-lg transition">
                        View All FAQs
                    </a>
                </div>
            <?php else: ?>
                <?php if (!empty($search_query)): ?>
                    <!-- Search Results -->
                    <div class="space-y-4">
                        <?php foreach ($faqs as $faq): ?>
                            <div class="faq-item bg-white rounded-2xl p-6 shadow-lg" data-aos="fade-up">
                                <div class="faq-question flex justify-between items-start" onclick="toggleFAQ(this)">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                                            <?php echo htmlspecialchars($faq['question']); ?>
                                        </h3>
                                        <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold">
                                            <?php
                                            $cat_info = array_filter($categories, function($c) use ($faq) {
                                                return $c['slug'] === $faq['category'];
                                            });
                                            echo $cat_info ? htmlspecialchars(reset($cat_info)['name']) : 'General';
                                            ?>
                                        </span>
                                    </div>
                                    <svg class="faq-icon w-6 h-6 text-indigo-600 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                                <div class="faq-answer mt-4">
                                    <div class="text-gray-700 leading-relaxed">
                                        <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Was this helpful?</span>
                                        <div class="flex gap-2">
                                            <button onclick="markHelpful(<?php echo $faq['id']; ?>, true)" class="helpful-btn px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm font-semibold">
                                                👍 Yes
                                            </button>
                                            <button onclick="markHelpful(<?php echo $faq['id']; ?>, false)" class="helpful-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                                                👎 No
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Category View -->
                    <?php foreach ($faqs_by_category as $cat_slug => $cat_faqs): ?>
                        <?php
                        $cat_info = array_filter($categories, function($c) use ($cat_slug) {
                            return $c['slug'] === $cat_slug;
                        });
                        $cat_name = $cat_info ? reset($cat_info)['name'] : ucfirst($cat_slug);
                        $cat_icon = $cat_info ? reset($cat_info)['icon'] : '📋';
                        ?>
                        <div class="mb-12" data-aos="fade-up">
                            <h2 class="text-3xl font-bold mb-6 flex items-center text-gray-900">
                                <span class="text-4xl mr-3"><?php echo $cat_icon; ?></span>
                                <?php echo htmlspecialchars($cat_name); ?>
                            </h2>
                            <div class="space-y-4">
                                <?php foreach ($cat_faqs as $faq): ?>
                                    <div class="faq-item bg-white rounded-2xl p-6 shadow-lg">
                                        <div class="faq-question flex justify-between items-center" onclick="toggleFAQ(this)">
                                            <h3 class="text-lg font-bold text-gray-900 flex-1">
                                                <?php echo htmlspecialchars($faq['question']); ?>
                                            </h3>
                                            <svg class="faq-icon w-6 h-6 text-indigo-600 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                        <div class="faq-answer mt-4">
                                            <div class="text-gray-700 leading-relaxed">
                                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                            </div>
                                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                                <span class="text-sm text-gray-600">Was this helpful?</span>
                                                <div class="flex gap-2">
                                                    <button onclick="markHelpful(<?php echo $faq['id']; ?>, true)" class="helpful-btn px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm font-semibold">
                                                        👍 Yes
                                                    </button>
                                                    <button onclick="markHelpful(<?php echo $faq['id']; ?>, false)" class="helpful-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                                                        👎 No
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Still Have Questions? CTA -->
    <section class="pb-16 px-4">
        <div class="max-w-4xl mx-auto" data-aos="zoom-in">
            <div class="glass rounded-3xl p-12 shadow-2xl text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 gradient-text">
                    Still Have Questions?
                </h2>
                <p class="text-xl text-gray-700 mb-8">
                    Can't find what you're looking for? Our team is here to help!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-full hover:shadow-lg transition text-lg font-semibold">
                        Try Class Check Free
                    </a>
                    <a href="#" class="bg-white text-indigo-600 px-8 py-4 rounded-full hover:shadow-lg transition text-lg font-semibold border-2 border-indigo-600">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Resources -->
    <section class="pb-16 px-4">
        <div class="max-w-7xl mx-auto" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">📚 Helpful Class Check Resources</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="blog/how-to-take-attendance-in-large-classes-efficiently.php" class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition">
                    <div class="text-3xl mb-3">📖</div>
                    <h3 class="font-bold text-lg mb-2 text-indigo-600">Setup Guide</h3>
                    <p class="text-gray-600 text-sm">Learn how to take attendance efficiently in large classes</p>
                </a>
                <a href="class-check-pricing-guide.php" class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition">
                    <div class="text-3xl mb-3">💰</div>
                    <h3 class="font-bold text-lg mb-2 text-indigo-600">Pricing Guide</h3>
                    <p class="text-gray-600 text-sm">Explore class check pricing and find your perfect plan</p>
                </a>
                <a href="class-check-security-features.php" class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition">
                    <div class="text-3xl mb-3">🔒</div>
                    <h3 class="font-bold text-lg mb-2 text-indigo-600">Security Features</h3>
                    <p class="text-gray-600 text-sm">Understand how class check protects your data</p>
                </a>
                <a href="blog.php" class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition">
                    <div class="text-3xl mb-3">✍️</div>
                    <h3 class="font-bold text-lg mb-2 text-indigo-600">Blog & Guides</h3>
                    <p class="text-gray-600 text-sm">Read more class check articles and tutorials</p>
                </a>
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

        // Toggle FAQ
        function toggleFAQ(element) {
            const faqItem = element.closest('.faq-item');
            const answer = faqItem.querySelector('.faq-answer');
            const icon = faqItem.querySelector('.faq-icon');

            // Close all other FAQs
            document.querySelectorAll('.faq-answer').forEach(item => {
                if (item !== answer) {
                    item.classList.remove('active');
                }
            });
            document.querySelectorAll('.faq-icon').forEach(item => {
                if (item !== icon) {
                    item.classList.remove('active');
                }
            });

            // Toggle current FAQ
            answer.classList.toggle('active');
            icon.classList.toggle('active');

            // Track in analytics
            if (typeof gtag === 'function' && answer.classList.contains('active')) {
                gtag('event', 'faq_view', {
                    'question': element.querySelector('h3').textContent
                });
            }
        }

        // Mark as helpful
        function markHelpful(faqId, isHelpful) {
            // Send to analytics
            if (typeof gtag === 'function') {
                gtag('event', 'faq_helpful', {
                    'faq_id': faqId,
                    'helpful': isHelpful
                });
            }

            // Show thank you message
            alert(isHelpful ? 'Thank you for your feedback!' : 'We\'ll work on improving this answer. Thanks for your feedback!');

            // In a real implementation, you'd send this to the server
            // fetch('api/faq-feedback.php', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ faq_id: faqId, helpful: isHelpful })
            // });
        }

        // Search tracking
        document.getElementById('search-form').addEventListener('submit', function(e) {
            const searchQuery = document.getElementById('search-input').value;
            if (typeof gtag === 'function' && searchQuery) {
                gtag('event', 'search', {
                    'search_term': searchQuery,
                    'search_type': 'faq'
                });
            }
        });

        // Auto-expand first FAQ if on category page
        <?php if ($selected_category !== 'all' && count($faqs) > 0): ?>
        window.addEventListener('load', function() {
            const firstFAQ = document.querySelector('.faq-question');
            if (firstFAQ) {
                setTimeout(() => toggleFAQ(firstFAQ), 500);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
