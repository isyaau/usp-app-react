<div class="app-main">


    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>


    <div class="app-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-6 flex-fill">

                    <div class="card card-danger card-outline mb-4">

                        <div class="card-header">
                            <div class="card-title">Formulir {{ $title }}</div>
                        </div>

                        <div x-data="signatureApp(@entangle('signatureBase64'))"
                            x-init="init()">
                            <form wire:submit.prevent="store">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                                        <input wire:model.debounce.500ms="tanggal" type="text" id="tanggal" placeholder="dd-mm-yyyy" class="form-control @error('tanggal') is-invalid @enderror">
                                                        @error('tanggal')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="no_rekening" class="form-label">No Simpanan <span class="text-danger">*</span></label>
                                                        <input
                                                            wire:model.debounce.500ms="no_rekening"
                                                            type="text"
                                                            id="no_rekening"
                                                            class="form-control @error('no_rekening') is-invalid @enderror">
                                                        @error('no_rekening')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="kantor_id" class="form-label">
                                                            Kelompok <span class="text-danger">*</span>
                                                        </label>

                                                        <select wire:model="kantor_id"
                                                            id="kantor_id"
                                                            class="form-select @error('kantor_id') is-invalid @enderror">
                                                            <option value="">-- Pilih Kantor --</option>
                                                            @foreach ($kantors as $kantor)
                                                            <option value="{{ $kantor->id }}">
                                                                [ {{ $kantor->kode }} ] {{ $kantor->nama }} - {{ $kantor->pejabat }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        @error('kantor_id')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3 position-relative">

                                                        <label for="searchAnggota" class="form-label">
                                                            No Anggota <span class="text-danger">*</span>
                                                        </label>

                                                        <!-- Input Bootstrap -->
                                                        <input
                                                            type="text"
                                                            id="searchAnggota"
                                                            class="form-control @error('queryAnggota') is-invalid @enderror"
                                                            placeholder="Ketik nama atau nomor anggota..."
                                                            wire:model.live="queryAnggota"
                                                            wire:focus="$set('showDropdownAnggota', true)">

                                                        @error('queryAnggota')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <!-- Dropdown Bootstrap -->
                                                        @if($showDropdownAnggota)
                                                        <div class="list-group position-absolute w-100 mt-1 shadow-sm"
                                                            style="max-height: 200px; overflow-y:auto; z-index: 1000;">

                                                            @forelse($anggotas as $anggota)
                                                            <button
                                                                type="button"
                                                                class="list-group-item list-group-item-action"
                                                                wire:mousedown="selectAnggota({{ $anggota->id }})">
                                                                [{{ $anggota->no_anggota }}] {{ $anggota->nama }}
                                                            </button>
                                                            @empty
                                                            @if(strlen($queryAnggota) > 1)
                                                            <div class="list-group-item text-muted">
                                                                Tidak ada hasil
                                                            </div>
                                                            @endif
                                                            @endforelse

                                                        </div>
                                                        @endif

                                                        <!-- Tampilan setelah pilih -->
                                                        @if($selectedAnggota)
                                                        <div class="form-text text-success mt-1">
                                                            Dipilih: {{ $selectedAnggota->no_anggota }}-{{ $selectedAnggota->nama }}
                                                        </div>
                                                        @endif

                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Status <span class="text-danger">*</span>
                                                        </label>

                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                id="aktifCheck"
                                                                wire:model.debounce.500ms="aktif" value="1">
                                                            <label class="form-check-label" for="aktifCheck">
                                                                Aktif
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="nama_anggota" class="form-label">Nama Anggota <span class="text-danger">*</span></label>
                                                <input

                                                    type="text"
                                                    value="@if($selectedAnggota){{ $selectedAnggota->nama }}@endif"
                                                    class="form-control @error('nama_anggota') is-invalid @enderror" readonly>

                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="jenis_id" class="form-label">
                                                            Produk Simpanan <span class="text-danger">*</span>
                                                        </label>

                                                        <select wire:model="jenis_id"
                                                            id="jenis_id"
                                                            class="form-select @error('jenis_id') is-invalid @enderror">
                                                            <option value="">-- Pilih Produk Simpanan --</option>
                                                            @foreach ($jenis as $item)
                                                            <option value="{{ $item->id }}">
                                                                [ {{ $item->kode }} ] {{ $item->nama }} - {{ $item->pejabat }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        @error('jenis_id')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="bunga" class="form-label">Bagi Hasil/Tahun <span class="text-danger">*</span></label>
                                                        <div class="input-group mb-3">
                                                            <input wire:model.debounce.500ms="bunga" type="text" class="form-control @error('bunga') is-invalid @enderror" id="bunga" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                        @error('bunga')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="qq" class="form-label">QQ <span class="text-danger">*</span></label>
                                                        <input
                                                            wire:model.debounce.500ms="qq"
                                                            type="text"
                                                            id="qq"
                                                            class="form-control @error('qq') is-invalid @enderror">
                                                        @error('qq')
                                                        <div class="form-text text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-3 position-relative">

                                                        <label for="searchMarketing" class="form-label">
                                                            Marketing <span class="text-danger">*</span>
                                                        </label>

                                                        <!-- Input Bootstrap -->
                                                        <input
                                                            type="text"
                                                            id="searchMarketing"
                                                            class="form-control @error('queryMarketing') is-invalid @enderror"
                                                            placeholder="Ketik nama marketing..."
                                                            wire:model.live="queryMarketing"
                                                            wire:focus="$set('showDropdownMarketing', true)">

                                                        @error('queryMarketing')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <!-- Dropdown Bootstrap -->
                                                        @if($showDropdownMarketing)
                                                        <div class="list-group position-absolute w-100 mt-1 shadow-sm"
                                                            style="max-height: 200px; overflow-y:auto; z-index: 1000;">

                                                            @forelse($marketings as $marketing)
                                                            <button
                                                                type="button"
                                                                class="list-group-item list-group-item-action"
                                                                wire:mousedown="selectMarketing({{ $marketing->id }})">
                                                                {{ $marketing->nama }}
                                                            </button>
                                                            @empty
                                                            @if(strlen($queryMarketing) > 1)
                                                            <div class="list-group-item text-muted">
                                                                Tidak ada hasil
                                                            </div>
                                                            @endif
                                                            @endforelse

                                                        </div>
                                                        @endif

                                                        <!-- Tampilan setelah pilih -->
                                                        @if($selectedMarketing)
                                                        <div class="form-text text-success mt-1">
                                                            Dipilih: {{ $selectedMarketing->id }}-{{ $selectedMarketing->nama }}
                                                        </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <!-- ===================== BAGI HASIL ===================== -->
                                            <h5 class="mb-3">Tanda Tangan</h5>
                                            <hr class="border border-danger border-2 opacity-50 mb-5">
                                            <div>
                                                <div>
                                                    <!-- Pilih Mode -->
                                                    <div class="mb-3">
                                                        <label class="form-label me-3">
                                                            <input type="radio" wire:model="mode" value="draw" x-model="mode" class="form-check-input"> Draw
                                                        </label>
                                                        <label class="form-label">
                                                            <input type="radio" wire:model="mode" value="upload" x-model="mode" class="form-check-input"> Upload
                                                        </label>

                                                    </div>

                                                    <!-- DRAW -->
                                                    <div x-show="mode==='draw'" class="mb-3">

                                                        <div class="border p-2 rounded bg-light" wire:ignore>

                                                            <canvas
                                                                x-ref="canvas"
                                                                width="400"
                                                                height="200"
                                                                style="border:1px solid #000; touch-action:none;">
                                                            </canvas>

                                                            <input type="hidden" wire:model="signatureBase64">
                                                        </div>
                                                        <div class="mt-2">
                                                            <button type="button" class="btn btn-warning me-2" @click="clearPad()">Clear</button>

                                                        </div>
                                                    </div>

                                                    <!-- UPLOAD -->
                                                    <div x-show="mode==='upload'" class="mb-3">
                                                        <label class="form-label">Upload Tanda Tangan</label>
                                                        <input
                                                            type="file"
                                                            wire:model="uploadedSignature"
                                                            accept="image/*"
                                                            class="form-control">

                                                        @error('uploadedSignature')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror

                                                        @if ($uploadedSignature)
                                                        <div class="mt-3">
                                                            <img
                                                                src="{{ $uploadedSignature->temporaryUrl() }}"
                                                                class="img-thumbnail"
                                                                style="max-width:200px;">
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @error('ttd')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Simpan -->
                                                </div>
                                            </div>

                                            <!-- ===================== BAGI HASIL ===================== -->
                                            <h5 class="mb-3 mt-5">Blokir</h5>
                                            <hr class="border border-danger border-2 opacity-50 mb-5">
                                            <div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                SMS <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    id="smsCheck"
                                                                    wire:model.debounce.500ms="sms" value="1">
                                                                <label class="form-check-label" for="smsCheck">
                                                                    Aktif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                Blokir <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    id="blokirSimpananCheck"
                                                                    wire:model.debounce.500ms="blokir_simpanan">
                                                                <label class="form-check-label" for="blokirSimpananCheck">
                                                                    Aktif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">Blokir Nominal <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-text">
                                                                    <input class="form-check-input me-2 mb-1" type="checkbox" value="1" wire:model.defer="blokir_nominal">
                                                                    <label class="mb-0">Blokir</label>
                                                                </div>
                                                                <input type="text" class="form-control" wire:model.defer="nominal_blokir">
                                                                @error('nominal_blokir')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">Blokir s/d Tanggal <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-text">
                                                                    <input class="form-check-input me-2 mb-1" type="checkbox" value="1" wire:model.defer="blokir_tgl">
                                                                    <label class="mb-0">Blokir</label>
                                                                </div>
                                                                <input wire:model.debounce.500ms="tgl_blokir" type="text" id="tgl_blokir" placeholder="dd-mm-yyyy" class="form-control @error('tgl_blokir') is-invalid @enderror">
                                                                @error('tgl_blokir')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>














                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a wire:navigate href="{{ route('superadmin.simpanan') }}" class="btn btn-secondary">
                                                    Kembali
                                                </a>
                                                <button type="submit" class="btn btn-primary" @click="prepareSignature">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>

                            </form>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {

        function initSelect2() {
            $('#select2-users').select2({
                placeholder: 'Cari user...',
                allowClear: true
            });

            // Saat user memilih dari Select2
            $('#select2-users').on('change', function() {
                Livewire.dispatch('setUser', $(this).val());
            });
        }

        initSelect2();

        // Reinitialize Select2 setiap kali Livewire update DOM
        Livewire.hook('message.processed', (message, component) => {
            initSelect2();
        });
    });
</script>

@endpush
<script src="https://cdn.jsdelivr.net/npm/signature_pad@latest/dist/signature_pad.umd.min.js"></script>
<script>
    function signatureApp(signatureBase64) {
        return {
            mode: @entangle('mode'),
            signaturePad: null,
            signatureBase64,

            init() {
                if (this.mode === 'draw') {
                    this.$nextTick(() => this.initCanvas())
                }

                this.$watch('mode', val => {
                    if (val === 'draw') {
                        this.signaturePad = null
                        this.$nextTick(() => this.initCanvas())
                    }
                })
            },

            initCanvas() {
                if (this.signaturePad) return

                const canvas = this.$refs.canvas
                if (!canvas) return

                const ratio = window.devicePixelRatio || 1
                const ctx = canvas.getContext('2d')

                canvas.width = 400 * ratio
                canvas.height = 200 * ratio
                ctx.setTransform(ratio, 0, 0, ratio, 0, 0)

                this.signaturePad = new SignaturePad(canvas)
            },

            prepareSignature() {
                if (
                    this.mode === 'draw' &&
                    this.signaturePad &&
                    !this.signaturePad.isEmpty()
                ) {
                    this.signatureBase64 =
                        this.signaturePad.toDataURL('image/png')
                }
            }
        }
    }
</script>









</div>