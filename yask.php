<?php

class Yask
{
	/**
	* Store all benchmark data
	*/
	private static $stats = [];

    /**
	* Microseconds per second
	*/    
    private static $MS = 1000000; // 10,00,000

	/**
	* @param $name [String] Benchmark Name. It will be display in report.
	* @param $fun  [Callable] Callback to benchmark.
	* @param $loop [int, Bool] loop count, false don't loop
	* @return Void
	*/
	public static function track($name, callable $fun, $loop = 100000) 
	{
		$loop = (is_int($loop) && $loop > 1) ? $loop : FALSE;

		self::compile($name, $fun, $loop);
	}

	public static function report()
	{
		$stats = self::$stats;

		if (empty($stats))
		{
			return;
		}

		usort($stats, ['self', 'usort_callback']);

		foreach ($stats as $stat) 
		{
			self::put($stat['name'], ': ', self::make_time($stats[0]['time'], $stat['time']));
		}

		self::$stats = []; // reset stats
	}


	// ------------------------------------------
	// 				Private Methods 
	// ------------------------------------------

	private static function usort_callback($item, $next_item)
	{
		if ($item['time'] == $next_item['time'])
		{
			return 0;
		} 
		
	    return ($item['time'] < $next_item['time']) ? -1 : 1;
	}

	/**
	* call user callback and record time.
	*/ 
	private function compile($name, callable $func, $loop)
	{
		$time = ($loop === FALSE) 
				? self::count_time($func) 
				: self::count_time_with_loop($func, $loop);

		
		self::$stats[] = // save time to stats
		[
			'name' => $name,
			'time' => $time,
		];
	}

	private static function count_time(callable $func)
	{
		ob_start();
		
		$start = microtime(TRUE);
		$func();
		$end = microtime(TRUE);

		ob_end_clean();

		return $end - $start;
	}

	private static function count_time_with_loop(callable $func, $loop)
	{	
		$total = 0;

		for ($i=1; $i <= $loop; $i++)
		{ 
			$total += self::count_time($func);
		}

		return $total;
	}


	// ------------------------------------------
	// 				Helper Methods
	// ------------------------------------------

	/**
	*  Print function Args then new line.
	*/
	private static function put()
	{
		foreach (func_get_args() as $arg)
		{
			echo $arg;
		}

		echo PHP_EOL;
	}


	private static function make_time($best, $time)
	{
		$per = self::get_percentage($best, $time);

		return round($time * self::$MS) . 'ms ('.number_format($per, 2).'% slower)';
	}

	/**
	* get percentage of the given time.
	* @param $best [float] best time
	*/
	private static function get_percentage($best, $time)
	{	
		return ($best == $time) ? 0 : (($time-$best)/$best) * 100;
	}

	/**
	* Print error Massage with new line
	*/  
	private static function log_error($msg, $func_name = FALSE)
	{
		if ($func_name === FALSE)
		{
			echo $msg;
		}
		else
		{
			echo $msg.' in '.__CLASS__.'::'.$func_name.'()'.' function.';
		}

		echo PHP_EOL;
		
		exit; // if there is error stop.
	}

}