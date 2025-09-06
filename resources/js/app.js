import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(function (registration) {        
    }).catch(function (registrationError) {
        console.log(`SW registration failed`);
    });
}

let deferredPrompt;

window.addEventListener("beforeinstallprompt", (event) => {
    event.preventDefault();
    deferredPrompt = event;

    // Mostra el botó d'instal·lació quan l'event estigui disponible
    const installBtn = document.getElementById("install-btn");
    if (installBtn) {
        installBtn.style.display = "block";
        installBtn.addEventListener("click", async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log("L'usuari ha triat:", outcome);
                deferredPrompt = null;
                installBtn.style.display = "none";
            }
        });
    }
});
