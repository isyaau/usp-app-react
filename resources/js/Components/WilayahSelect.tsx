import { useEffect, useState } from 'react';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import type { Wilayah } from '@/types/models';

interface LevelProps {
    label: string;
    value: string;
    options: Wilayah[];
    disabled?: boolean;
    placeholder?: string;
    onChange: (code: string) => void;
    error?: string;
}

function Level({
    label,
    value,
    options,
    disabled,
    placeholder = '-- Pilih --',
    onChange,
    error,
}: LevelProps) {
    return (
        <div className="space-y-2">
            <Label>
                {label} <span className="text-brand-600">*</span>
            </Label>
            <Select
                value={value || undefined}
                onValueChange={onChange}
                disabled={disabled}
            >
                <SelectTrigger className="w-full" aria-label={`Pilih ${label}`}>
                    <SelectValue placeholder={disabled ? 'Pilih atasnya dulu' : placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {options.map((opt) => (
                        <SelectItem key={opt.code} value={opt.code}>
                            {opt.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            {error && <p className="text-sm text-brand-600">{error}</p>}
        </div>
    );
}

interface Props {
    values: {
        provinsi_id: string;
        kota_id: string;
        kecamatan_id: string;
        kelurahan_id: string;
    };
    onChange: (field: 'provinsi_id' | 'kota_id' | 'kecamatan_id' | 'kelurahan_id', code: string) => void;
    errors?: Partial<Record<'provinsi_id' | 'kota_id' | 'kecamatan_id' | 'kelurahan_id', string>>;
}

/**
 * Dropdown berantai Provinsi → Kota → Kecamatan → Kelurahan.
 * Data diambil dari endpoint wilayah (laravolt/indonesia) dengan cache per sesi.
 */
export function WilayahSelect({ values, onChange, errors = {} }: Props) {
    const [provinces, setProvinces] = useState<Wilayah[]>([]);
    const [cities, setCities] = useState<Wilayah[]>([]);
    const [districts, setDistricts] = useState<Wilayah[]>([]);
    const [villages, setVillages] = useState<Wilayah[]>([]);

    // Muat provinsi sekali
    useEffect(() => {
        fetch(route('wilayah.provinces'))
            .then((r) => r.json())
            .then(setProvinces)
            .catch(() => {});
    }, []);

    // Muat kota saat provinsi berubah (atau saat mode edit dengan nilai awal)
    useEffect(() => {
        if (!values.provinsi_id) {
            setCities([]);
            return;
        }
        fetch(route('wilayah.cities', { province: values.provinsi_id }))
            .then((r) => r.json())
            .then(setCities)
            .catch(() => {});
    }, [values.provinsi_id]);

    useEffect(() => {
        if (!values.kota_id) {
            setDistricts([]);
            return;
        }
        fetch(route('wilayah.districts', { city: values.kota_id }))
            .then((r) => r.json())
            .then(setDistricts)
            .catch(() => {});
    }, [values.kota_id]);

    useEffect(() => {
        if (!values.kecamatan_id) {
            setVillages([]);
            return;
        }
        fetch(route('wilayah.villages', { district: values.kecamatan_id }))
            .then((r) => r.json())
            .then(setVillages)
            .catch(() => {});
    }, [values.kecamatan_id]);

    const change = (
        field: 'provinsi_id' | 'kota_id' | 'kecamatan_id' | 'kelurahan_id',
        code: string,
    ) => {
        if (field === 'provinsi_id') {
            onChange('provinsi_id', code);
            onChange('kota_id', '');
            onChange('kecamatan_id', '');
            onChange('kelurahan_id', '');
        } else if (field === 'kota_id') {
            onChange('kota_id', code);
            onChange('kecamatan_id', '');
            onChange('kelurahan_id', '');
        } else if (field === 'kecamatan_id') {
            onChange('kecamatan_id', code);
            onChange('kelurahan_id', '');
        } else {
            onChange('kelurahan_id', code);
        }
    };

    return (
        <div className="grid gap-5 sm:grid-cols-2">
            <Level
                label="Provinsi"
                value={values.provinsi_id}
                options={provinces}
                placeholder="-- Pilih Provinsi --"
                onChange={(code) => change('provinsi_id', code)}
                error={errors.provinsi_id}
            />
            <Level
                label="Kota/Kabupaten"
                value={values.kota_id}
                options={cities}
                disabled={!values.provinsi_id}
                placeholder="-- Pilih Kota/Kabupaten --"
                onChange={(code) => change('kota_id', code)}
                error={errors.kota_id}
            />
            <Level
                label="Kecamatan"
                value={values.kecamatan_id}
                options={districts}
                disabled={!values.kota_id}
                placeholder="-- Pilih Kecamatan --"
                onChange={(code) => change('kecamatan_id', code)}
                error={errors.kecamatan_id}
            />
            <Level
                label="Kelurahan/Desa"
                value={values.kelurahan_id}
                options={villages}
                disabled={!values.kecamatan_id}
                placeholder="-- Pilih Kelurahan --"
                onChange={(code) => change('kelurahan_id', code)}
                error={errors.kelurahan_id}
            />
        </div>
    );
}
