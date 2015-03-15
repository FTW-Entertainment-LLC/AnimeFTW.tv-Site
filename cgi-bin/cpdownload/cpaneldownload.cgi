#!/usr/bin/perl
# cpanel12 - cpaneldownload.cgi              Copyright(c) 1997-2006 cPanel, Inc.
#                                                           All Rights Reserved.
# copyright@cpanel.net                                         http://cpanel.net
# This code is subject to the cPanel license. Unauthorized copying is prohibited

eval "use Digest::MD5::File;";
my $has_md5_file = $@ ? 0 : 1;

$| = 1;

print "Content-type: text/html\r\n\r\n";
exit if $> == 0;

$SIG{'ALRM'} = sub {
    print "Backup timeout!\n";
    exit 1;
};

my $homedir = ( getpwuid($>) )[7];
my $user    = ( getpwuid($>) )[0];

if ( !$homedir || !-d $homedir ) {
    print "Unable to gather account information for user's home directory\n";
    exit 1;
}

alarm 5400;

system '/usr/local/cpanel/bin/backupwrap', 'BACKUP', 'homedir';    # Command creates archive in background

sleep 1;

my @archives = glob $homedir . '/backup-*_' . $user . '.tar.gz';
if ( !@archives ) {
    print "Unable to generate transfer archive!\n";
    exit 1;
}

my $archive = pop @archives;

while ( (stat($archive))[7] < 2000 ) {
    print ".\n";
    sleep 1;
}

print "DOWNLOAD READY in $archive\n";
my $md5 = get_file_md5($archive);
if ( $md5 ) {
    print "md5sum is: $md5\n";
}
print "\n";
exit;

sub get_file_md5 {
    my $file = shift;
    return if !$file || !-e $file;
    if ( $has_md5_file ) {
        return Digest::MD5::File::file_md5_hex($file);
    }
    else {
        # Linux
        foreach my $md5sum ( qw( /bin/md5sum /usr/bin/md5sum /usr/local/bin/md5sum /usr/sbin/md5sum /usr/local/sbin/md5sum ) ) {
            if ( -x $md5sum ) {
                my $md5_hex = `$md5sum $file`;
                chomp $md5_hex;
                $md5_hex =~ m/^\s*(\S+)\s+/;
                return $1;
            }
        }
        # BSD
        foreach my $md5sum ( qw( /bin/md5 /usr/bin/md5 /usr/local/bin/md5 /usr/sbin/md5 /usr/local/sbin/md5 ) ) {
            if ( -x $md5sum ) {
                my $md5_hex = `$md5sum $file`;
                chomp $md5_hex;
                $md5_hex =~ m/[=]\s+(\S+)\s*$/;
                return $1;
            }
        }
    }
}