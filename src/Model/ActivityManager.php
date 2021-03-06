<?php
/**
 * Created by PhpStorm.
 * User: Adrien
 * Date: 15/04/2020
 */

namespace App\Model;

/**
 *
 */
class ActivityManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'activity';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * Get all row from database.
     *
     * @return array
     */
    public function selectActivitiesToBeDisplayed(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table . ' WHERE to_be_displayed=1')->fetchAll();
    }

    public function selectAllActivitiesForAdmin(): array
    {
        return $this->pdo->query('SELECT ac.name, a.age, l.day, l.time FROM lesson as l 
        INNER JOIN activity as ac ON ac.id=l.activity_id JOIN age as a ON a.id=l.age_id')->fetchAll();
    }

    public function getActivityList(): array
    {
        return $this->pdo->query('SELECT id, name FROM activity')->fetchAll();
    }

    public function updateActivity(array $activity): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name, 
        `description` = :description, `picture` = :picture , `to_be_displayed` = :to_be_displayed WHERE id=:id");
        $statement->bindValue('name', $activity['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $activity['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $activity['picture'], \PDO::PARAM_STR);
        $statement->bindValue('id', $activity['id'], \PDO::PARAM_INT);
        $statement->bindValue('to_be_displayed', $activity['to_be_displayed'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function insert(array $activity):bool
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`name`, `description`, `picture`, `to_be_displayed`) 
            VALUES 
            (:name, :description, :picture, :to_be_displayed)");

        $statement->bindValue('name', $activity['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $activity['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $activity['picture'], \PDO::PARAM_STR);
        $statement->bindValue('to_be_displayed', $activity['to_be_displayed'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $lessonManager=new LessonManager();
        $lessons=$lessonManager->selectEverythingForOneById($id);

        foreach ($lessons as $lesson) {
            $lessonId=$lesson['lesson_id'];
            $lessonManager->delete($lessonId);
        }
    }
}
