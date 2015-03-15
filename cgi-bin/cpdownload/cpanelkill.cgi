#!/usr/bin/perl
# cpanel12 - cpanelkill.cgi                  Copyright(c) 1997-2006 cPanel, Inc.
#                                                           All Rights Reserved.
# copyright@cpanel.net                                         http://cpanel.net
# This code is subject to the cPanel license. Unauthorized copying is prohibited

print "Content-type: text/html\r\n\r\n";
$ENV{'QUERY_STRING'} =~ s/\n//g;
$ENV{'QUERY_STRING'} =~ s/\s//g;

my $user = (split( /\&/, $ENV{'QUERY_STRING'}, 2 ))[0];
if ( !$user ) {
    print "INVALID PARAMETERS\n";
    exit 1;
}

my $homedir = ( getpwnam($user) )[7];
if ( !$homedir || !-d $homedir || $homedir eq '/' ) {
    print "INVALID USER\n";
    exit 1;
}

my @account_pkgs = glob $homedir . '/backup-*_' . $user . '.tar.gz';
if ( @account_pkgs ) {
    my $acct_pkg = pop @account_pkgs; # Latest backup
    if ( unlink $acct_pkg ) {
        print "UNLINKED\n";
    }
}
else {
    print "NO VALID ACCOUNT PACKAGES EXIST\n";
}

system '/bin/rm', '-rvf', $homedir . '/public_html/cgi-bin/cpdownload';

exit;
