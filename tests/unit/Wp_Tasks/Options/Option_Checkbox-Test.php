<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option_Checkbox;

class Option_Checkbox_Test extends WP_UnitTestCase
{
    /**
     * TEST: Can sanitize save value
     */
    public function testCanSanitizeSaveValue() : void
    {
        $opt = $this->create_option();

        $value = $opt->sanitize( ['Tha biz'] );

        $this->assertTrue( $value );
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
    private function create_option( array $args = [] ) : Option_Checkbox
    {
        $args = array_replace([
            'id' => 'fake_option',
            'page' => 'fake_options_page',
        ], $args );
        return new Option_Checkbox( $args );
    }

}   // End of class