<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Verify AI · Sign Up</title>
    <!-- Google Fonts + simple reset -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="Stylesheet" href="{{asset('css/signup.css')}}" />
</head>
<body>
    <div class="app-container">
        <div class="brand-header">
            <h1>Verify AI</h1>
            <div class="brand-tag">Media verification · Real-time authenticity engine</div>
        </div>

        <div class="signup-grid">
            <!-- left side: similar to "See What's Real Before It Spreads" & stats -->
            <div class="info-panel">
                <div class="hero-badge">
                    <div class="verified-quote">See What's Real <br>Before It Spreads.</div>
                    <div class="quote-sub">
                        Cross‑reference, metadata check & clear verdict — no jargon, just truth.
                    </div>
                    <div class="stat-row">
                        <div class="stat-item">
                            <div class="stat-number">94%</div>
                            <div class="stat-label">ACCURACY RATE</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">2.4s</div>
                            <div class="stat-label">AVG. CHECK TIME</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">12k+</div>
                            <div class="stat-label">CLAIMS CHECKED</div>
                        </div>
                    </div>
                    <div class="free-badge">
                        ⚡ No signup required · 3 free analyses
                    </div>
                </div>
                <!-- additional context from original theme (optional) but adds consistency -->
                <div style="margin-top: 1rem; background: transparent; border-left: 2px solid #cbd5e1; padding-left: 1rem;">
                    <p style="font-size: 0.8rem; color: #3b4a6b;">✓ Trusted by fact-checkers <br>✓ Real-time URL & image forensics</p>
                </div>
            </div>

            <!-- right side: SIGNUP FORM (name, email, password, confirm) -->
            <div class="form-panel">
                <div class="form-title">Create account</div>
                <div class="form-sub">Start verifying media instantly — secure & free tier</div>

                <form id="signupForm" action="{{ route('signup.post') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <label>Full name</label>
                        <input type="text" id="fullName" name="name" placeholder="e.g., Taylor Chen" autocomplete="name">
                        <div id="nameError" class="error-message"></div>
                    </div>

                    <div class="input-group">
                        <label>Email address</label>
                        <input type="email" id="email" name="email" placeholder="hello@example.com" autocomplete="email">
                        <div id="emailError" class="error-message"></div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="new-password">
                        <div class="password-hint">Minimum 8 characters, at least 1 letter & 1 number</div>
                        <div id="passwordError" class="error-message"></div>
                    </div>

                    <div class="input-group">
                        <label>Confirm password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" autocomplete="off">
                        <div id="confirmError" class="error-message"></div>
                    </div>

                    <button type="submit" class="btn-primary">Sign up →</button>
                    <div class="login-redirect">
                        Already have an account? <a href="{{ route('login') }}" id="mockLoginLink">Log in</a>
                    </div>
                </form>
                <div style="font-size: 0.7rem; text-align: center; margin-top: 1rem; color: #7f8ea3;">
                    By signing up you agree to Verify AI’s terms & privacy.
                </div>
            </div>
        </div>
    </div>

    <!-- toast container -->
    <div id="toastMsg" class="toast-msg" style="opacity:0; visibility: hidden;">Message</div>


</body>
</html>
