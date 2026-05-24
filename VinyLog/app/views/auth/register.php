<?php
// Vložíme hlavičku stránky (navigace, flash zprávy, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Registrační stránka – centrovaný formulář -->
<div style="display:flex;align-items:flex-start;justify-content:center;padding:20px 0 40px;animation: fadeUp 0.5s var(--ease-out) both;">
    <div style="width:100%;max-width:520px;">

        <!-- Nadpis nad kartou -->
        <div style="text-align:center;margin-bottom:28px;">
            <h2 style="font-size:1.5rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:6px;">Nová registrace</h2>
            <p style="font-size:14px;color:var(--t2);">Vytvořte si účet pro přístup a správu sbírky.</p>
        </div>

        <!-- Karta formuláře -->
        <div class="auth-card">

            <!-- Formulář odesílá POST na AuthController::storeUser()
                 Která validuje data a pokud vše sedí, vytvoří nový účet v DB. -->
            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=storeUser"
                  method="post">
                <div style="display:flex;flex-direction:column;gap:18px;">

                    <!-- =====================================================
                         Sekce: Přihlašovací údaje (povinné)
                         ===================================================== -->
                    <span class="form-section-label">Přihlašovací údaje</span>

                    <!-- Pole: Uživatelské jméno -->
                    <div style="animation: fadeUp 0.4s var(--ease-out) 60ms both;">
                        <label for="username" class="form-label">
                            Uživatelské jméno <span style="color:var(--red);">*</span>
                        </label>
                        <input type="text" id="username" name="username" required
                               class="input-field" placeholder="jmeno123">
                    </div>

                    <!-- Pole: E-mail (musí být unikátní v systému) -->
                    <div style="animation: fadeUp 0.4s var(--ease-out) 100ms both;">
                        <label for="email" class="form-label">
                            E-mail <span style="color:var(--red);">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               class="input-field" placeholder="vas@email.cz">
                    </div>

                    <!-- Pole: Heslo + Potvrzení hesla (vedle sebe na 2 sloupce)
                         pattern a minlength provádí HTML5 validaci na straně prohlížeče.
                         Stejná validace je i na serveru v AuthController::storeUser(). -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;animation: fadeUp 0.4s var(--ease-out) 140ms both;">
                        <div>
                            <label for="password" class="form-label">
                                Heslo <span style="color:var(--red);">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                   minlength="8"
                                   pattern="(?=.*\d).{8,}"
                                   title="Minimálně 8 znaků a alespoň 1 číslice"
                                   class="input-field" placeholder="min. 8 znaků">
                        </div>
                        <div>
                            <label for="password_confirm" class="form-label">
                                Potvrzení <span style="color:var(--red);">*</span>
                            </label>
                            <!-- Shoda hesel je validována v AuthController, ne HTML5 -->
                            <input type="password" id="password_confirm" name="password_confirm" required
                                   minlength="8" class="input-field" placeholder="zopakujte heslo">
                        </div>
                    </div>

                    <!-- =====================================================
                         Sekce: Osobní údaje (volitelné)
                         ===================================================== -->
                    <span class="form-section-label" style="margin-top:8px;">
                        Osobní údaje
                        <span style="font-size:10px;color:var(--t3);font-weight:500;text-transform:none;letter-spacing:0;">(volitelné)</span>
                    </span>

                    <!-- Pole: Křestní jméno + Příjmení (vedle sebe) -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;animation: fadeUp 0.4s var(--ease-out) 180ms both;">
                        <div>
                            <label for="first_name" class="form-label">Křestní jméno</label>
                            <input type="text" id="first_name" name="first_name"
                                   class="input-field" placeholder="Jan">
                        </div>
                        <div>
                            <label for="last_name" class="form-label">Příjmení</label>
                            <input type="text" id="last_name" name="last_name"
                                   class="input-field" placeholder="Novák">
                        </div>
                    </div>

                    <!-- Pole: Přezdívka – zobrazuje se v navigaci místo uživatelského jména -->
                    <div style="animation: fadeUp 0.4s var(--ease-out) 220ms both;">
                        <label for="nickname" class="form-label">Přezdívka</label>
                        <input type="text" id="nickname" name="nickname"
                               class="input-field" placeholder="Jak vám máme říkat?">
                    </div>

                    <!-- Odesílací tlačítko -->
                    <div style="padding-top:8px;animation: fadeUp 0.4s var(--ease-out) 260ms both;">
                        <button type="submit" class="btn btn-gold" style="width:100%;padding:13px 24px;font-size:15px;">
                            Vytvořit účet
                        </button>
                    </div>

                </div>
            </form>

            <div class="auth-divider"></div>

            <!-- Odkaz na přihlášení pro stávající uživatele -->
            <p style="text-align:center;font-size:13px;color:var(--t3);">
                Už máte účet?
                <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login"
                   style="color:var(--accent);font-weight:600;transition:color 150ms ease;"
                   onmouseover="this.style.color='var(--accent-h)'"
                   onmouseout="this.style.color='var(--accent)'">
                    Přihlaste se &rarr;
                </a>
            </p>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
