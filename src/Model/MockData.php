<?php

namespace App\Model;

use DateTime;

class MockData
{
    public function getEmployer()
    {
        return [
            [
                'id' => 1,
                'title' => 'Иванов Иван Иванович',
                'company' => 'Интаро Софт',
                'competence' => 'Backend (symfony)',
                'employment' => 'Штат',
                'grade' => 'junior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 11
            ],
            [
                'id' => 2,
                'title' => 'Петров Николай Николаевич',
                'company' => 'Интаро Софт',
                'competence' => 'Backend (bitrix)',
                'employment' => 'Штат',
                'grade' => 'middle',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 12
            ],
            [
                'id' => 3,
                'title' => 'Поленников Артем Андреевич',
                'company' => 'Интаро Софт',
                'competence' => 'Frontend (vue)',
                'employment' => 'Штат',
                'grade' => 'junior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 13
            ],
            [
                'id' => 4,
                'title' => 'Жидков Эдуард Сергеевич',
                'company' => 'Интаро Софт',
                'competence' => 'Менеджер',
                'employment' => 'Штат',
                'grade' => 'senior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 13
            ],
            [
                'id' => 5,
                'title' => 'Белов Андрей Николаевич',
                'company' => 'Интаро Софт',
                'competence' => 'Frontend (vue)',
                'employment' => 'Штат',
                'grade' => 'senior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 14
            ],
            [
                'id' => 6,
                'title' => 'Корнеев Виталий Викторович',
                'company' => 'Интаро Софт',
                'competence' => 'Аналитик',
                'employment' => 'СЗ',
                'grade' => 'middle',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 15
            ]

        ];
    }

    public function getUser()
    {
        return [
            [
                'id' => 1,
                'title' => 'Иванов Иван Иванович',
                'company' => 'Интаро Софт',
                'competence' => 'Backend (symfony)',
                'employment' => 'Штат',
                'grade' => 'junior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 11
            ],
            [
                'id' => 2,
                'title' => 'Петров Николай Николаевич',
                'company' => 'Интаро Софт',
                'competence' => 'Backend (bitrix)',
                'employment' => 'Штат',
                'grade' => 'middle',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 12
            ],
            [
                'id' => 3,
                'title' => 'Поленников Артем Андреевич',
                'role' => 'dev',
                'login' => 'polennikov',
                'password' => 'polennikov'
            ],
            [
                'id' => 4,
                'title' => 'Жидков Эдуард Сергеевич',
                'company' => 'Интаро Софт',
                'competence' => 'Менеджер',
                'employment' => 'Штат',
                'grade' => 'senior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 13
            ],
            [
                'id' => 5,
                'title' => 'Белов Андрей Николаевич',
                'company' => 'Интаро Софт',
                'competence' => 'Frontend (vue)',
                'employment' => 'Штат',
                'grade' => 'senior',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 14
            ],
            [
                'id' => 6,
                'title' => 'Корнеев Виталий Викторович',
                'company' => 'Интаро Софт',
                'competence' => 'Аналитик',
                'employment' => 'СЗ',
                'grade' => 'middle',
                'intaroHours' => '',
                'dateEnd' => '',
                'idB24' => 15
            ]

        ];
    }

    public function getProject()
    {
        return [
            [
                'id' => 1,
                'title' => 'Интернет магазин Лакост',
                'redmine_url' => 'http//:',
                'status' => 'выполнение'
            ],
            [
                'id' => 2,
                'title' => 'Проект Inventive',
                'redmine_url' => 'http//:',
                'status' => 'выполнение'
            ],
            [
                'id' => 3,
                'title' => 'Проект Nike',
                'redmine_url' => 'http//:',
                'status' => 'выполнение'
            ],
            [
                'id'=>4,
                'title' => 'Проект Erborian',
                'redmine_url' => 'http//:',
                'status'=>'выполнение'
            ],
            [
                'id'=>5,
                'title' => 'Проект Loccitane',
                'redmine_url' => 'http//:',
                'status'=>'выполнение'
            ]
        ];
    }

    public function getTaskFilter($month, $year, $projectId)
    {
        $arrayTask = [];
        foreach ($this->getTask() as $task){
            $datetime = new DateTime($task['date']);
            if ($datetime->format('m') == $month
                && $datetime->format('Y') == $year
                && $projectId == $task['project_id']) {
                $arrayTask[] = $task;
            }
        }
        return $arrayTask;
    }

    public function getTaskFilterInfo($month, $year, $employerId)
    {
        $arrayTask = [];
        foreach ($this->getTask() as $task){
            $datetime = new DateTime($task['date']);
            if ($datetime->format('m') == $month
                && $datetime->format('Y') == $year
                && $employerId == $task['employer_id']) {
                $arrayTask[] = $task;
            }
        }
        return $arrayTask;
    }

    public function getTask()
    {
        return [
            [
                'id' => 1,
                'title' => 'Проработка ТЗ',
                'date' => '03.04.2023',
                'time' => 1.5,
                'comment' => '',
                'employer_id' => 1,
                'employer_name' => 'Иванов Иван Иванович',
                'project_id' => 1,
                'project_name' => 'Интернет магазин Лакост',
                'date_update' => '03.04.2023 16:15:43',
                'task_id' => 1
            ],
            [
            'id' => 2,
            'title' => 'Проработка ТЗ',
            'date' => '03.06.2023',
            'time' => 1.5,
            'comment' => '',
            'employer_id' => 3,
            'employer_name' => 'Иванов Иван Иванович',
            'project_id' => 1,
            'project_name' => 'Интернет магазин Лакост',
            'date_update' => '03.04.2023 16:15:43',
            'task_id' => 1
            ],
            [
                'id' => 3,
                'title' => 'Проработка ТЗ',
                'date' => '03.06.2023',
                'time' => 1.5,
                'comment' => '',
                'employer_id' => 3,
                'employer_name' => 'Иванов Иван Иванович',
                'project_id' => 1,
                'project_name' => 'Интернет магазин Лакост',
                'date_update' => '03.04.2023 16:15:43',
                'task_id' => 1
            ]
        ];
    }
}
