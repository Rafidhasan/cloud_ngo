<div class="col-lg-5">
    <div class="hero-form">
        <div class="container">
            <div class="row">
                <img src="{{ asset('storage/profile-image/'.$user->image) }}" class="avatar rounded-circle mx-auto d-block" alt="">
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Name: <span class="text-secondary">{{ $user->name }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Phone Number: <span class="text-secondary">{{ $user->mobile_number }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Father's Name <span class="text-secondary">{{ $user->fathers_name }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Mother's Name: <span class="text-secondary">{{ $user->mothers_name }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Date_of_birth: <span class="text-secondary">{{ $user->date_of_birth }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Address: <span class="text-secondary">{{ $user->address }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Thana: <span class="text-secondary">{{ $user->thana }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">NID / Birth Certificate Number: <span class="text-secondary">{{ $user->NID_or_birth_certificate_number }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Nominee Name: <span class="text-secondary">{{ $user->nominee_name }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Nominee NID: <span class="text-secondary">{{ $user->nominee_nid }}</span></h5>
                </div>
            </div>
            <div class="row">
                <div class="text-white">
                    <h5 class="mt-4">Refer Account Number: <span class="text-secondary">{{ $user->refer_account_number }}</span></h5>
                </div>
            </div>
        </div>
    </div>
</div>
