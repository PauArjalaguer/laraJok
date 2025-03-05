let deferredPrompt;

window.addEventListener("beforeinstallprompt", (event) => {    
    event.preventDefault();
    deferredPrompt = event;

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
    } else {
        console.warn("No s'ha trobat el botó d'instal·lació");
    }
});