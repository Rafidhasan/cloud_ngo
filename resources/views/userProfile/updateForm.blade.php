<div class="col-lg-7">
    <form class="hero-form" method="post" action="/update/{{ $user->id }}">
        @method('patch')
        @csrf

        <input type="text" name="name" value="{{ $user->name }}" placeholder="Your Name"  required autofocus>
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="mobile_number" value="{{ $user->mobile_number }}" placeholder="Your mobile number" required autofocus>
        @error('mobile_number')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <label for="user_image" class="text-white">Your Image</label>
        <input type="file" name="image" class="form-control-file">
        @error('image')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="fathers_name" value="{{ $user->fathers_name }}" placeholder="Your Father's/Husbend's Name" required autofocus>
        @error('fathers_name')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="mothers_name" value="{{ $user->mothers_name }}" placeholder="Your Mother's Name" required autofocus>
        @error('mothers_name')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="date" class="form-control" name="date_of_birth" value="{{ $user->date_of_birth }}" placeholder="Enter you date of birth" required autofocus>
        @error('date_of_birth')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="address" value="{{ $user->address }}" placeholder="Your Address" required autofocus>
        @error('address')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="thana" value="{{ $user->thana }}" placeholder="Your related Thana" required autofocus>
        @error('thana')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <label for="nid_image" class="text-white">Your NID Card / Birth Certificate Image</label>
        <input type="file" name="nid_image" class="form-control-file">
        @error('nid_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="NID_or_birth_certificate_number" value="{{ $user->NID_or_birth_certificate_number }}" placeholder="Your NID or Birth Certificate number" required autofocus>
        @error('NID_or_birth_certificate_number')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="nominee_name" value="{{ $user->nominee_name }}" placeholder="Enter your nominee name" autofocus>
        @error('nominee_name')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="nominee_nid" value="{{ $user->nominee_nid }}" placeholder="NID of your nominee" autofocus>
        @error('nominee_nid')
            <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="text" name="refer_account_number" value="{{ $user->refer_account_number }}" placeholder="Reference Account number" autofocus>
        @error('refer_account_number')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <p class="text-white">Note About Refer Account number</p>

        <button type="submit" class="site-btn">Update</button>
    </form>
</div>
</div>
