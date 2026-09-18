<div class="card card-flush mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-900">{{ __('user.update_password') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('user.update_password_subtitle') }}</span>
        </h3>
    </div>
    <div class="card-body pt-0">
        @if (session('status') === 'password-updated')
            <div class="alert alert-success mb-6">
                <span>{{ __('user.password_updated_success') }}</span>
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="fv-row mb-10">
                <label class="required form-label">{{ __('user.current_password') }}</label>
                <input type="password" id="update_password_current_password" name="current_password"
                    class="form-control" placeholder="{{ __('user.enter_current_password') }}" autocomplete="current-password" />
                @error('updatePassword.current_password')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="fv-row mb-10">
                <label class="required form-label">{{ __('user.new_password') }}</label>
                <input type="password" id="update_password_password" name="password" class="form-control"
                    placeholder="{{ __('user.enter_new_password') }}" autocomplete="new-password" />
                @error('updatePassword.password')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="fv-row mb-10">
                <label class="required form-label">{{ __('user.confirm_password') }}</label>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                    class="form-control"
                    placeholder="{{ __('user.confirm_new_password') }}"
                    autocomplete="new-password" />
                @error('updatePassword.password_confirmation')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary">
                    {{ __('user.update_password') }}
                </button>
            </div>
        </form>
    </div>
</div>
