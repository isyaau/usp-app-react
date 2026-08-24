import { createInertiaApp, ResolvedComponent } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import '../css/react.css';

createInertiaApp({
    title: (title) => `${title} — KSP KOPINKA`,
    resolve: async (name): Promise<ResolvedComponent> => {
        const pages = import.meta.glob('./Pages/**/*.tsx') as Record<
            string,
            () => Promise<{ default: ResolvedComponent }>
        >;
        const page = await pages[`./Pages/${name}.tsx`]?.();

        return page?.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#e11d48',
        showSpinner: true,
    },
});
