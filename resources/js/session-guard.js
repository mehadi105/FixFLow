/**
 * If the browser restores a logged-in page from back/forward cache after logout,
 * force a reload so Laravel auth middleware can redirect to login.
 */
window.addEventListener('pageshow', (event) => {
    if (! event.persisted) {
        return;
    }

    if (! document.body?.dataset?.requiresAuth) {
        return;
    }

    window.location.reload();
});
