<?php

namespace Hodgy\Ec2IpFinder;

class Ec2IpFinder
{
    /*
     * Examples of the following constants in the various configurations they can be in
     *
     * releases (phar):
     * const VERSION = '1.8.2';
     * const RELEASE_DATE = '2019-01-29 15:00:53';
     * const SOURCE_VERSION = '';
     *
     *
     * source (git clone):
     * const VERSION = '@package_version@';
     * const RELEASE_DATE = '@release_date@';
     * const SOURCE_VERSION = '1.8-dev+source';
     */
    const VERSION = '@package_version@';
    const SOURCE_VERSION = '0.0.1-dev+source';

    public static function getVersion()
    {
        // no replacement done, this must be a source checkout
        if (self::VERSION === '@package_version'.'@') {
            return self::SOURCE_VERSION;
        }

        return self::VERSION;
    }
}
