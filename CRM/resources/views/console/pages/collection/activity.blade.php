@extends('console.layouts.wrappers.vertical', ['type' => 'collection.activity'])

@section('content')
	<div class="content-window">
		<div class="b-collection-activity">
			<!-- Borrower of FCP information -->
			<div class="b-collection-activity-borrower row">
				<div class="col s6">
					<div class="row">
						<div class="col s12">
							<h3>Borrower information</h3>
						</div>
						<div class="input-field col s6">
							<input id="name" type="text" value="{{ $person->name }}" readonly>
							<label for="name">Customer name</label>
						</div>
						<div class="input-field col s6">
							<input id="loans_count" type="text" value="{{ $person->deals_count }}" readonly>
							<label for="loans_count">Loans count</label>
						</div>
						<div class="input-field col s6">
							<input id="bank_name" type="text" value="CB" readonly>
							<label for="bank_name"></label>
						</div>
						<div class="input-field col s6">
							<input id="bank_account" type="text" value="4783 2344 2001 0230" readonly>
							<label for="bank_account">Bank account</label>
						</div>
					</div>
					
					<!-- Second row -->
					<div class="row">
						<div class="input-field col s6">
							<input id="workplace" type="text" value="{{ $person->workplaces->first()->company }}" readonly>
							<label for="workplace">Workplace</label>
						</div>
						<div class="input-field col s6">
							<input id="position" type="text" value="{{ $person->workplaces->first()->occupation }}" readonly>
							<label for="position">Position</label>
						</div>
						<div class="input-field col s6">
							<input id="salary" type="text" value="{{ $person->workplaces->first()->salary }} MMK" readonly>
							<label for="salary">Salary</label>
						</div>
						<div class="input-field col s6">
							<input id="payday" type="text" value="14th" readonly>
							<label for="payday">Payday</label>
						</div>
					</div>
				</div>
				<div class="col s6 row">
					<div class="col s12">
						<h3>Contacts</h3>
					</div>
					@foreach($person->contacts as $contact)
						<div class="input-field col s12">
							<input id="{{ $contact->id }}" type="text" name="{{ $contact->type }}" value="{{ $contact->value }}" readonly>
							<label for="{{ $contact->id }}">{{ ucwords($contact->type) }}</label>
						</div>
					@endforeach
				</div>
			</div>
			
			<!-- Loan life graphic -->
			<div class="b-collection-activity-lifescape row mb60">
				<div class="col s12">
					<h3>Loan life</h3>
				</div>
				<div class="col s12">
					<div class="b-lifescape">
						<div class="b-lifescape-back b-lifescape-bar--red"></div>
						<div class="b-lifescape-bar b-lifescape-bar--blue" style="width: {{ ceil($deal->approved_term / $deal->current_term * 100) }}%;"></div>
					</div>
				</div>
			</div>
			
			<!-- Loan details -->
			<div class="b-collection-activity-loan row">
				<div class="col s12">
					<h3>Loan information</h3>
				</div>
				<div class="input-field col s3">
					<input id="money_sent_date" type="text" value="{{ $deal->statusMoneySent->first()->created_at }}" readonly>
					<label for="money_sent_date">Money sent date</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_approved_term" type="text" value="{{ $deal->approved_term }}" readonly>
					<label for="loan_approved_term">Approved term</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_current_term" type="text" value="{{ $deal->current_term }}" readonly>
					<label for="loan_current_term">Current term</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_stage" type="text" value="{{ $deal->statuses->first()->status()->first()->name }}" readonly>
					<label for="loan_stage">Stage</label>
				</div>
				
				<!-- Second row -->
				<div class="input-field col s3">
					<input id="money_sent_amount" type="text" value="{{ $deal->payments->first()->amount }} MMK" readonly>
					<label for="money_sent_amount">Money sent amount</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_total_debt" type="text" value="{{ $deal->total_debt }} MMK" readonly>
					<label for="loan_total_debt">Total debt</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_current_debt" type="text" value="{{ $deal->current_debt }} MMK" readonly>
					<label for="loan_current_debt">Current debt</label>
				</div>
				<div class="input-field col s3">
					<input id="loan_return_amount" type="text" value="{{ $deal->returned_amount }} MMK" readonly>
					<label for="loan_return_amount">Returned</label>
				</div>
			</div>
			
			<!-- Script -->
			<div class="b-collection-activity-script script">
				<div class="row">
					<div class="col s12">
						<h3>Script</h3>
					</div>
					<div class="script-text col s12">
						You borrow 50 000mmk on XX January.<br>
						You must pay 65 000 mmk on Last promise to pay (Date) or Approved Repay Date.<br>
						if you fail to pay, you will be charged PROLONGATION FEE - 5000mmk!<br>
						to avoid additional fees we want to offer you<br>
						to PROLONGATE your LOAN on 15 days more and pay only service fee 20000 now. and after 15 days you should pay only rest money 50 000mmk<br>
						or pay FULL AMOUNT 65 000mmk<br>
						so, you want to pay only SERVICE FEE 20000mmk and continue use money or want to pay FULL AMOUNT 60 000mmk?
					</div>
				</div>
				<div class="script-actions">
					<div class="row">
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 1</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 2</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 3</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 4</a>
						</div>
					</div>
					
					<!-- Second row -->
					<div class="row">
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 5</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 6</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 7</a>
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn">Action 8</a>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Options -->
			<div class="b-collection-activity-options">
				<div class="row">
					<div class="col s4">
						BAD option<br>
						if you are OVERDUE and dont want to pay<br>
						we charge PROLONGATION PENALTY 5000 !!!<br>
						every day we charge 1000mmk !!!<br>
						after 1 month your debt will be 100000mmk!<br>
						after 1 month we will report to POLICE<br>
						after 1 month we will report to your WORKPLACE about your DEBT<br>
						after 1 month we will report to all your RELATIVE about your DEBT<br>
						after 1,5 month we will SUE you in CORT<br>
						You should pay total XXX XXXmmk<br>
						You will be have VERY BAD CREDIT HISTORY<br>
						You and all your RELATIVE - NEVER can borrow money in the future in any BANK or MFI.<br>
					</div>
					<div class="col s4">
						BAD option<br>
						if you are OVERDUE and dont want to pay<br>
						we charge PROLONGATION PENALTY 5000 !!!<br>
						every day we charge 1000mmk !!!<br>
						after 1 month your debt will be 100000mmk!<br>
						after 1 month we will report to POLICE<br>
						after 1 month we will report to your WORKPLACE about your DEBT<br>
						after 1 month we will report to all your RELATIVE about your DEBT<br>
						after 1,5 month we will SUE you in CORT<br>
						You should pay total XXX XXXmmk<br>
						You will be have VERY BAD CREDIT HISTORY<br>
						You and all your RELATIVE - NEVER can borrow money in the future in any BANK or MFI.<br>
					</div>
					<div class="col s4">
						BAD option<br>
						if you are OVERDUE and dont want to pay<br>
						we charge PROLONGATION PENALTY 5000 !!!<br>
						every day we charge 1000mmk !!!<br>
						after 1 month your debt will be 100000mmk!<br>
						after 1 month we will report to POLICE<br>
						after 1 month we will report to your WORKPLACE about your DEBT<br>
						after 1 month we will report to all your RELATIVE about your DEBT<br>
						after 1,5 month we will SUE you in CORT<br>
						You should pay total XXX XXXmmk<br>
						You will be have VERY BAD CREDIT HISTORY<br>
						You and all your RELATIVE - NEVER can borrow money in the future in any BANK or MFI.<br>
					</div>
				</div>
			</div>
			
			<!-- Calculator -->
			<div class="b-collection-activity-calculator">
			
			</div>
			
			<!-- Previous promises -->
			<div class="b-collection-activity-promises">
			
			</div>
			
			<!-- Notifications status -->
			<div class="b-collection-activity-notifications">
				<table class="b-notifications bn-5">
					<tbody>
						<tr>
							<td class="b-notifications-item">
								<div class="b-notifications-item-name">SMS</div>
								<div class="b-notifications-item-status bnis-processing">Processing</div>
							</td>
							<td class="b-notifications-item">
								<div class="b-notifications-item-name">E-mail</div>
								<div class="b-notifications-item-status bnis-sent">Sent</div>
							</td>
							<td class="b-notifications-item">
								<div class="b-notifications-item-name">PUSH</div>
								<div class="b-notifications-item-status bnis-delivered">Delivered</div>
							</td>
							<td class="b-notifications-item">
								<div class="b-notifications-item-name">Viber</div>
								<div class="b-notifications-item-status bnis-copy">Click to copy text</div>
							</td>
							<td class="b-notifications-item">
								<div class="b-notifications-item-name">Facebook</div>
								<div class="b-notifications-item-status bnis-error tooltipped" data-position="top" data-delay="50" data-tooltip="Contact not found">Error</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<!-- Finish -->
			<div class="collection-activity-finish center-align">
				<a class="waves-effect waves-light btn" disabled href="/">Next<i class="mdi mdi-arrow-right right"></i></a>
			</div>
		</div>
	</div>
@stop