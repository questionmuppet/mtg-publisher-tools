<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Admin_Post\Admin_Request_Processor;
use Mtgtools\Exceptions\Admin_Post as Exceptions;

class Admin_Request_Processor_WpTest extends Mtgtools_UnitTestCase
{
    /**
     * Nonce context
     */
    private $nonce_context = 'request_fake_data';

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        wp_get_current_user()->add_cap( 'manage_options' );
        $_REQUEST['_wpnonce'] = wp_create_nonce( $this->nonce_context );
    }

    /**
     * Teardown after class
     */
    static public function tearDownAfterClass() : void
    {
        unset( $_REQUEST['_wpnonce'] );
        parent::tearDownAfterClass();
    }

    /**
     * -----------------------------
     *   A U T H O R I Z A T I O N
     * -----------------------------
     */
    
    /**
     * TEST: Insufficient permissions throws PostHandlerException
     */
    public function testInsufficientPermissionsThrowsPostHandlerException() : void
    {
        $processor = $this->create_processor([ 'capability' => 'fake_cap_too_high' ]);
        
        $this->expectException( Exceptions\PostHandlerException::class );

        $processor->process_request();
    }

    /**
     * TEST: Invalid nonce throws PostHandlerException
     */
    public function testInvalidNonceThrowsPostHandlerException() : void
    {
        $_REQUEST['_wpnonce'] = 'Invalid_Nonce';
        $processor = $this->create_processor();
        
        $this->expectException( Exceptions\PostHandlerException::class );

        $processor->process_request();
    }

    /**
     * TEST: Can process an authorized action
     * 
     * @depends testInsufficientPermissionsThrowsPostHandlerException
     * @depends testInvalidNonceThrowsPostHandlerException
     */
    public function testCanProcessAuthorizedAction() : void
    {
        $processor = $this->create_processor();

        $result = $processor->process_request();

        $this->assertIsArray( $result );
    }
    
    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * TEST: Invalid nonce context throws InvalidArgumentException
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testInvalidNonceContextThrowsInvalidArgumentException() : void
    {
        $processor = $this->create_processor([ 'nonce_context' => 69 ]);

        $this->expectException( \InvalidArgumentException::class );

        $processor->process_request();
    }
    
    /**
     * TEST: Invalid callback throws InvalidArgumentException
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testInvalidCallbackThrowsInvalidArgumentException() : void
    {
        $processor = $this->create_processor([ 'callback' => 'An uncallable string' ]);

        $this->expectException( \InvalidArgumentException::class );

        $processor->process_request();
    }
    
    /**
     * TEST: Can set props at time of processing
     * 
     * @depends testInvalidNonceContextThrowsInvalidArgumentException
     * @depends testInvalidCallbackThrowsInvalidArgumentException
     */
    public function testCanSetPropsAtTimeOfProcessing() : void
    {
        $processor = $this->create_processor([
            'nonce_context' => 'bad_nonce',
            'callback' => 'bad_callback',
        ]);
        
        $result = $processor->process_request([
            'nonce_context' => $this->nonce_context,
            'callback' => array( $this, 'dummy_callback' ),
        ]);

        $this->assertIsArray( $result );
    }

    /**
     * -------------------
     *   C A L L B A C K
     * -------------------
     */

    /**
     * TEST: Bad return value from callback throws UnexpectedValueException
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testBadReturnValueFromCallbackThrowsUnexpectedValueException() : void
    {
        $callback = function( array $args ) {
            return 'An invalid string response';
        };
        $processor = $this->create_processor([ 'callback' => $callback ]);

        $this->expectException( \UnexpectedValueException::class );

        $processor->process_request();
    }

    /**
     * TEST: User args are passed to callback
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testUserArgsPassedToCallback() : void
    {
        $_REQUEST['return_different'] = 1;
        $processor = $this->create_processor([
            'user_args' => array( 'return_different' )
        ]);

        $result = $processor->process_request();

        $this->assertEqualsCanonicalizing( [ 'An alternate result' ], $result );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create processor
     */
    private function create_processor( array $args = [] ) : Admin_Request_Processor
    {
        $args = array_replace([
            'nonce_context' => $this->nonce_context,
            'callback'      => array( $this, 'dummy_callback' ),
        ], $args );
        return new Admin_Request_Processor( $args );
    }
    
    /**
     * Dummy callback function
     */
    public function dummy_callback( array $args = [] ) : array
    {
        $alternate = boolval( $args['return_different'] ?? '' );
        return $alternate
            ? [ 'An alternate result' ]
            : [ 'Some nice, fake result data' ];
    }

}   // End of class