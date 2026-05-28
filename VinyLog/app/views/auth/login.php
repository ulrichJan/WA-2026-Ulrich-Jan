<?php
// Vložíme hlavičku stránky (navigace, flash zprávy, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Přihlašovací stránka – centrovaný formulář ve středu stránky -->
<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;animation: fadeUp 0.5s var(--ease-out) both;">
    <div style="width:100%;max-width:420px;">

        <!-- Dekorativní animovaná vinylová deska nad formulářem -->
        <div style="display:flex;justify-content:center;margin-bottom:28px;">
            <div style="width:56px;height:56px;border-radius:50%;
                        background:radial-gradient(circle,#0a0a0a 0%,#0a0a0a 22%,#2a2a2a 23%,#2a2a2a 38%,#161616 39%,#161616 47%,var(--accent) 48%,var(--accent) 52%,#161616 53%,#161616 100%);
                        animation: spinVinyl 8s linear infinite;opacity:0.7;"></div>
        </div>

        <!-- Karta formuláře -->
        <div class="auth-card">
            <div style="text-align:center;margin-bottom:32px;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:6px;">Přihlášení</h2>
                <p style="font-size:14px;color:var(--t2);">Vítejte zpět v aplikaci VinyLog.</p>
            </div>

            <!-- Formulář odesílá POST na AuthController::authenticate()
                 Metoda ověří heslo proti hash v DB a při úspěchu vytvoří session. -->
            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=authenticate"
                  method="post">
                <div style="display:flex;flex-direction:column;gap:18px;">

                    <!-- Pole: E-mail (autofocus = okamžitě aktivní po načtení stránky) -->
                    <div>
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" required autofocus
                               class="input-field" placeholder="vas@email.cz">
                    </div>

                    <!-- Pole: Heslo + toggle zobrazení -->
                    <div>
                        <label for="password" class="form-label">Heslo</label>
                        <div class="pwd-wrap">
                            <input type="password" id="password" name="password" required
                                   class="input-field" placeholder="••••••••">
                            <button type="button" class="pwd-toggle" onclick="vlTogglePwd('password',this)" aria-label="Zobrazit/skrýt heslo">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Odesílací tlačítko -->
                    <div style="padding-top:6px;">
                        <button type="submit" class="btn btn-gold" style="width:100%;padding:13px 24px;font-size:15px;">
                            Přihlásit se
                        </button>
                    </div>

                </div>
            </form>

            <div class="auth-divider"></div>

            <!-- Odkaz na registraci pro nové uživatele -->
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

<script>
/* Přepnutí viditelnosti hesla */
function vlTogglePwd(inputId, btn) {
    var input = document.getElementById(inputId);
    var show  = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = show
        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    btn.setAttribute('aria-label', show ? 'Skrýt heslo' : 'Zobrazit heslo');
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
