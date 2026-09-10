<div class="card card-flush border-danger">
    <div class="card-header border-0 pt-6 bg-danger bg-opacity-10">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-danger">Delete Account</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Permanent action - cannot be undone</span>
        </h3>
    </div>
    <div class="card-body pt-0">
        <p class="text-gray-700 mb-6">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>

        <button type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#confirm-user-deletion">
            <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5 7h14M8 7V4c0-1 1-2 2-2h4c1 0 2 1 2 2v3M10 11v6M14 11v6M6 7h12v11c0 1-1 2-2 2H8c-1 0-2-1-2-2V7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            Delete Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-body">
                    <h4 class="mb-3">{{ __('Are you sure you want to delete your account?') }}</h4>

                    <p class="text-gray-700 mb-6">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="fv-row mb-0">
                        <label class="required form-label">Password</label>
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required />
                        @error('userDeletion.password')
                            <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
