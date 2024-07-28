<?php

namespace FaultyFiles;

class SyntaxError02
{
    /**
     * @return void
     */
    public function method01(): void
    {
        return;
    }

    /**
     * @return void
     */
    public function method02(): void
    {
        return "return";
    }

    /**
     * @return void
     */
    public function method03(): void
    {
        return;
    }
}