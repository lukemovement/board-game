<?php

namespace App\Domain\Common\Type;

class Position {
    
    private string $column; // Left to right
    private string $row; // Top to bottom

    public function __construct(array $position) {
        $this->column = $position[0];
        $this->row = $position[1];
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    public function getRow(): string
    {
        return $this->row;
    }

    public function setRow(string $row): self
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