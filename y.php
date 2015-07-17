<?php

class Y
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
    * @param $loop [int] loop count, false don't loop
    * @return Void
    */
    public static function track($name, callable $fun, $loop = 100000) 
    {
        self::compile($name, $fun, intval($loop));
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
            echo $stat['name'] 
                 . ': ' 
                 . self::make_time($stats[0]['time'], $stat['time'])) 
                 . PHP_EOL;
        }

        self::$stats = []; // reset stats
    }


    // ------------------------------------------
    //              Private Methods 
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
    * run callback and track time.
    */ 
    private function compile($name, callable $func, $loop)
    {
        if ($loop > 1) // loop and collect time
        {
            $total = 0;
            for ($i=1; $i <= $loop; $i++)
            { 
                $total += self::count_time($func);
            }

        } 
        else
        {
            $time = self::count_time($func);
        }
        
        // save time to stats
        self::$stats[] = 
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


    // ------------------------------------------
    //              Helper Methods
    // ------------------------------------------

    /**
    *  Print function Args then new line.
    */
    private static function make_time($best, $time)
    {
        $per = self::get_percentage($best, $time);

        return round($time * self::$MS) . 'ms ('. $per .'% slower)';
    }

    /**
    * get percentage of the given time.
    * @param $best [int]
    * @param $time [int]
    */
    private static function get_percentage($best, $time)
    {   
        return ($best == $time) 
               ? 0 
               : number_format((($time-$best)/$best) * 100);
    }
}