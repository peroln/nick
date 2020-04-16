<div class="mt-5">
    <form class="form" action="{{route('product.index')}}"  method="GET">
        @csrf
        <div class="row">
            <div class="col">
                <input type="text" name="keyword" class="form-control" id="keyword" value="{{ old('keyword') }}"
                       placeholder="Keyword">
            </div>
            <div class="col">
                <select name="area" class="form-control">
                    @foreach($options as $option)
                        @if(old('area') && old('area') === ucfirst($option))
                            <option selected>{{old('area')}}</option>
                        @else
                            <option>{{ucfirst($option)}}</option>
                        @endif

                    @endforeach
                </select>

            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary mb-2 btn-block">Search</button>
            </div>
        </div>
    </form>
</div>
