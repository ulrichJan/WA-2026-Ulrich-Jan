<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-light tracking-widest text-slate-300 uppercase">Nová registrace</h2>
            <p class="text-slate-500 italic mt-2 text-sm">Vytvořte si účet pro správu vašeho knižního katalogu.</p>
        </div>
        
        <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-8">
            <form action="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/AuthController.php?action=storeUser" method="post">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-emerald-400 text-xs font-bold uppercase tracking-widest border-b border-slate-700 pb-2 mb-4">Přihlašovací údaje</h3>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Uživatelské jméno <span class="text-[#c1121f]">*</span></label>
                        <input type="text" id="username" name="username" required 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">E-mail <span class="text-[#c1121f]">*</span></label>
                        <input type="email" id="email" name="email" required 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Heslo <span class="text-[#c1121f]">*</span></label>
                           <input type="password" id="password" name="password" required minlength="8" pattern="(?=.*\d).{8,}" title="Minimálně 8 znaků a alespoň 1 číslice" 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Potvrzení hesla <span class="text-[#c1121f]">*</span></label>
                           <input type="password" id="password_confirm" name="password_confirm" required minlength="8" 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-blue-400 text-xs font-bold uppercase tracking-widest border-b border-slate-700 pb-2 mb-4">Osobní údaje (Volitelné)</h3>
                    </div>

                    <div>
                        <label for="first_name" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Křestní jméno</label>
                        <input type="text" id="first_name" name="first_name" 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Příjmení</label>
                        <input type="text" id="last_name" name="last_name" 
                               class="w-full p-3 rounded-md border border-[#f0ded5]">
                    </div>

                    <div class="md:col-span-2">
                        <label for="nickname" class="block text-sm font-medium text-[#6b291f] mb-1 uppercase tracking-wider">Zobrazovaná přezdívka</label>
                        <input type="text" id="nickname" name="nickname" placeholder="Jak vám máme v aplikaci říkat?"
                               class="w-full p-3 rounded-md border border-[#f0ded5] placeholder-[#a77b6f]">
                    </div>

                    <div class="md:col-span-2 mt-6">
                        <button type="submit" 
                                class="w-full bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] font-bold py-3 px-4 rounded-md shadow-lg border border-[#c1121f] transition-all uppercase tracking-widest text-sm">
                            Vytvořit účet
                        </button>
                        <p class="text-center text-slate-500 text-sm mt-4">
                               Už máte účet? <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/AuthController.php?action=login" class="text-blue-400 hover:text-white transition-colors">Přihlaste se zde</a>.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
