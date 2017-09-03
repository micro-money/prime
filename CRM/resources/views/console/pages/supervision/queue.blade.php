@extends('console.layouts.wrappers.vertical')

@section('content')
	<div class="content-window">
		<h1>Calls queue</h1>
		<table class="striped">
			<tbody>
				@foreach($activities as $activity)
					<tr>
						<td>{{ $activity->person->name }}</td>
						<td>{{ $activity->is_cp ? 'CP' : '' }}</td>
						<td>{{ $activity->person->bad ? 'Bad Guy' : 'Not Bad Guy' }}</td>
						<td>{{ $activity->deal->debts->sum('amount') }} MMK</td>
						<td>{{ $activity->status }}</td>
						<td>
							@if(!$activity->is_cp)
								<a class="btn btn-flat" href="{{ route('console.collection.activity', ['activityId' => $activity->id]) }}">View</a>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop