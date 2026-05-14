<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;animation: fadeUp 0.5s var(--ease-out) both;">
    <div style="width:100%;max-width:420px;">

        <!-- Vinyl logo decoration -->
        <div style="display:flex;justify-content:center;margin-bottom:28px;">
            <div style="width:56px;height:56px;border-radius:50%;background:radial-gradient(circle,#0a0a0a 0%,#0a0a0a 22%,#2a2a2a 23%,#2a2a2a 38%,#161616 39%,#161616 47%,var(--accent) 48%,var(--accent) 52%,#161616 53%,#161616 100%);animation: spinVinyl 8s linear infinite;opacity:0.7;"></div>
        </div>

        <div class="auth-card">
            <div style="text-align:center;margin-bottom:32px;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:6px;">Přihlášení</h2>
                <p style="font-size:14px;color:var(--t2);">Vítejte zpět v aplikaci VinyLog.</p>
            </div>

            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=authenticate"
                  method="post">
                <div style="display:flex;flex-direction:column;gap:18px;">

                    <div>
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" required autofocus
                               class="input-field" placeholder="vas@email.cz">
                    </div>

                    <div>
                        <label for="password" class="form-label">Heslo</label>
                        <input type="password" id="password" name="password" required
                               class="input-field" placeholder="••••••••">
                    </div>

                    <div style="padding-top:6px;">
                        <button type="submit" class="btn btn-gold" style="width:100%;padding:13px 24px;font-size:15px;">
                            Přihlásit se
                        </button>
                    </div>

                </div>
            </form>

            <div class="auth-divider"></div>

            <p style="text-align:center;font-size:13px;color:var(--t3);">
                Nemáte ještě účet?
                <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=register"
                   style="color:var(--accent);font-weight:600;transition:color 150ms ease;"
                   onmouseover="this.style.color='var(--accent-h)'"
                   onmouseout="this.style.color='var(--accent)'">
                    Zaregistrujte se &rarr;
                </a>
            </p>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
