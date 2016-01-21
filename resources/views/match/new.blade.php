@extends('master')

@section('title')
    New Match
@stop

@section('subtitle')
    New Match Results
@stop
@section('js')
    <script type="text/javascript" src="{{asset('js/new_match.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/multi_check.js')}}"></script>
@stop
@section('content')
    {!! Form::open(['route'=>'match.new', 'method' => 'put']) !!}
    <div class="form-group" id="team_num_div">
        {!! Form::label('team_number', 'Team Number', ['class'=>'control-label']) !!}
        <span class="help-block" id="team_num_help"></span>
        {!! Form::input('tel', 'team_number', isset($_GET['team'])? $_GET['team'] : null, ['class'=>'form-control', 'id'=>'team_num']) !!}
    </div>
    <hr/>
    <h2>Autonomous</h2>
    <div class="form-group">
        {!! Form::checkbox('climbers_scored', 1, false, ['id'=>'c_s_a']) !!}
        {!! Form::label('climbers_scored', 'Scored Climbers') !!}
    </div>
    <div class="form-group">
        {!! Form::checkbox('beacon_scored') !!}
        {!! Form::label('beacon_scored', 'Scored Beacon') !!}
    </div>
    <label for="auto_park_zone">Autonomous End Location</label>
    <div class="form-group">
        <div id="auto_park_zone">
            {!! Form::radio('auto_zone', '0') !!} N/A<br/>
            {!! Form::radio('auto_zone', '1') !!} Floor Goal<br/>
            {!! Form::radio('auto_zone', '2') !!} Repair Zone<br>
            {!! Form::radio('auto_zone', '3') !!} Low Zone Touching Floor<br/>
            {!! Form::radio('auto_zone', '4') !!} Low Zone<br/>
            {!! Form::radio('auto_zone', '5') !!} Mid Zone<br/>
            {!! Form::radio('auto_zone', '6') !!} High Zone<br/>
        </div>
    </div>
    <div class="form-group">
        {!! Form::checkbox('t_climbers_scored', 1, false, ['id'=>'c_s_t']) !!}
        {!! Form::label('t_climbers_scored', 'Scored Climbers (Teleop)') !!}
    </div>
    {!! Form::label('zl', 'Zipline Climbers') !!}
    <div class="form-group" id="zl">
        {!! Form::radio('zl_climbers', '0', true) !!} 0<br/>
        {!! Form::radio('zl_climbers', '1') !!} 1<br/>
        {!! Form::radio('zl_climbers', '2') !!} 2<br/>
        {!! Form::radio('zl_climbers', '3') !!} 3<br/>
    </div>
    <hr/>
    <h2>Teleop</h2>
    {!! Form::label('d', 'Debris Scored') !!}
    <div class="form-group" id="debris">
        {!! Form::checkbox('d_none', 1, true, ['id'=>'mcheck_default']) !!} None<br/>
        {!! Form::checkbox('d_fz', 1, false, ['class'=>'mcheck_o']) !!} Floor Goal<br/>
        {!! Form::checkbox('d_lz', 1, false, ['class'=>'mcheck_o']) !!} Low Goal<br/>
        {!! Form::checkbox('d_mz', 1, false, ['class'=>'mcheck_o']) !!} Mid Goal<br/>
        {!! Form::checkbox('d_hz', 1, false, ['class'=>'mcheck_o']) !!} High Goal<br/>
    </div>
    <div class="form-group">
        {!! Form::checkbox('all_clear') !!}
        {!! Form::label('allClear', 'All Clear Signal') !!}
    </div>
    <div class="form-group">
        <label for="tele_park_zone">Final Resting Position</label>
        <div id="tele_park_zone">
            {!! Form::radio('tele_park', '0', true) !!} N/A<br/>
            {!! Form::radio('tele_park', '1') !!} Floor Goal<br/>
            {!! Form::radio('tele_park', '2') !!} Repair Zone<br>
            {!! Form::radio('tele_park', '3') !!} Low Goal Touching Floor<br/>
            {!! Form::radio('tele_park', '4') !!} Low Zone<br/>
            {!! Form::radio('tele_park', '5') !!} Mid Zone<br/>
            {!! Form::radio('tele_park', '6') !!} High Zone<br/>
            {!! Form::radio('tele_park', '7') !!} Hang<br/>
        </div>
    </div>
    {!! Form::submit('Save', ['class'=> 'btn btn-success btn-block', 'id'=>'submit_btn']) !!}
    {!! Form::close() !!}
@stop