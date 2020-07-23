<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option_Key;

class Option_Key_Test extends WP_UnitTestCase
{
    /**
     * TEST: Can sanitize save value
     */
    public function testCanSanitizeSaveValue() : void
    {
        $opt = $this->create_option();

        $value = $opt->sanitize_save_value( 'Valid_Portion$;*&@!' );

        $this->assertEquals( 'Valid_Portion', $value );
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
    private function create_option( array $args = [] ) : Option_Key
    {
        $args = array_replace([
            'id' => 'fake_option',
        ], $args );
        return new Option_Key( $args );
    }

}   // End of class