<div class="col-lg-12">
    <div class="row">
        @if ($user->nominee_nid == '')
            <div class="col-lg-6">
                <h5>{{ $user->name }}</h5>
                <img src="{{ asset('/storage/profile_image/'.$user->image)}}" height="250" width="300" alt="">
            </div>
            <div class="col-lg-6">
                <h5>User NID</h5>
                <img src="{{ asset('/storage/nid_or_birth_certificate_image/'.$user->nid_image)}}" height="250" width="500" alt="">
            </div>
        @else
            <div class="col-lg-4">
                <h5>{{ $user->name }}</h5>
                <img src="{{ asset('/storage/profile-image/'.$user->image)}}" height="250" width="300">
            </div>
            <div class="col-lg-4">
                <h5>User NID</h5>
                <img src="{{ asset('/storage/nid_or_birth_certificate_image/'.$user->nid_image)}}" height="250" class="ml-3" width="350" alt="">
            </div>
            <div class="col-lg-4">
                <h5>{{ $user->nominee_name }} NID</h5>
                <img src="{{ asset('/storage/nid_or_birth_certificate_image/'.$user->nominee_nid)}}" height="250" class="ml-3" width="350" alt="">
            </div>
        @endif
    </div>
    <form class="hero-form" method="post" action="/admin/userUpdate/{{ $user->id }}">
        @method('put')
        @csrf

        <div class="form-group">
            <h5>Name</h5>
            <input type="text" class="form-control" name="name" value="{{ $user->name }}">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Mobile Number</h5>
            <input type="text" class="form-control" name="mobile_number" value="{{ $user->mobile_number }}">
            @error('mobile_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Mobile Number</h5>
            <input type="text" class="form-control" name="mobile_number" value="{{ $user->mobile_number }}">
            @error('mobile_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Father's Name</h5>
            <input type="text" class="form-control" name="fathers_name" value="{{ $user->fathers_name }}">
            @error('fathers_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Mother's Name</h5>
            <input type="text" class="form-control" name="mothers_name" value="{{ $user->mothers_name }}">
            @error('mothers_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Date Of birth</h5>
            <input type="date" class="form-control" name="date_of_birth" value="{{ $user->date_of_birth }}" placeholder="Enter you date of birth" required autofocus>
            @error('date_of_birth')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Address</h5>
            <input type="text" class="form-control" name="address" value="{{ $user->address }}" required autofocus>
            @error('address')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Thana</h5>
            <input type="text" class="form-control" name="thana" value="{{ $user->thana }}" required autofocus>
            @error('thana')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Nominee Name</h5>
            <input type="text" name="nominee_name" value="{{ $user->nominee_name }}" class="form-control" autofocus>
            @error('nominee_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <h5>Refer Account Number</h5>
            <input type="text" name="refer_account_number" value="{{ $user->refer_account_number }}"  class="form-control" autofocus>
            @error('refer_account_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Update</button>
    </form>
</div>
</div>
