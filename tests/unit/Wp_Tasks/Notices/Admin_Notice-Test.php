<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Notices\Admin_Notice;

class Admin_Notice_Test extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const NOTICE_MESSAGE = 'This is an admin notice';
    const NOTICE_TYPE = 'foo_bar';
    const NOTICE_TITLE = 'A Neat Notice';

    /**
     * Dummy buttons
     */
    const BUTTON_ONE = [
        'label' => 'Narf',
        'href' => 'http://example.org/narf/'
    ];
    const BUTTON_TWO = [
        'label' => 'Zort',
        'href' => 'http://example.org/zort/',
    ];

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
        $this->assertElementContains( self::NOTICE_MESSAGE, 'div.notice', $html, 'Did not find the notice message in the notice body.' );
        $this->assertContainsSelector( 'div.notice.notice-' . self::NOTICE_TYPE, $html, 'Notice element missing CSS type class in admin-notice markup.' );
        
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
        $this->assertElementContains( self::NOTICE_MESSAGE, 'div.notice p', $html, 'Notice element is missing <p> tags.' );
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
        $notice = $this->create_notice([ 'title' => self::NOTICE_TITLE ]);

        $html = $notice->get_markup();

        $this->assertContainsSelector( 'div.notice h2', $html, 'Could not add title element to notice via constructor args.' );
        $this->assertElementContains( self::NOTICE_TITLE, 'div.notice h2', $html, 'Did not find title string in the admin-notice header element.' );
    }

    /**
     * -----------
     *   L I S T
     * -----------
     */

    /**
     * TEST: Can add list markup
     * 
     * @depends testCanGetNoticeMarkup
     */
    public function testCanAddListMarkup() : void
    {
        $notice = $this->create_notice([
            'list' => [
                'First line',
                'Second line',
            ]
        ]);

        $html = $notice->get_markup();
        
        $this->assertSelectorCount( 2, 'div.notice li', $html, 'Failed to find two list items in the admin-notice markup.' );
        $this->assertElementContains( 'Second line', 'div.notice li', $html, 'Failed to find the list item text in the admin-notice markup.' );
    }

    /**
     * -----------------
     *   B U T T O N S
     * -----------------
     */

    /**
     * TEST: Can add button links
     * 
     * @depends testCanGetNoticeMarkup
     */
    public function testCanAddButtonLinks() : string
    {
        $notice = $this->create_notice([
            'buttons' => array( self::BUTTON_ONE, self::BUTTON_TWO ),
        ]);

        $html = $notice->get_markup();

        $this->assertSelectorCount( 2, 'div.notice a.button', $html,
            'Could not find the correct number of button links in the admin-notice markup.'
        );

        return $html;
    }
    
    /**
     * TEST: Button link contains href and label
     * 
     * @depends testCanAddButtonLinks
     */
    public function testButtonLinkIsCorrectlyFormed( string $html ) : string
    {
        $selector = sprintf( 'div.notice a[href="%s"]', self::BUTTON_ONE['href'] );
        
        $this->assertContainsSelector(
            $selector,
            $html,
            'Could not find the href attribute for a button link in the admin-notice markup.'
        );
        $this->assertElementContains(
            self::BUTTON_ONE['label'],
            $selector,
            $html,
            'Could not find the label text for a button link in the admin-notice markup.'
        );

        return $html;
    }
    
    /**
     * TEST: Button links have correct classes
     * 
     * @depends testButtonLinkIsCorrectlyFormed
     */
    public function testButtonLinksHaveCorrectClasses( string $html ) : void
    {
        $this->assertElementContains(
            self::BUTTON_ONE['label'],
            'div.notice a.button-primary',
            $html,
            'Could not find the "button-primary" CSS class on the first button in the admin-notice markup.'
        );
        $this->assertElementNotContains(
            self::BUTTON_TWO['label'],
            'div.notice a.button-primary',
            $html,
            'The "button-primary" CSS class was found on the second button in the admin-notice markup.'
        );
    }

    /**
     * TEST: Primary class on button links can be overridden
     * 
     * @depends testButtonLinksHaveCorrectClasses
     */
    public function testCanOverridePrimaryClass() : void
    {
        $buttons = array( self::BUTTON_ONE, self::BUTTON_TWO );
        $buttons[0]['primary'] = false;
        $buttons[1]['primary'] = true;

        $notice = $this->create_notice([ 'buttons' => $buttons ]);

        $html = $notice->get_markup();

        $this->assertElementContains(
            self::BUTTON_TWO['label'],
            'div.notice a.button-primary',
            $html,
            'Failed to assert that the "button-primary" CSS class could be turned on via constructor args.'
        );
        $this->assertElementNotContains(
            self::BUTTON_ONE['label'],
            'div.notice a.button-primary',
            $html,
            'Failed to assert that the "button-primary" CSS class could be turned off via constructor args.'
        );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */
    
    /**
     * Create new notice
     */
    private function create_notice( array $args = [] ) : Admin_Notice
    {
        $args = array_replace([
            'message'     => self::NOTICE_MESSAGE,
            'type'        => self::NOTICE_TYPE,
            'dismissible' => true,
            'p_wrap'      => true,
        ], $args );
        return new Admin_Notice( $args );
    }

}   // End of class