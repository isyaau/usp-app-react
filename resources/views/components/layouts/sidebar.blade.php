<aside class="app-sidebar bg-light" data-bs-theme="light">

    <div class="sidebar-brand">
        <a href="#" class="brand-link">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 100px;" class="opacity-75 shadow" />
            <span class="brand-text fw-light">KSP KOPINKA</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Main navigation"
                data-accordion="false">

                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">SUPER ADMIN</li>


                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.user') }}"
                        class="nav-link {{ request()->routeIs('superadmin.user') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-patch-check-fill"></i>
                        <p>User</p>
                    </a>
                </li>


                <li class="nav-item
                    {{ request()->routeIs('superadmin.anggota*') || request()->routeIs('superadmin.kelompok*') ? 'menu-open' : '' }}">

                    <a href="#" class="nav-link
                        {{ request()->routeIs('superadmin.anggota*') || request()->routeIs('superadmin.kelompok*') ? 'active' : '' }}">

                        <i class="nav-icon fa-solid fa-user"></i>
                        <p>
                            Anggota
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate href="{{ route('superadmin.kelompok') }}"
                                class="nav-link {{ request()->routeIs('superadmin.kelompok*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kelompok</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate href="{{ route('superadmin.anggota') }}"
                                class="nav-link {{ request()->routeIs('superadmin.anggota*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Anggota</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item
                    {{ request()->routeIs('superadmin.account-header*') || request()->routeIs('superadmin.account*') ? 'menu-open' : '' }}">

                    <a href="#" class="nav-link
                        {{ request()->routeIs('superadmin.account-header*') || request()->routeIs('superadmin.account*') ? 'active' : '' }}">

                        <i class="nav-icon fa-solid fa-receipt"></i>
                        <p>
                            Account
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate href="{{ route('superadmin.account-header') }}"
                                class="nav-link {{ request()->routeIs('superadmin.account-header*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Header</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate href="{{ route('superadmin.account') }}"
                                class="nav-link {{ request()->routeIs('superadmin.account.*') || request()->routeIs('superadmin.account') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Account</p>
                            </a>
                        </li>


                    </ul>
                </li>

                @php
                $pinjamanOpen = request()->routeIs('superadmin.pinjaman.*');
                @endphp

                <li class="nav-item {{ $pinjamanOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $pinjamanOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-cash-register"></i>
                        <p>
                            Pinjaman
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.produk') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.produk') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Produk Pinjaman</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.jaminan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.jaminan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Jaminan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.pinjaman') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.pinjaman') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pinjaman</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.proposal') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.proposal') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Proposal</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.jadwal-ulang') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.jadwal-ulang') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Jadwal Ulang</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.tagihan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.tagihan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Tagihan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.penghapusan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.penghapusan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Penghapusan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.surat-peringatan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.surat-peringatan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Surat Peringatan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.pinjaman.pengembalian-jaminan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pinjaman.pengembalian-jaminan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pengembalian Jaminan</p>
                            </a>
                        </li>

                    </ul>
                </li>


                @php
                // Menu Simpanan
                $simpananMenuOpen = request()->routeIs('superadmin.simpanan.kode-transaksi') ||
                request()->routeIs('superadmin.simpanan.produk-simpanan') ||
                request()->routeIs('superadmin.simpanan');

                // Menu Simpanan Berjangka
                $berjangkaMenuOpen = request()->routeIs('superadmin.simpanan-berjangka.produk') ||
                request()->routeIs('superadmin.simpanan-berjangka');
                @endphp

                <!-- Menu Simpanan -->
                <li class="nav-item {{ $simpananMenuOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $simpananMenuOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-money-bill-wave"></i>
                        <p>
                            Simpanan
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.simpanan.kode-transaksi') }}"
                                class="nav-link {{ request()->routeIs('superadmin.simpanan.kode-transaksi') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kode Transaksi</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.simpanan.produk-simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.simpanan.produk-simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Produk Simpanan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Simpanan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Simpanan Berjangka -->
                <li class="nav-item {{ $berjangkaMenuOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $berjangkaMenuOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-money-bill-1-wave"></i>
                        <p>
                            Simpanan Berjangka
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.simpanan-berjangka.produk') }}"
                                class="nav-link {{ request()->routeIs('superadmin.simpanan-berjangka.produk') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Produk Simpanan Berjangka</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.simpanan-berjangka') }}"
                                class="nav-link {{ request()->routeIs('superadmin.simpanan-berjangka') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Simpanan Berjangka</p>
                            </a>
                        </li>
                    </ul>
                </li>



                <li class="nav-header">Front Office</li>


                @php
                $kasharianOpen = request()->routeIs('superadmin.kas-harian.*');
                @endphp

                <li class="nav-item {{ $kasharianOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $kasharianOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-brands fa-cash-app"></i>
                        <p>
                            Kas Harian
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.kas-harian.kas-awal') }}"
                                class="nav-link {{ request()->routeIs('superadmin.kas-harian.kas-awal') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kas Awal</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.kas-harian.kas-keluar') }}"
                                class="nav-link {{ request()->routeIs('superadmin.kas-harian.kas-keluar') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kas Keluar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.kas-harian.kas-masuk') }}"
                                class="nav-link {{ request()->routeIs('superadmin.kas-harian.kas-masuk') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kas Masuk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.kas-harian.kas-akhir') }}"
                                class="nav-link {{ request()->routeIs('superadmin.kas-harian.kas-akhir') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kas Akhir</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @php
                $transaksipinjamanOpen = request()->routeIs('superadmin.transaksi-pinjaman.*');
                @endphp

                <li class="nav-item {{ $transaksipinjamanOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $transaksipinjamanOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-arrow-right-arrow-left"></i>
                        <p>
                            Transaksi Pinjaman
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.pencairan-pinjaman') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.pencairan-pinjaman') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pencairan Pinjaman</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.penalti-pinjaman') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.penalti-pinjaman') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Penalti Pinjaman</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.penalti-pinjaman-kolektif-tunai') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.penalti-pinjaman-kolektif-tunai') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Penalti Pinjaman Kolektif Tunai</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.angsuran-pinjaman') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.angsuran-pinjaman') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Angsuran Pinjaman</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-debet') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-debet') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Angsuran Pinjaman Kolektif Debet Simpanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Angsuran Pinjaman Kolektif Tunai</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-pinjaman.setoran-kolektif-bank') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-pinjaman.setoran-kolektif-bank') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Setoran Simpanan & Angsuran Pinjaman Kolektif Bank</p>
                            </a>
                        </li>

                    </ul>
                </li>

                @php
                $transaksisimpananOpen = request()->routeIs('superadmin.transaksi-simpanan.*');
                @endphp

                <li class="nav-item {{ $transaksisimpananOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $transaksisimpananOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-arrows-down-to-line"></i>
                        <p>
                            Transaksi Simpanan
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.setoran-simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.setoran-simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Setoran Simpanan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.setoran-simpanan-kolektif') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.setoran-simpanan-kolektif') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Setoran Simpanan Kolektif</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.tarikan-simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.tarikan-simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Tarikan Simpanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.tarikan-simpanan-kolektif') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.tarikan-simpanan-kolektif') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Tarikan Simpanan Kolektif</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.pemindahbukuan-simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pemindahbukuan Simpanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan.penutupan-simpanan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan.penutupan-simpanan') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Penutupan Simpanan</p>
                            </a>
                        </li>
                    </ul>
                </li>


                @php
                $transaksisimpananberjangkaOpen = request()->routeIs('superadmin.transaksi-simpanan-berjangka.*');
                @endphp

                <li class="nav-item {{ $transaksisimpananberjangkaOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $transaksisimpananberjangkaOpen ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-arrows-down-to-people"></i>
                        <p>
                            Transaksi Simpanan Berjangka
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Setoran Simpanan Berjangka</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a wire:navigate
                                href="{{ route('superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka') }}"
                                class="nav-link {{ request()->routeIs('superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Penalti Simpanan Berjangka</p>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.penarikan-dana-titipan') }}"
                        class="nav-link {{ request()->routeIs('superadmin.penarikan-dana-titipan') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-arrows-to-circle"></i>
                        <p>Penarikan Dana Titipan Anggota</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('superadmin.kantor*')||request()->routeIs('superadmin.marketing*')  ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-seam-fill"></i>
                        <p>
                            Setting
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('superadmin.kantor') }}" class="nav-link {{ request()->routeIs('superadmin.kantor*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kantor</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('superadmin.marketing') }}" class="nav-link {{ request()->routeIs('superadmin.marketing*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Marketing</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('superadmin.template') }}" class="nav-link {{ request()->routeIs('superadmin.template*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Options</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

        </nav>
    </div>

</aside>