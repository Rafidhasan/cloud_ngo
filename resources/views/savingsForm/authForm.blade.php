<form class="hero-form" method="post" action="/saving/{{ Auth::user()->id }}">
    @csrf
    <div class="form-group">
        <label class="text-white" for="sel1">Payment Method</label>
        <select id="method" name="method" class="form-control">
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
    <p class="text-white" style="font-size: 24px" id="show"></p>
    <button class="site-btn">Apply for Savings</button>
</form>
<script>
    const selectElement = document.getElementById("method");

    selectElement.addEventListener('change', function (event)  {
        if(event.target.value == 'bikash') {
            document.getElementById("show").innerHTML = "Bkash account Number is 01772974123";
        }   else if(event.target.value == 'nogod') {
            document.getElementById("show").innerHTML = "Nogod account Number is 01772974123";
        }   else if(event.target.value == 'rocket') {
            document.getElementById("show").innerHTML = "Rocket Account Number is 017729741230";
        }   else {
            document.getElementById("show").innerHTML = "Select A Method";
        }
    });
</script>
</div>
