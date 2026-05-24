<?php

// Spuštění session – musí být voláno před jakoukoliv prací se $_SESSION
session_start();

/**
 * Třída AuthController – správa autentizace uživatelů
 *
 * Zodpovídá za registraci, přihlášení a odhlášení.
 * Každá metoda odpovídá jedné "akci" přístupné přes URL parametr ?action=...
 *
 * Bezpečnostní principy aplikované v tomto controlleru:
 *  - Hesla jsou hashována (password_hash / password_verify)
 *  - Chybová hlášení jsou záměrně neurčitá (nevyzradíme, zda špatný email nebo heslo)
 *  - Vstupy jsou ošetřeny htmlspecialchars() před dalším zpracováním
 *  - Session uchovává jen minimum nutných dat (user_id, user_name, is_admin)
 */
class AuthController {

    // =========================================================================
    // 1. Zobrazení registračního formuláře
    // =========================================================================

    /**
     * Zobrazí HTML stránku s registračním formulářem.
     * Žádná logika – pouze include view souboru.
     */
    public function register() {
        require_once '../views/auth/register.php';
    }

    // =========================================================================
    // 2. Zpracování odeslaného registračního formuláře
    // =========================================================================

    /**
     * Zpracuje POST data z registračního formuláře a vytvoří nový účet.
     *
     * Postup:
     *  1) Sanitizace a načtení vstupů
     *  2) Validace (povinná pole, shoda hesel, síla hesla)
     *  3) Pokus o registraci přes User model
     *  4) Přesměrování s výsledkem (success/error flash zpráva)
     */
    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Textové vstupy sanitizujeme pomocí htmlspecialchars()
            // → převede znaky jako <, >, &, " na HTML entity, chrání před XSS
            $username  = htmlspecialchars($_POST['username']   ?? '');
            $email     = htmlspecialchars($_POST['email']      ?? '');
            $firstName = htmlspecialchars($_POST['first_name'] ?? '');
            $lastName  = htmlspecialchars($_POST['last_name']  ?? '');
            $nickname  = htmlspecialchars($_POST['nickname']   ?? '');

            // Heslo NESANITIZUJEME přes htmlspecialchars – speciální znaky jsou v heslech platné
            // a jejich zakódování by heslo změnilo (např. heslo "P@ssw>rd" by se uložilo jinak)
            $password        = trim($_POST['password']         ?? '');
            $passwordConfirm = trim($_POST['password_confirm'] ?? '');

            // --- Validace: povinná pole ---
            if (empty($username) || empty($email) || empty($password)) {
                $this->addErrorMessage('Vyplňte prosím všechna povinná pole.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            // --- Validace: shoda hesel ---
            if ($password !== $passwordConfirm) {
                $this->addErrorMessage('Zadaná hesla se neshodují.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            // --- Validace: síla hesla (min. 8 znaků a alespoň 1 číslice) ---
            // mb_strlen = multibyte strlen, správně počítá české znaky
            if (mb_strlen($password) < 8 || !preg_match('/\d/', $password)) {
                $this->addErrorMessage('Heslo musí mít alespoň 8 znaků a obsahovat alespoň jedno číslo.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            // --- Přístup k DB a modelu ---
            require_once '../models/Database.php';
            require_once '../models/User.php';

            $db        = (new Database())->getConnection();
            $userModel = new User($db);

            // --- Pokus o uložení do DB ---
            // User::register() uvnitř ověří unikátnost e-mailu
            if ($userModel->register($username, $email, $password, $firstName, $lastName, $nickname)) {
                $this->addSuccessMessage('Registrace byla úspěšná. Nyní se můžete přihlásit.');
                header('Location: AuthController.php?action=login');
                exit;
            } else {
                // Registrace selhala → e-mail je pravděpodobně obsazen
                $this->addErrorMessage('Uživatel s tímto e-mailem již existuje.');
                header('Location: AuthController.php?action=register');
                exit;
            }
        }
    }

    // =========================================================================
    // 3. Zobrazení přihlašovacího formuláře
    // =========================================================================

    /**
     * Zobrazí HTML stránku s přihlašovacím formulářem.
     */
    public function login() {
        require_once '../views/auth/login.php';
    }

    // =========================================================================
    // 4. Ověření přihlašovacích údajů a zahájení session
    // =========================================================================

    /**
     * Zpracuje POST data z přihlašovacího formuláře.
     *
     * Postup:
     *  1) Najde uživatele dle e-mailu (User::findByEmail)
     *  2) Ověří heslo proti uloženému hashi (password_verify)
     *  3) Při úspěchu uloží identitu do $_SESSION
     *  4) Při neúspěchu zobrazí neutrální chybovou zprávu (bez detail co bylo špatně)
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = htmlspecialchars($_POST['email']    ?? '');
            $password = $_POST['password'] ?? ''; // heslo nesanitizujeme, viz výše

            require_once '../models/Database.php';
            require_once '../models/User.php';

            $db        = (new Database())->getConnection();
            $userModel = new User($db);

            // Načteme uživatele z DB podle e-mailu
            $user = $userModel->findByEmail($email);

            // password_verify() porovná zadané heslo s bcrypt hashem z DB.
            // Nelze obrátit – z hashe se heslo nezjistí.
            if ($user && password_verify($password, $user['password'])) {

                // --- ÚSPĚCH: Uložení identity do session ---
                $_SESSION['user_id'] = $user['id'];

                // Příznak admina uložíme do session, aby VinylController mohl ověřit práva
                // bez dalšího DB dotazu. Hodnota 0 = běžný uživatel, 1 = administrátor.
                $_SESSION['is_admin'] = (int)($user['is_admin'] ?? 0);

                // Zobrazíme přezdívku, nebo uživatelské jméno pokud přezdívka chybí
                $_SESSION['user_name'] = !empty($user['nickname']) ? $user['nickname'] : $user['username'];

                $this->addSuccessMessage('Vítejte zpět, ' . $_SESSION['user_name'] . '!');
                header('Location: VinylController.php?action=index');
                exit;

            } else {
                // --- NEÚSPĚCH: záměrně neurčitá zpráva ---
                // Neříkáme, zda byl špatný e-mail nebo heslo – to by útočníkovi
                // pomohlo zjistit, zda e-mail v systému existuje.
                $this->addErrorMessage('Nesprávný e-mail nebo heslo.');
                header('Location: AuthController.php?action=login');
                exit;
            }
        }
    }

    // =========================================================================
    // 5. Odhlášení uživatele
    // =========================================================================

    /**
     * Odhlásí uživatele – smaže jeho identitu ze session.
     *
     * Mažeme pouze specifické klíče (ne celou session), protože session může
     * obsahovat i jiné data (např. flash zprávy čekající na zobrazení).
     */
    public function logout() {
        // Odebrání klíčů identifikujících přihlášeného uživatele
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['is_admin']);

        $this->addSuccessMessage('Byli jste úspěšně odhlášeni.');
        header('Location: VinylController.php?action=index');
        exit;
    }

    // =========================================================================
    // Pomocné metody – flash zprávy (notifikace)
    // =========================================================================
    // Zprávy se ukládají do session a zobrazí se při příštím načtení stránky
    // (header.php je přečte a vymaže). Tři úrovně: success, notice, error.

    protected function addSuccessMessage($message) {
        $_SESSION['messages']['success'][] = $message;
    }

    protected function addNoticeMessage($message) {
        $_SESSION['messages']['notice'][] = $message;
    }

    protected function addErrorMessage($message) {
        $_SESSION['messages']['error'][] = $message;
    }
}

// =============================================================================
// Routing – jednoduchý přepínač akcí dle URL parametru ?action=...
// =============================================================================

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST požadavky – zpracování formulářů
    $action = $_GET['action'] ?? null;
    if ($action === 'storeUser') {
        $controller->storeUser();      // Zpracuj registraci
    } elseif ($action === 'authenticate') {
        $controller->authenticate();   // Zpracuj přihlášení
    } else {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? './'));
        exit;
    }
} else {
    // GET požadavky – zobrazení formulářů
    $action = $_GET['action'] ?? 'login';
    if ($action === 'register') {
        $controller->register();       // Zobraz registrační formulář
    } elseif ($action === 'login') {
        $controller->login();          // Zobraz přihlašovací formulář
    } elseif ($action === 'logout') {
        $controller->logout();         // Proveď odhlášení
    } else {
        $controller->login();          // Výchozí akce
    }
}
