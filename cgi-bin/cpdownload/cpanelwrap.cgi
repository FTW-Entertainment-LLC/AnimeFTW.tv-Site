#!/usr/bin/perl
# cpanel12 - cpanelwrap.cgi                  Copyright(c) 1997-2006 cPanel, Inc.
#                                                           All Rights Reserved.
# copyright@cpanel.net                                         http://cpanel.net
# This code is subject to the cPanel license. Unauthorized copying is prohibited

print "Content-type: text/html\r\n\r\n";

$quser = $ENV{'QUERY_STRING'};
$quser =~ s/\n//g;
$quser =~ s/\r//g;

chomp( $pwd = `pwd` );
open( PASSWD, "/etc/passwd" );
while (<PASSWD>) {
    ( $name, $x, $uid, $gid, undef, $homedir, $shell ) = split( /:/, $_ );
    next if ( length($homedir) < 3 );

    if ( $pwd =~ /^${homedir}\// || $pwd =~ /^${homedir}$/ ) {
        $founduid = 1;
        last;
    }
}
close(PASSWD);
if ($founduid) {
    print "MYUID: $uid\n";
}
elsif ( getpwnam($quser) ) {
    $uid = ( getpwnam($quser) )[2];
    print "MYUID: $uid\n";
}

print "REALUID: $>\n";

