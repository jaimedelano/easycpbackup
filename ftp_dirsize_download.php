<?php

	print "Fetching recursive directory size...\n";
	#$cmd = "/usr/bin/lftp -u anoticia,'aneYBh0549' -e 'du -s / > out;bye' 69.64.43.150 3>&1 1>&2 2>&3";
	$host = '69.64.43.150';
	$user = 'anoticia';
	$pass =	'aneYBh0549';
	$path = '/';
	$save = 
<<<LFTP
    debug 8 -o debug.log
    open $host
    user $user '$pass'
    du -s $path > out
    bye
LFTP;
	file_put_contents ('lftpexec', $save) or exit ("Could not save config file!\n");	
	$size = get_ftp_dir_size($path);
	if ($size) print 'Total directory size is: ' . $size . "MB\n";
	else print "Could not get total file size\n";

	print "Downloading files...\n";
	define("PROCESS_ID", getmypid()); //Tá errado isso aqui!
	$number = ( !defined('PROCESS_ID') ? mt_rand(10000,999999) : PROCESS_ID ); # Número para identificar o processo
	$number = '35682';
	system('killall -9 script; killall -9 lftp');
	$cmd = "script -q /dev/stdout -c \"/usr/bin/lftp -u anoticia,'aneYBh0549' -e 'debug 8 -o ${number}.log; lcd download; mirror -vvv --only-newer public_html; bye' 69.64.43.150\" &> output.log & echo $!";
	
	$pid = NULL;
	exec($cmd, $out, $exit);
	sleep(3); # Necessário para que o processo principal se inicie
	$_ENV['debug']['lftp'] = array();
	//A barra de progresso deve lidar com a opção --only-newer
	
	exec("/bin/ps -ewwo pid,command", $processes);
	foreach ($processes as $process)  {
	    //print $process . "\n"; # Usar pra depurar
	    $_ENV['debug']['lftp'][] = $process;
	    if (strpos($process, $number . '.log') !== false) {  
		$pid = array_shift( explode (' ', preg_replace('!\s+!', ' ', ltrim($process))) );
	        if ($pid): print 'Process PID found: ' . $pid . "\n"; break;
	        endif;
	    }
	}	
	file_put_contents ($number . '.bug', 
			print_r($_ENV['debug']['lftp'], true), 
				FILE_APPEND);

	if ($pid) {
	    $size = '0'; $path = 'output.log'; 	
	    while ( file_exists('/proc/' . $pid) ) {
		read_last_byte($path, $size);
		$size = filesize($path);
		clearstatcache(); sleep(1);
		//passthru ('tail -f output.log --pid=' . $pid); // Substituir por read_last_byte()
	    }
	}
	else print "Process PID not found!\n";
	
	//var_dump(preg_match ('/ \[[0-9]+\] Moving to background/', $pid, $match));
	# Usar para rodar vários processos ao mesmo tempo? 
	
	$contents = @file_get_contents ('output.log');
	if (preg_match('/[0-9]+ errors detected/', $contents, $matches)) {
	    print "Some files could not be transferred!\n";
	}

    function get_ftp_dir_size ($path) {

	if ( ($size = @file_get_contents('cache/size')) )
	    return array_shift(preg_split('!\s+!', $size));
	    
	$cmd = "/usr/bin/lftp -f lftpexec 3>&1 1>&2 2>&3";
	passthru ($cmd, $exit); $size = NULL;
	if ($exit === 0) {
	    $out = @file_get_contents ('out');
	    if (preg_match('/^[0-9]+\s+(.*?)$/', $out, $match)) {
		$size = intval (trim($match[0]));
		file_put_contents ('cache/size', $out);
		return round ( ($size / 1024), 2); //Substituir por função de conversão
	    }
	}
	return $size;
    }

	function read_last_byte ($file, $size) {
                  
	    if (filesize($file) > $size) {
		$fp = fopen($file, "rb");
		fseek($fp, $size, SEEK_SET);
		while ( !(ftell($fp) >= filesize($file)) ) {
		    print ( php_sapi_name() == 'cli' ? 
		    		str_replace("\n", "\r", (fgetc($fp))) : nl2br (fgetc($fp)) );
		    clearstatcache();
		}
	        fclose($fp);
	    }
	}
	
?>