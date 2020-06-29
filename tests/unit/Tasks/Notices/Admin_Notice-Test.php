<?php
declare(strict_types=1);

use Mtgtools\Tasks\Notices\Admin_Notice;

class Admin_NoticeTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can output notice markup
     */
    public function testCanOutputNoticeMarkup() : string
    {
        $notice = $this->create_notice([
            'type'        => 'fake',
            'dismissible' => true,
        ]);

        ob_start();
        $notice->print();
        $html = ob_get_clean();

        $this->assertContainsSelector( 'div.notice', $html, 'No WP notice element found in the markup.' );

        return $html;    
    }
    
    /**
     * TEST: Markup contains notice-type class
     * 
     * @depends testCanOutputNoticeMarkup
     */
    public function testMarkupContainsTypeClass( string $html ) : void
    {
        $this->assertContainsSelector( 'div.notice.notice-fake', $html, 'Notice element is missing the notice-type class.' );
    }
    
    /**
     * TEST: Markup contains dismissibility class
     * 
     * @depends testCanOutputNoticeMarkup
     */
    public function testMarkupContainsDismissibilityClass( string $html ) : void
    {
        $this->assertContainsSelector( 'div.notice.is-dismissible', $html, 'Notice element is missing the class to enable user dismissal.' );
    }
    
    /**
     * TEST: Markup contains notice message
     * 
     * @depends testCanOutputNoticeMarkup
     */
    public function testMarkupContainsNoticeMessage( string $html ) : void
    {
        $this->assertElementContains( 'This is an admin notice', 'div.notice', $html, 'Did not find the notice message in the notice body.' );
    }

    /**
     * TEST: Can remove dismissibility in constructor
     * 
     * @depends testCanOutputNoticeMarkup
     */
    public function testCanRemoveDismissibility() : void
    {
        $notice = $this->create_notice([ 'dismissible' => false ]);

        ob_start();
        $notice->print();
        $html = ob_get_clean();

        $this->assertNotContainsSelector( 'div.notice.is-dismissible', $html, 'Dismissibility class could not be removed via constructor args.' );
    }

    /**
     * TEST: Can add title in constructor
     * 
     * @depends testCanOutputNoticeMarkup
     */
    public function testCanAddTitle() : void
    {
        $notice = $this->create_notice([ 'title' => 'A Neat Notice' ]);

        ob_start();
        $notice->print();
        $html = ob_get_clean();

        $this->assertContainsSelector( 'div.notice h2', $html, 'Could not add header element to the markup via constructor args.' );
        $this->assertElementContains( 'A Neat Notice', 'div.notice h2', $html, 'Did not find title string in the header element.' );
    }
    
    /**
     * Create new notice
     */
    private function create_notice( array $args = [] ) : Admin_Notice
    {
        $args = array_merge([
            'message' => 'This is an admin notice',
        ], $args );
        return new Admin_Notice( $args );
    }

}   // End of class