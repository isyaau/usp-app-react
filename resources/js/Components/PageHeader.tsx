import type { LucideIcon } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface Props {
    title: string;
    description?: string;
    icon?: LucideIcon;
    backHref?: string;
    children?: React.ReactNode;
}

export function PageHeader({ title, description, icon: Icon, backHref, children }: Props) {
    return (
        <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div className="flex items-start gap-3">
                {backHref && (
                    <Link
                        href={backHref}
                        preserveScroll
                        className="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg border bg-card text-muted-foreground transition hover:bg-muted hover:text-foreground"
                        aria-label="Kembali"
                    >
                        <ArrowLeft className="size-4" />
                    </Link>
                )}
                <div>
                    <h1 className="flex items-center gap-2.5 text-2xl font-bold tracking-tight">
                        {Icon && (
                            <span className="grid size-9 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/25">
                                <Icon className="size-4.5" />
                            </span>
                        )}
                        {title}
                    </h1>
                    {description && <p className="mt-1 text-sm text-muted-foreground">{description}</p>}
                </div>
            </div>
            {children && <div className="flex items-center gap-2">{children}</div>}
        </div>
    );
}
