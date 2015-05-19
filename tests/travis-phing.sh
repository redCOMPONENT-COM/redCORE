#!/bin/bash
#-----------------------------------------------------------
#
# Purpose: Run phing in a travis environment
#
# Target system: travis-ci
#-----------------------------------------------------------

if [[ $TRAVIS_PHP_VERSION != 'hhvm-nightly' && $TRAVIS_PHP_VERSION != 'hhvm' ]]; then
    echo -e "\nAuto-discover pear channels and upgrade ..."
    pear config-set auto_discover 1
    pear -qq channel-update pear.php.net
    pear -qq channel-discover pear.phing.info
    echo "... OK"
fi

sudo apt-get install python-docutils

if [[ $TRAVIS_PHP_VERSION < 5.3 ]]; then
    pear upgrade pecl.php.net/Phar ||
        pear install pecl.php.net/Phar

    echo -e "\nInstalling / upgrading phpcs ... "
    which phpcs >/dev/null                             &&
        pear upgrade pear.php.net/PHP_CodeSniffer ||
        pear install pear.php.net/PHP_CodeSniffer
    phpenv rehash
    # re-test for phpcs:
    phpcs --version 2>&1 >/dev/null   &&
        echo "... OK"
       
    echo -e "\nInstalling / upgrading phpdepend ... "
    which pdepend >/dev/null                      &&
        pear upgrade pear.pdepend.org/PHP_Depend-1.1.0 ||
        pear install pear.pdepend.org/PHP_Depend-1.1.0
   
    echo -e "\nInstalling PEAR packages ... "
    pear install pear/XML_Serializer-beta
    pear install --alldeps PEAR_PackageFileManager
    pear install --alldeps PEAR_PackageFileManager2
    pear install Net_Growl
    pear install HTTP_Request2
    pear install VersionControl_SVN-alpha
    pear install VersionControl_Git-alpha
else
    echo -e "\nInstalling composer packages ... "
    ./composer.phar selfupdate --quiet
    ./composer.phar install -o --no-progress
fi
