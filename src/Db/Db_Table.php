<?php
/**
 * Db_Table
 * 
 * Exposes a simplified API for querying a custom database table
 */

namespace Mtgtools\Db;

use Mtgtools\Db\Sql_Tokens\Sql_Token;
use Mtgtools\Exceptions\Db as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Db_Table extends Db_Ops
{
    /**
     * Unprefixed table name
     */
    private $table;

    /**
     * Search filters used in WHERE statements
     */
    private $filters = [];

    /**
     * Map of column-key => placeholder
     */
    private $field_types = [];

    /**
     * Constructor
     */
    public function __construct( $db, array $props = [] )
    {
        parent::__construct( $db );
        $this->set_table_props( $props );
    }

    /**
     * -----------------
     *   R E C O R D S
     * -----------------
     */

    /**
     * Check if a record exists in the db
     * 
     * @param array $identifiers    One or more search filters that uniquely identify the record
     */
    public function record_exists( array $identifiers ) : bool
    {
        return (bool) $this->db()->get_var(
            sprintf(
                "SELECT id FROM `%s` WHERE %s",
                $this->get_table_name(),
                $this->where_conditions( $identifiers )
            )
        );
    }
    
    /**
     * Update a table row, or create it if it doesn't exist
     * 
     * @param array $values     Associative array of "column" => "value" attributes to save. Will be escaped.
     * @return int              AUTO_INCREMENT id of newly inserted/updated row
     * @throws SqlErrorException
     */
    public function save_record( array $values ) : int
    {
        $values = $this->sanitize_column_values( $values );
        $columns = array_keys( $values );
        $assignments = array_map(
            function( $col, $val ) {
                return sprintf( '%s = %s', $col, $val );
            },
            $columns,
            $values
        );

        $this->execute_query(
            sprintf(
                "INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), %s;",
                $this->strip_backticks( $this->get_table_name() ),
                implode( ',', $columns ),
                implode( ',', $values ),
                implode( ',', $assignments )
            )
        );
        return $this->db()->insert_id;
    }

    /**
     * Get a single record
     * 
     * @param array $filters    One or more filters to search by
     * @return array            Exactly one record matching filters
     * @throws NoResultsException
     */
    public function get_record( array $filters ) : array
    {
        $results = $this->find_records([
            'filters' => $filters,
            'limit' => 1,
        ]);
        if ( !count( $results ) )
        {
            throw new Exceptions\NoResultsException(
                sprintf(
                    "%s failed to find a requested record. No results found in table '%s' matching the provided search filters.",
                    get_called_class(),
                    $this->get_table_name()
                )
            );
        }
        return $results[0];
    }

    /**
     * Find table records matching filters
     * 
     * @param array $args['filters']        One or more filters to search by
     * @param int $args['limit']            Maximum results to return. Default: No limit
     * @param int $args['offset']           Beginning offset. Default: 0
     * @param bool $args['exact']           Set to false to fuzzy-search. Default: true.
     * @return array                        Zero or more rows. Each row is an associative array keyed by column.
     */
    public function find_records( array $args = [] ) : array
    {
        $filters = $args['filters'] ?? [];
        $limit = $args['limit'] ?? 0;
        $offset = $args['offset'] ?? 0;
        $exact = $args['exact'] ?? true;
        
        return $this->db()->get_results(
            sprintf(
                "SELECT * FROM `%s` %s %s;",
                $this->get_table_name(),
                $this->where_statement( $filters, $exact ),
                $this->limit_statement( $limit, $offset )
            ),
            ARRAY_A
        );
    }

    /**
     * -----------------
     *   F I L T E R S
     * -----------------
     */

    /**
     * Generate WHERE statement
     * 
     * @return string Sanitized WHERE statement or empty string
     */
    public function where_statement( array $filters, bool $exact = true ) : string
    {
        return count( $filters )
            ? sprintf(
                "WHERE %s",
                $this->where_conditions( $filters, $exact )
            )
            : '';
    }

    /**
     * Generate WHERE conditions from filters
     * 
     * @param array $filters    One or more "column" => "value" pairs to use as conditions
     * @param bool $exact       Whether to search for an exact match in string comparisons
     * @return string           Sanitized expression for use in WHERE statement
     */
    protected function where_conditions( array $filters, bool $exact = true ) : string
    {
        $conditions = [];
        foreach ( $filters as $key => $value )
        {
            if ( !$this->is_valid_filter( $key ) )
            {
                throw new \DomainException(
                    sprintf(
                        "%s cannot generate WHERE conditions for table '%s' using unknown filter key '%s'. To search against a column you must first define it as a valid filter key.",
                        get_called_class(),
                        $this->get_table_name(),
                        $key
                    )
                );
            }
            $conditions[] = $this->prepare_conditional_expression( $key, $value, $exact );
        }
        return implode( ' && ', $conditions );
    }
    
    /**
     * Generate sanitized expression to match a single column value
     * 
     * @param string $column    Column in SQL table to compare against
     * @param mixed $value      Value to match
     * @param bool $exact       Set to false to enable LIKE comparison for string fields
     * @return string           Prepared conditional safe for use in WHERE statement
     */
    public function prepare_conditional_expression( string $column, $value, bool $exact = true ) : string
    {
        $column = $this->strip_backticks( $column );
        $comparison = '=';
        $placeholder = $this->get_column_placeholder( $column );

        if ( !$exact && '%s' === $placeholder )
        {
            // Fuzzy comparison
            $comparison = 'LIKE';
            $value = '%' . $this->db()->esc_like( $value ) . '%';
        }

        $expression = sprintf( '`%s` %s %s', $column, $comparison, $placeholder );
        return $this->db()->prepare( $expression, $value );
    }

    /**
     * Generate a LIMIT statement
     * 
     * @return string LIMIT statement sanitized for use in SQL query, empty string if limit = 0
     */
    public function limit_statement( int $limit, int $offset = 0 ) : string
    {
        return $limit > 0
            ? sprintf( 'LIMIT %d OFFSET %d', $limit, absint( $offset ) )
            : '';
    }
    
    /**
     * ---------------------------
     *   S A N I T I Z A T I O N
     * ---------------------------
     */
    
    /**
     * Sanitize column-value pairs for SQL statement
     * 
     * @return array Sanitized "column" => "value" pairs. Columns will be in backticks, values escaped as per wpdb::prepare()
     */
    public function sanitize_column_values( array $raw ) : array
    {
        $sanitized = [];
        foreach ( $raw as $column => $value )
        {
            $key = sprintf(
                '`%s`',
                $this->strip_backticks( $column )
            );
            $placeholder = $this->get_column_placeholder( $column );
            $sanitized[ $key ] = $value instanceof Sql_Token
                ? $this->get_whitelisted_token( 'column_value', $value )
                : $this->db()->prepare( $placeholder, $value );
        }
        return $sanitized;
    }

    /**
     * -----------------
     *   C O L U M N S
     * -----------------
     */

    /**
     * Check if a column key is valid for comparisons
     */
    protected function is_valid_filter( string $key ) : bool
    {
        return in_array( $key, $this->filters );
    }
    
    /**
     * Get format placeholders
     * 
     * @param array $values     Associative array of "column" => "value"
     * @return array            Placeholders mapped to column keys; defaults to %s for undefined field types
     */
    public function get_format_placeholders( array $values ) : array
    {
        $formats = [];
        foreach ( $values as $column => $value )
        {
            $formats[] = $this->get_column_placeholder( $column );
        }
        return $formats;
    }

    /**
     * Get placeholder by column key
     * 
     * @return string Placeholder as defined in $field_types, '%s' if undefined
     */
    public function get_column_placeholder( string $column ) : string
    {
        return $this->field_types[ $column ] ?? '%s';
    }
    
    /**
     * -----------------------
     *   P A R A M E T E R S
     * -----------------------
     */

    /**
     * Get full table name for SQL queries
     */
    public function get_table_name() : string
    {
        if ( !isset( $this->table ) )
        {
            throw new \OutOfRangeException(
                sprintf(
                    "Failed to retreive an undefined table name. You must provide a table definition before executing a database operation with %s.",
                    get_called_class()
                )
            );
        }
        return $this->table;
    }

    /**
     * Set table parameters
     */
    public function set_table_props( array $props ) : void
    {
        foreach ( $props as $key => $value )
        {
            $method = array( $this, "set_{$key}" );
            if ( is_callable( $method ) && !is_null( $value ) )
            {
                call_user_func( $method, $value );
            }
        }
    }

    /**
     * Set table name
     * 
     * @param string $key Unique key for table – will be prepended with db prefix
     */
    public function set_table( string $key ) : void
    {
        $this->table = $this->db()->prefix . $key;
    }

    /**
     * Set search filters
     */
    public function set_filters( array $filters ) : void
    {
        $this->filters = $filters;
    }

    /**
     * Set field types
     */
    public function set_field_types( array $fields ) : void
    {
        $this->field_types = $fields;
    }

}   // End of class