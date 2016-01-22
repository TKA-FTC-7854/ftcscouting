@extends('master')
<?php
function getParkLoc($parkLocId) {
    switch ($parkLocId) {
        case 0:
            return "N/A";
            break;
        case 1:
            return "Floor Goal";
            break;
        case 2:
            return "Repair Zone";
            break;
        case 3:
            return "Low Zone touching Floor";
            break;
        case 4:
            return "Low Zone";
            break;
        case 5:
            return "Mid Zone";
            break;
        case 6:
            return "High Zone";
            break;
        case 7:
            return "Hang";
            break;
        default:
            return "";
    }
}
?>

@section('js')
    <script type="text/javascript" src="{{asset('js/team_list.js')}}"></script>
@stop
@section('title')
    All Teams
@stop
@section('subtitle')
    Team Overview
@stop
@section('content')
    <label for="sort_by">Sort By:</label>
    <select id="sort_by">
        <option value="0">---------</option>
        <option value="team_number">Team Number</option>
        <option value="match_count">Match Count</option>
        <option value="raw_pin">Raw PIN Number</option>
        <option value="pin">PIN Number</option>
    </select>
    <table class="table table-responsive table-condensed table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th>Team Number</th>
            <th>Number of Matches</th>
            <th>Conflicting Autonomous</th>
            <th>Climbers Scored (Autonomous)</th>
            <th>Rescue Beacon</th>
            <th>Autonomous Parking Location</th>
            <th>Climbers Scored (Teleop)</th>
            <th>Zipline Climbers Scored</th>
            <th>Debris Locations</th>
            <th>All Clear Signal</th>
            <th>Hang</th>
            <th>Endgame Parking Location</th>
            <th>Submitted By</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($teams as $team)
            <tr>
                <td>{{Html::link(route('match.details').'/'.$team->id, $team->team_number)}}</td>
                <td>{{$team->p_match_count}}</td>
                @if($team->starting_loc)
                    <td class="danger">Yes</td>
                @else
                    <td class="success">No</td>
                @endif

                @if($team->climbers_scored)
                    <td class="success">Yes</td>
                @else
                    <td class="danger">No</td>
                @endif

                @if($team->beacon_scored)
                    <td class="success">Yes</td>
                @else
                    <td class="danger">No</td>
                @endif
                <?php
                echo '<td>' . getParkLoc($team->auto_zone) . '</td>';
                ?>

                @if($team->climbers_scored)
                    <td class="success">From Auto</td>
                @elseif($team->t_climbers_scored)
                    <td class="success">Yes</td>
                @else
                    <td class="danger">No</td>
                @endif

                @if($team->zl_climbers == 3)
                    <td>Low, Mid, High</td>
                @elseif($team->zl_climbers == 2)
                    <td>Low, Mid</td>
                @elseif($team->zl_climbers == 1)
                    <td>Low</td>
                @else
                    <td>None</td>
                @endif

                <td>
                    @if($team->d_fz)
                        F,
                    @endif
                    @if($team->d_lz)
                        L,
                    @endif
                    @if($team->d_mz)
                        M,
                    @endif
                    @if($team->d_hz)
                        H
                    @endif
                </td>

                @if($team->all_clear)
                    <td class="success">Yes</td>
                @else
                    <td class="danger">No</td>
                @endif

                @if($team->hang)
                    <td class="success">Yes</td>
                @else
                    <td class="danger">No</td>
                @endif
                <td>
                    @if($team->rz)
                        R,
                    @endif
                    @if($team->fz)
                        FZ,
                    @endif
                    @if($team->lz_f)
                        F,
                    @endif
                    @if($team->lz)
                        L,
                    @endif
                    @if($team->mz)
                        M,
                    @endif
                    @if($team->hz)
                        H
                    @endif
                </td>
                <td>
                    {{$team->submitter_name}}
                </td>
                <td>
                    <a href="{{route('team.edit').'/'.$team->id}}" class="btn btn-sm btn-primary">Edit</a>
                </td>
            </tr>
        </tbody>
        @endforeach
    </table>
@stop