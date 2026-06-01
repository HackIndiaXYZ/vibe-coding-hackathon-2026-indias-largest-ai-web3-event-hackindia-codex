<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Verify AI · Log in</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="Stylesheet" href="{{asset('css/login.css')}}" />
</head>
<body>
    <div class="app-container">
        <div class="brand-header">
            <h1>Verify AI</h1>
            <div class="brand-tag">Media verification · Real-time authenticity engine</div>
        </div>

        <div class="login-grid">
            <!-- left side: consistent branding + stats -->
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
                        🔍 No signup required · 3 free analyses
                    </div>
                </div>
                <div style="margin-top: 1rem; background: transparent; border-left: 2px solid #cbd5e1; padding-left: 1rem;">
                    <p style="font-size: 0.8rem; color: #3b4a6b;">✓ Trusted by fact-checkers <br>✓ Real-time URL & image forensics</p>
                </div>
            </div>

            <!-- right side: LOGIN FORM (email, password, optional stay signed in) -->
            <div class="form-panel">
                <div class="form-title">Welcome back</div>
                <div class="form-sub">Log in to access your verification history & pro tools</div>

                <form id="loginForm" action="{{ route('login.post') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <label>Email address</label>
                        <input type="email" id="loginEmail" name="email" placeholder="hello@example.com" autocomplete="email">
                        <div id="emailError" class="error-message"></div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" id="loginPassword" name="password" placeholder="••••••••" autocomplete="current-password">
                        <div id="passwordError" class="error-message"></div>
                       
                    </div>

                    <div class="checkbox-row">
                        <input type="checkbox" id="staySignedIn">
                        <label for="staySignedIn">Stay signed in for 30 days</label>
                    </div>

                    <button type="submit" class="btn-primary">Log in →</button>

                    <div class="signup-redirect">
                        Don't have an account? <a href="{{ route('signup') }}" id="createAccountLink">Create free account</a>
                    </div>
                </form>
                <div style="font-size: 0.7rem; text-align: center; margin-top: 1rem; color: #7f8ea3;">
                    Secure login · End-to-end encrypted verification data
                </div>
            </div>
        </div>
    </div>

    <div id="toastMsg" class="toast-msg" style="opacity:0; visibility: hidden;">Message</div>


</body>
</html>
