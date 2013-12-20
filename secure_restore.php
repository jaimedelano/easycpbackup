<?php

        $security = new Security;
        $security->path = '/backup/cpmove-migrar';
        var_dump($security->remove_root_privs());
	
class Security {

    public $path;
    
    public function remove_shell_access () {
	
	$file = $this->path . '/shell';
	if (!file_exists($file)) return null;    
	if ( !@file_put_contents ($file, '/usr/local/cpanel/bin/noshell') )
	    return false;
	    	    
	$user = glob($this->path . '/suspendinfo/*');    
	if (!empty($user)) {
	    $contents = file_get_contents($user[0]);
	    if ( preg_match ('/^shell=(.*?)$/', $user[0], $match) ) {
		$contents = str_replace ($match[0], '/usr/local/cpanel/bin/noshell', $contents);
		if ( !@file_put_contents ($user[0], $contents) )
		    return false;
	    }		    
	}	    
	return true;    	    
    }
    
    public function remove_root_privs () {
	
	$file = $this->path . '/resellerconfig/resellers';
	if ( !file_exists ($file) ) return null;
	$contents = file_get_contents ($file);    
	if ( preg_match ('/(:?)(,?)(\s?)all(\s?)(?:,|$)/', $contents, $match) ) {
	    $match[0] = ltrim ($match[0], ':,'); 
	    if ( !@file_put_contents ($file, str_replace ($match[0], '', $contents) ) )
	    return false;	    
	}    		        	
	return true;    	    
    }    
        	
}

?>