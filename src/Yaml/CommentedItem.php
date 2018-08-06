<?php


namespace TheAentMachine\Yaml;

class CommentedItem
{
    /**
     * @var mixed
     */
    private $item;
    /**
     * @var string|null
     */
    private $comment;

    /**
     * @param mixed $item
     * @param string|null $comment
     */
    public function __construct($item, ?string $comment)
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
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
