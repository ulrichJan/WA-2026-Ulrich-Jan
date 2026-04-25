<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-light tracking-widest text-slate-300 uppercase">Přihlášení</h2>
            <p class="text-slate-500 italic mt-2 text-sm">Vítejte zpět v naší Knihovně.</p>
        </div>
        
        <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-8">
            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=authenticate" method="post">
                
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">E-mail</label>
                        <input type="email" id="email" name="email" required autofocus
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Heslo</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] font-bold py-3 px-4 rounded-md shadow-lg border border-[#c1121f] transition-all uppercase tracking-widest text-sm">
                            Přihlásit se
                        </button>
                    </div>
                    
                    <p class="text-center text-slate-500 text-sm border-t border-slate-700 pt-4">
                        Nemáte ještě účet? <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=register" class="text-emerald-400 hover:text-white transition-colors">Zaregistrujte se</a>.
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
