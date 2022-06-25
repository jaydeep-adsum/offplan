<table class="table">
    <tbody>
        @foreach ($data as $item)
        <tr>
            <th scope="col" style="width: 100px;">Client Name : {{$item->name}}</th>
        </tr>
        <tr>
            <th scope="col" style="width: 100px;">Email : {{$item->email}}</th>
        </tr>
        <tr>
            <th scope="col" style="width: 100px;">Phone Number : {{$item->phone}}</th>
        </tr>
        <tr>
            <th scope="col" style="width: 100px; word-wrap: break-word;">Note : {{$item->note}}</th>
        </tr>
        <tr>
            <th scope="col" style="width: 100px;">Project : {{$item->project_id}}</th>
        </tr>
        @endforeach
    </tbody>
</table>
