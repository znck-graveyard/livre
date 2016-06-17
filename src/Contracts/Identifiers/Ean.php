<?php namespace Znck\Livre\Contracts\Identifiers;

interface Ean
{
    public function getPrefix();

    public function getCompany();

    public function getItem();

    public function isValid();
}
