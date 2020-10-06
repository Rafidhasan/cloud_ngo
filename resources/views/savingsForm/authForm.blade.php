<form class="hero-form" method="post" action="/saving/{{ Auth::user()->id }}">
    @csrf
    <input type="text" name="tracking_number" placeholder="Enter Tracking Number"  required autofocus>
    @error('tracking_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <input type="text" name="amount" placeholder="Enter Amount"  required autofocus>
    @error('amount')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <input type="password" name="password" placeholder="Enter Passweord" required autofocus>
    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. </p>
    <button class="site-btn">Apply for Loan</button>
</form>
