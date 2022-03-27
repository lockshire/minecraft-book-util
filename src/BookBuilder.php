<?php namespace Lockshire\Minecraft\Tools;

class BookBuilder
{
    private string $mode;
    private Book $book;

    public function __construct(string $mode)
    {
        $this->mode = $mode;
        $this->book = $this->initBook();
    }

    private function initBook(): Book
    {
        switch ($this->mode) {
            case 'cli':
                $text = file_get_contents('../sandbox/sample-book-text4.txt');
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

    public function build()
    {
        $this->book->build();
    }
    
    public function getCommand()
    {
        $bookCommandBuilder = new BookCommandBuilder($this->book);
        return $bookCommandBuilder->build();
    }

    public function output(string $msg = '')
    {
        if ($this->mode === 'cli') {
            echo $msg . "\n\n";
        } else {
            $msg = str_replace("\r", '', $msg); // strip hidden carriage returns
            $msg = str_replace("\n", '', $msg); // strip hidden newlines
            echo '<p><code>' . $msg . '</code></p>';
        }
    }

    public function debug(string $msg = '')
    {
        if ($this->mode === 'cli') {
            echo $msg . "\n\n";
        } else {
            $sanityCheck = str_split($msg);
            echo '<table border="1">';
            $i = 0;
            foreach ($sanityCheck as $char) {
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $char . '</td>';
                echo '<td>' . ord($char) . '</td>';
                echo '</tr>';
                $i++;
            }
            echo '</table>';
        }
    }
}
