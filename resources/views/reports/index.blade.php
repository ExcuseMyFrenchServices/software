@extends('layouts.main')
@section('content')

	<div class="container">
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

@stop