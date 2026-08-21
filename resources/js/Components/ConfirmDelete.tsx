import { useState } from 'react';
import { router } from '@inertiajs/react';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/Components/ui/alert-dialog';
import { buttonVariants } from '@/Components/ui/button';
import { cn } from '@/lib/utils';

interface Props {
    /** Nama route delete, mis. 'superadmin.user.destroy' */
    routeName: string;
    id: number;
    label: string;
    description?: string;
}

export function ConfirmDelete({ routeName, id, label, description }: Props) {
    const [deleting, setDeleting] = useState(false);

    const confirm = () => {
        setDeleting(true);
        router.delete(route(routeName, id), {
            onFinish: () => setDeleting(false),
        });
    };

    return (
        <AlertDialog>
            <AlertDialogTrigger
                className={cn(
                    buttonVariants({ variant: 'ghost', size: 'icon' }),
                    'size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive',
                )}
                aria-label={`Hapus ${label}`}
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="size-4">
                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                </svg>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Hapus {label}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        {description ??
                            `Data "${label}" akan dihapus permanen dan tidak dapat dikembalikan.`}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction
                        onClick={confirm}
                        disabled={deleting}
                        className="bg-destructive text-white hover:bg-destructive/90"
                    >
                        {deleting ? 'Menghapus…' : 'Ya, Hapus'}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
