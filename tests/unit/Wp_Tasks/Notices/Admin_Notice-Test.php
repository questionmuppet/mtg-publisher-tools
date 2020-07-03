<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Notices\Admin_Notice;

class Admin_NoticeTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can print notice
     */
    public function testCanPrintNotice() : void
    {
        $notice = $this->create_notice();

        ob_start();
        $notice->print();
        $html = ob_get_clean();

        $this->assertIsString( $html );
    }
    
    /**
     * TEST: Can get notice markup
     * 
     * @depends testCanPrintNotice
     */
    public function testCanGetNoticeMarkup() : string
    {
        $notice = $this->create_notice();
        
        $html = $notice->get_markup();
        
        $this->assertContainsSelector( 'div.notice', $html, 'WordPress notice element not found in admin-notice markup.' );
        $this->assertElementContains( 'This is an admin notice', 'div.notice', $html, 'Did not find the notice message in the notice body.' );
        $this->assertContainsSelector( 'div.notice.notice-fake', $html, 'Notice element missing CSS type class in admin-notice markup.' );
        
        return $html;
    }
    
    /**
     * -------------------------
     *   D I S M I S S I B L E
     * -------------------------
     */
    
    /**
     * TEST: Notice is dismissible
     * 
     * @depends testCanGetNoticeMarkup
     */
    public function testNoticeIsDismissible( string $html ) : void
    {
        $this->assertContainsSelector( 'div.notice.is-dismissible', $html, 'Notice element missing CSS class to enable user-dismissal via "x" button.' );
    }
    
    /**
     * TEST: Can remove dismissible via constructor
     * 
     * @depends testNoticeIsDismissible
     */
    public function testCanRemoveDismissible() : void
    {
        $notice = $this->create_notice([ 'dismissible' => false ]);

        $html = $notice->get_markup();

        $this->assertNotContainsSelector( 'div.notice.is-dismissible', $html, 'Could not remove user-dismissal CSS class from notice via constructor args.' );
    }
    
    /**
     * ---------------
     *   P - W R A P
     * ---------------
     */
    /**
     * TEST: Notice contains <p> wrap
     * 
     * @depends testCanGetNoticeMarkup
     */
    public function testNoticeContainsPWrap( string $html ) : void
    {
        $this->assertElementContains( 'This is an admin notice', 'div.notice p', $html, 'Notice element is missing <p> tags.' );
    }
    
    /**
     * TEST: Can remove <p> wrap via constructor
     * 
     * @depends testNoticeContainsPWrap
     */
    public function testCanRemovePWrap() : void
    {
        $notice = $this->create_notice([ 'p_wrap' => false ]);

        $html = $notice->get_markup();

        $this->assertNotContainsSelector( 'div.notice p', $html, 'Could not remove <p>-tag wrap from notice via constructor args.' );
    }

    /**
     * -------------
     *   T I T L E
     * -------------
     */

    /**
     * TEST: Can add title via constructor
     * 
     * @depends testCanGetNoticeMarkup
     */
    public function testCanAddTitle() : void
    {
        $notice = $this->create_notice([ 'title' => 'A Neat Notice' ]);

        $html = $notice->get_markup();

        $this->assertContainsSelector( 'div.notice h2', $html, 'Could not add title element to notice via constructor args.' );
        $this->assertElementContains( 'A Neat Notice', 'div.notice h2', $html, 'Did not find title string in the admin-notice header element.' );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */
    
    /**
     * Create new notice
     */
    private function create_notice( array $args = [] ) : Admin_Notice
    {
        $args = array_merge([
            'message'     => 'This is an admin notice',
            'type'        => 'fake',
            'dismissible' => true,
            'p_wrap'      => true,
        ], $args );
        return new Admin_Notice( $args );
    }

}   // End of class