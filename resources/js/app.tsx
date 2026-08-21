import { createInertiaApp, ResolvedComponent } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import '../css/react.css';

createInertiaApp({
    title: (title) => `${title} — KSP KOPINKA`,
    resolve: (name): ResolvedComponent => {
        const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true }) as Record<
            string,
            { default: ResolvedComponent }
        >;
        return pages[`./Pages/${name}.tsx`]?.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#e11d48',
        showSpinner: true,
    },
});
