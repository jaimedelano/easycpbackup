#!/usr/bin/perl

use strict;
use warnings;
use LWP::UserAgent;

my $file   = 'http://mirrors.hpcf.upr.edu/ftp/pub/Mirrors/CentOS/6.3/isos/i386/CentOS-6.3-i386-LiveCD.iso';
my $header = GetFileSize($file);

if ($header) {
    print "File size: " . $header->content_length . " bytes\n";
    print "Last moified: " . localtime($header->last_modified) . "\n";
}

    sub GetFileSize {
        my $url=shift;
        my $ua = new LWP::UserAgent;
        $ua->agent("Mozilla/5.0");
        my $req = new HTTP::Request 'HEAD' => $url;
        $req->header('Accept' => 'text/html');
        my $res = $ua->request($req);
        if ($res->is_success) {
            my $headers = $res->headers;
            return $headers;
        }
	return 0;
    }
    