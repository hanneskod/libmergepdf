<?php

namespace Bob\BuildConfig;

task('default', ['test', 'phpstan', 'sniff']);

desc('Run unit and feature tests');
task('test', ['phpunit', 'behat']);

desc('Run unit tests');
task('phpunit', function() {
    shell('phpunit');
    println('Unit tests passed');
});

desc('Run behat feature tests');
task('behat', function() {
    shell('behat --stop-on-failure');
    println('Behat feature tests passed');
});

desc('Run statical analysis using phpstan');
task('phpstan', function() {
    shell('phpstan analyze src -l 7');
    println('Phpstan analysis passed');
});

desc('Run php code sniffer');
task('sniff', function() {
    shell('phpcs src tests --standard=PSR2');
    println('Syntax checker passed');
});

desc('Globally install development tools');
task('install_dev_tools', function() {
    shell('composer global require consolidation/cgr');
    shell('cgr phpunit/phpunit');
    shell('cgr behat/behat');
    shell('cgr phpstan/phpstan');
    shell('cgr squizlabs/php_codesniffer');
});

function shell(string $command)
{
    return sh($command, null, ['failOnError' => true]);
}
