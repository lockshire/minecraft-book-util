<?php namespace Lockshire\Minecraft\Tools;

class BookCommandBuilder
{
    private Book $book;
    
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function build()
    {
        $pages = [];

        foreach ($this->book->getPages() as $page) {
            $page = str_replace("'", "\'", $page);
            $page = str_replace('"', '\\\"', $page);
            $pages[] = sprintf('\'{"text":"%s"}\'', $page);
        }

        $contents = sprintf(
            'pages:[%s],title:"%s",author:"%s",generation:%d',
            join(',', $pages),
            $this->book->getTitle(),
            $this->book->getAuthor(),
            $this->book->getGeneration(),
        );

        $cmd = sprintf('/give @p written_book{%s}', $contents);

        return $cmd;
    }
}
