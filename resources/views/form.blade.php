<form action="{{route('product.update', $product->id)}}" method="post">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nameInput">Name</label>
        <input name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" value="{{$product->name}}">
        <small id="nameHelp" class="form-text text-muted">Write please new product here.</small>
    </div>
    <div class="form-group">
        <label for="totalInput">Total</label>
        <input type="text" class="form-control" id="totalInput" name="total" value="{{$product->total}}">
    </div>
    <div class="form-group">
        <label for="clientSelect">Client select</label>
        <select class="form-control" id="clientSelect" name="client_id">
            @foreach($clients as $client)
                @if($product->id === $client->id)
                    <option value={{$client->id}} selected>{{$client->name}}</option>
                @else
                    <option value={{$client->id}}>{{$client->name}}</option>
                @endif

            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="dateInput">Date</label>
        <input type="date" class="form-control" id="dateInput" name="created_at" value="{{$product->created_at->format('Y-m-d')}}">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
