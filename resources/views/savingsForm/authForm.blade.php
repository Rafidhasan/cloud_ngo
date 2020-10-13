<form class="hero-form" method="post" action="/saving/{{ Auth::user()->id }}">
    @csrf
    <div class="form-group">
        <label class="text-white" for="sel1">Payment Method</label>
        <select name="method" class="form-control">
          <option value="bikash">Bikash</option>
          <option value="nogod">Nogod</option>
          <option value="rocket">Rocket</option>
          <option value="bank">Bank</option>
        </select>
    </div>
    <input type="text" name="tracking_number" placeholder="Enter Tracking Number"  required autofocus>
    @error('tracking_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <input type="text" name="amount" placeholder="Enter Amount"  required autofocus>
    @error('amount')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <input type="password" name="password" placeholder="Enter Password" required autofocus>
    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. </p>
    <button class="site-btn">Apply for Savings</button>
</form>
