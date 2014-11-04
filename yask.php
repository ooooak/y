<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

class Yask
{
	/**
	* Store all benchmark data
	*/
	private static $stats = [];

	/**
	* Microseconds per 100 millisecond 
	*/
    private static $MS_PER_100MS = 100000; // 1,000,00

    /**
	* Microseconds per second
	*/    
    private static $MS_PER_SECOND = 1000000; // 10,00,000

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

		usort($stats, ['self', 'usort_callback']);

		$best = $stats[0]['time'];

		foreach ($stats as $stat) 
		{
			self::put($stat['name'], ': ', self::make_time($best, $stat['time']));
		}

		// do we really need this ?
		self::$stats = [];
	}

	private static function usort_callback($item, $next_item)
	{
		if ($item['time'] == $next_item['time'])
		{
			return 0;
		} 
		
	    return ($item['time'] < $next_item['time']) ? -1 : 1;
	}


	// ------------------------------------------
	// 				Private Methods 
	// ------------------------------------------

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

	/**
	* TODO: test loop count
	*/
	private static function count_time_with_loop(callable $func, $loop)
	{	
		$time = 0;

		for ($i=0; $i < $loop; $i++)
		{ 
			$time += self::count_time($func);
		}

		return $time;
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

	/* TODO: maybe convert time from float to int ms
	* then get percentage
	*/
	private static function make_time($best, $time)
	{
		// $_time = round($time * 1000000); time:$_time

		$per = self::get_percentage($best, $time);

		return round($time * self::$MS_PER_SECOND) . "ms (slower:$per%)";
	}

	/**
	* get percentage of the given time !!
	* @param $best [float] best time
	*/
	private static function get_percentage($best, $time)
	{
		// return 100 - ($best/$time) * 100;

		return ($best == $time) ? 0 : round(100 - ($best/$time) * 100);
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