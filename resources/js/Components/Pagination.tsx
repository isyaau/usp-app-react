import { Link } from '@inertiajs/react';
import type { Paginated } from '@/types/models';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface Props<T> {
    links: Paginated<T>['links'];
    currentPage: number;
    lastPage: number;
    from: number | null;
    to: number | null;
    total: number;
}

export function Pagination<T>({ links, currentPage, lastPage, from, to, total }: Props<T>) {
    if (lastPage <= 1) return null;

    return (
        <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
            <p className="text-sm text-muted-foreground">
                Menampilkan <span className="font-medium text-foreground">{from ?? 0}</span>–
                <span className="font-medium text-foreground">{to ?? 0}</span> dari{' '}
                <span className="font-medium text-foreground">{total.toLocaleString('id-ID')}</span> data
            </p>

            <nav className="flex items-center gap-1" aria-label="Paginasi">
                {links.map((link, i) => {
                    if (link.label === '&laquo; Previous') {
                        return (
                            <PaginationArrow
                                key={i}
                                href={link.url}
                                direction="prev"
                                disabled={!link.url}
                            />
                        );
                    }
                    if (link.label === 'Next &raquo;') {
                        return (
                            <PaginationArrow
                                key={i}
                                href={link.url}
                                direction="next"
                                disabled={!link.url}
                            />
                        );
                    }
                    const page = Number(link.label);
                    if (Number.isNaN(page)) return null;

                    return link.url ? (
                        <Link
                            key={i}
                            href={link.url}
                            preserveState
                            preserveScroll
                            className={`grid size-9 place-items-center rounded-lg text-sm font-medium transition ${
                                link.active
                                    ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30'
                                    : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                            }`}
                        >
                            {page}
                        </Link>
                    ) : (
                        <span
                            key={i}
                            className="grid size-9 place-items-center rounded-lg bg-brand-600 text-sm font-medium text-white"
                        >
                            {page}
                        </span>
                    );
                })}
            </nav>
        </div>
    );
}

function PaginationArrow({
    href,
    direction,
    disabled,
}: {
    href: string | null;
    direction: 'prev' | 'next';
    disabled: boolean;
}) {
    const Icon = direction === 'prev' ? ChevronLeft : ChevronRight;

    if (disabled || !href) {
        return (
            <span className="grid size-9 place-items-center rounded-lg text-muted-foreground/40">
                <Icon className="size-4" />
            </span>
        );
    }

    return (
        <Link
            href={href}
            preserveState
            preserveScroll
            className="grid size-9 place-items-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-foreground"
            aria-label={direction === 'prev' ? 'Halaman sebelumnya' : 'Halaman berikutnya'}
        >
            <Icon className="size-4" />
        </Link>
    );
}
