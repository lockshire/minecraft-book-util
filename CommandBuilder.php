<?php namespace Lockshire\Minecraft\Tools;

class CommandBuilder
{
    private Book $book;
    
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function exec()
    {
        $pages = [];

        foreach ($this->book->getPages() as $page) {
            $page = str_replace("'", "\'", $page);
            $page = str_replace('"', '\\"', $page);
            $pages[] = sprintf('\'{"text":"%s"}\'', $page);
        }

        $contents = sprintf(
            'pages:[%s],title:"%s",author:"%s"',
            join(',', $pages),
            $this->book->getTitle(),
            $this->book->getAuthor(),
        );

        $cmd = sprintf('/give @p written_book{%s}', $contents);

        return $cmd;
    }
}
