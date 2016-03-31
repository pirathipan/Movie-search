<?php
namespace Controller;

use Doctrine\DBAL\Query\QueryBuilder;

class IndexController
{
    public function indexAction()
    {
        include("../views/search.php");
    }

    public function searchAction()
    {
        //se connecter à la bdd
        header('Content-Type: application/json');

        $conn = \MovieSearch\Connexion::getInstance();

        $title = $_POST['title'];
        $duration = $_POST['duration'];
        $yearStart = $_POST['year_start'];
        $yearEnd = $_POST['year_end'];
        $gender = $_POST['gender'];
        $author = $_POST['author'];

        if (isset($title)) {
            $newTitle = " SELECT * FROM film_director
                          INNER JOIN artist AS a
                          ON artist_id = a.id
                          INNER JOIN film AS f
                          ON film_director.film_id = f.id
                          WHERE f.title
                          LIKE '%$title%' ";
        }

        if (isset($duration)) {

            if ($duration == "all") {
                $newTime = "";

            } else if ($duration == "lessHour") {
                $newTime = " AND duration < 3600 ";

            } else if ($duration == "between1Hour") {
                $newTime = " AND duration BETWEEN 3600 AND 5400 ";

            } else if ($duration == "between2Hour") {
                $newTime = " AND duration BETWEEN 5400 AND 9000 ";

            } else if ($duration == "moreHour") {
                $newTime = " AND duration > 9000 ";
            }
        }

        if (isset($yearStart)) {
            $newYearStart = " AND year >= '$yearStart' ";
        }
        if (empty($yearStart)) {
            $newYearStart = "";
        }
        if (isset($yearEnd)) {
            $newYearEnd = " AND year <= '$yearEnd' ";
        }
        if (empty($yearEnd)) {
            $newYearEnd = "";
        }


        if (isset($author)) {
            $Producer = " AND ( a.first_name LIKE '%$author%' OR a.last_name LIKE '%$author%')  ";
        }
        if (empty($author)) {
            $Producer = "";
        }

        if (isset($gender)) {
            if ($gender == "") {
                $newGender = "";
            } else if ($gender == "M") {
                $newGender = " AND ( gender = 'M') ";
            } else if ($gender == "F") {
                $newGender = " AND ( gender = 'F') ";
            }
        }

        $stmt = $conn->prepare($newTitle . $newTime . $newYearStart . $newYearEnd .$Producer. $newGender.'GROUP BY title');
        $stmt->execute();
        $films = $stmt->fetchAll();
        return json_encode(["films" => $films]);
    }
}