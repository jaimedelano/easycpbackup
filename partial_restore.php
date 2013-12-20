<?php
    
	$path = '/root/easycpbackup';
	$file = '/home/backup-9.10.2012_16-36-44_bomretor.tar.gz';
	$backup = new Extract ($path, $file);
	$items  = $backup->get_backup_user ();
	var_dump($items);
	if (array_empty($items)) exit ("No items found!\n");
	
	
	
class Extract {

    private $path;
    private $file;
    
    public function __construct ($path, $file) {

    	$this->path = $path;
        $this->file = $file;
        $name = basename ($file);
        if ( strpos($name, '_') !== false )           
            $name = array_pop(explode ('_', $name, 2));
        $type = array_pop(explode ('.', $name, 2));
        $args = ($type == 'tar.gz') ? '-xzf' : '-xf'; 
    	passthru ('/bin/tar ' . $args . ' ' . $file . ' -C ' . $path);
	$this->user = $this->get_backup_user();
	if (!$this->user) print "Unable to find username in the backup file\n";
	$this->base = str_replace (array('.tar', '.gz'), '', basename ($file));	
    }

    public function fetch_backup_contents () {       

	# Obtém dados como banco de dados e emails
        $base = $this->path . '/' . $this->base;       
        $tar = $base . '/homedir.tar';
        if (file_exists($tar))
            system ('/bin/tar -xf ' . $tar . ' -C ' . $base . '/homedir');
        $items = array ('mysql' => '*.sql', 'pgsql' => '*.sql',
	'mail' => '.*@*', 'dnszones' => '*.db','homedir/public_html' => '*');
	foreach ($items as $item=>$value) {		     
	    $contents[$item] = glob ($base . '/' . $item . '/' . $value);	
	}
	return $contents;
    }      	

    public function get_backup_user () {        

	# Obtemos o nome do usuário à partir do nome de arquivo
	$backup = basename($this->file);
	$search = array('^cpmove-(\S+)\.tar(?:\.gz$|$)',
	    '^backup-\d+\.\d+\.\d+_\d+\-\d+\-\d+_(\S+)\.tar(?:\.gz$|$)',
	    '^(\S+)\.tar(?:\.gz$|$)',
	    'cpmove-(\S+)\/?$'	        
	);
	foreach ($search as $name) {	
	    if (preg_match("/${name}/", $backup, $match))
	        if (strlen($match[1]) <= 8) return $match[1];
	}        	
	return false;	        
    }            
}

    function array_empty($mixed) {
    
	if (is_array($mixed)) {
	    foreach ($mixed as $value) {
		if (!array_empty($value)) return false;
	    }
	}
	elseif (!empty($mixed)) return false;
	
	return true;	
    }	

?>