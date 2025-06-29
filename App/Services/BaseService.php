<?php

namespace App\Services;

class BaseService
{
    /**
     * Converts all tags in the given array to lowercase.
     *
     * @param array|null $tags Array of tag strings to convert. Can be null.
     * @return array An array of lowercase tag strings. Returns an empty array if input is null.
     */
    protected function convertTagsToLowerCase(?array $tags = null): array
    {
        return $tags ? array_map('strtolower', $tags) : [];
    }
}