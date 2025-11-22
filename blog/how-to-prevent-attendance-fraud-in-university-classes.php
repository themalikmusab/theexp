<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

require_once __DIR__ . '/../config/connection.php';

$post_slug = 'how-to-prevent-attendance-fraud-in-university-classes';

try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :slug AND is_published = 1");
    $stmt->execute([':slug' => $post_slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        $update_stmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id");
        $update_stmt->execute([':id' => $post['id']]);
    }
} catch (PDOException $e) {
    $post = null;
}

if (!$post) {
    $post = [
        'title' => 'How to Prevent Attendance Fraud in University Classes',
        'meta_description' => 'Stop students from faking attendance with these proven class check security strategies. Learn how to prevent proxy attendance, QR code sharing, and other fraud tactics.',
        'author' => 'Class Check Team',
        'category' => 'Best Practices',
        'read_time' => 7,
        'published_at' => date('Y-m-d H:i:s'),
        'featured_image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1200',
        'excerpt' => 'Attendance fraud costs universities millions and undermines academic integrity. Discover how modern class check systems prevent cheating better than traditional methods.'
    ];
}

$page_title = htmlspecialchars($post['title']) . " - Class Check Blog";
$meta_description = htmlspecialchars($post['meta_description']);
$canonical_url = "https://classcheck.me/blog/how-to-prevent-attendance-fraud-in-university-classes.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="prevent attendance fraud, class check security, stop proxy attendance, attendance cheating, university fraud prevention">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        #reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            z-index: 9999;
            transition: width 0.2s ease;
        }
        .article-content {
            font-size: 18px;
            line-height: 1.8;
            color: #334155;
        }
        .article-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
        }
        .article-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }
        .article-content p {
            margin-bottom: 1.5rem;
        }
        .article-content ul {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }
        .article-content li {
            margin-bottom: 0.75rem;
        }
        .highlight-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div id="reading-progress"></div>

    <nav class="glass fixed w-full z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="../index.php" class="text-2xl font-bold gradient-text">Classcheck</a>
                <div class="hidden md:flex space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-indigo-600 transition">Home</a>
                    <a href="../blog.php" class="text-indigo-600 font-semibold">Blog</a>
                    <a href="../faqs.php" class="text-gray-700 hover:text-indigo-600 transition">FAQs</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <a href="../pages/dashboard.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">Dashboard</a>
                    <?php else: ?>
                        <a href="../pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <article class="pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4">
            <nav class="mb-8">
                <ul class="flex items-center space-x-2 text-sm text-gray-600">
                    <li><a href="../index.php" class="hover:text-indigo-600">Home</a></li>
                    <li>→</li>
                    <li><a href="../blog.php" class="hover:text-indigo-600">Blog</a></li>
                    <li>→</li>
                    <li class="text-indigo-600">Prevent Attendance Fraud</li>
                </ul>
            </nav>

            <div class="mb-6">
                <span class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold">
                    <?php echo htmlspecialchars($post['category']); ?>
                </span>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight text-gray-900">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <div class="flex items-center space-x-4 mb-8 pb-8 border-b border-gray-200">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">CC</div>
                <div>
                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($post['author']); ?></p>
                    <p class="text-sm text-gray-600"><?php echo date('F d, Y', strtotime($post['published_at'])); ?> · <?php echo $post['read_time']; ?> min read</p>
                </div>
            </div>

            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-auto">
            </div>

            <div class="article-content">
                <p class="text-xl font-semibold text-gray-800 mb-8">
                    <strong>Attendance fraud is rampant in universities.</strong> Students signing for absent friends, sharing QR codes, or using proxy attendance undermine academic integrity. Here's how to stop it with modern class check security.
                </p>

                <h2>The 5 Most Common Attendance Fraud Tactics</h2>

                <h3>1. Proxy Sign-In (Paper Sheets)</h3>
                <p>Student A signs the paper sheet for absent Student B. This is the #1 fraud method with traditional attendance, affecting 25-40% of paper-based systems.</p>

                <h3>2. QR Code Screenshot Sharing</h3>
                <p>Students screenshot a QR code and send it to absent friends who scan from home. Only works if your class check system doesn't have proper security.</p>

                <h3>3. Clicker Device Sharing</h3>
                <p>Student brings multiple clicker devices to class and clicks for absent students. Costs $50+ per device to cheat.</p>

                <h3>4. Location Spoofing</h3>
                <p>Tech-savvy students use GPS spoofing apps to fake their location. Rare but possible with basic location-based systems.</p>

                <h3>5. Account Sharing</h3>
                <p>Students share login credentials so friends can mark attendance remotely. Common with weak authentication systems.</p>

                <h2>How Modern Class Check Systems Prevent Fraud</h2>

                <p>Well-designed class check platforms use layered security to make fraud practically impossible:</p>

                <h3>🔒 Security Layer 1: One-Time QR Codes</h3>
                <ul>
                    <li><strong>Unique per session:</strong> Each class gets a new QR code that can't be reused</li>
                    <li><strong>Time-limited:</strong> QR code expires after 10-15 minutes</li>
                    <li><strong>One scan per student:</strong> System detects duplicate scans and flags them</li>
                    <li><strong>Dynamic generation:</strong> QR codes can't be pre-screenshotted</li>
                </ul>

                <h3>📍 Security Layer 2: Location Verification</h3>
                <ul>
                    <li><strong>GPS coordinates:</strong> Verify student is physically in classroom</li>
                    <li><strong>WiFi detection:</strong> Check if connected to campus network</li>
                    <li><strong>Geofencing:</strong> Create virtual boundary around classroom</li>
                    <li><strong>IP address tracking:</strong> Detect off-campus scan attempts</li>
                </ul>

                <h3>🎯 Security Layer 3: Pattern Detection</h3>
                <ul>
                    <li><strong>Scan timing analysis:</strong> Flag accounts that scan within seconds (impossible if different locations)</li>
                    <li><strong>Behavior patterns:</strong> Detect students who always mark attendance but never participate</li>
                    <li><strong>Device fingerprinting:</strong> Track which devices are used for scanning</li>
                    <li><strong>Network analysis:</strong> Identify students scanning from same IP address</li>
                </ul>

                <h3>🔐 Security Layer 4: Authentication</h3>
                <ul>
                    <li><strong>Student ID verification:</strong> Match against official university roster</li>
                    <li><strong>Two-factor authentication:</strong> Require phone verification for first scan</li>
                    <li><strong>Biometric options:</strong> Face ID/Touch ID for high-stakes courses</li>
                    <li><strong>SSO integration:</strong> Connect to university authentication systems</li>
                </ul>

                <div class="highlight-box">
                    <p class="font-semibold text-lg mb-2">🛡️ Security Comparison</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="font-bold text-red-700 mb-2">Paper Sheets</p>
                            <p class="text-sm text-gray-700">25-40% fraud rate<br>Zero verification<br>Easy to cheat</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="font-bold text-green-700 mb-2">Class Check QR</p>
                            <p class="text-sm text-gray-700">&lt;1% fraud rate<br>Multi-layer security<br>Nearly impossible to cheat</p>
                        </div>
                    </div>
                </div>

                <h2>Implementation: How to Enable Security Features</h2>

                <h3>For Individual Professors</h3>
                <ol>
                    <li><strong>Enable time limits:</strong> Set QR codes to expire 10 minutes after class starts</li>
                    <li><strong>Turn on duplicate detection:</strong> System auto-flags multiple scans from same account</li>
                    <li><strong>Review flagged students:</strong> Check weekly report of suspicious activity</li>
                    <li><strong>Spot check attendance:</strong> Occasionally verify QR scans match actual room count</li>
                </ol>

                <h3>For University Administrators</h3>
                <ol>
                    <li><strong>Require location verification:</strong> Make GPS/WiFi checking mandatory campus-wide</li>
                    <li><strong>Integrate with student ID system:</strong> Sync class check with official rosters</li>
                    <li><strong>Enable SSO:</strong> Require university credentials for attendance marking</li>
                    <li><strong>Monitor fraud patterns:</strong> Use analytics to identify systemic cheating</li>
                </ol>

                <h2>What to Do When You Detect Fraud</h2>

                <p>Even with security, determined students may try to cheat. Here's your response protocol:</p>

                <h3>First Offense (Warning)</h3>
                <ul>
                    <li>Private conversation with student</li>
                    <li>Explain how you detected the fraud</li>
                    <li>Document the incident</li>
                    <li>Give one warning (university policy permitting)</li>
                </ul>

                <h3>Second Offense (Academic Consequences)</h3>
                <ul>
                    <li>Report to academic integrity board</li>
                    <li>Apply course-specific penalties</li>
                    <li>Require in-person attendance verification</li>
                    <li>Document for student record</li>
                </ul>

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white text-center my-12">
                    <h3 class="text-3xl font-bold mb-4">Protect Your Class from Attendance Fraud</h3>
                    <p class="text-xl mb-6 opacity-90">Switch to a secure class check system with built-in fraud prevention</p>
                    <a href="../pages/register.php" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition">
                        Start Free Trial →
                    </a>
                </div>

                <div class="border-t-2 border-gray-200 pt-12 mt-12">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900">📚 Related Articles</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <a href="../class-check-security-features.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Complete Security Guide</h4>
                            <p class="text-gray-600">Deep dive into class check security features and data protection</p>
                        </a>
                        <a href="how-to-take-attendance-in-large-classes-efficiently.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Efficient Attendance Guide</h4>
                            <p class="text-gray-600">Learn how to take attendance in large classes</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 gradient-text">Classcheck</h3>
                    <p class="text-gray-400">Secure, modern attendance for universities</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Solutions</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../class-check-security-features.php" class="hover:text-white transition">Security</a></li>
                        <li><a href="../class-check-for-universities.php" class="hover:text-white transition">Universities</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../blog.php" class="hover:text-white transition">Blog</a></li>
                        <li><a href="../faqs.php" class="hover:text-white transition">FAQs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../index.php" class="hover:text-white transition">About</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 mt-8 text-center text-gray-400">
                <p>&copy; 2025 Classcheck. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        function updateReadingProgress() {
            const scrollPercentage = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            document.getElementById('reading-progress').style.width = scrollPercentage + '%';
        }
        window.addEventListener('scroll', updateReadingProgress);
    </script>
</body>
</html>
