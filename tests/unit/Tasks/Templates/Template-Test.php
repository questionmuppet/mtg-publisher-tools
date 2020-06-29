<?php
declare(strict_types=1);

use Mtgtools\Tasks\Templates\Template;

class TemplateTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can include template file
     */
    public function testCanIncludeTemplateFile() : void
    {
        $template = $this->create_template([
            'path' => 'foo-bar.php',
            'vars' => [],
        ]);

        ob_start();
        $template->include();
        $output = ob_get_clean();
        
        $this->assertContainsSelector( 'div.fake-class', $output, 'Could not find the appropriate markup in the template output.' );
    }

    /**
     * TEST: Template file can access passed variables
     * 
     * @depends testCanIncludeTemplateFile
     */
    public function testTemplateFileCanAccessPassedVariables() : void
    {
        $template = $this->create_template();

        ob_start();
        $template->include();
        $output = ob_get_clean();

        $this->assertContainsSelector( 'div.special-fake-class', $output, 'Failed to assert that selector "div.special-fake-class" appears in the template output.' );
    }

    /**
     * TEST: Query vars are deleted after include statement
     * 
     * @depends testTemplateFileCanAccessPassedVariables
     */
    public function testQueryVarsDeletedAfterIncludeStatement() : void
    {
        $template = $this->create_template();

        ob_start();
        $template->include();
        ob_get_clean();
        $variable = get_query_var( 'div_class', null );

        $this->assertNull( $variable );
    }

    /**
     * Create Template object
     */
    private function create_template( array $params = [] ) : Template
    {
        $params = array_merge([
            'path'     => 'foo-bar-with-vars.php',
            'base_dir' => MTGTOOLS__PATH . 'tests/test-templates/',
            'vars'     => [
                'div_class' => 'special-fake-class',
            ],
        ], $params );
        return new Template( $params );
    }

}   // End of class