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
    /** URL gambar TTD lama untuk mode edit. */
    existingUrl?: string | null;
}

/**
 * Panel dua-mode untuk tanda tangan digital:
 * - draw  : gambar pada canvas (signature_pad), output data URL PNG
 * - upload: unggah file gambar
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

    // Inisialisasi canvas sekali.
    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d')?.scale(ratio, ratio);

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

        const handleResize = () => {
            const data = pad.toData();
            const r = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * r;
            canvas.height = canvas.offsetHeight * r;
            canvas.getContext('2d')?.scale(r, r);
            pad.fromData(data); // pertahankan gambar saat resize
        };
        window.addEventListener('resize', handleResize);

        return () => {
            window.removeEventListener('resize', handleResize);
            pad.removeEventListener('endStroke', handleEndStroke);
            pad.off();
        };
    }, []);

    const changeMode = (m: 'draw' | 'upload') => {
        setMode(m);
        onModeChange?.(m);
    };

    const clear = () => {
        padRef.current?.clear();
        onChange(null);
    };

    const resizeCanvas = () => {
        const canvas = canvasRef.current;
        const pad = padRef.current;
        if (!canvas || !pad) return;

        const data = pad.toData();
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d')?.scale(ratio, ratio);
        pad.fromData(data);
    };

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
                    <canvas
                        ref={canvasRef}
                        onMouseDown={resizeCanvas}
                        style={{ touchAction: 'none' }}
                        className="h-48 w-full max-w-md rounded-lg border bg-white"
                        aria-label="Kanvas tanda tangan"
                    />
                    <div>
                        <Button type="button" variant="outline" size="sm" onClick={clear}>
                            <Eraser />
                            Bersihkan
                            <span className="sr-only">Bersihkan kanvas</span>
                        </Button>
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
                            // Laporkan file terpilih ke form (dikirim via FormData).
                            onFileChange?.(f);
                        }}
                    />
                </div>
            )}

            {/* Pratinjau TTD lama (mode edit). */}
            {existingUrl && !file && (
                <div className="space-y-1">
                    <p className="text-xs text-muted-foreground">Tanda tangan tersimpan:</p>
                    <img
                        src={`/storage/${existingUrl}`}
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
