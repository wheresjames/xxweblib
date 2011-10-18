<?php
/*------------------------------------------------------------------
//
// Copyright (c) 1997-2011
// Robert Umbehant, Oliver Dodd
// xxweblib@wheresjames.com
// https://code.google.com/p/xxweblib/
//
// Redistribution and use in source and binary forms, with or
// without modification, are permitted for commercial and
// non-commercial purposes, provided that the following
// conditions are met:
//
// * Redistributions of source code must retain the above copyright
//   notice, this list of conditions and the following disclaimer.
// * The names of the developers or contributors may not be used to
//   endorse or promote products derived from this software without
//   specific prior written permission.
//
//   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
//   CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
//   INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
//   MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
//   DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
//   CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
//   SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
//   NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
//   LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
//   HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
//   CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
//   OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
//   EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//----------------------------------------------------------------*/


//------------------------------------------------------------------
// Config
//------------------------------------------------------------------

	// Global config array
	static $g_xc_cfg = array();
	
	/** Writes a value into the global config array
		@param [in] $k		Key value
		@param [in] $v		Value to save into the array
	*/
	function xc_set( $k, $v )
	{	global $g_xc_cfg;
		$g_xc_cfg[ $k ] = $v;
	}

	/** Reads a value from the global config array
		@param [in] $k		Key value to read
		@param [in] $d		Default value to use if $k is not found
	*/
	function xc_get( $k, $d = '' )
	{	global $g_xc_cfg;
		return isset( $g_xc_cfg[ $k ] ) ? $g_xc_cfg[ $k ] : $d;
	}

//------------------------------------------------------------------
// Strings
//------------------------------------------------------------------


	/** Encodes a string for an html page
		
		@param [in] $s		String to encode
		
		@notice This mainly exists to work around a bug with closures
				in some versions of php.
		
		@return Returns encoded string
	*/
	function xs_html( $s )
	{
		return htmlentities( $s, ENT_QUOTES ); 
	}

	/** Applies template to a string value
	
		@param [in]	$s		string
		@param [in]	$tmpl	template to apply
		@param [in] $sf		string encoding function
		@param [in] $def	default value to return if $s is empty
		@param [in] $sr		String replace token
		
		Example:
			
		@code

			// Outputs 'Hello World!'
			echo xs_tmpl( "Hello", "$s World!" );

		@endcode

	*/
	function xs_tmpl( $s, $tmpl, $sf = 0, $def = '', $sr = '$s' )
	{	
		if ( !is_string( $s ) || !strlen( $s ) )
			return $def;
		return str_replace( $sr, $sf ? xf_call( $sf, $s ) : $s, $tmpl );
	}

	/** Limits the maximum length of a string
	
		@param [in]	$s		string
		@param [in] $max	max string length
		@param [in] $trail	string to append on truncation
	*/
	function xs_limit( $s, $max, $trail = '' )
	{
		if ( strlen( $s ) <= $max )
			return $s;

		if ( $max <= $trail )
			$trail = "";

		return substr( $s, 0, $max - strlen( $trail ) ) . $trail;
	}


//------------------------------------------------------------------
// Functions
//------------------------------------------------------------------

	/** Calls function or function array $f with optional arguments

		@param [in]	$f		a function or array of functions
		@param [in]	...		arguments to $f

		@return Result of function call(s)
	*/
	function xf_call( $f )
	{
		// Get parameters
		$a = func_get_args(); array_shift( $a );
		if ( !count( $a ) ) 
			return null;

		// Single function
		if ( !is_array( $f ) || is_callable( $f ) )
			return is_callable( $f ) ? call_user_func_array( $f, $a ) : $a[ 0 ];

		// Function array
		$i = 0;
		foreach( $f as $v )
			$a = is_callable( $v ) ? ( !$i++ ? call_user_func_array( $v, $a ) : call_user_func( $v, $a ) ) : $a[ 0 ];

		return $a;
	}

//------------------------------------------------------------------
// Arrays
//------------------------------------------------------------------

	/** Returns it's input
		
		@param [in]	$v	Value to reteurn
		
		@return Returns $v
	*/
	function xa_nop( $v ) { return $v; }

	/** Returns value of $k in array $a or $d if it doesn't exist
	
		@param [in] $a		Array
		@param [in] $k		Key in array
		@param [in] $d		Default value
		@param [in] $f		Optional encoding function
	*/
	function xa( $a, $k, $d = '', $f = 0 )
	{
		$v = ( is_array( $a ) && isset( $a[ $k ] ) ) ? $a[ $k ] : $d;
		return $f ? xf_call( $f, $v ) : $v;
	}

	/** Returns value of $k2 in $k1 in array $a or $d if it doesn't exist
	
		@param [in] $a		Array
		@param [in] $k1		Key in array $a
		@param [in] $k2		Key in array $k1
		@param [in] $d		Default value
		@param [in] $f		Optional encoding function
	*/
	function xaa( $a, $k1, $k2, $d = '', $f = 0 )
	{
		return xa( xa( $a, $k1 ), $k2, $d, $f );
	}

	/** Returns value of $k in array $a or $d if it doesn't exist html encoded
	
		@param [in] $a		Array
		@param [in] $k		Key in array
		@param [in] $d		Default value
	*/
	function xa_html( &$a, $k, $d = '' )
	{
		return xs_html( ( is_array( $a ) && isset( $a[ $k ] ) ) ? $a[ $k ] : $d );
	}

	/** Returns non-zero if $k in $a is equal to $v
	
		@param [in] $a		Array
		@param [in] $k		Key in array
		@param [in] $v		Value

	*/
	function xa_equ( $a, $k, $v )
	{
		return ( is_array( $a ) && isset( $a[ $k ] ) && ( $a[ $k ] === $v ) ) ? true : false;
	}

	/** Returns non-zero if $k is in $a
	
		@param [in] $a		Array
		@param [in] $k		Key in array

	*/
	function xa_in( $a, $k )
	{
		return ( is_array( $a ) && isset( $a[ $k ] ) ) ? true : false;
	}

	/** Returns the size of $k in $a
	
		@param [in] $a		Array
		@param [in] $k		Key in array

	*/
	function xa_sz( $a, $k = null )
	{
		if ( $k === null ) 
			return is_array( $a ) ? count( $a ) : strlen( $a );
		if ( !is_array( $a ) || !isset( $a[ $k ] ) ) 
			return 0;
		return is_array( $a[ $k ] ) ? count( $a[ $k ] ) : strlen( $a[ $k ] );
	}


	/** Returns non-zero if $k in $a is not zero
	
		@param [in] $a		Array
		@param [in] $k		Key in array, or array of keys to check

	*/
	function xa_nz( $a, $k = null )
	{
		if ( $k === null || !is_array( $k ) )	
			return xa_sz( $a, $k ) ? true : false;
		foreach( $k as $v )
			if ( !xa_sz( $a, $v ) )
				return false;
		return true;
	}

	/** Returns non-zero if $k in $a is zero
	
		@param [in] $a		Array
		@param [in] $k		Key in array, or array of keys to check

	*/
	function xa_z( $a, $k = null )
	{	
		return !xa_nz( $a, $k ) ? true : false; 
	}

	/** Check for associative array
		
		@param [in]	$a	Value to reteurn
		
		@return Returns true if passed an associative array 
	*/
	function xa_isAssoc( $a )
	{
		if ( is_array( $a ) )
			foreach ( array_keys( $a ) as $k => $v ) 
				if ( $k !== $v ) return true;
		return false;
	}

	/** Limits the maximum length of all strings in an array
	
		@param [in]	$a		array
		@param [in] $max	max string length
		@param [in] $trail	string to append on truncation
	*/
	function xa_limit( &$a, $max, $trail = '' )
	{	$lt = strlen( $trail );
		foreach( $a as $k=>$v )
			if ( is_string( $v ) && strlen( $v ) > $max )
			{	$t = ( $max <= $lt ) ? "" : $trail;
				$a[ $k ] = substr( $v, 0, $max - strlen( $t ) ) . $t;
			} // end if
		return $a;
	}

	/** Applies template to array
		@param [in]	$a		an array
		@param [in]	$tmpl	template to apply
		@param [in]	$join	string used to join multiple items
		@param [in]	$kf		optional function to encode key
		@param [in]	$vf		optional function to encode value
		@param [in]	$def	default value to return
		@param [in]	$kr		string in template to replace with key
		@param [in]	$vr		string in template to replace with value
		
		Examples:
		
		@code

			// Urlencode array
			xa_join( $a, '$k=$v', '&', 'urlencode' );

			// Create escaped MySQL query
			xa_join( $a, '`$k`=\'$v\'', ' AND ', 'mysql_real_escape_string' );

		@endcode
	*/
	function xa_join( $a, $tmpl, $join, $kf = 0, $vf = 0, $def = '', $kr = '$k', $vr = '$v' )
	{
		if ( !is_array( $a ) || !count( $a ) )
			return $def;

		$i = 0; 
		if ( $kf && !$vf ) 
			$vf = $kf;

		if ( xa_isAssoc( $a ) )
			foreach( $a as $k=>$v ) 
				$def .= ( $i++ ? $join : '' ) 
						. str_replace( $kr, 
									   $kf ? xf_call( $kf, $k ) : $k, 
									   str_replace( $vr, $vf ? xf_call( $vf, $v ) : $v, $tmpl ) );

		else
			foreach( $a as $v ) 
				$def .= ( $i++ ? $join : '' ) 
						. str_replace( $vr, $vf ? xf_call( $vf, $v ) : $v, $tmpl );

		return $def;
	}

	/** Applies template to the specified array value
		@param [in]	$s		string
		@param [in]	$tmpl	template to apply
		@param [in] $sf		string encoding function
		@param [in] $def	default value to return if $s is empty
		@param [in] $sr		String replace token
	*/
	function xa_tmpl( $a, $k, $tmpl, $vf = 0, $def = '', $vr = '$v' )
	{
		if ( !is_array( $a ) || !isset( $a[ $k ] ) )
			return $def;

		return xs_tmpl( $a[ $k ], $tmpl, $vf, $def, $vr );
	}
	
	/** Substitute array values in template

		@param [in]	$a		an array
		@param [in]	$tmpl	template to apply
		@param [in]	$kf		optional function to apply to key
		@param [in]	$vf		optional function to encode value
		@param [in]	$pre	prefix that identifies replace token

		Example:
		
		@code

			// Outputs 'b,d'
			xa_sub( array( 'a'=>'b', 'c'=>'d' ), '$a,$c' );

			// Display database record
			xa_sub( array( 'name'=>'Bob', 'desc'=>'Humanoid' ), "<td>$name</td><td>$desc</td>", 
				    0, function( $v ){ return htmlentities( $v, ENT_QUOTES ); } );

		@endcode
		
		@return String with substituted values
	*/
	function xa_sub( $a, $tmpl, $kf = 0, $vf = 0, $pre = '$' )
	{
		if ( !xa_isAssoc( $a ) || !count( $a ) )
			return $tmpl;

		foreach( $a as $k=>$v ) 
			$tmpl = str_replace( $pre.( $kf ? xf_call( $kf, $k ) : $k ), 
								 $vf ? xf_call( $vf, $v ) : $v, 
								 $tmpl );
		return $tmpl;
	}

	/** Extracts specified keys from array

		@param [in]	$a		an array
		@param [in]	$ka		array of keys or comma separated string
		@param [in]	$vf		optional function to encode value
		@param [in]	$def	default value to add if non exist

		@return Array containing extracted keys
	*/
	function xa_extract( $a, $ka, $vf = 0, $def = null )
	{
		if ( !xa_isAssoc( $a ) || !count( $a ) )
			return array();

		// Create array of the values we want if needed
		if ( !is_array( $ka ) )
			$ka = explode( ',', $ka );

		// Create an array of wanted values
		$r = array();
		foreach( $ka as $k )
			if ( isset( $a[ $k ] ) )
				$r[ $k ] = $vf ? ( xf_call( $vf, $a[ $k ] ) ) : $a[ $k ];
			else if ( $def !== null )
				$r[ $k ] = $def;

		return $r;
	}

	/** Filters specified keys from array

		@param [in]	$a		an array
		@param [in]	$ka		array of keys or comma separated string
		@param [in]	$vf		optional function to encode value

		@return Array containing all but filtered keys
	*/
	function xa_filter( $a, $ka, $vf = 0 )
	{
		if ( !xa_isAssoc( $a ) || !count( $a ) )
			return array();

		// Create a map of the values we don't want
		if ( !xa_isAssoc( $ka ) )
		{	if ( !is_array( $ka ) )
				$ka = explode( ',', $ka );
			$t = array();
			foreach( $ka as $k )
				$t[ $k ] = '';
			$ka = $t;
		} // end if

		// Filter the array
		$r = array();
		foreach( $a as $k => $v )
			if ( !isset( $ka[ $k ] ) )
				$r[ $k ] = $vf ? xf_call( $vf, $v ) : $v;

		return $r;
	}

	/** Extracts keys from array with matching values

		@param [in]	$a		an array
		@param [in]	$va		value or array of values
		@param [in]	$vf		optional function to encode value

		@return Array containing all but filtered keys
	*/
	function xa_extract_by_value( $a, $va, $vf = 0 )
	{
		if ( !xa_isAssoc( $a ) || !count( $a ) )
			return array();

		$r = array();
		if ( !is_array( $va ) )
		{	foreach( $a as $k => $v )
				if ( $va == $v )
					$r[ $k ] = $vf ? xf_call( $vf, $v ) : $v;
		} // end if
		else
			foreach( $a as $k => $v )
				foreach( $va as $vm )
					if ( $vm == $v )
						$r[ $k ] = $vf ? xf_call( $vf, $v ) : $v;

		return $r;
	}

	/** Filters keys from array with matching values

		@param [in]	$a		an array
		@param [in]	$va		value or array of values
		@param [in]	$vf		optional function to encode value

		@return Array containing all but filtered keys
	*/
	function xa_filter_by_value( $a, $va, $vf = 0 )
	{
		// Build an array of keys we will remove
		$t = xa_extract_by_value( $a, $va );

		// Build the desired array
		$r = array();
		foreach( $a as $k => $v )
			if ( !isset( $t[ $k ] ) )
				$r[ $k ] = $vf ? xf_call( $vf, $v ) : $v;

		return $r;
	}

	/** Merge two arrays with an optional recursive flag
	
		@param $a1		array to be merged into
		@param $a2		merging array
		@param $r		recursive flag
	*/
	function xa_merge( $a1, $a2, $r = true )
	{
		// Ensure two valid arrays
		$a = ( is_array( $a1 ) ) ? $a1 : array();
		if ( !is_array( $a2 ) ) 
			return $a;

		// Merge the arrays
		foreach ( $a2 as $k => $v )
			if ( $r && is_array( xa( $a1, $k ) ) && is_array( $v ) )
				$a[ $k ] = xa_merge( $a1[ $k ], $v , true );
			else 
				$a[ $k ] = $v;

		return $a;
	}


//------------------------------------------------------------------
// include
//------------------------------------------------------------------

	/// Global variable holding current page parameters
	static $g_xi_incparams = array();

	/** Retrieves the specified value from the current page parameters
		
		@param [in] $k		Parameter to retrieve
		@param [in] $d		Default value to return if the key does 
							not exist
		@param [in] $f		Optional encoding function
	*/
	function xi( $k, $d = '', $f = 0 ) 
	{ 	global $g_xi_incparams;
		return xa( $g_xi_incparams, $k, $d, $f );
	}

	/** Retrieves the specified value from an array in the current page parameters
		
		@param [in] $k1		Parameter to retrieve
		@param [in] $k2		Key in parameter $k1 to retrieve
		@param [in] $d		Default value to return if the key does 
							not exist
		@param [in] $f		Optional encoding function
	*/
	function xii( $k1, $k2, $d = '', $f = 0 ) 
	{
		return xa( xi( $k1 ), $k2, $d, $f ); 
	}

	/// Returns the entire page parameter array
	function xi_pa() 
	{
		global $g_xi_incparams;
		return $g_xi_incparams; 
	}

	/** Includes the specified file and pushes any parameters onto the page stack
		
		@param [in] $f		File to include
		@param [in] ...		All other parameters pushed onto the page stack
		
		@return Returns the output form the page
	*/
	function xi_include( $f ) 
	{
		global $g_xi_incparams;
	
		// Save previous params
		$old = $g_xi_incparams; 

		// Get new params
		$g_xi_incparams = func_get_args();

		// Save previous output data
		$prev = ob_get_clean();

		// Start buffer capture
		ob_start(); 

		// Run the script
		include( $f );

		// Restore old params
		$g_xi_incparams = $old; 

		// Save output
		$ret = ob_get_clean(); 

		// Restore the previous output
		if ( $prev ) 
		{	ob_start(); 
			echo ( $prev );
		} // end if

		return $ret;
	}

//------------------------------------------------------------------
// Paths and files
//------------------------------------------------------------------
	
	/** Creates a proper path from the specified components
	
		@param [in] $root	root path component
		@param [in] $path	path component
		@param [in] $sep	path separator
		
		@return Assembled path
	*/
	function xp_make( $root, $path, $sep = '/' )
	{
		if( !strlen( $root ) ) 
			return $path; 

		if ( !strlen( $path ) ) 
			return $root;

		if ( is_array( $root ) )
			$root = implode( $sep, $root );

		if ( is_array( $path ) )
			$path = implode( $sep, $path );
			
		return rtrim( $root, '\/\\' . $sep ) . $sep . ltrim( $path, '\/\\' . $sep );
	}

	/** Returns the full root for the specified path
	
		@param $path		path component
		@param $sep			optional separator(s)
	 
	*/
	function xp_root( $path, $sep = 0 )
	{
		if ( !$sep )
		{	$lbs = strlen( strrchr( $path, '\\' ) ); 
			$lfs = strlen( strrchr( $path, '/' ) );
		} // end if
		else
		{	$lbs = strlen( strrchr( $path, $sep ) );
			$lfs = 0;
		} // end else

		if ( !$lbs && !$lfs ) 
			return $path;

		if ( !$lbs || ( $lfs && $lfs < $lbs ) )
			return substr( $path, 0, strlen( $path ) - $lfs );

		return substr( $path, 0, strlen( $path ) - $lbs );
	}

	/** Returns the file name from the path
		@param $path		= path component
		@param $sep			= optional separators
	*/
	function xp_file( $path, $sep = 0 )
	{
		if ( !$sep )
		{	$lbs = strlen( strrchr( $path, '\\' ) );
			$lfs = strlen( strrchr( $path, '/' ) );
		} // end if
		else
		{	$lbs = strlen( strrchr( $path, $sep ) );
			$lfs = 0;
		} // end else

		if ( !$lbs && !$lfs ) 
			return $path;

		if ( !$lbs || ( $lfs && $lfs < $lbs ) )
			return substr( $path, strlen( $path ) - $lfs + 1 );

		return substr( $path, strlen( $path ) - $lbs + 1 );
	}

	/** Returns a path suitable for indexing
		@param $token			token for building the path
		@param $block_size		max directory characters
		@param $depth			max directory depth
		@param $sep				path separator
	*/
	function xp_idx( $root, $token, $block_size = 3, $depth = 3, $sep = '/' )
	{
		$path = '';
		$max = strlen( $token );

		for ( $i = 0; $i < $depth && 0 < $max; $i++ )
		{
			if ( $i )
				$path .= $sep;

			$max -= $block_size;

			$path .= substr( $token, $i * $block_size, $block_size );

		} // end for

		return xp_make( $root, xp_make( $path, $token ) );
	}

	/** Returns the root disk path
		
		@param [in] $f		Path to append to the root disk path
	*/
	function xp_disk( $f = '' )
	{	
		$r = str_replace( '\\', '/', xp_root( xa( $_SERVER, 'SCRIPT_FILENAME' ) ) );
		return ( $f ? xp_make( $r, $f ) : $r );
	}

	/** Returns the root web path
		
		@param [in] $f		Path to append to the root web path
		@param [in] $q		Optional GET parameters
		@param [in] $proto	Web protocol to use
		@param [in] $port	TCP port to use
		@param [in] $full	Non-zero to build a complete url
	*/
	function xp_web( $f = '', $q = 0, $proto = 0, $port = 0, $full = false )
	{
		if ( xa_isAssoc( $q ) ) 
			$q = xa_join( $q, '$k=$v', '&', 'urlencode' );

		$s = xp_root( xa( $_SERVER, 'PHP_SELF' ) );
		if ( !$full ) 
			return ( strlen( $f ) ? xp_make( $s, $f ) : $s ) . ( $q ? "?$q" : '' );

		$h = xa( $_SERVER, 'SERVER_NAME' );
		$p = $port ? $port : xa( $_SERVER, 'SERVER_PORT' );
		$r = ( $full ? ( "$proto://$h" . ( ( 80 != $p ) ? ":$p" : "" ) ) : "" ) . $s;
		if ( !$proto ) $proto = 'http';

		return ( $f ? xp_make( $r, $f ) : $r ) . ( $q ? "?$q" : '' );
	}

	/** Returns the root disk path
		
		@param [in] $q		Optional GET parameters
		@param [in] $proto	Web protocol to use
		@param [in] $port	TCP port to use
		@param [in] $full	Non-zero to build a complete url
	*/
	function xp_self( $q = 0, $proto = 0, $port = 0, $full = false )
	{
		if ( xa_isAssoc( $q ) ) 
			$q = xa_join( $q, '$k=$v', '&', 'urlencode' );

		$f = str_replace( '\\', '/', xp_file( xa( $_SERVER, 'SCRIPT_FILENAME' ) ) );
		
		if ( !$full )
			return $f . ( $q ? "?$q" : "" );

		$h = xa( $_SERVER, 'SERVER_NAME' );
		$s = xp_make( xp_root( xa( $_SERVER, 'PHP_SELF' ) ), $f );
		$p = $port ? $port : xa( $_SERVER, 'SERVER_PORT' );
		if ( !$proto ) $proto = 'http';

		return "$proto://$h" . ( ( 80 != $p ) ? ":$p" : "" ) . $s . ( $q ? "?$q" : "" );
	}

//------------------------------------------------------------------
// Database
//------------------------------------------------------------------

	/** Creates an insert string for the specified array
		@param $table			table name
		@param $a				array contianing key/value pairs to insert
		@param $esc				Function to use to escape keys and values
		
		Example:
		
		@code

			// Create database
			require_once 'DB.php';
			$db = &DB::connect( 'mysql://user:pass@localhost/test' );
			if ( DB::isError( $db ) ) die( $db->getMessage() );

			// Ensure table exists
			if ( DB::isError( $db->query( 'SELECT 1 FROM `users`' ) ) )
			{	$res = $db->query( file_get_contents( xp_disk( 'sql/users.sql' ) ) );
				if ( DB::isError( $res ) ) die( $res->getMessage() );
			} // end if

			// Get data to insert
			$insert = xa_extract( $_POST, array( 'name', 'display' ), 0, '' );
			$sql = xd_insert( 'users', $insert, array( $db, 'escapeSimple' ) );

			// Attempt to add to the database
			$res = $db->query( $sql );
			if ( DB::isError( $res ) ) die( $res->getMessage() );

		@endcode

	*/
	function xd_insert( $table, $a, $esc = 0 )
	{
		if ( !xa_isAssoc( $q ) ) 
			return '';
		
		return 'INSERT INTO `' 
				. ( $esc ? xf_call( $esc, $table ) : $table ) 
				. '` ('
				. xa_join( $a, '`$k`', ',', $esc )
				. ') VALUES (' 
				. xa_join( $a, '"$v"', ',', $esc ) 
				. ')';
	}

?>
