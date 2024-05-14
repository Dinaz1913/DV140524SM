<?php


declare(strict_types=1);

// Slot Machine
$personData =
    json_decode(file_get_contents
    ('Jack.json'), true);

echo "Hello Mr.{$personData['name']} {$personData['surname']}! \n";
echo "You have {$personData['credits']} € on your account. \n\n";

if ($personData['credits'] > 0) {
    $columns = (int)readline("Enter column quantity: ");
}

while ($personData['credits'] > 0) {
    echo "1. Enter amount of your bet. \n";
    $bet = (int)readline("Your Bet: ");
    echo "\n\n";
    if ($bet > $personData['credits']) {
        echo "You don't have enough money to place this bet!\n";
        continue;
    }

    $personData['credits'] -= $bet;

    $gamingSymbols = [
        "\u{265B}", "\u{2727}", "\u{20AC}", "\u{2660}",
        "\u{2663}", "\u{2662}", "\u{2665}", "\u{20AC}", "\u{20AC}",
        "\u{265B}", "\u{2727}", "\u{20AC}", "\u{2660}", "\u{2663}",
        "\u{2662}", "\u{2665}", "\u{20AC}", "\u{20AC}", "\u{2662}",
        "\u{2665}", "\u{20AC}", "\u{20AC}", "\u{265B}", "\u{2727}",
        "\u{20AC}", "\u{2660}", "\u{2663}", "\u{2662}", "\u{2665}",
        "\u{20AC}", "\u{20AC}", "\u{265B}", "\u{2727}", "\u{20AC}",
        "\u{2660}", "\u{2663}", "\u{2662}", "\u{2665}", "\u{20AC}",
        "\u{20AC}", "\u{2662}", "\u{2665}", "\u{20AC}", "\u{20AC}"
    ];

    $board =
        array_fill(0, 3,
            array_fill(0, $columns, ''));
    shuffle($gamingSymbols);

    $symbolIndex = 0;
    foreach ($board as &$row) {
        foreach ($row as &$cell) {
            if ($symbolIndex < count($gamingSymbols)) {
                $cell = $gamingSymbols[$symbolIndex++];
            } else {
                break 2;
            }
        }
    }

    echo "1. Bet goes for horizontal lines full \n" .
        "2. Bet goes for vertical lines and horizontal full \n" .
        "3. Bet goes for diagonal lines and previous lines combined \n";
    $choice = (int)readline("Enter your combination: ");
    echo "\n\n";

    $outcomes = checkTheWinner($board, $bet, $choice, $columns);
    $totalSum = array_sum($outcomes);

    echo "Your Win: " . implode(", ", $outcomes) . "\n\n";
    echo "Total Count: " . count($outcomes) . "\n\n";
    echo "Total Win: $totalSum\n\n";

    displayBoard($board, $columns);

    $personData['credits'] += $totalSum;

    file_put_contents('Jack.json',
        json_encode($personData, JSON_PRETTY_PRINT));


    if ($personData['credits'] <= 0) {
        echo "\nYou've run out of money! Game over.\n";
        break;
    } else {
        echo "You have {$personData['credits']} € remaining.\n\n";
    }
}

function checkTheWinner
(array $board, int $bet, int $choice, int $columns):
array
{
    $outcomes = [];

    switch ($choice) {
        case 1:
            foreach ($board as $row) {
                if (count(array_unique($row)) === 1 && $row[0]) {
                    $outcomes[] = $bet * 15;
                }
            }
            break;

        case 2:
            // Check rows
            foreach ($board as $row) {
                if (count(array_unique($row)) === 1 && $row[0]) {
                    $outcomes[] = $bet * 10;
                }
            }
            // Check columns
            for ($i = 0; $i < $columns; $i++) {
                $column = array_column($board, $i);
                if (count(array_unique($column)) === 1 && $column[0]) {
                    $outcomes[] = $bet * 10;
                }
            }
            break;

        case 3:
            // Check rows
            foreach ($board as $row) {
                if (count(array_unique($row)) === 1 && $row[0]) {
                    $outcomes[] = $bet * 5;
                }
            }

            // Check columns
            for ($i = 0; $i < $columns; $i++) {
                $column = array_column($board, $i);
                if (count(array_unique($column)) === 1 && $column[0]) {
                    $outcomes[] = $bet * 5;
                }
            }


            if ($columns >= 3) {
                if (count(array_unique(array($board[0][0],
                        $board[1][1], $board[2][2]))) === 1
                    && $board[0][0]) {
                    $outcomes[] = $bet * 5; // Add outcome to array
                }
                if (count(array_unique(array
                    ($board[0][$columns - 1],
                        $board[1][1],
                        $board[2][0]))) === 1 &&
                    $board[0][$columns - 1]) {
                    $outcomes[] = $bet * 5; // Add outcome to array
                }
            }
            break;

        default:
            echo "\nThere are no lines of this number\n";
            break;
    }

    return $outcomes; // Return array of outcomes
}

function displayBoard(array $board, int $columns): void
{
    echo "╔═══════════════════════════════════════════╗\n";
    echo "║               [Slot Machine]              ║\n";
    echo "╠═══════════════════════════════════════════╣\n";
    for ($i = 0; $i < count($board); $i++) {
        echo "║ ";
        for ($j = 0; $j < $columns; $j++) {
            echo " {$board[$i][$j]} ";
            if ($j < $columns - 1) {
                echo " | ";
            }
        }
        echo " ║\n";
        if ($i < count($board) - 1) {
            echo "╠═══════════════════════════════════════════╣\n";
        }
    }
    echo "╠═══════════════════════════════════════════╣\n";
    echo "║                 [ LEVER ]                 ║\n";
    echo "╚═══════════════════════════════════════════╝\n";
}

