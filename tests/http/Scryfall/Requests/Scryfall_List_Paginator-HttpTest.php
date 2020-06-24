<?php
declare(strict_types=1);
use Mtgtools\Scryfall\Requests\Scryfall_List_Paginator;

class Scryfall_List_Paginator_HttpTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can check for more pages
     */
    public function testCanCheckHasMore() : Scryfall_List_Paginator
    {
        $paginator = $this->create_paginator( 'multiple' );

        $result = $paginator->has_more();

        $this->assertTrue( $result );

        return $paginator;
    }

    /**
     * TEST: Can get first page
     * 
     * @depends testCanCheckHasMore
     */
    public function testCanGetFirstPage( Scryfall_List_Paginator $paginator ) : Scryfall_List_Paginator
    {
        $result = $paginator->get_next_page();

        $this->assertIsArray( $result );

        return $paginator;
    }

    /**
     * TEST: Can get second page
     * 
     * @depends testCanGetFirstPage
     */
    public function testCanGetSecondPage( Scryfall_List_Paginator $paginator ) : void
    {
        $result = $paginator->get_next_page();

        $this->assertIsArray( $result );
    }

    /**
     * TEST: Can get total count
     * 
     * @depends testCanGetFirstPage
     */
    public function testCanGetTotalCount( Scryfall_List_Paginator $paginator ) : int
    {
        $result = $paginator->get_total_count();

        $this->assertIsInt( $result );

        return $result;
    }
    
    /**
     * TEST: Can get single page list with one call
     */
    public function testCanGetSinglePageListWithOneCall() : Scryfall_List_Paginator
    {
        $paginator = $this->create_paginator( 'single' );

        $result = $paginator->get_full_list();

        $this->assertIsArray( $result );
        
        return $paginator;
    }

    /**
     * TEST: Requesting unavailable page throws LogicException
     * 
     * @depends testCanGetSinglePageListWithOneCall
     */
    public function testRequestingUnavailablePageThrowsLogicException( Scryfall_List_Paginator $paginator ) : void
    {
        $this->expectException( \LogicException::class );

        $result = $paginator->get_next_page();
    }

    /**
     * TEST: Can get multiple page list with one call
     */
    public function testCanGetMultiplePageListWithOneCall() : Scryfall_List_Paginator
    {
        $paginator = $this->create_paginator( 'multiple' );

        $result = $paginator->get_full_list();

        $this->assertIsArray( $result );

        return $paginator;
    }

    /**
     * TEST: Page has correct data
     * 
     * @depends testCanGetMultiplePageListWithOneCall
     * @depends testCanGetTotalCount
     */
    public function testPageHasCorrectData( Scryfall_List_Paginator $paginator, int $total ) : void
    {
        $result = $paginator->get_full_list();

        $this->assertCount( $total, $result );
        $this->assertEquals( 'card', $result[0]['object'] ?? '' );
    }

    /**
     * TEST: Can get data out of order
     * 
     * @depends testPageHasCorrectData
     */
    public function testCanGetDataOutOfOrder() : void
    {
        $paginator = $this->create_paginator( 'multiple' );
        $total = $paginator->get_total_count();
        $paginator->get_next_page();
        $list = $paginator->get_full_list();

        $this->assertIsInt( $total );
        $this->assertCount( $total, $list );
        $this->assertEquals( 'card', $list[0]['object'] ?? '' );
    }

    /**
     * Create new paginator object
     */
    private function create_paginator( string $type ) : Scryfall_List_Paginator
    {
        $endpoints = [
            'single'   => 'symbology',
            'multiple' => 'cards/search?q=c%3Awhite+cmc%3D1',
        ];
        return new Scryfall_List_Paginator([
            'endpoint' => $endpoints[ $type ],
        ]);
    }

}   // End of class