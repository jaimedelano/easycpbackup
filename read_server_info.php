<?php
    

	error_reporting(E_ALL);
	
	$config = '/etc/wwwacct.conf';		
	$info = read_config_file($config, '\s');
	
	var_dump(get_user ());
	
	/* var_dump(get_ip_address());
	var_dump($_ENV['REMOTE_USER']);
	var_dump($_SERVER);
	*/

	/* 
	Talvez deva certificar-se da restauração do proprietário
	Ver também se o dono da revenda é restaurado primeiro 
	*/  

    function get_user () {
    
        $pid = get_process_id();
        $cmd = exec ('/bin/ps u ' . $pid, $out, $exit);
        if ($exit === 0) {
	    $return = preg_replace('!\s+!', ' ', array_pop($out));
	    return array_shift(explode (' ', $return));	            
        }
        
	$ignore_security_check = true;
	if ($ignore_security_check) {
	    $names = array('USER', 'REMOTE_USER');
	    foreach ($names as $name) {
		if (isset($_SERVER[$name])) return $_SERVER[$name];
		if (isset($_ENV[$name])) return $_ENV[$name]; 
	    }
	}
	return null;    
    }
    
    function get_ip_address () {

	$info = read_config_file('/etc/wwwacct.conf', '\s');
	if (isset($info['ETHDEV'])) {
	
	    $inet = $info['ETHDEV'];
	    $output = exec ("/sbin/ifconfig " . $inet . " | grep 'inet addr:'");
	    preg_match("/\saddr:(.*?)\s/", $output, $match);
	    if (isset($match[1])) return $match[1];
	    
	    $config = '/etc/sysconfig/network-scripts/ifcfg-' . $inet;
	    if (file_exists($config)) {
		$ifcfg = read_config_file($config, '=');
		if (isset($ifcfg['IPADDR'])) return $ifcfg['IPADDR']; 
	    }	     
	}
	
	if (isset($info['ADDR'])) return $info['ADDR'];	
	if (isset($info['HOST'])) return gethostbyname ($info['HOST']);

	return NULL;
    }    

    function get_unsafe_ip_address () {
    
	if (isset($_SERVER['SERVER_ADDR'])) return $_SERVER['SERVER_ADDR'];	
	if (isset($_SERVER['HTTP_HOST'])) return gethostbyname($_SERVER['HTTP_HOST']);	    
	if (isset($_SERVER['SERVER_NAME'])) return gethostbyname($_SERVER['SERVER_NAME']);
	if (isset($_SERVER['HOSTNAME'])) return gethostbyname($_SERVER['HOSTNAME']);
	
	return NULL;
    }
    	
    function get_process_id() {
        
        $pid = ( !getmypid() ? posix_getpid() : getmypid() );
        return $pid;
    }
            
    function read_config_file ($file, $sep) {
    
	# Leitura do arquivo com os dados    
	if (!file_exists($file)) return array(); // Exibir mensagem de erro, usar constante   
	$contents = file ($file, FILE_IGNORE_NEW_LINES);
	if (!$contents) return array(); // Exibir mensagem de erro, usar constante
	
	$params = array();		    
	foreach ($contents as $content) {
	    # Validação dos argumentos do cache			  
	    if (preg_match("/^([a-zA-Z-\-]*)${sep}(.*?)$/", $content, $match)) 
		$params[$match[1]] = $match[2];
	    unset($match);				
	}	 
	return $params;
    }
               

?>