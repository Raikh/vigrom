<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ApiErrorException extends Exception
{
    protected $errorCode;
    protected $details;

    public function __construct(string $errorCode, ?array $details = [], $code = 400)
    {
        parent::__construct($errorCode, $code);

        if (empty($details)) {
            $details = null;
        }

        if (is_array($details)) {
            $details = array_map(function ($item) {
                return (array) $item;
            }, $details);
        }

        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return array[]|null
     */
    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = \array_merge($this->getDetails(), $details);
    }
}
