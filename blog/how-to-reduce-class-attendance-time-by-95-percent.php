<?php
session_start();

// Check if user_id is set in the session to determine login status
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

// Include database connection
require_once __DIR__ . '/../config/connection.php';

// Get blog post slug from URL
$post_slug = 'how-to-reduce-class-attendance-time-by-95-percent';

// Fetch blog post from database
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

// Default data if post doesn't exist in database yet
if (!$post) {
    $post = [
        'title' => 'How to Reduce Class Attendance Time by 95%',
        'meta_description' => 'Discover the exact method to reduce class check attendance time from 15 minutes to just 30 seconds. Save 95% of attendance time with modern QR code systems.',
        'author' => 'Class Check Team',
        'category' => 'Tips & Tricks',
        'read_time' => 6,
        'published_at' => date('Y-m-d H:i:s'),
        'featured_image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1200',
        'excerpt' => 'The average professor spends 15-20 minutes per class on attendance. Here\'s how to reduce that to 30 seconds using class check QR systems – a 95% time reduction.'
    ];
}

$page_title = htmlspecialchars($post['title']) . " - Class Check Blog";
$meta_description = htmlspecialchars($post['meta_description']);
$canonical_url = "https://classcheck.me/blog/how-to-reduce-class-attendance-time-by-95-percent.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="reduce attendance time, class check time savings, QR code attendance, fast attendance, efficient class check">
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($post['featured_image']); ?>">

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
            color: #1e293b;
        }
        .article-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
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
        .highlight-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0.5rem;
        }
        .stats-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
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
                    <a href="../class-check-pricing-guide.php" class="text-gray-700 hover:text-indigo-600 transition">Pricing</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <a href="../pages/dashboard.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">Dashboard</a>
                    <?php else: ?>
                        <a href="../pages/login.php" class="text-gray-700 hover:text-indigo-600 transition">Login</a>
                        <a href="../pages/register.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <article class="pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4">
            <nav class="mb-8" data-aos="fade-right">
                <ul class="flex items-center space-x-2 text-sm text-gray-600">
                    <li><a href="../index.php" class="hover:text-indigo-600">Home</a></li>
                    <li>→</li>
                    <li><a href="../blog.php" class="hover:text-indigo-600">Blog</a></li>
                    <li>→</li>
                    <li class="text-indigo-600">Reduce Attendance Time</li>
                </ul>
            </nav>

            <div class="mb-6" data-aos="fade-up">
                <span class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold">
                    <?php echo htmlspecialchars($post['category']); ?>
                </span>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight text-gray-900" data-aos="fade-up">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200" data-aos="fade-up">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">CC</div>
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($post['author']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo date('F d, Y', strtotime($post['published_at'])); ?> · <?php echo $post['read_time']; ?> min read</p>
                    </div>
                </div>
            </div>

            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl" data-aos="zoom-in">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-auto">
            </div>

            <div class="article-content">
                <p class="text-xl font-semibold text-gray-800 mb-8" data-aos="fade-up">
                    Time is the most valuable resource in education. Yet the average professor wastes <strong>15-20 minutes per class</strong> just taking attendance. Over a semester, that's <strong>12+ hours of lost teaching time</strong>. Here's the exact method to reclaim that time.
                </p>

                <h2 data-aos="fade-up">The 95% Time Reduction Formula</h2>

                <div class="grid md:grid-cols-2 gap-6 my-8" data-aos="fade-up">
                    <div class="bg-red-50 border-2 border-red-300 rounded-xl p-6">
                        <h3 class="text-2xl font-bold text-red-600 mb-3">Before: Traditional Method</h3>
                        <div class="text-5xl font-bold text-red-600 mb-2">15-20 min</div>
                        <ul class="text-gray-700 space-y-2">
                            <li>✗ Manual roll call</li>
                            <li>✗ Paper sign-in sheets</li>
                            <li>✗ Manual data entry</li>
                            <li>✗ Error-prone</li>
                        </ul>
                    </div>
                    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-6">
                        <h3 class="text-2xl font-bold text-green-600 mb-3">After: Class Check QR</h3>
                        <div class="text-5xl font-bold text-green-600 mb-2">30 sec</div>
                        <ul class="text-gray-700 space-y-2">
                            <li>✓ One QR code scan</li>
                            <li>✓ Instant digital record</li>
                            <li>✓ Automatic reporting</li>
                            <li>✓ 99% accuracy</li>
                        </ul>
                    </div>
                </div>

                <div class="stats-box my-8" data-aos="zoom-in">
                    <div class="text-5xl font-bold mb-3">95% Faster</div>
                    <p class="text-lg opacity-90">From 15 minutes to 30 seconds = 12.1 hours saved per semester</p>
                </div>

                <h2 data-aos="fade-up">The 3-Step Implementation</h2>

                <h3 data-aos="fade-up">Step 1: Switch to Class Check QR System (5 minutes setup)</h3>

                <p data-aos="fade-up">The fastest way to reduce attendance time is to eliminate manual processes entirely. Class check QR systems work like this:</p>

                <ol data-aos="fade-up">
                    <li><strong>Display QR code</strong> on your lecture screen before class</li>
                    <li><strong>Students scan</strong> with their phones as they enter</li>
                    <li><strong>Attendance recorded</strong> automatically and instantly</li>
                    <li><strong>You start teaching</strong> on time, every time</li>
                </ol>

                <div class="highlight-box" data-aos="fade-up">
                    <p class="font-semibold text-lg mb-2">⚡ Why This Works</p>
                    <p>Traditional attendance is sequential (one student at a time). Class check QR is parallel (all students simultaneously). 200 students scanning takes the same time as 20 students – about 30 seconds total.</p>
                </div>

                <h3 data-aos="fade-up">Step 2: Automate Your Workflow (One-time, 10 minutes)</h3>

                <p data-aos="fade-up">Set up your class check system to run on autopilot:</p>

                <ul data-aos="fade-up">
                    <li><strong>Auto-generate QR codes:</strong> New unique code for each class session</li>
                    <li><strong>Auto-display schedule:</strong> QR appears 5 minutes before class starts</li>
                    <li><strong>Auto-close window:</strong> QR expires 10 minutes after class begins</li>
                    <li><strong>Auto-reports:</strong> Weekly attendance summaries emailed to you</li>
                </ul>

                <p data-aos="fade-up">After initial setup, you literally do nothing. The class check system handles everything.</p>

                <h3 data-aos="fade-up">Step 3: Track Your Time Savings (Ongoing)</h3>

                <p data-aos="fade-up">Modern class check platforms show you exactly how much time you're saving:</p>

                <ul data-aos="fade-up">
                    <li>Total attendance time per class</li>
                    <li>Cumulative time saved vs traditional methods</li>
                    <li>Hours recovered per week/month/semester</li>
                    <li>ROI calculations</li>
                </ul>

                <h2 data-aos="fade-up">Real-World Time Savings Examples</h2>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-8 my-8" data-aos="fade-up">
                    <h3 class="text-2xl font-bold mb-6 text-center text-gray-900">📊 Time Saved by Class Size</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full bg-white rounded-lg shadow-lg">
                            <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Class Size</th>
                                    <th class="px-6 py-3 text-left">Old Method</th>
                                    <th class="px-6 py-3 text-left">Class Check</th>
                                    <th class="px-6 py-3 text-left">Time Saved</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 font-semibold">30 students</td>
                                    <td class="px-6 py-4">5 minutes</td>
                                    <td class="px-6 py-4 text-green-600">30 seconds</td>
                                    <td class="px-6 py-4 text-green-600 font-bold">4.5 min/class</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-semibold">100 students</td>
                                    <td class="px-6 py-4">12 minutes</td>
                                    <td class="px-6 py-4 text-green-600">30 seconds</td>
                                    <td class="px-6 py-4 text-green-600 font-bold">11.5 min/class</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-semibold">200 students</td>
                                    <td class="px-6 py-4">18 minutes</td>
                                    <td class="px-6 py-4 text-green-600">30 seconds</td>
                                    <td class="px-6 py-4 text-green-600 font-bold">17.5 min/class</td>
                                </tr>
                                <tr class="bg-indigo-50">
                                    <td class="px-6 py-4 font-semibold">500 students</td>
                                    <td class="px-6 py-4">25+ minutes</td>
                                    <td class="px-6 py-4 text-green-600">30 seconds</td>
                                    <td class="px-6 py-4 text-green-600 font-bold">24.5 min/class</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <h2 data-aos="fade-up">Common Objections (And Why They're Wrong)</h2>

                <h3 data-aos="fade-up">"Students won't adopt it"</h3>
                <p data-aos="fade-up"><strong>Reality:</strong> 98% of college students have smartphones. Scanning a QR code takes 2 seconds – faster than signing a paper sheet. Students actually prefer class check systems because class starts on time.</p>

                <h3 data-aos="fade-up">"It's too expensive"</h3>
                <p data-aos="fade-up"><strong>Reality:</strong> Most class check systems are free for individual professors or $20-50/month for departments. That's less than one hour of professor time saved per semester. ROI is 10x minimum.</p>

                <h3 data-aos="fade-up">"Setup is complicated"</h3>
                <p data-aos="fade-up"><strong>Reality:</strong> Modern class check platforms take 5-10 minutes to set up. If you can create a Google Form, you can set up a class check QR system. Many have one-click import from your LMS roster.</p>

                <h3 data-aos="fade-up">"Students will cheat"</h3>
                <p data-aos="fade-up"><strong>Reality:</strong> Class check QR systems have built-in fraud prevention: one scan per student, time-limited codes, location verification, and IP tracking. Actually more secure than paper sheets where students sign for absent friends.</p>

                <h2 data-aos="fade-up">Your Action Plan</h2>

                <p data-aos="fade-up">Want to save 95% of your attendance time? Here's what to do right now:</p>

                <ol data-aos="fade-up">
                    <li><strong>Today:</strong> Sign up for a free class check account (takes 2 minutes)</li>
                    <li><strong>This week:</strong> Add your first class and test the QR system yourself</li>
                    <li><strong>Next class:</strong> Roll out to students with 30-second explanation</li>
                    <li><strong>This semester:</strong> Save 12+ hours and never manually take attendance again</li>
                </ol>

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white text-center my-12" data-aos="zoom-in">
                    <h3 class="text-3xl font-bold mb-4">Ready to Save 12 Hours This Semester?</h3>
                    <p class="text-xl mb-6 opacity-90">Join thousands of professors who've already made the switch to efficient class check attendance.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="../pages/register.php" class="bg-white text-indigo-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition">
                            Start Free Trial →
                        </a>
                        <a href="../class-check-vs-traditional-attendance.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-indigo-600 transition">
                            See Comparison
                        </a>
                    </div>
                </div>

                <div class="border-t-2 border-gray-200 pt-12 mt-12" data-aos="fade-up">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900">📚 Related Class Check Articles</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <a href="how-to-take-attendance-in-large-classes-efficiently.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Complete Attendance Guide</h4>
                            <p class="text-gray-600">In-depth guide for taking attendance in large lecture halls</p>
                        </a>
                        <a href="../class-check-pricing-guide.php" class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition">
                            <h4 class="font-bold text-lg mb-2 text-indigo-600">Pricing Calculator</h4>
                            <p class="text-gray-600">Find the perfect class check plan for your needs</p>
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
                    <p class="text-gray-400">Modern QR code-based attendance system for educational institutions.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Solutions</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="../class-check-for-universities.php" class="hover:text-white transition">For Universities</a></li>
                        <li><a href="../class-check-vs-traditional-attendance.php" class="hover:text-white transition">vs Traditional</a></li>
                        <li><a href="../class-check-pricing-guide.php" class="hover:text-white transition">Pricing</a></li>
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
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;
            document.getElementById('reading-progress').style.width = scrollPercentage + '%';
        }

        window.addEventListener('scroll', updateReadingProgress);
        updateReadingProgress();
    </script>
</body>
</html>
