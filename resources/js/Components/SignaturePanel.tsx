import { useEffect, useRef, useState } from 'react';
import SignaturePad from 'signature_pad';
import { Eraser } from 'lucide-react';

import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

interface Props {
    /** Base64 data URL hasil gambar tangan (mode draw). */
    onChange: (dataUrl: string | null) => void;
    /** Mode aktif (draw/upload) dilaporkan ke form. */
    onModeChange?: (mode: 'draw' | 'upload') => void;
    /** File terpilih pada mode upload. */
    onFileChange?: (file: File | null) => void;
    /** URL gambar TTD lama untuk mode edit (path relatif storage atau data URL). */
    existingUrl?: string | null;
}

/** Ukuran kanvas (piksel logis) agar hasil goresan konsisten & tajam. */
const DRAW_WIDTH = 600;
const DRAW_HEIGHT = 200;

/** Ubah nilai existingUrl menjadi URL yang bisa ditampilkan/dimuat kanvas. */
function resolveUrl(url: string | null | undefined): string {
    if (!url) return '';
    if (url.startsWith('data:') || url.startsWith('/') || url.startsWith('http')) {
        return url;
    }
    return `/storage/${url}`;
}

/**
 * Panel dua-mode untuk tanda tangan digital:
 * - draw  : gambar pada canvas (signature_pad), output data URL PNG
 * - upload: unggah file gambar
 * Saat mode edit dengan TTD tersimpan, gambar tersebut dimuat ke kanvas
 * sehingga pratinjau konsisten dengan detail & hasil yang disimpan.
 */
export function SignaturePanel({
    onChange,
    onModeChange,
    onFileChange,
    existingUrl,
}: Props) {
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const padRef = useRef<SignaturePad | null>(null);
    const [mode, setMode] = useState<'draw' | 'upload'>('draw');
    const [file, setFile] = useState<File | null>(null);
    const [loadedExisting, setLoadedExisting] = useState(false);

    // Inisialisasi canvas sekali.
    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        canvas.width = DRAW_WIDTH;
        canvas.height = DRAW_HEIGHT;

        const pad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
        padRef.current = pad;

        // Kirim hasil goresan ke form setiap stroke selesai.
        const handleEndStroke = () => {
            if (pad.isEmpty()) {
                onChange(null);
                return;
            }
            onChange(pad.toDataURL('image/png'));
        };
        pad.addEventListener('endStroke', handleEndStroke);

        // Muat TTD tersimpan ke kanvas agar pratinjau konsisten.
        const existing = resolveUrl(existingUrl);
        if (existing) {
            pad.fromDataURL(existing)
                .then(() => setLoadedExisting(true))
                .catch(() => setLoadedExisting(false));
        }

        return () => {
            pad.removeEventListener('endStroke', handleEndStroke);
            pad.off();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const changeMode = (m: 'draw' | 'upload') => {
        setMode(m);
        onModeChange?.(m);
    };

    const clear = () => {
        padRef.current?.clear();
        setLoadedExisting(false);
        onChange(null);
    };

    const hasStored = Boolean(existingUrl) && loadedExisting;

    return (
        <div className="space-y-3">
            <div className="flex items-center gap-4">
                <label className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                        type="radio"
                        checked={mode === 'draw'}
                        onChange={() => changeMode('draw')}
                        aria-label="Mode gambar"
                    />
                    Gambar
                </label>
                <label className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                        type="radio"
                        checked={mode === 'upload'}
                        onChange={() => changeMode('upload')}
                        aria-label="Mode unggah"
                    />
                    Unggah
                </label>
            </div>

            {mode === 'draw' ? (
                <div className="space-y-2">
                    <div className="max-w-md overflow-hidden rounded-lg border bg-white">
                        <canvas
                            ref={canvasRef}
                            style={{
                                touchAction: 'none',
                                display: 'block',
                                width: '100%',
                                aspectRatio: `${DRAW_WIDTH} / ${DRAW_HEIGHT}`,
                            }}
                            aria-label="Kanvas tanda tangan"
                        />
                    </div>
                    <div className="flex items-center gap-3">
                        <Button type="button" variant="outline" size="sm" onClick={clear}>
                            <Eraser />
                            Bersihkan
                            <span className="sr-only">Bersihkan kanvas</span>
                        </Button>
                        {hasStored && (
                            <span className="text-xs text-muted-foreground">
                                TTD tersimpan. Cukup simpan untuk mempertahankan, atau gambar ulang untuk mengganti.
                            </span>
                        )}
                    </div>
                </div>
            ) : (
                <div className="max-w-md space-y-2">
                    <Label htmlFor="uploaded_signature">File Tanda Tangan</Label>
                    <Input
                        id="uploaded_signature"
                        name="uploaded_signature"
                        type="file"
                        accept="image/*"
                        onChange={(e) => {
                            const f = e.target.files?.[0] ?? null;
                            setFile(f);
                            onFileChange?.(f);
                        }}
                    />
                </div>
            )}

            {/* Saat gagal dimuat ke kanvas, tampilkan thumbnail TTD lama. */}
            {mode === 'draw' && existingUrl && !loadedExisting && (
                <div className="space-y-1">
                    <p className="text-xs text-muted-foreground">Tanda tangan tersimpan:</p>
                    <img
                        src={resolveUrl(existingUrl)}
                        alt="Tanda tangan tersimpan"
                        className="h-16 rounded border bg-white p-1"
                    />
                </div>
            )}

            {file && (
                <img
                    src={URL.createObjectURL(file)}
                    alt="Pratinjau tanda tangan baru"
                    className="h-16 rounded border bg-white p-1"
                />
            )}
        </div>
    );
}
