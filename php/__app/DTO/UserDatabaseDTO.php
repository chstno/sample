<?php


namespace DTO;


use Core\DTO\BaseDTO;

class UserDatabaseDTO extends BaseDTO
{
    protected static array $map = [
        'id' => 'id',
        'name' => 'name',
        'gender' => 'gender',
        'birth_date' => 'birthDate',
    ];

    // example with dateTime reformat

    /**
     * @param int $unixTimestamp
     *
     * if we get "birth_date" value from database, we convert it to DateTime before writing into birthDate
     * @return \DateTime
     */
    public function __birth_date(int $unixTimestamp): \DateTime
    {
        // incorrect, something wrong with time zones (even though it should always be utc)
        // (new \DateTime())->setTimestamp($unixTimestamp);

        return \DateTime::createFromFormat('U', $unixTimestamp);
    }

    /**
     * @param \DateTime $dateTime
     *
     * same logic as above, but in reverse
     * @return int
     */
    public function __birthDate(\DateTime $dateTime): int
    {
        return $dateTime->getTimestamp();
    }
}