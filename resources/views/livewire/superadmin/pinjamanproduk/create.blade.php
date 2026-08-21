<div class="app-main">

    <div class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">{{ $title }}</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="/superadmin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 flex-fill">
                        <div class="card card-danger card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Formulir {{ $title }}</div>
                            </div>

                            <form wire:submit="store">
                                <div class="card-body">
                                    <h5 class="mb-3">Produk</h5>
                                    <hr class="border border-danger border-2 opacity-50 mb-4">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                                                <input wire:model="kode" type="text" id="kode" class="form-control @error('kode') is-invalid @enderror">
                                                @error('kode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                                <input wire:model="nama" type="text" id="nama" class="form-control @error('nama') is-invalid @enderror">
                                                @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="account_id" class="form-label">No Account <span class="text-danger">*</span></label>
                                                <select wire:model="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Account --</option>
                                                    @foreach ($account as $item)
                                                    <option value="{{ $item->id }}">{{ $item->no_account }} - {{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                                @error('account_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="insentif" class="form-label">Insentif Marketing <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input wire:model="insentif" id="insentif" type="text" class="form-control @error('insentif') is-invalid @enderror">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                @error('insentif')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label d-block">Simpanan Wajib <span class="text-danger">*</span></label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model.live="is_aktif" id="is_aktif" value="1">
                                                    <label class="form-check-label" for="is_aktif">Aktif</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model="swp_cair" id="swp_cair" value="1" @disabled(!$is_aktif)>
                                                    <label class="form-check-label" for="swp_cair">Pencairan</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model="swp_angsur" id="swp_angsur" value="1" @disabled(!$is_aktif)>
                                                    <label class="form-check-label" for="swp_angsur">Angsuran</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nominal Simpanan Wajib <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" wire:model="nominal_simpanan" @disabled(!$is_aktif)>
                                                    <div class="input-group-text">
                                                        <input class="form-check-input me-2 mb-1" type="checkbox" wire:model="swp_persen" value="1" @disabled(!$is_aktif)>
                                                        <label class="mb-0">%</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Simpanan Pokok <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <input class="form-check-input me-2 mb-1" type="checkbox" value="1" wire:model="simpanan_pokok">
                                                        <label class="mb-0">Aktif</label>
                                                    </div>
                                                    <input type="text" class="form-control" wire:model="nominal_simpanan_pokok">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="toleransi" class="form-label">Toleransi Tunggakan <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input wire:model="toleransi" id="toleransi" type="number" class="form-control @error('toleransi') is-invalid @enderror">
                                                        <span class="input-group-text">hari</span>
                                                    </div>
                                                    @error('toleransi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="angsuran" class="form-label">Jenis Pinjaman <span class="text-danger">*</span></label>
                                                    <select wire:model="angsuran" id="angsuran" class="form-select @error('angsuran') is-invalid @enderror">
                                                        <option value="">-- Pilih Jenis --</option>
                                                        @foreach ($listAngsuran as $item)
                                                        <option value="{{ $item }}">{{ $item }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('angsuran')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="mt-5 mb-3">Kolektabilitas</h5>
                                    <hr class="border border-danger border-2 opacity-50 mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead class="table-danger text-center">
                                                <tr>
                                                    <th style="width:200px">Kolektabilitas</th>
                                                    <th>Kriteria (Rumus)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($kolektabilitas as $i => $k)
                                                <tr>
                                                    <td>{{ $k['label'] }}</td>
                                                    <td>
                                                        <input type="text" readonly class="form-control cursor-pointer" wire:click="openRumusModal({{ $i }})" value="{{ $k['keterangan'] }}" placeholder="Klik untuk isi rumus...">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <h5 class="mb-3 mt-5">Bagi Hasil</h5>
                                    <hr class="border border-danger border-2 opacity-50 mb-4">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="bunga" class="form-label">Persentase <span class="text-danger">*</span></label>
                                            <input wire:model="bunga" type="text" id="bunga" class="form-control @error('bunga') is-invalid @enderror">
                                            @error('bunga')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="account_bunga" class="form-label">No Account Bunga <span class="text-danger">*</span></label>
                                            <select wire:model="account_bunga" id="account_bunga" class="form-select @error('account_bunga') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($account as $item)
                                                <option value="{{ $item->id }}">{{ $item->no_account }} - {{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('account_bunga')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Bagi Hasil Accrual</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input class="form-check-input me-2 mb-1" type="checkbox" value="1" wire:model="ditangguhkan">
                                                    <label class="mb-0">Aktif</label>
                                                </div>
                                                <select wire:model="account_ditangguhkan" id="account_ditangguhkan" class="form-select">
                                                    <option value="">-- Pilih Account --</option>
                                                    @foreach ($account as $item)
                                                    <option value="{{ $item->id }}">{{ $item->no_account }} - {{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="mb-3 mt-5">Komponen Pinjaman</h5>
                                    <hr class="border border-danger border-2 opacity-50 mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered align-middle">
                                            <thead class="table-danger text-center">
                                                <tr>
                                                    <th style="width: 50px;"></th>
                                                    <th style="width: 200px">Komponen</th>
                                                    <th>Nominal</th>
                                                    <th>%</th>
                                                    <th>No. Account</th>
                                                    <th>C</th>
                                                    <th>A</th>
                                                    <th>P</th>
                                                    <th>Rumus C</th>
                                                    <th>Rumus A</th>
                                                    <th>Rumus P</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($komponen as $i => $row)
                                                <tr>
                                                    <td class="text-center">
                                                        @if(count($komponen) > 1)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeKomponen({{ $i }})" tabindex="-1">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm @error(" komponen.$i.nama") is-invalid @enderror" wire:model.live.debounce.500ms="komponen.{{ $i }}.nama" placeholder="Ketik nama komponen...">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm text-end" wire:model="komponen.{{ $i }}.nominal" min="0" step="any">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input" wire:model="komponen.{{ $i }}.persen" value="1">
                                                    </td>
                                                    <td class="text-center">
                                                        <select wire:model="komponen.{{ $i }}.account_id" class="form-select form-control-sm @error(" komponen.$i.account_id") is-invalid @enderror">
                                                            <option value="">-- Account --</option>
                                                            @foreach ($account as $item)
                                                            <option value="{{ $item->id }}">{{ $item->no_account }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center"><input type="checkbox" class="form-check-input" wire:model="komponen.{{ $i }}.c" value="1"></td>
                                                    <td class="text-center"><input type="checkbox" class="form-check-input" wire:model="komponen.{{ $i }}.a" value="1"></td>
                                                    <td class="text-center"><input type="checkbox" class="form-check-input" wire:model="komponen.{{ $i }}.p" value="1"></td>
                                                    <td>
                                                        <input type="text" readonly class="form-control form-control-sm cursor-pointer" wire:click="openFormulaModal({{ $i }}, 'rumus_c')" value="{{ $row['rumus_c'] }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" readonly class="form-control form-control-sm cursor-pointer" wire:click="openFormulaModal({{ $i }}, 'rumus_a')" value="{{ $row['rumus_a'] }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" readonly class="form-control form-control-sm cursor-pointer" wire:click="openFormulaModal({{ $i }}, 'rumus_p')" value="{{ $row['rumus_p'] }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a wire:navigate href="{{ route('superadmin.pinjaman.produk') }}" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($showRumusModal)
        <div class="modal fade show d-block" style="background:rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fs-5"><i class="fas fa-keyboard me-2"></i>Kriteria</h5>
                        <button type="button" class="btn-close" wire:click="closeRumusModal"></button>
                    </div>
                    <div class="modal-body bg-light pt-2">
                        <textarea class="form-control mb-2" rows="5" wire:model="rumus" style="font-family: monospace; font-size: 16px; resize: none;"></textarea>

                        <div class="btn-group mb-2 flex-wrap" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('+')">+</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('-')">-</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('*')">x</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('/')">/</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('=')">=</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('<')">
                                << /button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('>')">></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('<>')">
                                        <>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('(')">(</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas(')')">)</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('\'')">'</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('%')">%</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('LIKE')">LIKE</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('AND')">AND</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('OR')">OR</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertRumusKolektabilitas('NOT')">NOT</button>
                        </div>

                        <div class="border bg-white" style="height: 180px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 1;">
                                    <tr>
                                        <th style="width: 25%; border-bottom: 1px solid #ccc;">Kode</th>
                                        <th style="border-bottom: 1px solid #ccc;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listKodeRumus as $k)
                                    <tr style="cursor: pointer;" wire:click="insertRumusKolektabilitas('{{ $k['kode'] }}')">
                                        <td class="border-end text-primary fw-bold">{{ $k['kode'] }}</td>
                                        <td>{{ $k['keterangan'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-success me-2" wire:click="saveRumusKolektabilitas">
                                <i class="fas fa-check"></i> OK
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm">
                                <i class="fas fa-cog text-primary"></i> Tes
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light border shadow-sm me-2" wire:click="clearRumusKolektabilitas">
                                <i class="fas fa-eraser text-warning"></i> Bersih
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm" wire:click="closeRumusModal">
                                <i class="fas fa-times text-danger"></i> Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($isFormulaModalOpen)
        <div class="modal fade show d-block" style="background:rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fs-5"><i class="fas fa-folder-open me-2 text-warning"></i>Rumus Komponen Pinjaman</h5>
                        <button type="button" class="btn-close" wire:click="closeFormulaModal"></button>
                    </div>
                    <div class="modal-body bg-light pt-2">
                        <textarea class="form-control mb-2" rows="5" wire:model="formulaValue" style="font-family: monospace; font-size: 16px; resize: none;"></textarea>

                        <div class="btn-group mb-2" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('+')">+</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('-')">-</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('*')">x</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('/')">/</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('(')">(</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula(')')">)</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" wire:click="insertFormula('%')">%</button>
                        </div>

                        <div class="border bg-white" style="height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 1;">
                                    <tr>
                                        <th style="border-bottom: 1px solid #ccc;">Parameter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($parameter as $p)
                                    <tr style="cursor: pointer;" wire:click="insertFormula('{{ $p->nama }}')">
                                        <td class="text-primary fw-bold">{{ $p->nama }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-success me-2" wire:click="saveFormula">
                                <i class="fas fa-check"></i> OK
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm">
                                <i class="fas fa-cog text-primary"></i> Tes
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light border shadow-sm me-2" wire:click="clearFormula">
                                <i class="fas fa-eraser text-warning"></i> Bersih
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm" wire:click="closeFormulaModal">
                                <i class="fas fa-times text-danger"></i> Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</div>