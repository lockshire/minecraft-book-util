<?php namespace Lockshire\Minecraft\Tools;

class Book
{
    private string $text;
    private string $title;
    private string $author;
    private int $generation;
    private string $display;

    private array $parsedText;
    private array $page;

    private array $strings;

    public function __construct(string $text = null)
    {
        if ($text !== null) {
            $this->setText($text);
        }

        $this->page = [];

        $this->strings = [
            40 => "!|;:','.i",
            60 => "l",
            80 => "[]tI\" ",
            100 => "fk*(){}<>",
            140 => "@",
        ];
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

    public function addPage(): Book
    {
        $this->page[] = '';
        return $this;
    }

    public function updatePage(int $pageNo, string $page): Book
    {
        $this->page[$pageNo] = $page;
        return $this;
    }

    public function getPages(): array
    {
        return $this->page;
    }

    public function build(): Book
    {
        $this->parse();
        $this->paginate();
        return $this;
    }

    private function parse(): Book
    {
        // Start with raw text
        $text = $this->getText();

        // Replace any CR+LF combinations with a newline
        $text = str_replace("\r\n", "\n", $text);

        // Replace any dangling CRs with a newline
        $text = str_replace("\r", "\n", $text);

        // Split file into lines
        $lines = explode("\n", $text);

        // Split lines into words
        foreach ($lines as $line) {
            $this->parsedText[] = explode(' ', $line);
        }

        return $this;
    }

    private function paginate(): Book
    {
        // Initialize tracking variables
        $lines = 0;
        $currentLine = '';
        $currentLineLen = 0.0;
        $currentPageNo = 0;
        $currentPageText = '';

        // Begin with a blank page (a bare minimum book has one page and no text)
        $this->addPage();

        // Loop through book text one line at a time
        // These are hard returns (or paragraphs) part of the source text
        foreach ($this->parsedText as $line) {

            // Loop through lines one word at a time
            foreach ($line as $word) {

                // Running count of line length
                $wordLength = $this->calcWordLength($word);
                $currentLineLen += $wordLength;

                // If the line length is too long, add to page and start counting a new line
                if ($currentLineLen > 19) {

                    $currentPageText .= $currentLine;

                    $lines++;
                    $currentLine = '';
                    $currentLineLen = $wordLength;

                    // If the number of lines is too long, start a new page
                    if ($lines > 13) {
                        $this->updatePage($currentPageNo, $currentPageText);
                        $this->addPage();
                        $lines = 0;
                        $currentLine = '';
                        $currentLineLen = 0;
                        $currentPageNo++;
                        $currentPageText = '';
                    }
                }

                // Add word to current line
                $currentLine .= $word . ' ';
                $currentLineLen += $this->calcCharLen(' ');
            }

            // Add line to current page
            $currentPageText .= $currentLine . '\\\n';
            $this->updatePage($currentPageNo, $currentPageText);

            $lines++;
            $currentLine = '';
            $currentLineLen = 0;

            // If the number of lines is too long, start a new page
            if ($lines > 13) {
                $this->addPage();
                $lines = 0;
                $currentPageNo++;
                $currentPageText = '';
            }
        }

        return $this;
    }

    private function calcWordLength(string $word): float
    {
        $len = 0.0;
        $chars = str_split($word);
        foreach ($chars as $char) {
            $len += $this->calcCharLen($char);
        }
        return $len;
    }

    private function calcCharLen($char): float
    {
        $len = 120.0;

        if ($char === '') {
            return 0.0;
        } else {
            foreach ($this->strings as $size => $collection) {
                if (strpos($collection, $char) !== false) {
                    $len = $size;
                    break;
                }
            }
        }

        return $len / 120;
    }

}