<?php
session_start();

class AuthController {

    // 1. Zobrazení registračního formuláře
    public function register() {
        require_once '../views/auth/register.php';
    }

    // 2. Zpracování dat z registrace
    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Očištění textových vstupů
            $username = htmlspecialchars($_POST['username'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $firstName = htmlspecialchars($_POST['first_name'] ?? '');
            $lastName = htmlspecialchars($_POST['last_name'] ?? '');
            $nickname = htmlspecialchars($_POST['nickname'] ?? '');

            // Hesla neočišťujeme přes htmlspecialchars, protože by to mohlo rozbít speciální znaky v hesle
            $password = trim($_POST['password'] ?? '');
            $passwordConfirm = trim($_POST['password_confirm'] ?? '');

            // Základní validace na straně serveru
            if (empty($username) || empty($email) || empty($password)) {
                $this->addErrorMessage('Vyplňte prosím všechna povinná pole.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            if ($password !== $passwordConfirm) {
                $this->addErrorMessage('Zadaná hesla se neshodují.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            // Kontrola síly hesla: minimálně 8 znaků a alespoň 1 číslice
            if (mb_strlen($password) < 8 || !preg_match('/\d/', $password)) {
                $this->addErrorMessage('Heslo musí mít alespoň 8 znaků a obsahovat alespoň jedno číslo.');
                header('Location: AuthController.php?action=register');
                exit;
            }

            // Napojení na DB a Model
            require_once '../models/Database.php';
            require_once '../models/User.php';

            $db = (new Database())->getConnection();
            $userModel = new User($db);

            // Pokus o uložení do databáze
            if ($userModel->register($username, $email, $password, $firstName, $lastName, $nickname)) {
                $this->addSuccessMessage('Registrace byla úspěšná. Nyní se můžete přihlásit.');
                header('Location: AuthController.php?action=login');
                exit;
            } else {
                $this->addErrorMessage('Uživatel s tímto e-mailem již existuje.');
                header('Location: AuthController.php?action=register');
                exit;
            }
        }
    }

    // 3. Zobrazení přihlašovacího formuláře
    public function login() {
        require_once '../views/auth/login.php';
    }

    // 4. Zpracování přihlášení (Ověření hesla)
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = htmlspecialchars($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            require_once '../models/Database.php';
            require_once '../models/User.php';

            $db = (new Database())->getConnection();
            $userModel = new User($db);

            // Najdeme uživatele podle emailu
            $user = $userModel->findByEmail($email);

            // ZABEZPEČENÍ: Zkontrolujeme, zda uživatel existuje a zda zadané heslo 
            // odpovídá zahašovanému heslu v databázi (pomocí password_verify)
            if ($user && password_verify($password, $user['password'])) {

                // ÚSPĚCH: Uložíme si důležitá data do Session
                $_SESSION['user_id'] = $user['id'];

                // Uložíme si jméno pro uvítání (přezdívku, nebo uživatelské jméno)
                $_SESSION['user_name'] = !empty($user['nickname']) ? $user['nickname'] : $user['username'];

                $this->addSuccessMessage('Vítejte zpět, ' . $_SESSION['user_name'] . '!');
                header('Location: BookController.php?action=index');
                exit;

            } else {
                // CHYBA: Záměrně neříkáme, zda byl špatný email, nebo heslo (bezpečnost!)
                $this->addErrorMessage('Nesprávný e-mail nebo heslo.');
                header('Location: AuthController.php?action=login');
                exit;
            }
        }
    }

    // 5. Odhlášení uživatele
    public function logout() {
        // Vymažeme specifická uživatelská data ze Session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        $this->addSuccessMessage('Byli jste úspěšně odhlášeni.');
        header('Location: BookController.php?action=index');
        exit;
    }

    // --- Pomocné metody pro notifikace (stejné jako v BookControlleru) ---
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

// Instantiate and route the request (simple router compatible with direct controller calls)
$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;
    if ($action === 'storeUser') {
        $controller->storeUser();
    } elseif ($action === 'authenticate') {
        $controller->authenticate();
    } else {
        // Unknown POST action — redirect to login
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? './'));
        exit;
    }
} else {
    $action = $_GET['action'] ?? 'login';
    if ($action === 'register') {
        $controller->register();
    } elseif ($action === 'login') {
        $controller->login();
    } elseif ($action === 'logout') {
        $controller->logout();
    } else {
        // Fallback
        $controller->login();
    }
}
