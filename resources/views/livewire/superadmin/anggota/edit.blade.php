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

                        <form wire:submit.prevent="update" autocomplete="off">
                            @csrf
                            <div class="card-body">
                                <!-- ===================== IDENTITAS ANGGOTA ===================== -->
                                <h5 class="mb-3">Identitas Anggota</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="kelompok_id" class="form-label">
                                                Kelompok <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="kelompok_id"
                                                id="kelompok_id"
                                                class="form-select @error('kelompok_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kelompok --</option>
                                                @foreach ($kelompoks as $kelompok)
                                                <option value="{{ $kelompok->id }}">
                                                    [ {{ $kelompok->kode }} ] {{ $kelompok->nama }} - {{ $kelompok->pejabat }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('kelompok_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="kantor_id" class="form-label">
                                                Kantor <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="kantor_id"
                                                id="kantor_id"
                                                class="form-select @error('kantor_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kantor --</option>
                                                @foreach ($kantors as $kantor)
                                                <option value="{{ $kantor->id }}">
                                                    [ {{ $kantor->kode }} ] {{ $kantor->nama_kantor }}
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
                                        <div class="mb-3">
                                            <label for="no_anggota" class="form-label">Nomor Anggota <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="no_anggota" type="text" id="no_anggota" class="form-control @error('no_anggota') is-invalid @enderror">
                                            @error('no_anggota')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="pin" class="form-label">PIN <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="pin" type="text" id="pin" class="form-control @error('pin') is-invalid @enderror">
                                            @error('pin')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="nama" type="text" id="nama" class="form-control @error('nama') is-invalid @enderror">
                                            @error('nama')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="email" type="email" id="email" class="form-control @error('email') is-invalid @enderror">
                                            @error('email')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea wire:model="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3"></textarea>
                                    @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label>Provinsi *</label>
                                            <select wire:model.live="provinsi_id" class="form-control @error('provinsi_id') is-invalid @enderror" id="provinsi">
                                                <option value="">-- Pilih Provinsi --</option>
                                                @foreach($provinces as $prov)
                                                <option value="{{ $prov->code }}">{{ $prov->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('provinsi_id') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label>Kota/Kabupaten *</label>
                                            <select wire:model.live="kota_id" class="form-control @error('kota_id') is-invalid @enderror" id="kota">
                                                <option value="">-- Pilih Kota/Kab --</option>
                                                @foreach($cities as $city)
                                                <option value="{{ $city->code }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kota_id') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label>Kecamatan *</label>
                                            <select wire:model.live="kecamatan_id" class="form-control @error('kecamatan_id') is-invalid @enderror" id="kecamatan">
                                                <option value="">-- Pilih Kecamatan --</option>
                                                @foreach($districts as $district)
                                                <option value="{{ $district->code }}">{{ $district->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kecamatan_id') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label>Kelurahan *</label>
                                            <select wire:model.live="kelurahan_id" class="form-control @error('kelurahan_id') is-invalid @enderror" id="kelurahan">
                                                <option value="">-- Pilih Kelurahan --</option>
                                                @foreach($villages as $village)
                                                <option value="{{ $village->code }}">{{ $village->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kelurahan_id') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ===================== DATA PRIBADI ===================== -->
                                <h5 class="mb-3 mt-5">Data Pribadi</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="tempat_lahir" type="text" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror">
                                            @error('tempat_lahir')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="tgl_lahir" type="text" id="tgl_lahir" placeholder="dd-mm-yyyy" class="form-control @error('tgl_lahir') is-invalid @enderror">
                                            @error('tgl_lahir')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label d-block">Jenis Kelamin <span class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="jk_laki" name="jenis_kelamin" wire:model="jenis_kelamin" value="Laki-laki">
                                                <label class="form-check-label" for="jk_laki">Laki-Laki</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="jk_perempuan" name="jenis_kelamin" wire:model="jenis_kelamin" value="Perempuan">
                                                <label class="form-check-label" for="jk_perempuan">Perempuan</label>
                                            </div>
                                            @error('jenis_kelamin')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">

                                        <div class="mb-3">
                                            <label for="agama" class="form-label">
                                                Agama <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="agama"
                                                id="agama"
                                                class="form-select @error('agama') is-invalid @enderror">
                                                <option value="">-- Pilih Agama --</option>

                                                @foreach ($listAgama as $agama)
                                                <option value="{{ $agama }}">{{ $agama }}</option>
                                                @endforeach
                                            </select>

                                            @error('agama')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="telepon" type="text" id="telepon" class="form-control @error('telepon') is-invalid @enderror">
                                            @error('telepon')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="no_hp" type="text" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror">
                                            @error('no_hp')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="pendidikan" class="form-label">
                                                Pendidikan <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model.defer="pendidikan"
                                                id="pendidikan"
                                                class="form-select @error('pendidikan') is-invalid @enderror">
                                                <option value="">-- Pilih Pendidikan --</option>

                                                @foreach ($listPendidikan as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                                @endforeach
                                            </select>

                                            @error('pendidikan')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="pekerjaan" class="form-label">
                                                Pekerjaan <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model.defer="pekerjaan"
                                                id="pekerjaan"
                                                class="form-select @error('pekerjaan') is-invalid @enderror">
                                                <option value="">-- Pilih Pekerjaan --</option>

                                                @foreach ($listPekerjaan as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                                @endforeach
                                            </select>

                                            @error('pekerjaan')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="status_perkawinan" class="form-label">
                                                Status Perkawinan <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.defer="status_perkawinan"
                                                id="status_perkawinan"
                                                class="form-select @error('status_perkawinan') is-invalid @enderror">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Belum Kawin">Belum Kawin</option>
                                                <option value="Kawin">Kawin</option>
                                                <option value="Cerai Hidup">Cerai Hidup</option>
                                                <option value="Cerai Mati">Cerai Mati</option>
                                            </select>

                                            @error('status_perkawinan')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="pasangan" class="form-label">Nama Pasangan</label>
                                            <input wire:model.debounce.500ms="pasangan" type="text" id="pasangan" class="form-control @error('pasangan') is-invalid @enderror">
                                            @error('pasangan')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="ibu" class="form-label">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="ibu" type="text" id="ibu" class="form-control @error('ibu') is-invalid @enderror">
                                            @error('ibu')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label d-block">Jenis Identitas <span class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="KTP" name="jenis_identitas" wire:model="jenis_identitas" value="KTP">
                                                <label class="form-check-label" for="ktp">KTP</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="SIM" name="jenis_identitas" wire:model="jenis_identitas" value="SIM">
                                                <label class="form-check-label" for="SIM">SIM</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="Lainnya" name="jenis_identitas" wire:model="jenis_identitas" value="Lainnya">
                                                <label class="form-check-label" for="Lainnya">Lainnya</label>
                                            </div>
                                            @error('jenis_identitas')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="no_identitas" class="form-label">Nomor Identitas <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="no_identitas" type="text" id="no_identitas" class="form-control @error('no_identitas') is-invalid @enderror">
                                            @error('no_identitas')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="npwp" class="form-label">NPWP <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="npwp" type="text" id="npwp" class="form-control @error('npwp') is-invalid @enderror">
                                            @error('npwp')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Foto</label>

                                    {{-- Preview Foto Lama --}}
                                    @if($oldFoto)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $oldFoto) }}"
                                            alt="Foto"

                                            style="object-fit: cover;"
                                            width="150"
                                            height="150">
                                    </div>
                                    @endif

                                    {{-- Upload Foto Baru --}}
                                    <input type="file" id="foto" wire:model="foto"
                                        class="form-control @error('foto') is-invalid @enderror">

                                    @error('foto')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>




                                <!-- Keanggotaan, Pengurus, Pengawas, Ahli Waris Tabs -->
                                <h5 class="mb-3 mt-5">Data Keanggotaan</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-3">
                                <ul class="nav nav-pills nav-fill mb-5">
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'keanggotaan' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('keanggotaan')">
                                            Keanggotaan
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'pengurus' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('pengurus')">
                                            Pengurus
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'pengawas' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('pengawas')">
                                            Pengawas
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'ahliwaris' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('ahliwaris')">
                                            Ahli Waris
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Keanggotaan -->
                                    <div style="{{ $activeTab === 'keanggotaan' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="statusAnggotaCheck" wire:model.live="status" checked>
                                                        <label class="form-check-label" for="statusAnggotaCheck">Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tgl_anggota_berhenti" class="form-label">Tanggal Berhenti </label>
                                                    <input wire:model.live="tgl_anggota_berhenti"
                                                        type="text"
                                                        id="tgl_anggota_berhenti"
                                                        class="form-control"
                                                        @disabled($status)>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="anggota_berhenti" class="form-label">Keterangan Berhenti </label>
                                            <textarea wire:model.live="anggota_berhenti" id="anggota_berhenti" class="form-control" rows="3" @disabled($status)></textarea>
                                        </div>
                                    </div>

                                    <!-- Pengurus -->
                                    <div style="{{ $activeTab === 'pengurus' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Status </label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="statusPengurusCheck" wire:model.live="pengurus" value="1">
                                                        <label class="form-check-label" for="statusPengurusCheck">Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="jabatanPengurus" class="form-label">Jabatan</label>
                                                    <input wire:model.live="pengurus_jabatan" type="text" id="jabatanPengurus" class="form-control @error('pengurus_jabatan') is-invalid @enderror">
                                                    @error('pengurus_jabatan') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tgl_pengurus_diangkat" class="form-label">Tanggal Diangkat </label>
                                                    <input wire:model.live="tgl_pengurus_diangkat" type="text" id="tgl_pengurus_diangkat" class="form-control" @disabled(!$pengurus)>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tgl_pengurus_berhenti" class="form-label">Tanggal Berhenti </label>
                                                    <input wire:model.live="tgl_pengurus_berhenti" type="text" id="tgl_pengurus_berhenti" class="form-control" @disabled($pengurus)>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pengurus_berhenti" class="form-label">Keterangan Berhenti </label>
                                            <textarea wire:model.live="pengurus_berhenti" id="pengurus_berhenti" class="form-control" rows="3" @disabled($pengurus)></textarea>
                                        </div>
                                    </div>

                                    <!-- Pengawas -->
                                    <div style="{{ $activeTab === 'pengawas' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Status </label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="statusPengawaCheck" wire:model.live="pengawas" value="1">
                                                        <label class="form-check-label" for="statusPengawasCheck">Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="jabatanPengawas" class="form-label">Jabatan</label>
                                                    <input wire:model.live="pengawas_jabatan" type="text" id="jabatanPengawas" class="form-control @error('pengawas_jabatan') is-invalid @enderror">
                                                    @error('pengawas_jabatan') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tgl_pengawas_diangkat" class="form-label">Tanggal Diangkat </label>
                                                    <input wire:model.live="tgl_pengawas_diangkat" type="text" id="tgl_pengawas_diangkat" class="form-control" @disabled(!$pengawas)>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tgl_pengawas_berhenti" class="form-label">Tanggal Berhenti </label>
                                                    <input wire:model.live="tgl_pengawas_berhenti" type="text" id="tgl_pengawas_berhenti" class="form-control" @disabled($pengawas)>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pengawas_berhenti" class="form-label">Keterangan Berhenti</label>
                                            <textarea wire:model.live="pengawas_berhenti" id="pengawas_berhenti" class="form-control" rows="3" @disabled($pengawas)></textarea>
                                        </div>
                                    </div>

                                    <!-- Ahli Waris -->
                                    <div style="{{ $activeTab === 'ahliwaris' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Ahli Waris 1</label>
                                                    <input type="text" wire:model="waris1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Hubungan Ahli Waris 1</label>
                                                    <input type="text" wire:model="hubungan_waris1" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Ahli Waris 2</label>
                                                    <input type="text" wire:model="waris2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Hubungan Ahli Waris 2</label>
                                                    <input type="text" wire:model="hubungan_waris2" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a wire:navigate href="{{ route('superadmin.anggota') }}" class="btn btn-secondary">
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
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
    @script
    <script data-navigate-once>
        document.addEventListener('livewire:navigated', function() {

            // ============ FUNGSI INIT FLATPICKR ============
            function initFlatpickr(selector, livewireEvent) {
                const el = document.querySelector(selector);
                if (!el) return;

                // Hancurkan instance sebelumnya bila ada
                if (el._flatpickr) {
                    el._flatpickr.destroy();
                }

                flatpickr(el, {
                    dateFormat: "d-m-Y",
                    locale: "id",
                    allowInput: true,
                    clickOpens: true,
                    onChange: (selectedDates, dateStr) => {
                        Livewire.dispatch(livewireEvent, {
                            date: dateStr
                        });
                    }
                });
            }


            // ============ DAFTAR FIELD TANGGAL ============
            const dateFields = [{
                    selector: "#tgl_lahir",
                    event: "updateTglLahir"
                },
                {
                    selector: "#tgl_anggota_berhenti",
                    event: "updateTgl_anggota_berhenti"
                },
                {
                    selector: "#tgl_pengurus_diangkat",
                    event: "updateTgl_pengurus_diangkat"
                },
                {
                    selector: "#tgl_pengurus_berhenti",
                    event: "updateTgl_pengurus_berhenti"
                },
                {
                    selector: "#tgl_pengawas_diangkat",
                    event: "updateTgl_pengawas_diangkat"
                },
                {
                    selector: "#tgl_pengawas_berhenti",
                    event: "updateTgl_pengawas_berhenti"
                }
            ];

            // Init semua Flatpickr
            dateFields.forEach(field => {
                initFlatpickr(field.selector, field.event);
            });


            // ============ EVENT UNTUK RESET DARI LIVEWIRE ============
            Livewire.on('resetDateField', ({
                field
            }) => {
                const el = document.querySelector(`#${field}`);
                if (el?._flatpickr) {
                    el._flatpickr.clear(); // reset tampilan input
                }
            });
        });
    </script>
    @endscript





</div>