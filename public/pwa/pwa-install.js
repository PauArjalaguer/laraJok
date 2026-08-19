window.deferredPrompt = null;

window.addEventListener("beforeinstallprompt", (event) => {
    console.log("beforeinstallprompt detectat!");
    event.preventDefault();
    window.deferredPrompt = event;

    const pwaBtnText = document.getElementById("pwaInstallBtnText");
    if (pwaBtnText) {
        pwaBtnText.innerText = "Instal·lar Ara";
    }

    const installBtn = document.getElementById("install-btn");
    if (installBtn) {
        installBtn.style.display = "block";
    }
});