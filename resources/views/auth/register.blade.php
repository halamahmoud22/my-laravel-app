<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="form w-100">
        @csrf

        <div class="mb-10 text-center">
            <h1 class="text-dark mb-3">Create an Account</h1>
            <div class="text-gray-400 fw-bold fs-4">Already have an account?
                <a href="{{ route('login') }}" class="link-primary fw-bolder">Sign in here</a>
            </div>
        </div>

        <div class="fv-row mb-7">
            <label for="name" class="form-label fw-bolder text-dark fs-6">Name</label>
            <input id="name" class="form-control form-control-lg form-control-solid"
                    type="text" name="name" value="{{ old('name') }}"
                    required autofocus autocomplete="name" />
            @error('name')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="fv-row mb-7">
            <label for="email" class="form-label fw-bolder text-dark fs-6">Email</label>
            <input id="email" class="form-control form-control-lg form-control-solid"
                    type="email" name="email" value="{{ old('email') }}"
                    required autocomplete="username" />
            @error('email')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="fv-row mb-7">
            <label for="password" class="form-label fw-bolder text-dark fs-6">Password</label>
            <input id="password" class="form-control form-control-lg form-control-solid"
                    type="password" name="password"
                    required autocomplete="new-password" />
            @error('password')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="fv-row mb-10">
            <label for="password_confirmation" class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
            <input id="password_confirmation" class="form-control form-control-lg form-control-solid"
                    type="password" name="password_confirmation"
                    required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100">
                Submit
            </button>
        </div>
    </form>
</x-guest-layout>
