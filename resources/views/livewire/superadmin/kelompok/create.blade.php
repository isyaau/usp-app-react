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
                            <div class="card-title">Formulir Tambah Kelompok</div>
                        </div>

                        <form wire:submit.prevent="store">
                            @csrf
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input
                                        wire:model.debounce.500ms="kode"
                                        type="text"
                                        id="kode"
                                        class="form-control @error('kode') is-invalid @enderror">
                                    @error('kode')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input
                                        wire:model.debounce.500ms="nama"
                                        type="text"
                                        id="nama"
                                        class="form-control @error('nama') is-invalid @enderror">
                                    @error('nama')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3 position-relative">

                                    <label for="searchUser" class="form-label">
                                        Ketua <span class="text-danger">*</span>
                                    </label>

                                    <!-- Input Bootstrap -->
                                    <input
                                        type="text"
                                        id="searchUser"
                                        class="form-control @error('query') is-invalid @enderror"
                                        placeholder="Ketik nama user..."
                                        wire:model.live="query"
                                        wire:focus="$set('showDropdown', true)">

                                    @error('query')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Dropdown Bootstrap -->
                                    @if($showDropdown)
                                    <div class="list-group position-absolute w-100 mt-1 shadow-sm"
                                        style="max-height: 200px; overflow-y:auto; z-index: 1000;">

                                        @forelse($users as $user)
                                        <button
                                            type="button"
                                            class="list-group-item list-group-item-action"
                                            wire:mousedown="selectUser({{ $user->id }})">
                                            {{ $user->nama }}
                                        </button>
                                        @empty
                                        @if(strlen($query) > 1)
                                        <div class="list-group-item text-muted">
                                            Tidak ada hasil
                                        </div>
                                        @endif
                                        @endforelse

                                    </div>
                                    @endif

                                    <!-- Tampilan setelah pilih -->
                                    @if($selectedUser)
                                    <div class="form-text text-success mt-1">
                                        Dipilih: {{ $selectedUser->id }}-{{ $selectedUser->nama }}
                                    </div>
                                    @endif

                                </div>








                            </div>


                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a wire:navigate href="{{ route('superadmin.kelompok') }}" class="btn btn-secondary">
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

</div>