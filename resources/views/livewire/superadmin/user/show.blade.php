<div class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.user') }}">Dashboard</a></li>
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

                    <div class="card card-info card-outline mb-4">

                        <div class="card-header">
                            <div class="card-title">Detail User</div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $user->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>{{ ucfirst($user->role) }}</td>
                                </tr>
                                <tr>
                                    <th>Avatar</th>
                                    <td>
                                        @if($user->avatar)
                                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" width="80">
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>{{ $user->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diubah</th>
                                    <td>{{ $user->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a wire:navigate href="{{ route('superadmin.user') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>