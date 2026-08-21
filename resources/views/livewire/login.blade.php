<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <div class="card card-outline card-primary">

            <div class="card-header text-center">
                <a href="#" class="text-decoration-none link-dark">
                    <h1 class="mb-0"><img width="200px" src="{{ asset('img/logo-banner.jpg') }}" alt="Logo" class="mb-2"> KSP</h1>
                </a>
            </div>

            <div class="card-body login-card-body text-center">

                <p class="login-box-msg">Silakan masuk terlebih dahulu untuk mengakses sistem ini.</p>


                <form wire:submit="login" method="POST" action="#">
                    @csrf
                    <!-- EMAIL -->
                    <div class="input-group mb-3">
                        <div class="form-floating flex-grow-1">
                            <input wire:model="email" id="email" type="email" class="form-control" placeholder=" " />
                            <label for="email">Email</label>
                        </div>
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    </div>

                    <!-- PASSWORD -->
                    <div class="input-group mb-3">
                        <div class="form-floating flex-grow-1">
                            <input wire:model="password" id="password" type="password" class="form-control" placeholder=" " />
                            <label for="password">Password</label>
                        </div>
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    </div>

                    <div class="row mb-3 p-3">



                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>

                    </div>

                </form>

                <p class="mb-1">
                    <a href="forgot-password.html" class="text-decoration-none">
                        I forgot my password
                    </a>
                </p>

                <p class="mb-0">
                    <a href="register.html" class="text-decoration-none">
                        Register a new membership
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>