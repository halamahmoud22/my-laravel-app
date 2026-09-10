<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="form w-100">
        @csrf

        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Sign In</h1>
            <div class="text-gray-400 fw-bold fs-4">New Here?
                <a href="{{ route('register') }}" class="link-primary fw-bolder">Create an Account</a>
            </div>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="fv-row mb-10">
            <label for="email" class="form-label fs-6 fw-bolder text-dark">Email</label>
            <input id="email" class="form-control form-control-lg form-control-solid"
                    type="email" name="email" value="{{ old('email') }}"
                    required autofocus autocomplete="username" />
            @error('email')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="fv-row mb-10">
            <div class="d-flex flex-stack mb-2">
                <label for="password" class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-primary fs-6 fw-bolder">Forgot Password?</a>
                @endif
            </div>
            <input id="password" class="form-control form-control-lg form-control-solid"
                    type="password" name="password"
                    required autocomplete="current-password" />
            @error('password')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="fv-row mb-10">
            <label class="form-check form-check-custom form-check-solid form-check-inline">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <span class="form-check-label fw-bold text-gray-700 fs-6">Remember me</span>
            </label>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100">
                Continue
            </button>
        </div>
    </form>
</x-guest-layout>
