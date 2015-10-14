    
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




