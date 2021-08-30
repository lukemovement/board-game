<?php

namespace App\Domain\Common\Type;
class Position {
    
    private int $column; // Left to right
    private int $row; // Top to bottom

    public function __construct(array $position) {
        $this->column = (int) $position[0];
        $this->row = (int) $position[1];
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function setColumn(int $column): self
    {
        $this->column = $column;

        return $this;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): self
    {
        $this->row = $row;

        return $this;
    }

    public function up()
    {
        return new Position([
            $this->column,
            $this->row - 1
        ]);
    }

    public function down()
    {
        return new Position([
            $this->column,
            $this->row + 1
        ]);
    }

    public function left()
    {
        return new Position([
            $this->column - 1,
            $this->row
        ]);
    }

    public function right()
    {
        return new Position([
            $this->column + 1,
            $this->row
        ]);
    }

    public function matches(Position $position)
    {
        return $this->column === $position->getColumn() &&
            $this->row === $position->getRow();
    }
}