@extends('layouts.main')

@section('content')

	<div class="container">
		@if($agent->isMobile())
			<br>
			<br>
			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-info btn-block btn-lg" href="/reports/week-report">
						<span class="glyphicon glyphicon-user"> Week Report</span>
					</a>	
				</div>
			</div>
			<br>
			<br>
			<br>
			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-primary btn-block btn-lg" href="/reports/month-report">
						<span class="glyphicon glyphicon-stats"> Month Report</span>
					</a>
				</div>
			</div>
		@else
		<a href="/reports/month-report">	
			<div class="col-xs-12 col-sm-6" style="text-align: center; margin-top: 200px;">
				<div style="height: 400px; border: 1px solid silver; border-radius: 10px/10px">
					<h3 style="margin-top: 150px; font-size: 50px"><span class="glyphicon glyphicon-stats"> Month Report</span></h3>
				</div>	
			</div>
		</a>	
		<a href="/reports/week-report">	
			<div class="col-xs-12 col-sm-6" style="text-align: center; margin-top: 200px;">
				<div style="height: 400px; border: 1px solid silver; border-radius: 10px/10px">
					<h3 style="margin-top: 150px; font-size: 50px"><span class="glyphicon glyphicon-user"> Week Report</span></h3>
				</div>	
			</div>
		</a>
		@endif	
	</div>
@stop