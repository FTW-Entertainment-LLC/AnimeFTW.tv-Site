#!/usr/bin/perl
# cpanel12 - cpaneldownacct.cgi              Copyright(c) 1997-2006 cPanel, Inc.
#                                                           All Rights Reserved.
# copyright@cpanel.net                                         http://cpanel.net
# This code is subject to the cPanel license. Unauthorized copying is prohibited

$ENV{'QUERY_STRING'} =~ s/\n//g;
$ENV{'QUERY_STRING'} =~ s/\s//g;

my $user = (split( /\&/, $ENV{'QUERY_STRING'}, 2 ))[0];
if ( !$user ) {
    print "Content-type: text/html\r\n\r\n";
    print "INVALID PARAMETERS\n";
    exit 1;
}

my $homedir = ( getpwnam($user) )[7];
if ( !$homedir || !-d $homedir || $homedir eq '/' ) {
    print "Content-type: text/html\r\n\r\n";
    print "INVALID USER\n";
    exit 1;
}

my $acct_pkg;
my @account_pkgs = glob $homedir . '/backup-*_' . $user . '.tar.gz';
if ( @account_pkgs ) {
    $acct_pkg = pop @account_pkgs; # Latest backup
}
else {
    print "Content-type: text/html\r\n\r\n";
    print "NO VALID ACCOUNT PACKAGES EXIST\n";
    exit 1;
}

if ( !-e $acct_pkg ) {
    print "Content-type: text/html\r\n\r\n";
    print "INVALIDE ACCOUNT PACKAGE\n";
    exit 1;
}

if ( open my $pkg_fh, '<', $acct_pkg ) {
    print "Content-type: application/x-tar\r\nContent-Encoding: x-gzip\r\n\r\n";
    while ( readline $pkg_fh ) {
        print;
    }
    close $pkg_fh;
    exit;
}
else {
    print "Content-type: text/html\r\n\r\n";
    print "UNABLE TO OPEN ARCHIVE: $!\n";
}

exit 1;
