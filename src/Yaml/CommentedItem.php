<?php


namespace TheAentMachine\Yaml;

class CommentedItem
{
    /**
     * @var mixed
     */
    private $item;
    /**
     * @var string
     */
    private $comment;

    /**
     * @param mixed $item
     * @param string $comment
     */
    public function __construct($item, string $comment)
    {

        $this->item = $item;
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
