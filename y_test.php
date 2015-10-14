<?php 

require 'y.php';

class Y_test extends Y{
    function __construct()
    {
        assert(self::measure_time(24, 12) == -2);
        assert(self::measure_time(12, 24) == 2);


        Y::track('1', function(){});
        Y::track('2', function(){});


        Y::report();
    }
}


new Y_test();