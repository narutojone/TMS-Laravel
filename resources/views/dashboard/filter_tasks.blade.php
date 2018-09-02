{{-- Filter tasks --}}
<div>
    <form role="form" class="form-inline" method="get" action="">
        {{-- Category filter --}}
        <label class="control-label m-r-xs" for="category">Category</label>

        <select class="form-control chosen-select" name="category" id="category" style="min-width: 160px;">
            <option></option>
            @foreach ($categories as $category)
                <option{{ ($selectedCategory == $category) ? ' selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>

        {{-- Client filter --}}
        <label class="control-label m-l-md m-r-xs" for="client">Client</label>

        <select class="form-control chosen-select" name="client" id="client" style="min-width: 160px;">
            <option></option>
            @foreach ($clients as $client)
                <option{{ ($selectedClient == $client->id) ? ' selected' : '' }} value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>

        <button class="btn btn-primary m-l-md" type="submit">Filter</button>
    </form>
</div>
