@extends('reports.template')

@section('report-content')
    <div class="row">
        {{-- Content --}}
        <div class="col-md-12">
            <table class="table table-hover issue-tracker m-b-none">
                <thead>
                <tr>
                    <th>User</th>
                    @for($weekIndex = 0; $weekIndex < \App\Lib\Reports\src\WeekReport::NUMBER_OF_WEEKS ; $weekIndex++)
                        <th>Week {{ $weekIndex+1 }}</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $userId => $userData)
                    <tr>
                        <td>
                            <a href="{{ action('UserController@show', $userId) }}">{{ $userData['userData']['name'] }}</a>
                            <span class="label label-info m-l-md">Level {{ $userData['userData']['level'] }}</span>
                        </td>
                        @for($weekIndex = 0; $weekIndex < \App\Lib\Reports\src\WeekReport::NUMBER_OF_WEEKS ; $weekIndex++)
                            @if(isset($userData['data'][$weekIndex]))
                                <td>
                                    <a href="{{ action('UserController@weekTasks', [$userId, $weekIndex+1]) }}">{{$userData['data'][$weekIndex]}}</a>
                                </td>
                            @else
                                <td>0</td>
                            @endif
                        @endfor
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection