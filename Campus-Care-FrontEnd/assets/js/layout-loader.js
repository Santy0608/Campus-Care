document.addEventListener('DOMContentLoaded', async () => {
    const headerTarget = document.getElementById('header-placeholder');
    const footerTarget = document.getElementById('footer-placeholder');

    try {
        const [headerMarkup, footerMarkup] = await Promise.all([
            cargarFragmento('/includes/header.html'),
            cargarFragmento('/includes/footer.html'),
        ]);

        if (headerTarget) {
            headerTarget.innerHTML = headerMarkup;
            if (typeof renderNavbar === 'function') {
                const usuario = window.CampusCareApi ? window.CampusCareApi.getStoredUser() : null;
                renderNavbar(usuario);
            }
        }

        if (footerTarget) {
            footerTarget.innerHTML = footerMarkup;
        }
    } catch (error) {
        console.error('No se pudieron cargar los fragmentos compartidos:', error);
    }
});

async function cargarFragmento(url) {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error(`No se pudo cargar ${url}`);
    }

    return response.text();
}