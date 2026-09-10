<div class="card card-flush mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-900">Profile Information</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Update your account's profile information and email
                address</span>
        </h3>
    </div>
    <div class="card-body pt-0">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-6">
                <span>{{ __('Your profile has been updated successfully.') }}</span>
            </div>
        @endif

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="fv-row mb-10 text-center">
                <label for="avatar" style="cursor: pointer; display: inline-block; position: relative;">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/media/avatars/blank.png') }}"
                        alt="Current avatar" id="avatar-preview" class="rounded-circle"
                        style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #e4e6ef;" />

                    <span
                        style="position: absolute; bottom: 0; right: 0; background: #009ef7; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;">
                        <i class="ki-duotone ki-pencil fs-6 text-white">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </label>

                <input type="file" id="avatar" name="avatar" accept="image/*" class="d-none"
                    onchange="document.getElementById('avatar-preview').src = URL.createObjectURL(this.files[0])" />

                @error('avatar')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>
            {{-- ...unchanged... --}}
            <div class="fv-row mb-10">
                <label class="required form-label">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control" placeholder="Enter your name" required autofocus autocomplete="name" />
                @error('name')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="fv-row mb-10">
                <label class="required form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control" placeholder="Enter your email" required autocomplete="username" />
                @error('email')
                    <span class="text-danger fs-7 mt-2 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
