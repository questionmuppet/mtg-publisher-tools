<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Setting_Section;

class Setting_Section_Test extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const DESCRIPTION = 'A nice, fake description.';

    /**
     * TEST: Can register with WP
     */
    public function testCanRegisterWithWp() : void
    {
        $section = $this->create_section();

        $result = $section->wp_register();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can print description
     */
    public function testCanPrintDescription() : void
    {
        $section = $this->create_section();

        ob_start();
        $section->print_description();
        $html = ob_get_clean();

        $this->assertIsString( $html );
        $this->assertElementContains( self::DESCRIPTION, 'p', $html, 'Did not find the expected description text in the markup.' );
    }

    /**
     * Create section
     */
    private function create_section( array $args = [] ) : Setting_Section
    {
        $args = array_replace([
            'id' => 'fake_id',
            'title' => 'Fake Title',
            'page' => 'fake_page',
            'description' => self::DESCRIPTION,
        ], $args );
        return new Setting_Section( $args );
    }

}   // End of class