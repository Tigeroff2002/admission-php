<?php

namespace App\Contracts;

use App\Contracts\AbiturientEmptyMark;

final class AbiturientEmptyMarksList
{
    /**
     * @var [] The users
     */
    private array $list;

    /**
     * The constructor.
     * 
     * @param AbiturientEmptyMark...$user The users
     */
    public function __construct(AbiturientEmptyMark ...$abiturientEmptyMark) 
    {
        $this->list = $abiturientEmptyMark;
    }
    
    /**
     * Add user to list.
     *
     * @param AbiturientEmptyMark $user The user
     *
     * @return void
     */
    public function add(AbiturientEmptyMark $abiturientEmptyMark): void
    {
        $this->list[] = $abiturientEmptyMark;
    }

    /**
     * Get all users.
     *
     * @return AbiturientEmptyMark[] The users
     */
    public function all(): array
    {
        return $this->list;
    }
}