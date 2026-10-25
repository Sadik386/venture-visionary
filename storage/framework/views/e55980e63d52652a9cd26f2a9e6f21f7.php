<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Feedback & Rating | Venture Visionary Qatar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between antialiased">

    <!-- Header -->
    <header class="bg-[#0F2942] text-white py-4 shadow-sm border-b border-amber-500/30">
        <div class="max-w-3xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center font-bold text-slate-900 text-sm">
                    VV
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight leading-none text-white">Venture Visionary</h1>
                    <p class="text-[11px] text-amber-400 font-medium">Executive Immigration & Advisory Desk</p>
                </div>
            </div>
            <span class="text-xs text-slate-300">Client Quality Assurance</span>
        </div>
    </header>

    <!-- Main Feedback Container -->
    <main class="max-w-xl mx-auto px-4 py-10 flex-1 w-full">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('feedback_result')): ?>
            <?php $res = session('feedback_result'); ?>
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($res['is_positive']): ?>
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                        ★
                    </div>
                    <h2 class="text-2xl font-black text-[#0F2942]">Thank You, <?php echo e($review->recipient_name); ?>!</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <?php echo e($res['message']); ?>

                    </p>
                    
                    <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                        Help fellow travelers and entrepreneurs in Qatar discover seamless visa consultancy:
                    </div>

                    <a href="<?php echo e($res['redirect_url'] ?? 'https://search.google.com/local/writereview'); ?>" target="_blank"
                        class="mt-4 inline-flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow transition">
                        <span>⭐</span> Post Your 5-Star Review on Google →
                    </a>
                <?php else: ?>
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                        🛡️
                    </div>
                    <h2 class="text-xl font-bold text-[#0F2942]">We Appreciate Your Honest Feedback</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <?php echo e($res['message']); ?>

                    </p>
                    <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 text-left">
                        Our executive management has been notified. A senior partner will review your file to ensure our service standards are continuously improved.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-8">
                    <a href="/" class="text-xs text-slate-400 hover:text-slate-600">Return to Homepage</a>
                </div>
            </div>
        <?php elseif($review->status === 'completed'): ?>
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm text-center">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl mx-auto mb-3">
                    ✓
                </div>
                <h2 class="text-xl font-bold text-slate-900">Feedback Already Received</h2>
                <p class="text-xs text-slate-500 mt-2">
                    Thank you! Your rating of <strong><?php echo e($review->rating); ?> / 5 stars</strong> was recorded on <?php echo e($review->completed_at?->format('M d, Y')); ?>.
                </p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-[#0F2942] to-slate-800 p-6 text-white text-center">
                    <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full mb-2">
                        Client Experience Survey
                    </span>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">How was your experience?</h2>
                    <p class="text-xs text-slate-300 mt-1">Hello <strong><?php echo e($review->recipient_name); ?></strong>, your feedback helps us maintain Doha's highest visa success rate.</p>
                </div>

                <form action="/review/<?php echo e($review->token); ?>" method="POST" class="p-6 sm:p-8 space-y-6">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 text-center">
                            Rate Your Experience With Our Team
                        </label>
                        
                        <div class="flex justify-center items-center gap-3" id="starsContainer">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                <button type="button" data-rating="<?php echo e($i); ?>"
                                    class="star-btn text-4xl sm:text-5xl text-slate-200 hover:text-amber-400 transition-transform hover:scale-110 focus:outline-none">
                                    ★
                                </button>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <input type="hidden" name="rating" id="selectedRating" required />
                        <div id="ratingLabel" class="text-center text-xs font-semibold text-amber-600 mt-2 h-4"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Share Any Remarks or Compliments (Optional)
                        </label>
                        <textarea name="feedback" rows="3" placeholder="Tell us about the advisor who assisted you, speed of service, or any suggestions..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50"></textarea>
                    </div>

                    <button type="submit" id="submitReviewBtn" disabled
                        class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-40 disabled:cursor-not-allowed text-slate-950 font-bold text-sm shadow transition">
                        Submit Feedback →
                    </button>
                </form>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
        © 2026 Venture Visionary Qatar • Developer Handover Specification v2.0
    </footer>

    <script>
        const stars = document.querySelectorAll('.star-btn');
        const input = document.getElementById('selectedRating');
        const label = document.getElementById('ratingLabel');
        const btn = document.getElementById('submitReviewBtn');

        const labels = {
            1: '★ Disappointing Experience',
            2: '★★ Needs Improvement',
            3: '★★★ Average Service',
            4: '★★★★ Very Good',
            5: '★★★★★ Outstanding Service!'
        };

        stars.forEach(s => {
            s.addEventListener('click', () => {
                const r = parseInt(s.dataset.rating);
                input.value = r;
                btn.disabled = false;
                label.innerText = labels[r] || '';

                stars.forEach(other => {
                    const otherR = parseInt(other.dataset.rating);
                    if (otherR <= r) {
                        other.classList.remove('text-slate-200');
                        other.classList.add('text-amber-400');
                    } else {
                        other.classList.remove('text-amber-400');
                        other.classList.add('text-slate-200');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/reputation/review.blade.php ENDPATH**/ ?>