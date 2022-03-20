<?php namespace Lockshire\Minecraft\Tools;

class BookBuilder
{
    private string $mode;
    private Book $book;
    private string $command;

    public function __construct(string $mode)
    {
        $this->mode = $mode;
        $this->book = $this->initBook();
        $this->command = '<tbd>';
    }

    private function initBook(): Book
    {
        switch ($this->mode) {
            case 'cli':
                $text = file_get_contents('book1.txt');
                $title = 'Book Title';
                $author = 'Author';
                $generation = 0;
                break;
            default:
                $text = $_POST['book_text'];
                $title = $_POST['book_title'];
                $author = $_POST['book_author'];
                $generation = $_POST['book_generation'];
                break;
        }

        $book = new Book();
        $book
            ->setText($text)
            ->setTitle($title)
            ->setAuthor($author)
            ->setGeneration($generation)
        ;

        return $book;
    }

    public function exec()
    {
        // Convert book to array
        $text = str_split($this->book->getText());
        
        $lineLength = 0;
        $lines = 0;
        $currentPageText = '';
        
        foreach ($text as $char) {
        
            // Intercept newlines
            if ($char === "\n") {
                $lines++;
                $lineLength = 0;
                $currentPageText .= '\\n';
                continue;
            }
        
            // Add current character to current page
            $currentPageText .= $char;
        
            // Figure out the line length
            $lineLength += $this->calcLen($char);
        
            // If the line length is too long, start counting a new line
            if ($lineLength > 25) {
                $lines++;
                //output("\$lines = $lines");
                $lineLength = 0;
            }
        
            // If the number of lines is too long, start a new page
            if ($lines > 13) {
                //output("Just started a new page");
                $this->book->addPage($currentPageText);
                $currentPageText = '';
                $lineLength = 0;
                $lines = 0;
            }
        }

        $commandBuilder = new CommandBuilder($this->book);
        $this->command = $commandBuilder->exec();
    }
    
    public function getCommand()
    {
        return $this->command;
    }

    private function calcLen($char)
    {
        switch ($char) {
        case 'abcdefghijklmnopqrstuvwxyz':
            return 1;
            break;
        case 'ABCDEFGHIJKLMNOPQRSTUVWXZY':
            return 2;
            break;
        default:
            return 1;
        }
    }

    private function buildCommand(array $book)
    {
        $pages = [];
        foreach ($book['page'] as $page) {
            $page = str_replace("'", "\'", $page);
            $page = str_replace('"', '\\"', $page);
            $pages[] = sprintf('\'{"text":"%s"}\'', $page);
        }

        $contents = sprintf(
            'pages:[%s],title:"%s",author:"%s"',
            join(',', $pages),
            $book['title'],
            $book['author']
        );

        $cmd = sprintf('/give @p written_book{%s}', $contents);

        return $cmd;
    }

    public function output(string $msg = '')
    {
        if ($this->mode === 'cli') {
            echo $msg . "\n";
        } else {
            //echo '<form><textarea width="120" height="10">' . str_replace('\\n', '&#92;&#92;n', $msg) . '</textarea></form>';
            $msg = str_replace('\\n', '&#92;&#92;n', $msg);
            echo '<p><code>' . $msg . '</code></p>';
        }
    }
}
