<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;

class TestsController extends Controller
{
    public function __construct()
    {

    }

    public function index() {

        return view('Test.tests');
    }

    public function submit() {

        $data = $this->calculateSomme($_POST['rolls']);
        $sessionData = [
            'data' => $data,
            'rolls' => $_POST['rolls']
        ];
        if (isset($_POST['sum'])) {
            foreach ($data as $roll => $value) {
                $sum[] = $value['sum'];
            }
            $sessionData['sum'] = array_sum($sum);
        }

        return redirect('tests')->with($sessionData);
    }

    public function calculateSomme($query) {

        $results = array();
        try {
            $query = trim($query);
            $rolls = explode(" ", $query);
            if (empty($rolls[0])) {
                $rolls[0] = '1d6';
            }

            foreach ($rolls as $roll) {

                if (strpos($roll, 'd') === false) {
                    throw new \Exception('Le format d\'un des lancés n\'est pas du type XdY.');
                }

                $rollTemp = explode('d' , $roll);
                if (!is_numeric($rollTemp[0]) || !is_numeric($rollTemp[1])) {
                    throw new \Exception('Le nombre de dés ou le nombre de faces n\'est pas de type numérique.');
                } else if ($rollTemp[0] <= 0 || $rollTemp[0] > 100) {
                    throw new \Exception('Le nombre de dés doit être compris entre 1 et 100.');
                } else if ($rollTemp[1] < 2 || $rollTemp[1] > 100) {
                    throw new \Exception('Le nombre de faces doit être compris entre 2 et 100.');
                }

                $number_dice = (int) $rollTemp[0];
                $number_faces = (int) $rollTemp[1];

                $sum = 0;
                for($i = 1; $i <= $number_dice; $i++) {
                    $randNumber = rand(2, $number_faces);
                    $sum += $randNumber;
                    if(isset($_POST['sp'])) {
                        $results[$roll]['sp'][$i] = $randNumber;
                    }
                }
                $results[$roll]['sum'] = $sum;
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
        return $results;
    }
}