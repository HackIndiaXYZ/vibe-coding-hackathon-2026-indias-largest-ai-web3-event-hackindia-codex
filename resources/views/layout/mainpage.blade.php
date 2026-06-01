<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VerifAI — See What's Real</title>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/mainpage.css')}}" />
</head>
<body>

    <nav>
        <div class="logo">
            <div class="logo-mark">V</div>
            Verify<span style="color: var(--orange);">AI</span>
        </div>
        <ul class="nav-links">
            <li><a href="#how-it-works">How it works</a></li>
            <li><a href="#about">Recent cases</a></li>
            <li><a href="#about">About</a></li>
        </ul>
        <span class="nav-tag">Beta</span>
        @auth

        <div class="user-plan-box">

            <span class="plan-name">
                {{ auth()->user()->plan }}
            </span>

            <span class="investigation-count">

                {{ auth()->user()->investigations_left }}

                left

            </span>

        </div>

        @endauth
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf

            <button type="submit" class="nav-login-btn">
                Log out
            </button>
        </form>


    </nav>


    <section class="hero">
        <div class="hero-left">
            <div class="eyebrow">Media Verification Tool</div>

            <h1>See What's <em>Real</em><br>Before It Spreads.</h1>
            <p class="hero-desc">Paste any image or video link. We cross-reference it against verified sources, check metadata, and give you a clear verdict — no guessing, no jargon.</p>
            <div class="stats-row">
                <div class="stat">
                    <div class="stat-num">94%</div>
                    <div class="stat-label">Accuracy Rate</div>
                </div>
                <div class="stat">
                    <div class="stat-num">2.4s</div>
                    <div class="stat-label">Avg. Check Time</div>
                </div>
                <div class="stat">
                    <div class="stat-num">12k+</div>
                    <div class="stat-label">Claims Checked</div>
                </div>
            </div>
        </div>

        <form id="analyseForm" action="{{route('analyse')}}" method="POST">
            @csrf
            <div class="hero-right" id="about">
                <div class="input-card">
                    <div class="card-header">
                        <span class="card-title">New Verification Request</span>
                        <span class="case-id" id="caseId">#VF-0000</span>
                    </div>

                    <div class="field-label">Content Type</div>
                    <div class="type-toggle">
                        <button type="button" class="type-btn active" onclick="setType(this, 'image')">🖼 Image</button>
                        <button type="button" class="type-btn" onclick="setType(this, 'video')">🎬 Video</button>
                        <button type="button" class="type-btn" onclick="setType(this, 'both')">📎 Both</button>
                    </div>

                    <div class="field-label">Media URL</div>
                    <input type="url" id="mediaUrl" placeholder="https://example.com/image.jpg or video link..." name="url" />

                    <div class="field-label">Claim / Title</div>
                    <input type="text" id="claimTitle" placeholder='e.g. "Viral photo of floods from 2024 japan"' name="title" />

                    <div class="field-label">Context (optional)</div>
                    <textarea id="context" rows="3" placeholder="Any additional context — where you saw it, what's being claimed..." name="context"></textarea>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span id="btnText">Investigate This →</span>
                        <div class="submit-icon" id="btnSpinner"></div>
                    </button>
                </div>
        </form>
        </div>
        <div class="limit-nudge" id="limitNudge" style="display:none;">
            <span class="nudge-emoji">✨</span>
            <span>Enjoying the results? <strong>Buy</strong> Our Plans.</span>
            <a href="#plans" class="nudge-cta">Plans →</a>
            <button class="nudge-close" onclick="document.getElementById('limitNudge').style.display='none'">✕</button>
        </div>


    </section>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text" id="loadingMsg">Fetching media metadata...</div>
        <div class="loading-steps">
            <div class="lstep" id="ls1">Source Check</div>
            <div class="lstep" id="ls2">Metadata Scan</div>
            <div class="lstep" id="ls3">Cross-reference</div>
            <div class="lstep" id="ls4">Verdict</div>
        </div>
    </div>

    <div class="section-divider" id="resultDivider" style="display:none;">
        <div class="divider-line"></div>
        <span class="divider-label">Investigation Report</span>
        <div class="divider-line"></div>
    </div>

    <section class="result-section" id="resultSection" style="display:none;">
        <div class="result-grid">
            <div>
                <div class="verdict-card" id="verdictCard">
                    <div class="verdict-banner false">
                        <div class="verdict-icon false">✕</div>
                        <div class="verdict-text false">
                            <strong>Misleading</strong>
                            <span>Content is out of context</span>
                        </div>
                    </div>
                    <div class="verdict-body">
                        <div class="confidence-row">
                            <div class="confidence-label-row">
                                <span class="conf-label">Confidence</span>
                                <span class="conf-val" id="confVal">87%</span>
                            </div>
                            <div class="conf-bar">
                                <div class="conf-fill false" id="confFill" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="tags-section">
                            <div class="tags-label">Flags</div>
                            <div class="tags-wrap" id="tagsWrap">
                                <span class="tag tag-red">Out of Context</span>
                                <span class="tag tag-orange">Wrong Date</span>
                                <span class="tag tag-blue">Original Found</span>
                                <span class="tag tag-gray">Image Reused</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="analysis-col" id="analysisCol">
                {{-- Dynamically filled by JS --}}
            </div>
        </div>
    </section>

    <div class="section-divider" style="margin-top: 4rem;">
        <div class="divider-line"></div>
        <span class="divider-label">How It Works</span>
        <div class="divider-line"></div>
    </div>

    <section id="how-it-works" class="how-section" style="margin-top: 2rem;">
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">Step 01</div>
                <div class="step-icon-wrap">🔗</div>
                <div class="step-h">Submit the link</div>
                <p class="step-p">Paste the image or video URL along with the claim being made. Add any context you have about where you found it.</p>
            </div>
            <div class="step-card">
                <div class="step-num">Step 02</div>
                <div class="step-icon-wrap">🔍</div>
                <div class="step-h">We investigate</div>
                <p class="step-p">The system cross-checks metadata, reverse image searches, archived sources, and verifiable databases to build an evidence trail.</p>
            </div>
            <div class="step-card">
                <div class="step-num">Step 03</div>
                <div class="step-icon-wrap">📋</div>
                <div class="step-h">Read the report</div>
                <p class="step-p">You get a clear verdict with evidence cards — what's wrong, what's right, and where the truth actually comes from.</p>
            </div>
        </div>
        <div class="plans-container" id="plans">
            <!-- Divider matching your dashboard style (optional but fits your request) -->
            <div class="section-divider">
                <div class="divider-line"></div>
                <span class="divider-label">Choose your plan</span>
                <div class="divider-line"></div>
            </div>

            <!-- Plans grid: exactly BASIC, PRO, UNLIMITED with given pricing -->
            <div class="plans-grid">

                <!-- 🔵 BASIC — ₹99/month -->
                <div class="plan-card basic-card">
                    <div class="plan-badge">🔵 MOST FLEXIBLE</div>
                    <div class="plan-name">BASIC</div>
                    <div class="plan-price">₹99 <small>/month</small></div>
                    <div class="plan-desc">Essential fact-checking for individuals</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 100 investigations / month</li>
                        <li><span class="check-icon">✓</span> Metadata & reverse image search</li>
                        <li><span class="check-icon">✓</span> Email support</li>
                        <li><span class="check-icon">✓</span> 7-day history</li>
                    </ul>
                    <a href="#" class="plan-cta" id="basicBtn">Get Basic →</a>
                </div>

                <!-- 🟠 PRO — ₹299/month -->
                <div class="plan-card pro-card">
                    <div class="plan-badge">🟠 POPULAR</div>
                    <div class="plan-name">PRO</div>
                    <div class="plan-price">₹299 <small>/month</small></div>
                    <div class="plan-desc">Advanced verification & priority checks</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 500 investigations / month</li>
                        <li><span class="check-icon">✓</span> Real-time cross-reference</li>
                        <li><span class="check-icon">✓</span> Priority support + API access</li>
                        <li><span class="check-icon">✓</span> Full evidence trail export</li>
                    </ul>
                    <a href="#" class="plan-cta" id="proBtn">Get Pro →</a>
                </div>

                <!-- 🔴 UNLIMITED — ₹699/month -->
                <div class="plan-card unlimited-card">
                    <div class="plan-badge">🔴 POWER USER</div>
                    <div class="plan-name">UNLIMITED</div>
                    <div class="plan-price">₹699 <small>/month</small></div>
                    <div class="plan-desc">Unlimited verifications for teams & creators</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> ♾️ Unlimited investigations</li>
                        <li><span class="check-icon">✓</span> Bulk URL analysis</li>
                        <li><span class="check-icon">✓</span> Custom webhook & Slack alerts</li>
                        <li><span class="check-icon">✓</span> Dedicated account manager</li>
                    </ul>
                    <a href="#" class="plan-cta" id="unlimitedBtn">Go Unlimited →</a>
                </div>
            </div>
            <!-- subtle footnote (optional, matches dashboard minimal style) -->
            <p style="text-align: center; font-size: 0.75rem; color: #888; margin-top: 2rem; letter-spacing: 0.2px;">
                All plans include weekly updates • No hidden fees • Cancel anytime
            </p>
        </div>
    </section>

    <footer>
        <div class="footer-left">VerifyAI — Built for truth, not clicks.</div>
        <div class="footer-right">A media verification tool · Not affiliated with any political entity</div>
    </footer>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        const isLoggedIn = @json(Auth::check());

        // ── Case ID generator ──
        document.getElementById('caseId').innerText =
            '#VF-' + Math.floor(1000 + Math.random() * 9000);

        function setType(button, type) {
            document.querySelectorAll('.type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        }

        const form = document.querySelector('#analyseForm');

        form.addEventListener('submit', async function(e) {


            e.preventDefault();
            // =========================
            // FREE TRIAL SYSTEM
            // =========================

            let analysisCount = parseInt(
                localStorage.getItem('vai_count') || '0'
            );

            // FREE users only
            if (!isLoggedIn && analysisCount >= 3) {

                document.getElementById('submitBtn').disabled = true;

                document.getElementById('btnText').innerText =
                    'Free Limit Reached';

                document.getElementById('submitBtn').style.opacity = '0.6';

                document.getElementById('submitBtn').style.cursor =
                    'not-allowed';

                document.getElementById('limitNudge').style.display =
                    'flex';

                return;
            }
            const url = document.getElementById('mediaUrl').value;
            const title = document.getElementById('claimTitle').value;
            const context = document.getElementById('context').value;

            const loadingOverlay = document.getElementById('loadingOverlay');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            // Start loading
            loadingOverlay.style.display = 'flex';
            btnText.innerText = 'Investigating...';
            btnSpinner.style.display = 'block';

            try {

                const response = await fetch('/analyse', {
                    method: 'POST'
                    , headers: {
                        'Content-Type': 'application/json'
                        , 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                    , body: JSON.stringify({
                        url
                        , title
                        , context
                    })
                });

                const data = await response.json();
                // Increase trial count
                analysisCount++;

                localStorage.setItem(
                    'vai_count'
                    , analysisCount
                );

                // Show signup nudge after 3rd try
                if (!isLoggedIn && analysisCount >= 3) {

                    document.getElementById('limitNudge').style.display =
                        'flex';
                }
                console.log(data);

                // Stop loading
                loadingOverlay.style.display = 'none';
                btnText.innerText = 'Investigate This →';
                btnSpinner.style.display = 'none';

                if (data.limit_reached) {

                    document.getElementById(
                        'limitNudge'
                    ).style.display = 'flex';

                    return;
                }

                if (data.status === false) {

                    return;
                }

                // Show result section
                document.getElementById('resultDivider').style.display = 'flex';
                document.getElementById('resultSection').style.display = 'block';

                const verdictCard = document.getElementById('verdictCard');
                const isVerified = data.analysis.match_percentage > 70;
                const matchPct = data.analysis.match_percentage;

                // ============================================================
                // X / TWITTER
                // ============================================================
                if (data.platform === 'X') {

                    verdictCard.innerHTML = `
                        <div class="verdict-banner ${isVerified ? 'true' : 'false'}">
                            <div class="verdict-icon ${isVerified ? 'true' : 'false'}">
                                ${isVerified ? '✓' : '✕'}
                            </div>
                            <div class="verdict-text ${isVerified ? 'true' : 'false'}">
                                <strong>${isVerified ? 'Verified' : 'Misleading'}</strong>
                                <span>${isVerified ? 'Claim matches the post' : 'Claim does not match the post'}</span>
                            </div>
                        </div>
                        <div class="verdict-body">
                            <div class="confidence-row">
                                <div class="confidence-label-row">
                                    <span class="conf-label">Confidence</span>
                                    <span class="conf-val">${matchPct}%</span>
                                </div>
                                <div class="conf-bar">
                                    <div class="conf-fill ${isVerified ? 'true' : 'false'}" style="width:${matchPct}%"></div>
                                </div>
                            </div>
                            <div class="tags-section">
                                <div class="tags-label">Platform</div>
                                <div class="tags-wrap">
                                    <span class="tag tag-blue">𝕏 / Twitter</span>
                                    ${isVerified
                                        ? '<span class="tag tag-green">Title Match</span>'
                                        : '<span class="tag tag-red">Title Mismatch</span>'
                                    }
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('analysisCol').innerHTML = `

                        <div class="evidence-card ${isVerified ? 'verified' : 'flagged'}">
                            <div class="ev-header">
                                <span class="ev-type">
                                    <span class="ev-dot ${isVerified ? 'verified' : 'flagged'}"></span>
                                    ${isVerified ? 'VERIFIED CONTENT' : 'MISLEADING CLAIM DETECTED'}
                                </span>
                            </div>
                            <div class="ev-content">
                                ${isVerified
                                    ? 'The provided claim strongly matches the actual X post content.'
                                    : 'The title or claim submitted does not properly match the actual X post content.'
                                }
                                <br><br>
                                <strong>User Claim:</strong> ${title}
                                <br><br>
                                <strong>Actual X Post:</strong> ${data.tweet_data.title}
                                <br><br>
                                The similarity score of <strong>${matchPct}%</strong> indicates that the claim is
                                ${isVerified ? 'likely authentic and correctly represented.' : 'possibly misleading, edited, or taken out of context.'}
                            </div>
                        </div>

                        <div style="margin-top:20px; background:white; border-radius:18px; border:1px solid #e8e8e8; overflow:hidden;">
                            ${data.tweet_data.image
                                ? `<img src="${data.tweet_data.image}" style="width:100%; display:block; border-radius:18px 18px 0 0;" onerror="this.style.display='none'">`
                                : `<div style="padding:30px; text-align:center; color:#888; font-size:14px; background:#f9f9f9; border-radius:18px 18px 0 0;">No image attached to this post.</div>`
                            }
                            <div style="padding:20px;">
                                <p style="font-size:15px; color:#444; margin-bottom:16px; line-height:1.6;">
                                    ${data.tweet_data.title}
                                </p>
                                <a href="${data.tweet_data.source_url}" target="_blank" style="padding:12px 20px; background:black; color:white; text-decoration:none; border-radius:12px; display:inline-block; font-weight:700; font-size:14px;">
                                    𝕏 &nbsp; Open Original Post
                                </a>
                                ${data.tweet_data.reverse_search_url
                                    ? `<a href="${data.tweet_data.reverse_search_url}" target="_blank" style="padding:12px 20px; background:#4285f4; color:white; text-decoration:none; border-radius:12px; display:inline-block; font-weight:700; font-size:14px; margin-top:10px; margin-left:10px;">
                                            🔍 Reverse Image Search
                                        </a>`
                                    : ''
                                }
                            </div>
                        </div>

                        <div class="correct-info-card">
                            <div class="correct-info-title">Summary</div>
                            <div class="correct-info-text">
                                ${isVerified
                                    ? `The claim appears to accurately represent the X post content. The similarity score of ${matchPct}% suggests the post is being shared in the correct context.`
                                    : `The claim does not accurately represent the X post. The actual post content differs significantly from what is being claimed. Always verify before sharing.`
                                }
                            </div>
                        </div>
                    `;

                    return;
                }

                // ============================================================
                // YOUTUBE
                // ============================================================

                if (matchPct > 50) {

                    verdictCard.innerHTML = `
                        <div class="verdict-banner true">
                            <div class="verdict-icon true">✓</div>
                            <div class="verdict-text true">
                                <strong>Verified</strong>
                                <span>Content matches the video</span>
                            </div>
                        </div>
                        <div class="verdict-body">
                            <div class="confidence-row">
                                <div class="confidence-label-row">
                                    <span class="conf-label">Confidence</span>
                                    <span class="conf-val">${matchPct}%</span>
                                </div>
                                <div class="conf-bar">
                                    <div class="conf-fill true" style="width:${matchPct}%"></div>
                                </div>
                            </div>
                            <div class="tags-section">
                                <div class="tags-label">Platform</div>
                                <div class="tags-wrap">
                                    <span class="tag tag-red">▶ YouTube</span>
                                    <span class="tag tag-green">Title Match</span>
                                </div>
                            </div>
                        </div>
                    `;

                } else {

                    verdictCard.innerHTML = `
                        <div class="verdict-banner false">
                            <div class="verdict-icon false">✕</div>
                            <div class="verdict-text false">
                                <strong>${data.analysis.result}</strong>
                                <span>Content does not match</span>
                            </div>
                        </div>
                        <div class="verdict-body">
                            <div class="confidence-row">
                                <div class="confidence-label-row">
                                    <span class="conf-label">Confidence</span>
                                    <span class="conf-val">${matchPct}%</span>
                                </div>
                                <div class="conf-bar">
                                    <div class="conf-fill false" style="width:${matchPct}%"></div>
                                </div>
                            </div>
                            <div class="tags-section">
                                <div class="tags-label">Flags</div>
                                <div class="tags-wrap">
                                    <span class="tag tag-red">▶ YouTube</span>
                                    <span class="tag tag-orange">Title Mismatch</span>
                                    ${data.possible_matches.length > 0 ? '<span class="tag tag-blue">Possible Reupload</span>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }

                if (matchPct > 50) {

                    document.getElementById('analysisCol').innerHTML = `
                        <div class="evidence-card verified">
                            <div class="ev-header">
                                <span class="ev-type">
                                    <span class="ev-dot verified"></span>
                                    VERIFIED CONTENT
                                </span>
                            </div>
                            <div class="ev-content">
                                The provided claim strongly matches the actual YouTube video metadata and title.
                                <br><br>
                                <strong>User Claim:</strong> ${title}
                                <br><br>
                                <strong>Actual Video:</strong> ${data.youtube_data.title}
                                <br><br>
                                The similarity score indicates that the content is likely authentic and correctly represented.
                            </div>
                        </div>

                        <div style="margin-top:20px; background:white; border-radius:18px; border:1px solid #e8e8e8; overflow:hidden;">
                            <img src="${data.youtube_data.thumbnail}" style="width:100%; display:block; border-radius:18px 18px 0 0;">
                            <div style="padding:20px;">
                                <p style="font-size:15px; font-weight:700; margin-bottom:6px;">${data.youtube_data.title}</p>
                                <p style="font-size:13px; color:#888; margin-bottom:16px;">
                                    ${data.youtube_data.channel} · ${Number(data.youtube_data.views).toLocaleString()} views
                                </p>
                            </div>
                        </div>

                        <div class="correct-info-card">
                            <div class="correct-info-title">Summary</div>
                            <div class="correct-info-text">
                                The claim accurately represents the YouTube video uploaded by
                                <strong>${data.youtube_data.channel}</strong>.
                                The content appears to be authentic and correctly shared.
                            </div>
                        </div>
                    `;

                } else {

                    document.getElementById('analysisCol').innerHTML = `
                        <div class="evidence-card flagged">
                            <div class="ev-header">
                                <span class="ev-type">
                                    <span class="ev-dot flagged"></span>
                                    MISLEADING CLAIM DETECTED
                                </span>
                            </div>
                            <div class="ev-content">
                                The title or claim submitted by the user does not properly match the actual YouTube video metadata.
                                <br><br>
                                <strong>User Claim:</strong> ${title}
                                <br><br>
                                <strong>Actual Video Title:</strong> ${data.youtube_data.title}
                                <br><br>
                                The system detected a low similarity score of <strong>${matchPct}%</strong>,
                                indicating that the claim may be misleading, edited, or taken out of context.
                            </div>
                        </div>

                        <div style="margin-top:20px; background:white; border-radius:18px; border:1px solid #e8e8e8; overflow:hidden;">
                            <img src="${data.youtube_data.thumbnail}" style="width:100%; display:block; border-radius:18px 18px 0 0;">
                            <div style="padding:20px;">
                                <p style="font-size:15px; font-weight:700; margin-bottom:6px;">${data.youtube_data.title}</p>
                                <p style="font-size:13px; color:#888; margin-bottom:16px;">
                                    ${data.youtube_data.channel} · ${Number(data.youtube_data.views).toLocaleString()} views
                                </p>
                            </div>
                        </div>

                        <h2 style="font-size:24px; font-weight:700; margin-top:30px; margin-bottom:20px;">
                            Possible Older Uploads
                        </h2>

                        ${data.possible_matches.length > 0
                            ? data.possible_matches.map(match => `
                                <div style="border:1px solid #e8e8e8; border-radius:18px; padding:16px; margin-bottom:18px; display:flex; gap:18px; background:white;">
                                    <img src="${match.thumbnail}" style="width:160px; height:100px; object-fit:cover; border-radius:12px; flex-shrink:0;">
                                    <div style="flex:1; min-width:0;">
                                        <h3 style="font-size:15px; font-weight:700; margin-bottom:8px; line-height:1.4;">${match.title}</h3>
                                        <p style="font-size:13px; color:#666; margin-bottom:4px;"><strong>Channel:</strong> ${match.channel}</p>
                                        <p style="font-size:13px; color:#666; margin-bottom:4px;"><strong>Uploaded:</strong> ${new Date(match.published_at).toDateString()}</p>
                                        <p style="color:#ff8c42; font-weight:700; font-size:13px; margin-top:8px;">Similarity: ${match.similarity}%</p>
                                        ${match.thumbnail_match
                                            ? '<p style="color:#35d07f; font-weight:700; font-size:13px; margin-top:4px;">✓ Thumbnail Match Detected</p>'
                                            : ''
                                        }
                                        <a href="https://youtube.com/watch?v=${match.video_id}" target="_blank" style="display:inline-block; margin-top:12px; padding:8px 16px; background:#f1f1f1; color:#000; border-radius:10px; text-decoration:none; font-weight:700; font-size:13px;">
                                            ▶ Watch Video
                                        </a>
                                    </div>
                                </div>
                            `).join('')
                            : '<p style="color:#888; font-size:14px;">No older uploads detected.</p>'
                        }

                        <div class="correct-info-card">
                            <div class="correct-info-title">What Actually Happened</div>
                            <div class="correct-info-text">
                                The original video appears to be related to:
                                <br><br>
                                <strong>${data.youtube_data.title}</strong>
                                <br><br>
                                Uploaded by: <strong>${data.youtube_data.channel}</strong>
                                <br><br>
                                The submitted claim may have altered or misrepresented the actual context of the video.
                            </div>
                        </div>
                    `;
                }

            } catch (error) {

                console.error(error);
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('btnText').innerText = 'Investigate This →';
                document.getElementById('btnSpinner').style.display = 'none';
                alert('Something went wrong. Please try again.');
            }
        });
        document.getElementById('basicBtn')
            .addEventListener('click', function(e) {

                e.preventDefault();

                const options = {

                    key: "{{ env('RAZORPAY_KEY') }}",

                    amount: 9900,

                    currency: "INR",

                    name: "VerifyAI",

                    description: "Basic Plan",

                    theme: {
                        color: "#111111"
                    },

                    handler: async function(response) {

                        const res = await fetch('/activate-basic', {

                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                            },

                            body: JSON.stringify({

                                payment_id: response.razorpay_payment_id
                            })
                        });

                        const data = await res.json();

                        if (data.status) {

                            alert(
                                '🎉 Basic Plan Activated!'
                            );

                            location.reload();
                        }
                    }
                };

                const rzp = new Razorpay(options);

                rzp.open();
            });
        document.getElementById('proBtn')
            .addEventListener('click', function(e) {

                e.preventDefault();

                const options = {

                    key: "{{ env('RAZORPAY_KEY') }}",

                    amount: 29900,

                    currency: "INR",

                    name: "VerifyAI",

                    description: "PRO Plan",

                    theme: {
                        color: "#111111"
                    },

                    handler: async function(response) {

                        const res = await fetch('/activate-pro', {

                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                            },

                            body: JSON.stringify({

                                payment_id: response.razorpay_payment_id
                            })
                        });

                        const data = await res.json();

                        if (data.status) {

                            alert('🎉 PRO Activated');

                            location.reload();
                        }
                    }
                };

                const rzp = new Razorpay(options);

                rzp.open();
            });
        document.getElementById('unlimitedBtn')
            .addEventListener('click', function(e) {

                e.preventDefault();

                const options = {

                    key: "{{ env('RAZORPAY_KEY') }}",

                    amount: 69900,

                    currency: "INR",

                    name: "VerifyAI",

                    description: "UNLIMITED Plan",

                    theme: {
                        color: "#111111"
                    },

                    handler: async function(response) {

                        const res = await fetch('/activate-unlimited', {

                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                            },

                            body: JSON.stringify({

                                payment_id: response.razorpay_payment_id
                            })
                        });

                        const data = await res.json();

                        if (data.status) {

                            alert('🚀 Unlimited Activated');

                            location.reload();
                        }
                    }
                };

                const rzp = new Razorpay(options);

                rzp.open();
            });

    </script>
</body>
</html>
