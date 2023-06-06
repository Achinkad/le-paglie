#!/usr/bin/perl

use strict;
use warnings;

my $directory = '/home/rasp/Desktop/photos';
my $python_script = '/home/rasp/Desktop/face_rec.py';
my %file_hashes;

while (1) {
    opendir(my $dh, $directory) or die "Failed to open directory: $!";
    my @files = grep { -f "$directory/$_" } readdir($dh);
    closedir($dh);

    my %current_hashes;
    foreach my $file (@files) {
        my $file_path = "$directory/$file";
        my $file_hash = `md5sum $file_path`;
        chomp($file_hash);
        $current_hashes{$file} = $file_hash;
    }

    my $has_changes = 0;
    foreach my $file (keys %current_hashes) {
        if (!exists $file_hashes{$file} || $file_hashes{$file} ne $current_hashes{$file}) {
            $has_changes = 1;
            last;
        }
    }

    if ($has_changes) {
        # Kill the Python script if it's already running
        my $pid = `pgrep -f $python_script`;
        if ($pid) {
            kill('TERM', $pid);
            waitpid($pid, 0);
        }

        # Start the Python script
        my $new_pid = fork();
        die "Failed to fork: $!" unless defined $new_pid;

        if ($new_pid == 0) {
            # Child process
            exec("python3", $python_script) or die "Failed to start Python script: $!";
        } else {
            # Parent process
            print "Python script restarted. New PID: $new_pid\n";
        }

        %file_hashes = %current_hashes;
    }

    # Sleep for 1 second before checking again
    sleep(1);
}
