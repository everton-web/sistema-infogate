import { createInertiaApp, router } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import AuthLayout from './Layouts/AuthLayout';

let progressTimeout;

router.on('start', () => {
    progressTimeout = setTimeout(() => {
        document.getElementById('page-content')?.classList.add('page-loading');
    }, 50);
});

router.on('finish', () => {
    clearTimeout(progressTimeout);
    document.getElementById('page-content')?.classList.remove('page-loading');
});

createInertiaApp({
    title: (title) => title ? `${title} | InfoGate Gestão` : 'InfoGate Gestão',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];
        if (!page.default.layout && !name.startsWith('Auth/')) {
            page.default.layout = (p) => <AuthLayout children={p} />;
        }
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: false,
});
