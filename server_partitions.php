<?php
    
    print_r(get_disk_partitions ());
    
    function get_disk_partitions () {
    
	# Retorna as partições, pontos de montagem e espaço em disco      
        
        $partitions = array();              
	$output = shell_exec('/bin/df -Th | grep "/dev"');	
	$return = explode ("\n", $output);
	if (empty($return)) return $partitions;

	foreach ($return as $explode) {
	
	    if (empty($explode)) continue;	
	    $info = explode (' ', preg_replace('!\s+!', ' ', $explode));

	    $device = array_shift($info);
	    $partitions[$device]['type'] = trim($info[0]);
	    $mount = array_pop($info);	    	    	    	    
	    $partitions[$device]['mount'] = trim($mount);	    	    
	    $attributes = array('total', 'used', 'free', '%');
	    	    
	    for ($i=1; $i<=3; $i++) {	    
	        $index = $attributes[($i - 1)];
	        $partitions[$device]['space'][$index] = trim($info[$i]);
	    }
	}
	if (!empty($partitions)) return $partitions;

	# Em caso de erro, retorna apenas os pontos de montagem
	preg_match_all("/%\s+(.*)\n/U", $output, $matches);
	if (isset($matches[1])) {
	    foreach($matches[1] as $match) {
		$partitions [] = trim($match);
	    }
	}
	return $partitions; 	    
    }
    	
?>	