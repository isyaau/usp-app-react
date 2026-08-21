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
                        <li class="breadcrumb-item active">{{ $title }}</li>
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
                            <div class="card-title">Formulir Tambah Kantor</div>
                        </div>

                        <form wire:submit.prevent="update">
                            @csrf
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode *</label>
                                    <input wire:model="kode" type="text" id="kode"
                                        class="form-control @error('kode') is-invalid @enderror">
                                    @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nama_kantor" class="form-label">Nama Kantor *</label>
                                    <input wire:model="nama_kantor" type="text" id="nama_kantor"
                                        class="form-control @error('nama_kantor') is-invalid @enderror">
                                    @error('nama_kantor') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>



                                <!-- ===================== WILAYAH INDONESIA ======================= -->
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


                                <!-- ===================== END WILAYAH ======================= -->

                                <div class="mb-3">
                                    <label for="alamat_kantor" class="form-label">Alamat Kantor *</label>
                                    <textarea wire:model="alamat_kantor" id="alamat_kantor"
                                        class="form-control @error('alamat_kantor') is-invalid @enderror" rows="3"></textarea>
                                    @error('alamat_kantor') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Pejabat *</label>
                                    <input wire:model="pejabat" type="text" class="form-control @error('pejabat') is-invalid @enderror">
                                    @error('pejabat') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Jabatan *</label>
                                    <input wire:model="jabatan" type="text" class="form-control @error('jabatan') is-invalid @enderror">
                                    @error('jabatan') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Bendahara *</label>
                                    <input wire:model="bendahara" type="text" class="form-control @error('bendahara') is-invalid @enderror">
                                    @error('bendahara') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                            </div>

                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a wire:navigate href="{{ route('superadmin.kantor') }}" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>