<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option_Factory;
use Mtgtools\Wp_Tasks\Options;

class Option_Factory_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const ID = 'fake_option';
    const PAGE = 'fake_options_page';

    /**
     * Factory object
     */
    private $factory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->factory = new Option_Factory();
    }

    /**
     * TEST: Can create text option
     */
    public function testCanCreateTextOption() : void
    {
        $opt = $this->factory->create_option([
            'type' => 'text',
            'id' => self::ID,
            'page' => self::PAGE,
        ]);

        $this->assertInstanceOf( Options\Option_Text::class, $opt );
    }

    /**
     * TEST: Can create key option
     */
    public function testCanCreateKeyOption() : void
    {
        $opt = $this->factory->create_option([
            'type' => 'key',
            'id' => self::ID,
            'page' => self::PAGE,
        ]);

        $this->assertInstanceOf( Options\Option_Key::class, $opt );
    }

    /**
     * TEST: Can create number option
     */
    public function testCanCreateNumberOption() : void
    {
        $opt = $this->factory->create_option([
            'type' => 'number',
            'id' => self::ID,
            'page' => self::PAGE,
        ]);

        $this->assertInstanceOf( Options\Option_Number::class, $opt );
    }

    /**
     * TEST: Can create checkbox option
     */
    public function testCanCreateCheckboxOption() : void
    {
        $opt = $this->factory->create_option([
            'type' => 'checkbox',
            'id' => self::ID,
            'page' => self::PAGE,
        ]);

        $this->assertInstanceOf( Options\Option_Checkbox::class, $opt );
    }

    /**
     * TEST: Can create select option
     */
    public function testCanCreateSelectOption() : void
    {
        $opt = $this->factory->create_option([
            'type' => 'select',
            'id' => self::ID,
            'page' => self::PAGE,
        ]);

        $this->assertInstanceOf( Options\Option_Select::class, $opt );
    }

}   // End of class