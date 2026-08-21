import { useState } from 'react';
import { useForm, Head, Link } from '@inertiajs/react';
import { Eye, EyeOff, Landmark, Lock, Mail, LoaderCircle } from 'lucide-react';

export default function Login({ status }) {
    const [showPassword, setShowPassword] = useState(false);

    const form = useForm({
        email: '',
        password: '',
        remember: true,
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route('login.attempt'), {
            onFinish: () => form.reset('password'),
        });
    };

    return (
        <div className="flex min-h-screen bg-night-900">
            {/* Panel kiri — branding */}
            <div className="relative hidden w-1/2 overflow-hidden lg:block">
                <div className="absolute inset-0 bg-gradient-to-br from-brand-600 via-brand-800 to-night-900" />
                <div
                    className="absolute inset-0 opacity-20"
                    style={{
                        backgroundImage:
                            'radial-gradient(circle at 25% 25%, rgba(255,255,255,.35) 0, transparent 45%), radial-gradient(circle at 75% 75%, rgba(255,255,255,.2) 0, transparent 40%)',
                    }}
                />
                <div className="relative z-10 flex h-full flex-col justify-between p-12 text-white">
                    <div className="flex items-center gap-3">
                        <span className="grid size-11 place-items-center rounded-xl bg-white/15 backdrop-blur">
                            <Landmark className="size-6" />
                        </span>
                        <span className="text-lg font-bold tracking-wide">KSP KOPINKA</span>
                    </div>

                    <div>
                        <h1 className="max-w-md text-4xl font-extrabold leading-tight">
                            Sistem Informasi Kredit & Simpan Pinjam.
                        </h1>
                        <p className="mt-4 max-w-md text-white/70">
                            Kelola anggota, pinjaman, simpanan, dan kas harian dalam satu platform yang cepat dan modern.
                        </p>
                    </div>

                    <p className="text-sm text-white/50">© {new Date().getFullYear()} KSP KOPINKA. Seluruh hak cipta.</p>
                </div>
            </div>

            {/* Panel kanan — form */}
            <div className="flex w-full items-center justify-center p-6 lg:w-1/2">
                <div className="w-full max-w-md">
                    <Head title="Masuk" />

                    <div className="mb-8 flex items-center gap-3 lg:hidden">
                        <span className="grid size-10 place-items-center rounded-xl bg-brand-600 text-white">
                            <Landmark className="size-5" />
                        </span>
                        <span className="text-lg font-bold text-white">KSP KOPINKA</span>
                    </div>

                    <h2 className="text-3xl font-bold text-white">Selamat datang 👋</h2>
                    <p className="mt-2 text-slate-400">Masuk untuk mengakses dashboard Anda.</p>

                    {status && (
                        <div className="mt-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit} className="mt-8 space-y-5">
                        <div>
                            <label htmlFor="email" className="mb-1.5 block text-sm font-medium text-slate-300">
                                Email
                            </label>
                            <div className="relative">
                                <Mail className="pointer-events-none absolute left-3.5 top-1/2 size-4.5 -translate-y-1/2 text-slate-500" />
                                <input
                                    id="email"
                                    type="email"
                                    autoFocus
                                    autoComplete="email"
                                    placeholder="nama@kopinka.co.id"
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    className="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-white placeholder:text-slate-600 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none"
                                />
                            </div>
                            {form.errors.email && <p className="mt-1.5 text-sm text-brand-400">{form.errors.email}</p>}
                        </div>

                        <div>
                            <label htmlFor="password" className="mb-1.5 block text-sm font-medium text-slate-300">
                                Password
                            </label>
                            <div className="relative">
                                <Lock className="pointer-events-none absolute left-3.5 top-1/2 size-4.5 -translate-y-1/2 text-slate-500" />
                                <input
                                    id="password"
                                    type={showPassword ? 'text' : 'password'}
                                    autoComplete="current-password"
                                    placeholder="••••••••"
                                    value={form.data.password}
                                    onChange={(e) => form.setData('password', e.target.value)}
                                    className="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-12 text-white placeholder:text-slate-600 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword((v) => !v)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-slate-500 transition hover:text-slate-300"
                                    aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                >
                                    {showPassword ? <EyeOff className="size-4.5" /> : <Eye className="size-4.5" />}
                                </button>
                            </div>
                            {form.errors.password && (
                                <p className="mt-1.5 text-sm text-brand-400">{form.errors.password}</p>
                            )}
                        </div>

                        <div className="flex items-center justify-between">
                            <label className="flex cursor-pointer items-center gap-2 text-sm text-slate-400">
                                <input
                                    type="checkbox"
                                    checked={form.data.remember}
                                    onChange={(e) => form.setData('remember', e.target.checked)}
                                    className="size-4 rounded border-white/20 bg-white/5 accent-brand-600"
                                />
                                Ingat saya
                            </label>
                        </div>

                        <button
                            type="submit"
                            disabled={form.processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 py-3 font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-500 active:scale-[.98] disabled:opacity-60"
                        >
                            {form.processing && <LoaderCircle className="size-4 animate-spin" />}
                            Masuk ke Dashboard
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}
