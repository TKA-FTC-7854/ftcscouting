@extends('layouts.panel')

@section('title')
    Team List
@endsection

@section('body')
    <h4>All Teams</h4>
    <table class="table table-responsive table-condensed table-striped table-bordered">
        <thead>
        <th>Team Number</th>
        <th>Team Name</th>
        </thead>
        <tbody>
        @foreach($teams as $team)
            <tr>
                <td><a href="{{route('teams.show', [$team->team_number])}}">{{$team->team_number}}</a></td>
                <td>{{$team->name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection