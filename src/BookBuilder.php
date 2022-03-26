<?php namespace Lockshire\Minecraft\Tools;

class BookBuilder
{
    private string $mode;
    private Book $book;
    private string $command;
    private array $strings;

    public function __construct(string $mode)
    {
        $this->mode = $mode;
        $this->book = $this->initBook();
        $this->command = '<tbd>';
        $this->strings = [
            40 => "!|;:','.i",
            60 => "l",
            80 => "[]tI\"",
            100 => "fk*(){}<>",
            140 => "@",
        ];
    }

    private function initBook(): Book
    {
        switch ($this->mode) {
            case 'cli':
                $text = file_get_contents('sandbox/sample-book-text3.txt');
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
        $this->processBook();
        $commandBuilder = new CommandBuilder($this->book);
        $this->command = $commandBuilder->exec();
    }
    
    public function getCommand()
    {
        return $this->command;
    }

    private function processBook()
    {
        // Convert book text to array
        $text = str_split($this->book->getText());
        
        // Initialize tracking variables
        $lineLength = 0;
        $lines = 0;
        $currentPageNo = 0;
        $currentPageText = '';

        // Insert a blank page into the final book
        $this->book->addPage();

        // Loop through book text, one character at a time
        foreach ($text as $char) {

            // Intercept newlines
            if ($char === "\n") {
                $lines++;
                $lineLength = 0;
                $currentPageText .= '\\\n';
                continue;
            }
        
            // Add current character to current page
            $currentPageText .= $char;
            $this->book->updatePage($currentPageNo, $currentPageText);

            // Figure out the line length
            $lineLength += $this->calcLen($char);
        
            // If the line length is too long, start counting a new line
            if ($lineLength >= 19) {
                $lines++;
                //output("\$lines = $lines");
                $lineLength = 0;
            }
        
            // If the number of lines is too long, start a new page
            if ($lines > 13) {
                //output("Just started a new page");
                $this->book->addPage();
                $currentPageText = '';
                $currentPageNo++;
                $lineLength = 0;
                $lines = 0;
            }
        }
    }

    private function calcLen($char): float
    {
        $finalLen = 120;

        foreach ($this->strings as $len => $collection) {
            if (strpos($collection, $char) !== false) {
                $finalLen = $len;
                break;
            }
        }

        return $finalLen / 120;
    }

    public function output(string $msg = '')
    {
        if ($this->mode === 'cli') {
            echo $msg . "\n\n";
        } else {
            //echo '<form><textarea width="120" height="10">' . str_replace('\\n', '&#92;&#92;n', $msg) . '</textarea></form>';
            //$msg = str_replace('\\"', '&#92;&#92;&#34;', $msg);
            //$msg = str_replace("\n", '', $msg); // strip hidden newlines
            //$msg = htmlspecialchars($msg);
            $msg = str_replace("\n", '', $msg); // strip hidden newlines
            $msg = str_replace("\r", '', $msg); // strip hidden carriage returns

            // Serious debugging tool
            /*
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
            */
            echo '<p><code>' . $msg . '</code></p>';
        }
    }
}
