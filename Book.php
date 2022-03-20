<?php namespace Lockshire\Minecraft\Tools;

class Book
{
    private string $text;
    private string $title;
    private string $author;
    private int $generation;
    private string $display;
    private array $page;

    public function __construct(string $text = null)
    {
        if ($text !== null) {
            $this->setText($text);
        }
        $this->page = [];
    }

    public function setText(string $text): Book
    {
        $this->text = $text;
        return $this;
    }

    public function setTitle(string $title): Book
    {
        $this->title = $title;
        return $this;
    }

    public function setAuthor(string $author): Book
    {
        $this->author = $author;
        return $this;
    }

    public function setGeneration(int $generation): Book
    {
        $this->generation = $generation;
        return $this;
    }

    public function setDisplay(string $display): Book
    {
        $this->display = $display;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getGeneration(): int
    {
        return $this->generation;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function addPage(string $page): Book
    {
        $this->page[] = $page;
        return $this;
    }

    public function getPages(): array
    {
        return $this->page;
    }

}