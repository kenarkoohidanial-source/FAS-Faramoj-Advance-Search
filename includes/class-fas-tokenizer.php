<?php
/**
 * FAS Tokenizer
 * Normalizes strings by intelligently separating letters and digits, stripping punctuation,
 * and converting to lowercase for strict exact token matching.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class FAS_Tokenizer {

    /**
     * Converts Persian and Arabic digits to standard English digits.
     */
    public static function normalize_digits( $string ) {
        $persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
        $english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

        $string = str_replace( $persian, $english, $string );
        return str_replace( $arabic, $english, $string );
    }

    /**
     * Tokenize a string into an array of normalized components.
     */
    public static function tokenize( $string ) {
        if ( empty( $string ) || ! is_string( $string ) ) {
            return array();
        }

        // 1. Lowercase
        $string = function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );

        // 2. Normalize digits
        $string = self::normalize_digits( $string );

        // 3. Strip explicit separators defined in requirements
        $separators = array( '-', '_', '.', ',', '/', '\\', '+', '(', ')', '[', ']', ':', ';', '#' );
        $string = str_replace( $separators, ' ', $string );

        // 4. Intelligently split letter-to-number and number-to-letter transitions
        // \p{L} matches any kind of letter from any language.
        // \p{N} matches any kind of numeric character.
        $string = preg_replace( '/(\p{L})(\p{N})/u', '$1 $2', $string );
        $string = preg_replace( '/(\p{N})(\p{L})/u', '$1 $2', $string );

        // 5. Replace multiple spaces with a single space and trim
        $string = preg_replace( '/\s+/u', ' ', $string );
        $string = trim( $string );

        if ( empty( $string ) ) {
            return array();
        }

        // 6. Explode into unique tokens
        $tokens = explode( ' ', $string );

        // Remove empty strings just in case, and deduplicate
        return array_values( array_unique( array_filter( $tokens ) ) );
    }

    /**
     * Returns the tokenized array as a single space-separated string.
     */
    public static function tokenize_to_string( $string ) {
        $tokens = self::tokenize( $string );
        return implode( ' ', $tokens );
    }
}
