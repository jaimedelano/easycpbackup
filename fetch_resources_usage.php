<?php

	require '/home/jaime/public_html/ARQUIVOS/PRINT/interact_stdin.php';
	/*$proc = get_proc_partition ();
	if (!$proc) var_dump(mount_proc_partition ());
	die();*/
	$stats = array();
	$cmd = shell_exec ('/scripts/pkgacct jaime /backup &> /dev/null & echo $!');
	if (!($pid = trim($cmd)))
	    exit ("ERROR: There was an issue while running command!\n");
	print 'Running with PID: ' . $pid . "\n";
	    
	while (file_exists('/proc/' . $pid)) {
	    $stats = array();
	    $usage = get_resources_usage($pid);
	    if (!empty_array($usage)) {
		//print 'CPU usage:' . $usage['cpu'] . "\n";
		//print 'Memory usage:' . $usage['mem'] . "\n";
		list_all_proc_child ($pid);
		var_dump($stats); interact_with_user ();	    
	    }
	    else exit ("ERROR: There was an issue fetching resource usage!\n");
	    sleep(3); clearstatcache();
	}

    function list_all_proc_child ($pid) {
    
	global $stats;
	$cmd = exec ('/bin/ps -ewwo pid,ppid,uid,user,pmem,pcpu,command 2> /dev/null', $out, $exit);	    
	if (!isset($out[1])) return NULL;
	unset($out[0]);
	
	foreach ($out as $proc) {
	$resources = explode (' ', preg_replace('!\s+!', ' ', $proc), 7);

	if (count($resources) > 5)
	    if ($resources[1] === $pid) {
	    //var_dump($resources);
	    $stats[$resources[6]] = get_resources_usage ($resources[0]);
	    list_all_proc_child ($resources[0]);
	    return TRUE;
	    }
	}
	return NULL;
    }	
	
    function get_resources_usage ($pid) {
	
	$cmd = exec ('/bin/ps u ' . $pid . ' 2> /dev/null', $out, $exit);	    
	if (!isset($out[1])) return NULL;
	$resources = explode (' ', preg_replace('!\s+!', ' ', $out[1]));
	if (count($resources) > 4)
	    return array( 'cpu' => $resources[2], 'mem' => $resources[3]);
	return NULL;
    }
    
    function empty_array ($mixed) {
    
	if (is_array($mixed)) {
	    foreach ($mixed as $value) {
		if (!empty_array($value)) return false;
	    }
	}
	elseif (!empty($mixed)) return false;
	
	return true;	
    }

// cat /proc/mounts | grep "^proc"
// stat -f -c '%T' /dev/shm
// df '/home' | awk '{print $1, $6}'
// findmnt

    function mount_proc_partition ($options=NULL) {

	$params = NULL; if (!$options) goto mount;
	if ($options == 'remount') $params = '-o remount,rw';
	elseif ($options == 'force') {
	    $params = '-o no-mtab';
	    $umount = manage_partition ('umount', '-l', '/proc');	    
	    if (!$umount) return false;
	}
	elseif ($options == 'move') {
	    $move = manage_partition ('mount', '-o move -t proc proc', '/opt/backup/proc');
	    return ($move === 0);
	}
	mount:
	$mount = manage_partition ('mount', "${params} -t proc proc", '/proc');
	return $mount;
    }
    
    function manage_partition ($bin, $params, $mount, $host=NULL) {
        
        $exe = $host . ' ' . $bin;
	$cmd = "${exe} ${params} ${mount} 2>/dev/null";
	$out = system ($cmd, $exit);
	return ($exit === 0);
    }
    
    function get_proc_partition () {
	 
	if (!file_exists('/proc/mounts')) return NULL;
	$cmd = '/bin/cat /proc/mounts 2>/dev/null | grep "^proc"';
	$exe = exec ($cmd, $out, $exit);
	if ($exit !== 0) return NULL;
	$mnt = preg_split ("/\s+/", array_shift($out));
	return ( !isset($mnt[1]) ? NULL : $mnt[1] );
    }    
    
        
?>