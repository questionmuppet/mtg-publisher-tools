<?php
/**
 * Custom test cases for MTG Publisher Tools plugin
 */

$dir = dirname( __FILE__ ) . '/';

// Test cases
require_once $dir . 'Mtgtools_UnitTestCase.php';
require_once $dir . 'Mtgtools_DashboardTestCase.php';

// Traits
require_once $dir . 'Traits/FunctionCallCounterTrait.php';
require_once $dir . 'Traits/WpRedirectAssertionsTrait.php';

// Exceptions
require_once $dir . 'Exceptions/WpRedirectAttemptException.php';