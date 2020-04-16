<div class="mt-5">
    <div class="form-group">
    <a href="{{route('report',['area' => old('area'), 'keyword' => old('keyword')])}}" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Send report</a>
    </div>
    <table class="table table-dark " id="nicksTable">

        <thead>
        <tr>
            <th scope="col">Client</th>
            <th scope="col">Name</th>
            <th scope="col">Total</th>
            <th scope="col">Date</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ is_string($product->client) ? $product->client : optional($product->client)->name }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->total }}</td>
                <td>{{ $product->created_at->format('d-m-Y') }}</td>
                <td>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1">
                            <form action="{{route('product.edit',[$product->id])}}" method="POST">
                                @method('GET')
                                @csrf
                                <button type="submit" class="btn-primary  text-white">Edit</button>
                            </form>
                        </div>
                        <div class="mr-1">
                            <form action="{{route('product.destroy',[$product->id])}}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class=" btn-danger text-white">Delete</button>
                            </form>
                        </div>
                    </div>


                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mx-auto" style="width: 200px;">
        {{$products->links()}}
    </div>

</div>
