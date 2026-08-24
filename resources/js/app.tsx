import { createInertiaApp, ResolvedComponent } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import '../css/react.css';

// Peta halaman lazy — chunk tiap halaman hanya dimuat saat dibuka,
// lalu di-prefetch di latar belakang agar navigasi berikutnya instan.
const pages = import.meta.glob('./Pages/**/*.tsx') as Record<
    string,
    () => Promise<{ default: ResolvedComponent }>
>;

createInertiaApp({
    title: (title) => `${title} — KSP KOPINKA`,
    resolve: async (name): Promise<ResolvedComponent> => {
        const page = await pages[`./Pages/${name}.tsx`]?.();

        return page?.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#e11d48',
        // Spinner menghilang; cukup progress bar tipis untuk muatan lambat saja.
        showSpinner: false,
        delay: 250,
    },
});

// Prefetch seluruh chunk halaman saat browser idle — navigasi jadi instan.
const startPrefetch = () => {
    Object.values(pages).forEach((load) => {
        void load().catch(() => {});
    });
};

if ('requestIdleCallback' in window) {
    window.requestIdleCallback(startPrefetch, { timeout: 4000 });
} else {
    setTimeout(startPrefetch, 1500);
}
