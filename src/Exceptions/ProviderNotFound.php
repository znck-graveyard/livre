<?php namespace Znck\Livre\Exceptions;

use Exception;

/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 */
class ProviderNotFound extends Exception
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 1000, Exception $previous = null)
    {
        parent::__construct('Book service provider not defined. Config object: '.$message, $code, $previous);
    }
}