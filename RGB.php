</form>
<?php
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001 The PHP Group             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Jason Lotito <jason@lehighweb.com>                          |
// +----------------------------------------------------------------------+
//
// $Id$

/**
*    Color
*    Handles and manages color mixing.
*    
*    TODO: Eventually, I would like to expand upon this class to include other
*    color types, and make it handle colors in a cleaner manner, however, as of
*    right now, I would rather get it out rather than remain vaporware.  At least
*    this way, other people can take a look at it, and make suggestions.
*    Besides, someone else might get more use out of it than I.
*
*    The class is really simple to use, and pretty much does its job fairly well.
*    A sample of what can be done with this class is found here: 
*        http://www.newbienetwork.net/class.colour.php
*    As you can well see, it is very good at what it does, and is rather quick.
*    If someone has ideas or thoughts on this, please let me know.  I would like
*    expand it to handling image colors, as well as converting to CMYK, and even
*    the dreaded Pantone(TM) colors!  If someone knows of converting algo's or
*    know of anything that might be of interest to me, let me know =).
*    about it to.
*
*    Also, one more thing - Yes, I know, I will be try to get off the setColors()
*    2 color limitation, but since this script started out as a simple function
*    that could _mix_ to colors together, it just kept going like that.
*
*   If you notice, the version is only 0.1.  This is because I don't know of
*   anyone really using it, and so it hasn't been tested completely.  The more
*   input I get back on it, the closer it goes to a 1.0 release.
*
*   @version    0.1
*   @author     Jason Lotito <jason@lehighweb.com>
*/
class Color_RGB
{
    /**
    *   first color that the class handles for ranges and mixes.
    *   @access private
    *   @see    setColors
    */
    var $color1 = array();
    
    /**
    *   second color that the class handles for ranges and mixes.
    *	@access private
    */
    var $color2 = array();
    
    /**
    *   Boolean value for determining whether colors outputted should be websafe or not.  Defaults to false.
    *	@access private
    *   @see    setWebSafe
    */
    var $_websafeb = false;
    
    /**
    *   the websafe ranges we use to determine where to set each value.  The key
    *   is the websafe part, and the value is the non-websafe value.
    *	@access private
    */
    var $websafe = array(   '00' => '00', 
                            '33' => '51', 
                            '66' => '102', 
                            '99' => '153', 
                            'cc' => '204', 
                            'ff' => '255');
    
    /**
    *    mixColors
    *    Given two colors, this will return a mix of the two together.
    *
    *    @access    public
    *    @param    string(optional)    $col1    The first color you want to mix
    *    @param    string(optional)    $col2    The second color you want to mix
    *    @result    string                        The mixed color.
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function mixColors ( $col1=false, $col2=false )
    {
        if ( $col1 )
        {
            $this->_setColors($col1, $col2);
        }
        
        // RED
        $color3[0] = ( $this->color1[0] + $this->color2[0] ) / 2;
        // GREEN
        $color3[1] = ( $this->color1[1] + $this->color2[1] ) / 2;
        // BLUE
        $color3[2] = ( $this->color1[2] + $this->color2[2] ) / 2;
        
        if ( $this->_websafeb )
        {
            array_walk( $color3, '_makeWebSafe' );
        }
        
        return $this->_returnColor( $color3 );
    }
    
    /**
    *    setWebSafe
    *    Sets whether we should output websafe colors or not.
    *
    *    @access    public
    *    @param    bool=true    If set to true (def.), websafe is on, otherwise not.
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function setWebSafe( $bool=true )
    {
        $this->_websafeb = $bool;
    }
    
    /**
    *    setColors
    *    This class primarily works with two colors, and using this function, you
    *    can easily set both colors.
    *
    *    @access    public
    *    @param    string    The first color in hex format
    *    @param    string    The second color in hex format
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function setColors( $col1, $col2 )
    {
        $this->_setColors($col1, $col2);
    }
    
    /**
    *   getRange
    *   Given a degree, you can get the range of colors between one color and
    *   another color.
    *
    *   @access     public
    *   @param      string  $degrees How much each 'step' between the colors we should take.
    *   @return     array   Returns an array of all the colors, one element for each color.
    *   @author     Jason Lotito <jason@lehighweb.com>
    */
    function getRange ( $degrees=2 )
    {
        if ( $degrees == 0 )
            $degrees = 1;
        
        /**
        The degrees give us how much we should advance each color at each phase
        of the loop.  This way, the advance is equal throughout all the colors.
        
        TODO: Allow for degrees for individual parts of the colors.
        */
        
        // RED
        $red_steps   = ( $this->color2[0] - $this->color[0] ) / $degrees;
        // GREEN
        $green_steps = ( $this->color2[1] - $this->color[1] ) / $degrees;
        // BLUE
        $blue_steps  = ( $this->color2[2] - $this->color[2] ) / $degrees;
        
        $allcolors = array();
        $x = 0;
        
        /**
        The loop stops once any color has gone beyond the end color.
        */
        
        // Loop through all the degrees between the colors
        for ( $x = 0; $x < $degrees; $x++ )
        {
            $col[0] = $red_steps * $x;
            $col[1] = $green_steps * $x;
            $col[2] = $blue_steps * $x;
            
            // Loop through each R, G, and B
            for ( $i = 0; $i < 3; $i++ )
            {
                $partcolor = $color1[$i] + $col[$i];
                // If the color is less than 256
                if (  $partcolor < 256 )
                {
                    // Makes sure the colors is not less than 0
                    if ( $partcolor > -1 )
                    {
                        $newcolor[$i] = $partcolor;
                    } else {
                        $newcolor[$i] = 0;
                    }
                // Color was greater than 255
                } else {
                    $newcolor[$i] = 255;
                }
            }
            
            if ( $this->_websafeb )
            {
                array_walk( $newcolor, '_makeWebSafe' );
            }
            
            $allcolors[] = $this->_returnColor($newcolor);
        }
        
          
        return $allcolors;
    }
    
    /**
    *    changeLightness
    *    Changes the lightness of the color.
    *
    *    The argument it takes determines the direction to go.  If you give it a
    *    negative number, it will make the color darker, however, if you give it
    *    a positive number, it gets lighter.
    *    @access public
    *    @param    int    degree    The degree of the change you wish to take place.
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function changeLightness ( $degree=10 )
    {
        $color2 =& $this->color2;
        $color1 =& $this->color1;
        
        for ( $x = 0; $x < 3; $x++ )
        {
            if ( ( $color1[$x] + $degree ) < 256 )
            {
                if ( ( $color1[$x] + $degree ) > -1 )
                {
                    $color1[$x] += $degree;
                } else {
                    $color1[$x] = 0;
                }
            } else {
                $color1[$x] = 255;
            }
        }
        
        for ( $x = 0; $x < 3; $x++ )
        {
            if ( ( $color2[$x] + $degree ) < 256 )
            {
                if ( ( $color2[$x] + $degree ) > -1 )
                {
                    $color2[$x] += $degree;
                } else {
                    $color2[$x] = 0;
                }
            } else {
                $color2[$x] = 255;
            }
        }
    }
    
    /**
    *    getTextColor
    *    Given a color, will return whether you should use a dark font or a light font.
    *
    *    You can change the dark and the light color, however by default, they are
    *    set to be 'white' (ffffff) and 'black' (000000), which are standard text
    *    colors.  This is determined by the G(reen) value of RGB.
    *
    *   @access     public
    *   @param      string  $color  The color to analyze
    *   @param      string  $light(optional) The light color value to return if we should have light text
    *   @param      string  $dark(optional) The dark color value to return if we should have dark text
    *   @author     Jason Lotito <jason@lehighweb.com>
    */
    function getTextColor ( $color, $light='FFFFFF', $dark='000000' )
    {
        $color = Color::_splitColor($color);
        if ( $color[1] > hexdec('66') )
        {
            return $dark;
        } else {
            return $light;
        }
    }
    
    /**
    *    _setColors
    *    Internal method to correctly set the colors.
    *
    *    @access    private
    *    @param    string    Color 1
    *    @param    string     Color 2
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function _setColors ( $col1, $col2 )
    {
        $this->color1 = Color::_splitColor($col1);
        $this->color2 = Color::_splitColor($col2);
    }
    
    /**
    *    _splitColor
    *    Given a color, it will properly split it up into a 3 element dec. array.
    *    
    *    @access    private
    *    @param    string    The color.
    *    @return    array    3 element array containing the RGB information.
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function _splitColor ( $color )
    {
        $c[] = hexdec( substr( $color, 0, 2 ) );
        $c[] = hexdec( substr( $color, 2, 2 ) );
        $c[] = hexdec( substr( $color, 4, 2 ) );
        return $c;
    }
    
    /**
    *    _returnColor
    *    Given an array of 3 elements containing RGB decimal information, it will
    *    return an HTML compatible HEX color.
    *
    *    @access    private
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function _returnColor ( $color )
    {
        return sprintf("%02X%02X%02X",$color[0],$color[1],$color[2]);
    }
    
    /**
    *    rgb2hex
    *    Given an array of 3 elements containing RGB information, it will return
    *    a string of the HEX color.
    *
    *    @access    public
    *    @param    array    3 element array.
    *    @return string
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function rgb2hex ( $color )
    {
        return Color::_returnColor( $color );
    }
    
    /**
    *    hex2rgb
    *    Given a hex color, returns a 4 element array, with keys 0-2 containing
    *    the RGB values appropriately and with key 3 containing the original
    *    color.
    *
    *    @access    public
    *    @param    string    The HEX string of the color.
    *    @return    array    4 element array.
    *    @author    Jason Lotito <jason@lehighweb.com>
    */
    function hex2rgb ( $hex )
    {
        $return = Color::_splitColor( $hex );
        $return['hex'] = $hex;
        return $return;
    }
}

// For Array Walk
// {{{
    /**
    *    _makeWebSafe
    *    Function for array_walk() to easily change colors from whatever to 
    *    the closests websafe representation.
    *
    *    @access   private
    *    @param    int        One element of the decimal RGB value of a color.
    *    @return   int        The websafe equivalent of the color setting.
    *    @author   Jason Lotito <jason@lehighweb.com>
    */
    function _makeWebSafe ( &$color )
    {
        if ( $color == 0 )
        {
            return $color;
        } else {
            if ( ($color % 51) == 0 )
            {
                return $color;
            } else {
                if ( $color < 26 ) {
                    $color = 00;
                    return $color;
                } else if ( $color < 77 && $color > 25 ) {
                    $color = 51;
                    return $color;
                } else if ( $color > 76 && $color < 127 ) {
                    $color = 102;
                    return $color;
                } else if ( $color > 126 && $color < 178 ) {
                    $color = 153;
                    return $color;
                } else if ( $color > 177 && $color < 229 ) {
                    $color = 204;
                    return $color;
                } else {
                    $color = 255;
                    return $color;
                }
            }
        }
    }
// }}}


/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
*/

?> 


