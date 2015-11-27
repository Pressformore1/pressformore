<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Storage strategy interface.
 */
interface IStorageStrategy {
    
    /**
     * Gets the current authorization token.
     * @return \P4M\MangoPayBundle\MangoPaySDK\OAuthToken Currently stored token instance or null.
     */
    function Get();
    
    /**
     * Stores authorization token passed as an argument.
     * @param \P4M\MangoPayBundle\MangoPaySDK\OAuthToken $token Token instance to be stored.
     */
    function Store($token);
}