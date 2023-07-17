<?php

namespace Mtgtools\Tests\TestCases\Exceptions;

use RuntimeException;

/**
 * Thrown when a call to the WordPress wp_redirect() method is intercepted.
 */
class WpRedirectAttemptException extends RuntimeException
{
    
}
