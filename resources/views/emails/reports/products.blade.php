
@component('mail::message')
    @component('mail::table')
        | Client        | Name          | Total    | Date      |
        |:-------------:|:-------------:|:--------:|:---------:|

        @foreach($products as $product)
            |{{ optional($product->client)->name ?: $product->client }}|{{ $product->name }} | {{ $product->total }} | {{ $product->created_at->format('d-m-Y') }} |
        @endforeach
    @endcomponent

@endcomponent
