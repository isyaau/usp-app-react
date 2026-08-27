import { useState } from 'react';
import { Settings2 } from 'lucide-react';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Switch } from '@/Components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';

interface Simpanan {
    id: number;
    no_rekening: string;
    nama_anggota?: string;
}

interface PassbookConfig {
    paper_width: number;
    paper_height: number;
    margin_top: number;
    margin_left: number;
    margin_right: number;
    font_size: number;
    line_height: number;
    show_header: boolean;
    col_tanggal: number;
    col_keterangan: number;
    col_setoran: number;
    col_penarikan: number;
}

const PRESETS: Record<string, Partial<PassbookConfig>> = {
    'standard-140': {
        paper_width: 140,
        paper_height: 200,
        margin_top: 15,
        margin_left: 5,
        margin_right: 5,
        font_size: 8,
        line_height: 4,
        col_tanggal: 20,
        col_keterangan: 55,
        col_setoran: 25,
        col_penarikan: 25,
    },
    'standard-105': {
        paper_width: 105,
        paper_height: 148,
        margin_top: 10,
        margin_left: 3,
        margin_right: 3,
        font_size: 7,
        line_height: 3.5,
        col_tanggal: 16,
        col_keterangan: 40,
        col_setoran: 20,
        col_penarikan: 20,
    },
    'a6': {
        paper_width: 105,
        paper_height: 148,
        margin_top: 8,
        margin_left: 3,
        margin_right: 3,
        font_size: 7,
        line_height: 3.5,
        col_tanggal: 15,
        col_keterangan: 38,
        col_setoran: 20,
        col_penarikan: 20,
    },
    'custom': {},
};

interface PassbookConfigModalProps {
    simpananList: Simpanan[];
    printRoute: string;
    filters: Record<string, string>;
}

export function PassbookConfigModal({ simpananList, printRoute, filters }: PassbookConfigModalProps) {
    const [open, setOpen] = useState(false);
    const [preset, setPreset] = useState('standard-140');
    const [simpananId, setSimpananId] = useState<string>('');
    const [config, setConfig] = useState<PassbookConfig>({
        paper_width: 140,
        paper_height: 200,
        margin_top: 15,
        margin_left: 5,
        margin_right: 5,
        font_size: 8,
        line_height: 4,
        show_header: true,
        col_tanggal: 20,
        col_keterangan: 55,
        col_setoran: 25,
        col_penarikan: 25,
    });

    const applyPreset = (key: string) => {
        setPreset(key);
        if (PRESETS[key] && Object.keys(PRESETS[key]).length > 0) {
            setConfig((prev) => ({ ...prev, ...PRESETS[key] }));
        }
    };

    const updateConfig = (key: keyof PassbookConfig, value: number | boolean) => {
        setConfig((prev) => ({ ...prev, [key]: value }));
        setPreset('custom');
    };

    const handlePrint = () => {
        if (!simpananId) return;

        const params: Record<string, string> = {
            simpanan_id: simpananId,
            ...filters,
            paper_width: String(config.paper_width),
            paper_height: String(config.paper_height),
            margin_top: String(config.margin_top),
            margin_left: String(config.margin_left),
            margin_right: String(config.margin_right),
            font_size: String(config.font_size),
            line_height: String(config.line_height),
            show_header: config.show_header ? '1' : '0',
            col_tanggal: String(config.col_tanggal),
            col_keterangan: String(config.col_keterangan),
            col_setoran: String(config.col_setoran),
            col_penarikan: String(config.col_penarikan),
        };

        Object.keys(params).forEach((key) => {
            if (params[key] === '' || params[key] === undefined) {
                delete params[key];
            }
        });

        window.open(route(printRoute, params), '_blank');
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline">
                    <Settings2 className="size-4" />
                    Cetak Buku Tabungan
                </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Cetak Buku Tabungan (Passbook)</DialogTitle>
                    <DialogDescription>
                        Konfigurasi ukuran dan format cetak buku tabungan untuk mesin passbook.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    {/* Simpanan Selection */}
                    <div className="space-y-2">
                        <Label>No. Rekening</Label>
                        <Select value={simpananId} onValueChange={setSimpananId}>
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih rekening simpanan..." />
                            </SelectTrigger>
                            <SelectContent>
                                {simpananList.map((s) => (
                                    <SelectItem key={s.id} value={String(s.id)}>
                                        {s.no_rekening}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Preset */}
                    <div className="space-y-2">
                        <Label>Preset Ukuran</Label>
                        <Select value={preset} onValueChange={applyPreset}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="standard-140">Standard 140mm × 200mm</SelectItem>
                                <SelectItem value="standard-105">Standard 105mm × 148mm (A6)</SelectItem>
                                <SelectItem value="a6">A6 105mm × 148mm (Kecil)</SelectItem>
                                <SelectItem value="custom">Custom</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Paper Size */}
                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label>Lebar Kertas (mm)</Label>
                            <Input
                                type="number"
                                value={config.paper_width}
                                onChange={(e) => updateConfig('paper_width', Number(e.target.value))}
                                min={50}
                                max={300}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>Tinggi Kertas (mm)</Label>
                            <Input
                                type="number"
                                value={config.paper_height}
                                onChange={(e) => updateConfig('paper_height', Number(e.target.value))}
                                min={50}
                                max={500}
                            />
                        </div>
                    </div>

                    {/* Margins */}
                    <div className="grid grid-cols-3 gap-4">
                        <div className="space-y-2">
                            <Label>Margin Atas (mm)</Label>
                            <Input
                                type="number"
                                value={config.margin_top}
                                onChange={(e) => updateConfig('margin_top', Number(e.target.value))}
                                min={0}
                                max={100}
                                step={0.5}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>Margin Kiri (mm)</Label>
                            <Input
                                type="number"
                                value={config.margin_left}
                                onChange={(e) => updateConfig('margin_left', Number(e.target.value))}
                                min={0}
                                max={50}
                                step={0.5}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>Margin Kanan (mm)</Label>
                            <Input
                                type="number"
                                value={config.margin_right}
                                onChange={(e) => updateConfig('margin_right', Number(e.target.value))}
                                min={0}
                                max={50}
                                step={0.5}
                            />
                        </div>
                    </div>

                    {/* Font & Line */}
                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label>Ukuran Font (px)</Label>
                            <Input
                                type="number"
                                value={config.font_size}
                                onChange={(e) => updateConfig('font_size', Number(e.target.value))}
                                min={5}
                                max={20}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>Tinggi Baris (mm)</Label>
                            <Input
                                type="number"
                                value={config.line_height}
                                onChange={(e) => updateConfig('line_height', Number(e.target.value))}
                                min={2}
                                max={10}
                                step={0.5}
                            />
                        </div>
                    </div>

                    {/* Show Header */}
                    <div className="flex items-center gap-3">
                        <Switch
                            checked={config.show_header}
                            onCheckedChange={(v) => updateConfig('show_header', v)}
                        />
                        <Label>Tampilkan Header (Info Rekening)</Label>
                    </div>

                    {/* Column Widths */}
                    <div className="space-y-2">
                        <Label className="text-base font-semibold">Lebar Kolom (mm)</Label>
                        <p className="text-xs text-muted-foreground">
                            Total lebar kolom harus sesuai dengan lebar kertas dikurangi margin kiri + kanan.
                            Saldo otomatis mengisi sisa ruang.
                        </p>
                        <div className="grid grid-cols-4 gap-4">
                            <div className="space-y-2">
                                <Label>Tanggal</Label>
                                <Input
                                    type="number"
                                    value={config.col_tanggal}
                                    onChange={(e) => updateConfig('col_tanggal', Number(e.target.value))}
                                    min={10}
                                    max={50}
                                    step={1}
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Keterangan</Label>
                                <Input
                                    type="number"
                                    value={config.col_keterangan}
                                    onChange={(e) => updateConfig('col_keterangan', Number(e.target.value))}
                                    min={20}
                                    max={100}
                                    step={1}
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Setoran</Label>
                                <Input
                                    type="number"
                                    value={config.col_setoran}
                                    onChange={(e) => updateConfig('col_setoran', Number(e.target.value))}
                                    min={10}
                                    max={50}
                                    step={1}
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Penarikan</Label>
                                <Input
                                    type="number"
                                    value={config.col_penarikan}
                                    onChange={(e) => updateConfig('col_penarikan', Number(e.target.value))}
                                    min={10}
                                    max={50}
                                    step={1}
                                />
                            </div>
                        </div>
                        <div className="text-xs text-muted-foreground mt-1">
                            Sisa lebar untuk kolom Saldo:{' '}
                            <span className="font-mono font-bold">
                                {config.paper_width - config.margin_left - config.margin_right - config.col_tanggal - config.col_keterangan - config.col_setoran - config.col_penarikan}
                            </span>
                            mm
                        </div>
                    </div>
                </div>

                <DialogFooter className="mt-4">
                    <Button variant="outline" onClick={() => setOpen(false)}>
                        Batal
                    </Button>
                    <Button onClick={handlePrint} disabled={!simpananId}>
                        Cetak Sekarang
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
