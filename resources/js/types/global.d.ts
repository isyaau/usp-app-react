import { Config } from 'ziggy-js';

export interface User {
    id: number;
    nama: string;
    email: string;
    role: string;
    avatar: string | null;
}

export interface SharedProps {
    auth: {
        user: User | null;
    };
    flash: {
        status?: string;
        error?: string;
    };
    [key: string]: unknown;
}

declare module '@inertiajs/core' {
    interface PageProps extends SharedProps {}
}

declare global {
    interface Window {
        route: typeof route;
    }

    // Fungsi route() dari direktif @routes (ziggy) tersedia global
    function route(): string & Config;
    function route(name: string, params?: Record<string, unknown> | number, absolute?: boolean): string & Config;
}

export {};
