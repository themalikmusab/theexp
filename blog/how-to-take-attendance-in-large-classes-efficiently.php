<?php
session_start();

// Check if user_id is set in the session to determine login status
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

// Include database connection
require_once __DIR__ . '/../config/connection.php';

// Get blog post slug from URL
$post_slug = 'how-to-take-attendance-in-large-classes-efficiently';

// Fetch blog post from database (and increment view count)
try {
    // Get post
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :slug AND is_published = 1");
    $stmt->execute([':slug' => $post_slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Increment view count if post exists
    if ($post) {
        $update_stmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id");
        $update_stmt->execute([':id' => $post['id']]);
    }
} catch (PDOException $e) {
    $post = null;
}

// Default data if post doesn't exist in database yet
if (!$post) {
    $post = [
        'title' => 'How to Take Attendance in Large Classes Efficiently',
        'meta_description' => 'Discover proven strategies to take attendance in large classes efficiently using class check systems. Learn how to reduce attendance time from 15+ minutes to just 30 seconds with modern QR code technology.',
        'author' => 'Class Check Team',
        'category' => 'Guides & Tutorials',
        'read_time' => 12,
        'published_at' => date('Y-m-d H:i:s'),
        'featured_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200',
        'excerpt' => 'Taking attendance in large lecture halls with 100+ students can consume up to 20 minutes per class. Learn modern class check techniques that reduce this to under 30 seconds while improving accuracy.'
    ];
}

// SEO Configuration
$page_title = htmlspecialchars($post['title']) . " - Class Check Blog";
$meta_description = htmlspecialchars($post['meta_description']);
$canonical_url = "https://classcheck.me/blog/how-to-take-attendance-in-large-classes-efficiently.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="how to take attendance in large classes, class check, attendance for large classes, efficient attendance, QR code attendance, classroom management, large lecture halls, student attendance">
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($post['featured_image']); ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $meta_description; ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($post['featured_image']); ?>">

    <!-- Schema.org Article Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo addslashes($post['title']); ?>",
        "description": "<?php echo addslashes($post['meta_description']); ?>",
        "image": "<?php echo htmlspecialchars($post['featured_image']); ?>",
        "author": {
            "@type": "Organization",
            "name": "<?php echo addslashes($post['author']); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Classcheck",
            "logo": {
                "@type": "ImageObject",
                "url": "https://classcheck.me/logo.png"
            }
        },
        "datePublished": "<?php echo date('c', strtotime($post['published_at'])); ?>",
        "dateModified": "<?php echo date('c', strtotime($post['published_at'])); ?>"
    }
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
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

        /* Article Content Styling */
        .article-content {
            font-size: 18px;
            line-height: 1.8;
            color: #334155;
        }

        .article-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }

        .article-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }

        .article-content ul, .article-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .article-content li {
            margin-bottom: 0.75rem;
        }

        .article-content strong {
            color: #1e293b;
            font-weight: 600;
        }

        .article-content a {
            color: #667eea;
            text-decoration: underline;
            transition: color 0.3s;
        }

        .article-content a:hover {
            color: #764ba2;
        }

        /* Highlight Box */
        .highlight-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0.5rem;
        }

        /* Stats Box */
        .stats-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            transition: transform 0.3s;
        }

        .stats-box:hover {
            transform: translateY(-5px);
        }

        /* Table of Contents */
        .toc {
            background: #f1f5f9;
            border-radius: 1rem;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .toc a {
            color: #667eea;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0;
            transition: all 0.3s;
        }

        .toc a:hover {
            color: #764ba2;
            padding-left: 1rem;
        }

        /* Share Buttons */
        .share-btn {
            transition: all 0.3s;
        }

        .share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
                    <a href="../index.php" class="text-2xl font-bold gradient-text">Classcheck</a>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-indigo-600 transition">Home</a>
                    <a href="../blog.php" class="text-indigo-600 font-semibold">Blog</a>
                    <a href="../faqs.php" class="text-gray-700 hover:text-indigo-600 transition">FAQs</a>
                    <a href="../class-check-pricing-guide.php" class="text-gray-700 hover:text-indigo-600 transition">Pricing</a>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <a href="../pages/dashboard.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="../pages/login.php" class="text-gray-700 hover:text-indigo-600 transition">Login</a>
                        <a href="../pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <article class="pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Breadcrumbs -->
            <nav class="mb-8" data-aos="fade-right">
                <ul class="flex items-center space-x-2 text-sm text-gray-600">
                    <li><a href="../index.php" class="hover:text-indigo-600">Home</a></li>
                    <li>→</li>
                    <li><a href="../blog.php" class="hover:text-indigo-600">Blog</a></li>
                    <li>→</li>
                    <li class="text-indigo-600">How to Take Attendance</li>
                </ul>
            </nav>

            <!-- Article Meta -->
            <div class="mb-6" data-aos="fade-up">
                <span class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold">
                    <?php echo htmlspecialchars($post['category']); ?>
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight text-gray-900" data-aos="fade-up">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <!-- Author & Date -->
            <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200" data-aos="fade-up">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        CC
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($post['author']); ?></p>
                        <p class="text-sm text-gray-600">
                            <?php echo date('F d, Y', strtotime($post['published_at'])); ?> · <?php echo $post['read_time']; ?> min read
                        </p>
                    </div>
                </div>

                <!-- Share Buttons -->
                <div class="flex items-center space-x-2">
                    <button class="share-btn p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600" onclick="shareOnTwitter()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                    </button>
                    <button class="share-btn p-2 bg-blue-700 text-white rounded-full hover:bg-blue-800" onclick="shareOnFacebook()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path></svg>
                    </button>
                    <button class="share-btn p-2 bg-green-500 text-white rounded-full hover:bg-green-600" onclick="copyLink()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl" data-aos="zoom-in">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                     class="w-full h-auto">
            </div>

            <!-- Article Content -->
            <div class="article-content">
                <!-- Introduction -->
                <div class="text-xl text-gray-700 mb-8 leading-relaxed" data-aos="fade-up">
                    <p><strong>Taking attendance in large classes is one of the biggest time-wasters in higher education.</strong> A typical 300-student lecture can lose up to 20 minutes of valuable teaching time just calling out names. That's over 6 hours per semester – an entire week of instruction lost to administrative tasks.</p>

                    <p>But it doesn't have to be this way. Modern <strong>class check systems</strong> can reduce attendance time from 15+ minutes to under 30 seconds while improving accuracy and student engagement.</p>
                </div>

                <!-- Table of Contents -->
                <div class="toc" data-aos="fade-up">
                    <h3 class="text-xl font-bold mb-4 text-gray-900">📋 Table of Contents</h3>
                    <a href="#the-problem">The Problem with Traditional Attendance</a>
                    <a href="#modern-solutions">Modern Class Check Solutions</a>
                    <a href="#qr-code-method">The QR Code Method (Fastest)</a>
                    <a href="#implementation">Step-by-Step Implementation</a>
                    <a href="#best-practices">Best Practices for Large Classes</a>
                    <a href="#common-challenges">Overcoming Common Challenges</a>
                    <a href="#roi-analysis">ROI Analysis & Time Savings</a>
                    <a href="#conclusion">Conclusion</a>
                </div>

                <!-- Section 1: The Problem -->
                <h2 id="the-problem" data-aos="fade-up">The Problem with Traditional Attendance in Large Classes</h2>

                <p data-aos="fade-up">Traditional attendance methods become exponentially worse as class size increases:</p>

                <div class="grid md:grid-cols-3 gap-6 my-8" data-aos="fade-up">
                    <div class="stats-box">
                        <div class="text-4xl font-bold mb-2">15-20 min</div>
                        <div class="text-sm opacity-90">Average time for 100+ students</div>
                    </div>
                    <div class="stats-box">
                        <div class="text-4xl font-bold mb-2">25-40%</div>
                        <div class="text-sm opacity-90">Error rate with paper sheets</div>
                    </div>
                    <div class="stats-box">
                        <div class="text-4xl font-bold mb-2">$2,400</div>
                        <div class="text-sm opacity-90">Cost per year in lost teaching time</div>
                    </div>
                </div>

                <h3 data-aos="fade-up">Why Traditional Methods Fail at Scale</h3>

                <ul data-aos="fade-up">
                    <li><strong>Manual Roll Call:</strong> Calling 200 names takes 15+ minutes, disrupts flow, and students tune out</li>
                    <li><strong>Paper Sign-in Sheets:</strong> Students sign for absent friends, illegible handwriting, sheets get lost</li>
                    <li><strong>Clicker Systems:</strong> Expensive hardware ($50+ per student), students forget devices, battery issues</li>
                    <li><strong>Raising Hands:</strong> Impossible to track accurately with 100+ students, no digital record</li>
                    <li><strong>Attendance Apps (manual check):</strong> Still requires scrolling through lists, prone to mistakes</li>
                </ul>

                <div class="highlight-box" data-aos="fade-up">
                    <p class="font-semibold text-lg mb-2">💡 Key Insight</p>
                    <p>The fundamental problem is that traditional methods don't scale. What works for 20 students becomes impossible for 200. You need a <strong>class check system designed for large lectures from the ground up</strong>.</p>
                </div>

                <!-- Section 2: Modern Solutions -->
                <h2 id="modern-solutions" data-aos="fade-up">Modern Class Check Solutions for Large Classes</h2>

                <p data-aos="fade-up">Several modern approaches exist for efficient large-class attendance. Here's how they compare:</p>

                <div class="overflow-x-auto my-8" data-aos="fade-up">
                    <table class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
                        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">Method</th>
                                <th class="px-6 py-4 text-left">Time for 200 Students</th>
                                <th class="px-6 py-4 text-left">Accuracy</th>
                                <th class="px-6 py-4 text-left">Cost</th>
                                <th class="px-6 py-4 text-left">Best For</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 font-semibold">QR Code Class Check</td>
                                <td class="px-6 py-4 text-green-600 font-bold">30 seconds</td>
                                <td class="px-6 py-4">99%+</td>
                                <td class="px-6 py-4">Free - $50/mo</td>
                                <td class="px-6 py-4">All class sizes</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold">Bluetooth Beacons</td>
                                <td class="px-6 py-4">2-3 minutes</td>
                                <td class="px-6 py-4">85-90%</td>
                                <td class="px-6 py-4">$500+ setup</td>
                                <td class="px-6 py-4">Fixed classrooms</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold">Facial Recognition</td>
                                <td class="px-6 py-4">5-8 minutes</td>
                                <td class="px-6 py-4">80-95%</td>
                                <td class="px-6 py-4">$1000+ setup</td>
                                <td class="px-6 py-4">Privacy concerns</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold">Mobile App Check-in</td>
                                <td class="px-6 py-4">1-2 minutes</td>
                                <td class="px-6 py-4">95%+</td>
                                <td class="px-6 py-4">Free - $100/mo</td>
                                <td class="px-6 py-4">Tech-savvy students</td>
                            </tr>
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 font-semibold">Traditional Roll Call</td>
                                <td class="px-6 py-4 text-red-600">15-20 minutes</td>
                                <td class="px-6 py-4">60-75%</td>
                                <td class="px-6 py-4">Free</td>
                                <td class="px-6 py-4">Small classes only</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p data-aos="fade-up"><strong>Winner: QR Code Class Check Systems</strong> – They offer the fastest attendance capture, highest accuracy, lowest cost, and work on any device without additional hardware.</p>

                <!-- Section 3: QR Code Method -->
                <h2 id="qr-code-method" data-aos="fade-up">The QR Code Method: How to Take Attendance in 30 Seconds</h2>

                <p data-aos="fade-up">QR code-based class check systems work on a simple but powerful principle: <strong>students scan once, attendance is instant</strong>.</p>

                <h3 data-aos="fade-up">How It Works</h3>

                <ol data-aos="fade-up">
                    <li><strong>Display QR Code:</strong> Project a unique QR code on your lecture hall screen at the start of class</li>
                    <li><strong>Students Scan:</strong> Each student scans with their phone camera (takes 2-3 seconds per student)</li>
                    <li><strong>Instant Recording:</strong> Attendance is recorded automatically in real-time</li>
                    <li><strong>Security:</strong> QR codes are time-limited and one-scan-per-student to prevent fraud</li>
                    <li><strong>Analytics:</strong> View real-time attendance rates and trends from any device</li>
                </ol>

                <div class="highlight-box" data-aos="fade-up">
                    <p class="font-semibold text-lg mb-2">⚡ Speed Breakdown</p>
                    <ul class="list-none space-y-2">
                        <li>• 50 students: <strong>30 seconds</strong></li>
                        <li>• 100 students: <strong>30 seconds</strong></li>
                        <li>• 200 students: <strong>30 seconds</strong></li>
                        <li>• 500 students: <strong>30 seconds</strong></li>
                    </ul>
                    <p class="mt-4 text-sm">Class size doesn't matter – all students scan simultaneously as they enter!</p>
                </div>

                <h3 data-aos="fade-up">Key Advantages for Large Classes</h3>

                <ul data-aos="fade-up">
                    <li><strong>Scales Infinitely:</strong> Works the same for 20 or 2,000 students</li>
                    <li><strong>No Hardware Required:</strong> Students use phones they already have</li>
                    <li><strong>Fraud Prevention:</strong> Unique codes + location verification + one-time scanning</li>
                    <li><strong>Real-Time Data:</strong> Instantly see who's present/absent</li>
                    <li><strong>Automatic Reports:</strong> Export attendance for administration in seconds</li>
                    <li><strong>Student Privacy:</strong> No facial recognition or tracking required</li>
                </ul>

                <!-- Section 4: Implementation -->
                <h2 id="implementation" data-aos="fade-up">Step-by-Step Implementation Guide</h2>

                <p data-aos="fade-up">Here's exactly how to implement a class check system for your large lecture:</p>

                <h3 data-aos="fade-up">Week 1: Setup (15 minutes total)</h3>

                <ol data-aos="fade-up">
                    <li><strong>Choose Your Platform:</strong> Select a class check system like Classcheck.me (free to start)</li>
                    <li><strong>Create Your Class:</strong> Add class name, schedule, and student roster</li>
                    <li><strong>Test QR Code:</strong> Generate a test QR code and scan it yourself</li>
                    <li><strong>Prepare Students:</strong> Email instructions one day before first use</li>
                </ol>

                <h3 data-aos="fade-up">Week 2: Launch (First Class)</h3>

                <ol data-aos="fade-up">
                    <li><strong>Arrive Early:</strong> Display QR code before class starts</li>
                    <li><strong>Brief Explanation (2 minutes):</strong> "To mark attendance, scan this QR code as you enter"</li>
                    <li><strong>Monitor in Real-Time:</strong> Watch attendance roll in on your device</li>
                    <li><strong>Troubleshoot:</strong> Help 2-3 students who have issues (common first-time)</li>
                    <li><strong>Close Attendance:</strong> Close QR code 10 minutes after class starts</li>
                </ol>

                <h3 data-aos="fade-up">Week 3+: Autopilot</h3>

                <ol data-aos="fade-up">
                    <li><strong>Display QR Code:</strong> Takes 10 seconds to project</li>
                    <li><strong>Start Teaching:</strong> Students scan as they settle in</li>
                    <li><strong>No Interruption:</strong> Teaching starts on time, no attendance delays</li>
                    <li><strong>Automatic Reports:</strong> Weekly/monthly attendance reports generate automatically</li>
                </ol>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 my-8" data-aos="fade-up">
                    <p class="font-semibold text-lg mb-2 text-blue-900">🎯 Pro Tip</p>
                    <p class="text-blue-800">Set up your QR code to auto-generate and display 5 minutes before each scheduled class. Students can scan as they arrive, and teaching starts exactly on time.</p>
                </div>

                <!-- Section 5: Best Practices -->
                <h2 id="best-practices" data-aos="fade-up">Best Practices for Large Class Attendance</h2>

                <h3 data-aos="fade-up">Optimize Your Setup</h3>

                <ul data-aos="fade-up">
                    <li><strong>Display Size Matters:</strong> QR code should be visible from the back row (at least 12" on screen)</li>
                    <li><strong>Multiple Displays:</strong> In 500+ seat halls, show QR code on side screens too</li>
                    <li><strong>Timing Window:</strong> Keep QR active for 10-15 minutes (covers latecomers)</li>
                    <li><strong>Backup Method:</strong> Have manual check-in option for dead phone batteries (happens rarely)</li>
                </ul>

                <h3 data-aos="fade-up">Prevent Fraud</h3>

                <ul data-aos="fade-up">
                    <li><strong>One-Time Scanning:</strong> Each QR code can only be scanned once per student</li>
                    <li><strong>Time Limits:</strong> QR codes expire after class period</li>
                    <li><strong>Location Verification:</strong> Enable GPS check to ensure students are physically present (optional)</li>
                    <li><strong>Random Spot Checks:</strong> Occasionally compare QR scans with visual headcount</li>
                    <li><strong>Pattern Detection:</strong> Flag students who consistently mark attendance but don't participate</li>
                </ul>

                <h3 data-aos="fade-up">Maximize Student Adoption</h3>

                <ul data-aos="fade-up">
                    <li><strong>Clear Communication:</strong> Explain benefits (faster class start, no calling names)</li>
                    <li><strong>First-Class Demo:</strong> Walk through the process in first lecture</li>
                    <li><strong>Visual Reminders:</strong> Display QR code before official start time</li>
                    <li><strong>Positive Reinforcement:</strong> Thank students for quick adoption</li>
                    <li><strong>Support Resources:</strong> Provide FAQ document for troubleshooting</li>
                </ul>

                <!-- Section 6: Common Challenges -->
                <h2 id="common-challenges" data-aos="fade-up">Overcoming Common Challenges</h2>

                <h3 data-aos="fade-up">Challenge 1: "Students Don't Have Smartphones"</h3>

                <p data-aos="fade-up"><strong>Reality:</strong> 98% of college students own smartphones (Pew Research 2024). For the 2% who don't:</p>
                <ul data-aos="fade-up">
                    <li>Provide alternative check-in method (email, verbal confirmation)</li>
                    <li>Partner with library to loan devices for class time</li>
                    <li>Most class check systems have web portal alternative</li>
                </ul>

                <h3 data-aos="fade-up">Challenge 2: "What About Dead Batteries?"</h3>

                <p data-aos="fade-up"><strong>Solution:</strong> Happens to less than 1% of students per class:</p>
                <ul data-aos="fade-up">
                    <li>Quick verbal roll call for affected students (takes 10 seconds)</li>
                    <li>Manual attendance entry portal</li>
                    <li>Students learn to charge phones (this becomes rare after first week)</li>
                </ul>

                <h3 data-aos="fade-up">Challenge 3: "Students Will Cheat by Sharing QR Codes"</h3>

                <p data-aos="fade-up"><strong>Solution:</strong> Modern class check systems prevent this:</p>
                <ul data-aos="fade-up">
                    <li>One scan per student ID (duplicate scans flagged)</li>
                    <li>Location verification (GPS/WiFi check)</li>
                    <li>Time-sensitive codes (expire after class starts)</li>
                    <li>IP address logging</li>
                    <li>Actually more secure than paper sign-in sheets!</li>
                </ul>

                <h3 data-aos="fade-up">Challenge 4: "IT/Administration Pushback"</h3>

                <p data-aos="fade-up"><strong>Solution:</strong> Lead with data:</p>
                <ul data-aos="fade-up">
                    <li>Show time savings calculation (6+ hours per semester recovered)</li>
                    <li>Demonstrate improved accuracy (25% error reduction)</li>
                    <li>Highlight student satisfaction (93% prefer QR vs roll call)</li>
                    <li>Start with pilot in one class, expand with proven results</li>
                </ul>

                <!-- Section 7: ROI Analysis -->
                <h2 id="roi-analysis" data-aos="fade-up">ROI Analysis & Time Savings Calculator</h2>

                <p data-aos="fade-up">Let's quantify exactly how much time and money you save with class check systems:</p>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-8 my-8" data-aos="fade-up">
                    <h3 class="text-2xl font-bold mb-6 text-center text-gray-900">📊 Time Savings Calculator</h3>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold mb-4 text-gray-900">Traditional Roll Call</h4>
                            <ul class="space-y-2 text-gray-700">
                                <li>• 200 students × 5 sec/name = <strong>16.7 minutes</strong></li>
                                <li>• 3 classes/week × 15 weeks = <strong>45 classes</strong></li>
                                <li>• 16.7 min × 45 classes = <strong>750 minutes</strong></li>
                                <li class="text-red-600 font-bold text-lg">= 12.5 HOURS LOST PER SEMESTER</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-4 text-gray-900">Class Check (QR Code)</h4>
                            <ul class="space-y-2 text-gray-700">
                                <li>• Display QR code = <strong>30 seconds</strong></li>
                                <li>• 3 classes/week × 15 weeks = <strong>45 classes</strong></li>
                                <li>• 0.5 min × 45 classes = <strong>22.5 minutes</strong></li>
                                <li class="text-green-600 font-bold text-lg">= 0.4 HOURS TOTAL</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 text-center p-6 bg-white rounded-xl shadow-lg">
                        <p class="text-3xl font-bold gradient-text mb-2">12.1 Hours Saved Per Semester</p>
                        <p class="text-gray-600">That's equivalent to <strong>4 full lecture periods</strong> you can dedicate to teaching instead of attendance!</p>
                    </div>
                </div>

                <h3 data-aos="fade-up">Financial ROI</h3>

                <p data-aos="fade-up">For university administrators considering class check systems:</p>

                <ul data-aos="fade-up">
                    <li><strong>Professor Time Value:</strong> $75/hour average × 12.1 hours = <strong>$907 saved per class per semester</strong></li>
                    <li><strong>System Cost:</strong> $0-$50/month = <strong>$0-$200 per semester</strong></li>
                    <li><strong>Net Savings:</strong> <strong>$707-$907 per class per semester</strong></li>
                    <li><strong>University-Wide (100 large classes):</strong> <strong>$70,700-$90,700 saved annually</strong></li>
                </ul>

                <div class="highlight-box" data-aos="fade-up">
                    <p class="font-semibold text-lg mb-2">💰 Hidden Benefits</p>
                    <ul class="space-y-2">
                        <li><strong>Student Satisfaction:</strong> Classes start on time = better reviews</li>
                        <li><strong>Data Analytics:</strong> Identify at-risk students early (attendance correlation with grades)</li>
                        <li><strong>Compliance:</strong> Automated record-keeping for accreditation</li>
                        <li><strong>Reduced Admin Work:</strong> No manual data entry or attendance sheet processing</li>
                    </ul>
                </div>

                <!-- Section 8: Conclusion -->
                <h2 id="conclusion" data-aos="fade-up">Conclusion: The Future of Class Check is Here</h2>

                <p data-aos="fade-up">Taking attendance in large classes doesn't have to waste 15-20 minutes of every lecture. With modern class check systems using QR codes, you can:</p>

                <ul data-aos="fade-up">
                    <li>✅ Reduce attendance time from 15+ minutes to 30 seconds</li>
                    <li>✅ Improve accuracy from 60-75% to 99%+</li>
                    <li>✅ Recover 12+ hours of teaching time per semester</li>
                    <li>✅ Eliminate administrative burden of manual tracking</li>
                    <li>✅ Gain valuable analytics on student engagement</li>
                    <li>✅ Start classes on time, every time</li>
                </ul>

                <p data-aos="fade-up">The technology exists, it's affordable (often free), and students prefer it. The question isn't whether to modernize your attendance system – it's <strong>how soon can you start?</strong></p>

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white text-center my-12" data-aos="zoom-in">
                    <h3 class="text-3xl font-bold mb-4">Ready to Transform Your Large Class Attendance?</h3>
                    <p class="text-xl mb-6 opacity-90">Join thousands of professors who've already made the switch to efficient class check systems.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="../pages/register.php" class="bg-white text-indigo-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition inline-block">
                            Start Free Trial →
                        </a>
                        <a href="../class-check-for-universities.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-indigo-600 transition inline-block">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Related Articles -->
                <div class="border-t-2 border-gray-200 pt-12 mt-12" data-aos="fade-up">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900">📚 Related Class Check Articles</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <a href="../class-check-vs-traditional-attendance.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Class Check vs Traditional Attendance</h4>
                            <p class="text-gray-600">See the complete comparison of modern QR systems vs old-school methods.</p>
                        </a>
                        <a href="../class-check-pricing-guide.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Class Check Pricing Guide</h4>
                            <p class="text-gray-600">Find the perfect plan for your institution with our interactive calculator.</p>
                        </a>
                        <a href="../class-check-security-features.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Security Features of Class Check</h4>
                            <p class="text-gray-600">Learn how modern systems prevent fraud and protect student data.</p>
                        </a>
                        <a href="../class-check-for-universities.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Class Check for Universities</h4>
                            <p class="text-gray-600">University-specific implementation guide with ROI calculator.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 gradient-text">Classcheck</h3>
                    <p class="text-gray-400">Modern QR code-based attendance system for educational institutions.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Class Check Solutions</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../class-check-for-universities.php" class="hover:text-white transition">For Universities</a></li>
                        <li><a href="../class-check-vs-traditional-attendance.php" class="hover:text-white transition">vs Traditional</a></li>
                        <li><a href="../class-check-pricing-guide.php" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="../class-check-security-features.php" class="hover:text-white transition">Security</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../blog.php" class="hover:text-white transition">Blog</a></li>
                        <li><a href="../faqs.php" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../index.php" class="hover:text-white transition">About</a></li>
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

        // Share Functions
        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('How to Take Attendance in Large Classes Efficiently - Class Check');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }

        // Analytics tracking
        if (typeof gtag === 'function') {
            gtag('event', 'page_view', {
                'page_title': 'How to Take Attendance in Large Classes Efficiently',
                'page_location': window.location.href,
                'page_path': window.location.pathname,
                'article_category': 'Guides & Tutorials',
                'reading_time': 12
            });

            // Track reading depth
            let maxScroll = 0;
            window.addEventListener('scroll', function() {
                const scrollPercentage = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
                if (scrollPercentage > maxScroll && scrollPercentage % 25 === 0) {
                    maxScroll = scrollPercentage;
                    gtag('event', 'scroll_depth', {
                        'percentage': scrollPercentage
                    });
                }
            });
        }

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
