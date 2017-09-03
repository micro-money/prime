@extends('console.layouts.wrappers.vertical')

@section('content')
	<div class="content-window">
		<h1>Clients</h1>
		<table class="striped">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Documents</th>
					<th>E-mail</th>
					<th>Phones</th>
					<th>City</th>
					<th>Workplaces</th>
					<th>Bad</th>
				</tr>
			</thead>
			<tbody>
				@foreach($persons as $person)
					<tr class="{{ $person->bad ? 'red-text' : '' }}">
						<td>{{ $person->id }}</td>
						<td><a href="https://mm.bpmonline.com/0/Nui/ViewModule.aspx#CardModuleV2/ContactPageV2/edit/{{ $person->bpm_id }}" target="_blank">{{ $person->name }}</a></td>
						<td style="white-space: nowrap">
							@if(count($person->documents))
								@foreach($person->documents as $document)
									<a href="?nrc={{ substr($document->value, strlen($document->value) - 6, 6) }}">{{ $document->value }}</a><br>
								@endforeach
							@endif
						</td>
						<td>{{ count($person->emails) ? $person->emails->first()->value : '' }}</td>
						<td style="white-space: nowrap">
							@if(count($person->phones))
								@foreach($person->phones as $phone)
									<a href="sip:{{ $phone->value }}"><i class="mdi mdi-cellphone"></i></a>
									<a href="?phone={{ $phone->value }}">{{ $phone->value }}</a><br>
								@endforeach
							@endif
						</td>
						<td>{{ isset($person->addresses->first()['city']) ? $person->addresses->first()['city']['name'] . ', ' . $person->addresses->first()['address'] : '' }}</td>
						<td>{{ count($person->workplaces) ? $person->workplaces->first()->occupation : '' }}</td>
						<td>
							@if($person->bad)
								<i class="mdi mdi-24px mdi-checkbox-marked-outline"></i>
							@else
								<i class="mdi mdi-24px mdi-checkbox-blank-outline"></i>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{{ $persons->links('console.vendor.pagination.default') }}
	</div>
@stop