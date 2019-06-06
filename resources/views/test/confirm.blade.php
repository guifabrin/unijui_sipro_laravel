@extends('layouts.app')

@section('title', __('lang.tests.confirm.title'))

@section('btn-left')
	<a class="{{config('constants.classes.buttons.back')}}" href="{{ url('test') }}">
		<i class="{{config('constants.classes.icons.back')}}"></i> {{ __('lang.back') }}
	</a>
@endsection

@section('body')
	<form method="POST" action="{{url("test/".$test->id) }}">
		@csrf
		@method("DELETE")
		<div class="alert alert-warning">
			{{ __("lang.tests.confirm.message") }}"{{$test->description}}"?
		</div>
		<p class="yes-no-buttons">
			<button class="{{config('constants.classes.buttons.yes')}}">
				<i class="{{config('constants.classes.icons.yes')}}"></i> {{ __("lang.yes") }}
			</button>
			<a class="{{config('constants.classes.buttons.no')}}" href="{{ url("test") }}">
				<i class="{{config('constants.classes.icons.no')}}"></i> {{ __("lang.no") }}
			</a>
		</p>
	</form>
@endsection