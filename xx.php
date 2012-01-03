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
	
	/** Returns the specified configuration value
		@param [in] $k		Key value to read
		@param [in] $d		Default value to use if $k is not found
	*/
	function xc( $k = null, $d = '')
	{	global $g_xc_cfg;
		if ( null === $k ) return $g_xc_cfg;
		return xak( $g_xc_cfg, $k, $d );
	}
	
	/** Writes a value into the global config array
		@param [in] $k		Key value
		@param [in] $v		Value to save into the array
	*/
	function xc_set( $k, $v )
	{	global $g_xc_cfg;
		$g_xc_cfg[ $k ] = $v;
	}

	/** Writes a value into the global config array
		@param [in] $k		Key value, can be multi dimensional
		@param [in] $v		Value to save into the array
	*/
	function xc_kset( $k, $v )
	{	global $g_xc_cfg;
		xak_set( $g_xc_cfg, $k, $v );
	}
	
	/** Reads a value from the global config array
		@param [in] $k		Key value to read
		@param [in] $d		Default value to use if $k is not found
	*/
	function xc_get( $k, $d = '' )
	{	global $g_xc_cfg;
		return isset( $g_xc_cfg[ $k ] ) ? $g_xc_cfg[ $k ] : $d;
	}

	/** Reads a value from the global config array
		@param [in] $k		Key value to read, can be multi dimensional
		@param [in] $d		Default value to use if $k is not found
	*/
	function xc_kget( $k, $d = '' )
	{	global $g_xc_cfg;
		return xak( $g_xc_cfg, $k, $d );
	}

	/** Does template replace with settings
		@param [in] $tmpl	Template string
		@param [in] $kf		Optional encode function for keys
		@param [in] $vf		Optional encode function for values
		@param [in] $pre	Variable prefix
	*/
	function xc_sub( $tmpl, $kf = 0, $vf = 0, $pre = '$' )
	{	global $g_xc_cfg;
		return xa_sub( $g_xc_cfg, $tmpl, $kf, $vf, $pre );
	}
	
	/** Does template replace with settings
		@param [in] $file	Name of file containing template
							Or for a safety check, specify
							array( '<root>', '<filename>' )
		@param [in] $kf		Optional encode function for keys
		@param [in] $vf		Optional encode function for values
		@param [in] $pre	Variable prefix
	*/
	function xc_file( $file, $kf = 0, $vf = 0, $pre = '$' )
	{	global $g_xc_cfg;
		if ( is_array( $file ) )
		{	if ( !xp_isfile( $file[ 0 ], $file[ 1 ] ) ) return '';
			$file = xp_make( $file[ 0 ], $file[ 1 ] );
		} // end if
		if ( !is_file( $file ) ) return '';
		return xa_sub( $g_xc_cfg, file_get_contents( $file ), $kf, $vf, $pre );
	}
	
	/** Does template replace with settings

		@param [in] $k		Root key
		@param [in] $n		Number of palette entries
		@param [in] $step	Palette step size
		@param [in] ...		Palette colors

	*/
	function xc_palette( $k, $n, $step )
	{	global $g_xc_cfg;
		$palette = xx_create_palette( array_slice( func_get_args(), 3 ), 10, 0.1 );
		if ( strlen( $k ) ) xc_set( $k, $palette );		
		return $palette;
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

	/** String compare function
		$a	- First string
		$b	- Second string
	*/
	function xs_cmp_length_desc( $a, $b ) 
	{	$al = strlen( $a ); $bl = strlen( $b );
		 return ( $al == $bl ) ? 0 : ( $al < $bl ? 1 : -1 );
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

	/** Returns value of $k in array $a or $d if it doesn't exist
	
		@param [in] $a		Array
		@param [in] $k		Key in array, can be multidimensional
		@param [in] $d		Default value
		@param [in] $f		Optional encoding function
		$param [in] $s		Key separator
	*/
	function xak( $a, $k, $d = '', $f = 0, $s = '.' )
	{
		$ka = explode( $s, $k );
		foreach( $ka as $kk )
			if ( isset( $a[ $kk ] ) )
				$a = $a[ $kk ];
			else
				return $d;
		return $f ? xf_call( $f, $a ) : $a;
	}

	/** Sets value of $k in array $a or $d if it doesn't exist
	
		@param [in] $a		Array
		@param [in] $k		Key in array, can be multidimensional
		@param [in] $d		Default value
		@param [in] $f		Optional encoding function
		$param [in] $s		Key separator
	*/
	function xak_set( &$a, $k, $v, $f = 0, $s = '.' )
	{	$ka = explode( $s, $k );
		foreach( $ka as $kk )
		{	if ( !isset( $a[ $kk ] ) )
				$a[ $kk ] = array();
			$a = $a[ $kk ];
		} // end foreach
		return $a = ( $f ? xf_call( $f, $v ) : $v );
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
	
	/*** Applies template using function array
		@param [in] $tmpl		Template string
		@param [in] $a			Function array
		
		Example:
		
		@code
			
			$af[ '$hello' ] = function( $s ) { return $s . ' World!'; };
			
			echo xa_fsub( '$hello', $af, 'Hello' );
			
			// Output
			> Hello World!
			
		@endcode		
	*/
	function xa_fsub( $tmpl, &$a )
	{
		// Get extra params
		$p = array_slice( func_get_args(), 2 ); 
		
		// Must process the keys from longest to shortest
		$ak = array_keys( $a );
		usort( $ak, @xs_cmp_length_desc );
		
		// Replace tokens
		foreach( $ak as $k )
		{	$v = $a[ $k ];
			$tmpl = str_replace( $k, is_callable( $v ) ? call_user_func_array( $v, $p ) : $v, $tmpl );
		} // end foreach
		
		return $tmpl;
	}

	/** Applies template to array
		@param [in]	$a		an array
		@param [in]	$tmpl	template to apply
		@param [in]	$join	string used to join multiple items
		@param [in]	$kf		optional function to encode key
		@param [in]	$vf		optional function to encode value
		@param [in]	$af		optional custom replace functions
							function( $k, $v, $i, $kf, $kv )
		
		Examples:
		
		@code

			// Urlencode array
			xa_join( $a, '$k=$v', '&', 'urlencode' );

			// Create escaped MySQL query
			xa_join( $a, '`$k`=\'$v\'', ' AND ', 'mysql_real_escape_string' );

		@endcode
	*/
	function xa_join( $a, $tmpl, $join = '', $kf = null, $vf = null, $af = array() )
	{
		if ( !is_array( $a ) || !count( $a ) )
			return $def;

		if ( $kf && null === $vf )
			$vf = $kf;				

		// Default functions
		if ( xa_z( $af, '$v' ) ) 
			$af[ '$v' ] = create_function( '$k, $v, $i, $kf, $vf',
										   'return $vf ? xf_call( $vf, $v ) : $v;' );
		if ( xa_z( $af, '$k' ) ) 
			$af[ '$k' ] = create_function( '$k, $v, $i, $kf, $vf',
						  				   'return $kf ? xf_call( $kf, $k ) : $k;' );
		if ( xa_z( $af, '$i' ) ) 
			$af[ '$i' ] = create_function( '$k, $v, $i',
										   'return $i;' );

		// Do substitiution
		$i = 0; $ret = '';
		if ( xa_isAssoc( $a ) )
			foreach( $a as $k=>$v ) 
				$ret .= ( $i++ ? $join : '' )
						. xa_fsub( $tmpl, $af, $k, $v, $i, $kf, $vf );
		else
			foreach( $a as $v ) 
				$ret .= ( $i++ ? $join : '' )
						. xa_fsub( $tmpl, $af, '', $v, $i, null, $vf );
					
		return $ret;
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

		@notice Consider built in str_replace()

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
		if ( !is_array( $a ) || !count( $a ) )
			return $tmpl;

		// Must process the keys from longest to shortest
		$ak = array_keys( $a );
		usort( $ak, @xs_cmp_length_desc );
		
		// Replace tokens
		foreach( $ak as $k )
		{	$v = $a[ $k ];
			if ( !is_array( $v ) )			
				$tmpl = str_replace( $pre.( $kf ? xf_call( $kf, $k ) : $k ), 
									 $vf ? xf_call( $vf, $v ) : $v, 
									 $tmpl );
			else
				$tmpl = xa_sub( $v, $tmpl, $kf, $vf, $pre . $k . '.' );
			
		} // end foreach

		return $tmpl;
	}

	/** Extracts specified keys from array

		@param [in]	$a		an array
		@param [in]	$ka		array of keys or comma separated string
		@param [in]	$vf		optional function to encode value
		@param [in]	$def	default value to add if non exist

		@notice Consider built in function array_intersect_key()

		@return Array containing extracted keys
	*/
	function xa_extract( $a, $ka, $vf = 0, $def = null )
	{
		// Create array of the values we want if needed
		if ( !is_array( $ka ) )
			$ka = explode( ',', $ka );

		// Create an array of wanted values
		$r = array();
		$valid = xa_isAssoc( $a ) && count( $a );
		foreach( $ka as $k )
			if ( $valid && isset( $a[ $k ] ) )
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
		
		// Safety check?
		if ( is_array( $f ) )
		{
			// Ensure valid file in specified directory
			if ( !xp_isfile( $f[ 0 ], $f[ 1 ] ) )
				return '';
			
			// Build full path
			$f = xp_make( $f[ 0 ], $f[ 1 ] );
		
		} // end if
	
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
		@param $root			root path
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

	/** Returns the contents of the specified directory in an array
		@param $dir		- The directory path
		@param $full	- non-zero for full paths
	*/
	function xp_dir( $dir, $full = false )
	{	
		$a = array();
		$h = opendir( $dir );
		if ( !$h ) return $a;
		
		// Read in directory contents
		while ( false !== ( $f = readdir( $h ) ) )
			if ( '.' != $f && '..' != $f )
				$a[] = $full ? xp_make( $dir, $f ) : $f;
				
		closedir( $h );
		
		return $a;
	}
	
	/** Returns non-zero if the specified file is in the specified directory
		@param $dir		- Directory to check in
		@param $file	- File to look for	
	*/
	function xp_isfile( $dir, $file, $case_sensitive = true )
	{
		$h = opendir( $dir );
		if ( !$h ) return false;
		
		// Read in directory contents
		$found = false;
		while ( !$found && false !== ( $f = readdir( $h ) ) )
			if ( '.' != $f && '..' != $f )
				$found = $case_sensitive 
						 ? ( !strcmp( $f, $file ) ) 
						 : ( !strcasecmp( $f, $file ) );
				
		closedir( $h );
		
		return $found;
	}

	/** Returns the contents of the specified directory in an array
		@param $dir		- The directory path
		@param $full	- non-zero for full paths
		@param $files	- non-zero to include files
		@param $dirs	- non-zero to include directories
		@param $a		- Array to merge
	*/
	function xp_rdir( $dir, $full = false, $files = true, $dirs = true, $a = array() )
	{	
		$h = opendir( $dir );
		if ( !$h ) return $a;
		
		// Read in files
		while ( false !== ( $f = readdir( $h ) ) )
			if ( '.' != $f && '..' != $f )
			{
				// Full path
				$p = xp_make( $dir, $f );
				
				// Is it a directory?
				if ( is_dir( $p ) )
				{	if ( $dirs ) $a[] = $full ? xp_make( $dir, $f ) : $f;
					$a = xp_rdir( $p, $full, $files, $dirs, $a );
				} // end if

				// Must be a file
				else if ( $files )
					$a[] = $full ? xp_make( $dir, $f ) : $f;

			} // end if
				
		closedir( $h );
		
		return $a;
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
		if ( !$proto ) $proto = 'http';
		$r = ( $full ? ( "$proto://$h" . ( ( 80 != $p ) ? ":$p" : "" ) ) : "" ) . $s;

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
// Numbers
//------------------------------------------------------------------

	function xn_max( $v, $min, $max )
	{	return ( $v > $max ) ? $max : $v; }

	function xn_min( $v, $min, $max )
	{	return ( $v < $min ) ? $min : $v; }

	function xn_range( $v, $min, $max )
	{	return ( $v < $min ) ? $min : ( ( $v > $max ) ? $max : $v ); }

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
	
//------------------------------------------------------------------
// Colors
//------------------------------------------------------------------
	
	function xx_scale_color( $c, $s )
	{	return str_pad( dechex( xn_range( floor( $s * hexdec( substr( $c, 0, 2 ) ) ), 0, 255 ) ), 2, '0', STR_PAD_LEFT )
			   . str_pad( dechex( xn_range( floor( $s * hexdec( substr( $c, 2, 2 ) ) ), 0, 255 ) ), 2, '0', STR_PAD_LEFT )
			   . str_pad( dechex( xn_range( floor( $s * hexdec( substr( $c, 4, 2 ) ) ), 0, 255 ) ), 2, '0', STR_PAD_LEFT );
	}

	function xx_create_palette( $primary, $num, $step )
	{	$palette = array();
		if ( !is_array( $primary ) ) 
			$primary = array( $primary );
		foreach( $primary as $k=>$v )
			for ( $i = -$num; $i <= $num; $i++ )
				$palette[ $k ][ $i ] = '#' . xx_scale_color( $v, 1 + ( $i * $step ) );
		return $palette;
	}

?>
