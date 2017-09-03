<?php
	
	use App\Models\Converter\zSync;
	
	/*
	 * 1. contact
	 * 2. cp
	 * 3. opportunity
	 * 4. lead
	 * 5. cash
	 * 6. message
	 */
	
	// All converter
	Artisan::command('convert {start?}', function ($start = 0) {
		$this->call('convert:contact', ['start' => $start]);
		$this->call('convert:opportunity', ['start' => $start]);
		$this->call('convert:lead', ['start' => $start]);
	});
	
	// Contact converter
	Artisan::command('convert:contact {start?} {step?}', function ($start = 0, $step = 15000) {
		$totalTime = time();
		
		if ($start == 'clear') {
			$this->call('migrate:refresh', ['--seed' => true]);
			$this->comment('Seeding finished. Spent: ' . ceil(time() - $totalTime) . ' sec.');
			$start = 0;
		}
		
		$converter = new App\Http\Controllers\Coverter\ContactTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Contact table conversion.');
	
	// UsrPrimaryContact converter
	Artisan::command('convert:cp {start?} {step?}', function ($start = 0, $step = 30000) {
		$totalTime = time();
		
		$converter = new App\Http\Controllers\Coverter\PrimaryContactTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Contact person table conversion.');
	
	// Opportunity converter
	Artisan::command('convert:opportunity {start?} {step?}', function ($start = 0, $step = 25000) {
		$totalTime = time();
		
		$converter = new App\Http\Controllers\Coverter\OpportunityTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Opportunity table conversion.');
	
	// Lead converter
	Artisan::command('convert:lead {start?} {step?}', function ($start = 0, $step = 25000) {
		$totalTime = time();
		
		$converter = new App\Http\Controllers\Coverter\LeadTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Lead table conversion.');
	
	// UsrCash converter
	Artisan::command('convert:cash {start?} {step?}', function ($start = 0, $step = 50000) {
		$totalTime = time();
		
		$converter = new App\Http\Controllers\Coverter\UsrCashTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Cash table conversion.');
	
	// MessageLog converter
	Artisan::command('convert:message {start?} {step?}', function ($start = 0, $step = 50000) {
		$totalTime = time();
		
		$converter = new App\Http\Controllers\Coverter\MessageLogTableConverterController();
		for ($i = $start; $i < $start + $step; $i += 1000) {
			$this->comment('Started next chunk. From ' . ($i + 1) . ' to ' . ($i + 1000) . '.');
			$log = $converter->convert($i);
		}
		
		$this->comment('Total spent: ' . ceil(time() - $totalTime) . ' sec.');
		
		// Log
		zSync::create($log);
	})->describe('Messages table conversion.');