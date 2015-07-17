How to use


    
    Y::track('method one', function(){
    	$arra = array();
    	$arra['one'] = 'one';
    	$arra['two'] = 'two';
    });
    
    Y::track('method two', function(){
    	$arra = array(
    		'one' => 'one',
    		'two' => 'two',
    	);
    });
    
    Y::report();
    
    
    // output
    method two: 69899ms (0.00% slower)
    method one: 84924ms (21.50% slower)
