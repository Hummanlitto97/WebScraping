<?php declare(strict_types=1);

namespace WebScraping\App\Services\Interfaces;

interface IService
{
    /**
     * Checks if this method initiating Service is available
     * @return bool
     */
    public function Is_Error() : bool;
}