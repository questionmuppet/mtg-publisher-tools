<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option_Select;

class Option_Select_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const DEFAULT = 'reasonable_default';
    const OPTIONS = [
        'narf' => 'Narf!',
        'zort' => 'Zort!',
    ];

    /**
     * TEST: Can sanitize save value
     */
    public function testCanSanitizeSaveValue() : void
    {
        $opt = $this->create_option();

        $value = $opt->sanitize( 'poit' );

        $this->assertEquals( self::DEFAULT, $value, 'Failed to assert that an invalid option is reverted to the default value.' );
    }

    /**
     * TEST: Can provide select options via callback
     * 
     * @depends testCanSanitizeSaveValue
     */
    public function testCanProvideOptionsViaCallback() : void
    {
        $opt = $this->create_option([ 'options_callback' => function() {
            return [
                'poit' => 'Poit!',
                'match' => 'Match!',
            ];
        }]);

        $value = $opt->sanitize( 'poit' );

        $this->assertEquals( 'poit', $value, 'Failed to assert that select options could be provided via callback.' );
    }
    
    /**
     * TEST: Can print input
     */
    public function testCanPrintInput() : void
    {
        $opt = $this->create_option();

        ob_start();
        $opt->print_input();
        $html = ob_get_clean();

        $this->assertIsString( $html );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create option
     */
    private function create_option( array $args = [] ) : Option_Select
    {
        $args = array_replace([
            'id' => 'fake_option',
            'page' => 'fake_options_page',
            'options' => self::OPTIONS,
            'default_value' => self::DEFAULT,
        ], $args );
        return new Option_Select( $args );
    }

}   // End of class