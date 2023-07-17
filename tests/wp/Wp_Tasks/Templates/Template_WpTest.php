<?php
declare(strict_types=1);

namespace Mtgtools\Wp_Tasks\Templates
{
    /**
     * WordPress mock functions
     */
    function locate_template( string $path )
    {
        Template_WpTest::$locate_template_count++;

        return MTGTOOLS__ADMIN_SLUG . '/foo-bar-in-theme.php' === $path
            ? Template_WpTest::BASE_DIR . 'theme/foo-bar-in-theme.php'
            : '';
    }

    /**
     * Test class
     */
    class Template_WpTest extends \Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase
    {
        /**
         * Test template directory
         */
        const BASE_DIR = MTGTOOLS__PATH . 'tests/test-templates/';

        /**
         * Function-call counter
         */
        static public $locate_template_count;

        /**
         * Setup
         */
        public function setUp() : void
        {
            parent::setUp();
            self::$locate_template_count = 0;
        }

        /**
         * ---------------
         *   O U T P U T
         * ---------------
         */
    
        /**
         * TEST: Can include template file
         */
        public function testCanIncludeTemplateFile() : void
        {
            $template = $this->create_template();
    
            ob_start();
            $template->include();
            $output = ob_get_clean();
            
            $this->assertContainsSelector( 'div.fake-class', $output, 'Could not find the appropriate markup in the template output.' );
        }
    
        /**
         * TEST: Can get markup as string
         */
        public function testCanGetMarkup() : void
        {
            $template = $this->create_template();
    
            $html = $template->get_markup();
    
            $this->assertIsString( $html );
        }
    
        /**
         * -----------------------
         *   Q U E R Y   V A R S
         * -----------------------
         */
    
        /**
         * TEST: Template file can access passed variables
         * 
         * @depends testCanGetMarkup
         */
        public function testTemplateFileCanAccessPassedVariables() : void
        {
            $template = $this->create_template([
                'path'     => 'foo-bar-with-vars.php',
                'vars'     => [
                    'div_class' => 'special-fake-class',
                ],
            ]);
    
            $html = $template->get_markup();
    
            $this->assertContainsSelector( 'div.special-fake-class', $html, 'Failed to assert that selector "div.special-fake-class" appears in the template output.' );
        }
    
        /**
         * TEST: Query vars are deleted after include statement
         * 
         * @depends testTemplateFileCanAccessPassedVariables
         */
        public function testQueryVarsDeletedAfterIncludeStatement() : void
        {
            $template = $this->create_template([
                'path'     => 'foo-bar-with-vars.php',
                'vars'     => [
                    'div_class' => 'special-fake-class',
                ],
            ]);
    
            $template->get_markup();
            $variable = get_query_var( 'div_class', null );
    
            $this->assertNull( $variable );
        }
    
        /**
         * ---------------------------
         *   T H E M E A B I L I T Y
         * ---------------------------
         */

        /**
         * TEST: Theme override enabled by default
         * 
         * @depends testCanGetMarkup
         */
        public function testThemeOverrideEnabledByDefault() : void
        {
            $template = $this->create_template();

            $template->get_markup();

            $this->assertEquals( 1, self::$locate_template_count, 'Failed asserting that WordPress function locate_template() was called once.' );
        }

        /**
         * TEST: Theme override modifies include path
         * 
         * @depends testThemeOverrideEnabledByDefault
         */
        public function testThemeOverrideModifiesIncludePath() : void
        {
            $template = $this->create_template([
                'path' => 'foo-bar-in-theme.php',
            ]);

            $html = $template->get_markup();

            $this->assertContainsSelector( 'div.fake-theme-class', $html, 'Could not find the theme-defined class in the markup.' );
        }

        /**
         * TEST: Theme override can be disabled
         * 
         * @depends testThemeOverrideModifiesIncludePath
         */
        public function testThemeOverrideCanBeDisabled() : void
        {
            $template = $this->create_template([
                'path'      => 'foo-bar-in-theme.php',
                'themeable' => false,
            ]);

            $html = $template->get_markup();

            $this->assertNotContainsSelector( 'div.fake-theme-class', $html, 'The theme-defined class was found in the markup.' );
        }
    
        /**
         * ---------------------
         *   P R O D U C E R S
         * ---------------------
         */
    
        /**
         * Create Template object
         */
        private function create_template( array $params = [] ) : Template
        {
            $params = array_merge([
                'base_dir' => self::BASE_DIR,
                'path'     => 'foo-bar.php',
            ], $params );
            return new Template( $params );
        }
    
    }   // End of class

}   // End of namespace