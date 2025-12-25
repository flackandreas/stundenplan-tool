<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use App\Entity\Room;
use App\Entity\School;
use App\Entity\StudentClass;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Create Main School
        $school = new School();
        $school->setName('Gymnasium Musterstadt');
        $school->setAddress('Musterstraße 1, 12345 Musterstadt');
        $school->setSettings([]);
        $manager->persist($school);

        // 2. Create Admin User
        $user = new User();
        $user->setEmail('admin@musterstadt.de');
        $user->setFirstName('Admin');
        $user->setLastName('User');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setSchool($school);
        $manager->persist($user);

        // 3. Create Subjects
        $subjects = [];
        $subjectData = [
            ['Mathe', 'M', '#FF0000'],
            ['Deutsch', 'D', '#0000FF'],
            ['Englisch', 'E', '#FFFF00'],
            ['Sport', 'Spo', '#00FF00'],
            ['Geschichte', 'G', '#808080'],
            ['Kunst', 'Ku', '#FFA500'],
            ['Musik', 'Mu', '#FFC0CB'],
            ['Physik', 'Ph', '#00FFFF'],
            ['Chemie', 'Ch', '#800080'],
            ['Biologie', 'Bio', '#008000'],
        ];

        foreach ($subjectData as $data) {
            $subject = new Subject();
            $subject->setName($data[0]);
            $subject->setShortcode($data[1]);
            $subject->setColor($data[2]);
            $subject->setSchool($school);
            $manager->persist($subject);
            $subjects[] = $subject;
        }

        // 4. Create Teachers
        $teachers = [];
        $faker = \Faker\Factory::create('de_DE');
        
        for ($i = 0; $i < 15; $i++) {
            $teacher = new Teacher();
            $teacher->setName($faker->name());
            $teacher->setShortcode(strtoupper($faker->lexify('???')));
            $teacher->setEmail($faker->email());
            $teacher->setSchool($school);
            $manager->persist($teacher);
            $teachers[] = $teacher;
        }

        // 5. Create Rooms
        $rooms = [];
        $roomData = [
            ['R101', 30],
            ['R102', 30],
            ['Sporthalle', 60]
        ];

        foreach ($roomData as $data) {
            $room = new Room();
            $room->setName($data[0]);
            $room->setCapacity($data[1]);
            $room->setSchool($school);
            $manager->persist($room);
            $rooms[] = $room;
        }

        // 6. Create Classes
        $classes = [];
        for ($level = 5; $level <= 10; $level++) {
            foreach (['a', 'b'] as $letter) {
                $class = new StudentClass();
                $class->setName($level . $letter);
                $class->setLevel($level);
                $class->setSchool($school);
                $manager->persist($class);
                $classes[] = $class;
            }
        }

        // 7. Create Lessons (Demand)
        // Scenario: Each class needs lessons
        foreach ($classes as $class) {
            foreach ($subjects as $index => $subject) {
                // Determine hours based on subject type logic (simplified)
                $hours = ($index < 3) ? 4 : 2; // First 3 subjects get 4h, others 2h
                
                $lesson = new Lesson();
                $lesson->setSchool($school);
                $lesson->setStudentClass($class);
                $lesson->setSubject($subject);
                $lesson->setTeacher($teachers[array_rand($teachers)]); // Random teacher assignment
                $lesson->setHoursPerWeek($hours);
                $lesson->setDoublePeriod($hours >= 2);
    
                if (rand(0, 100) > 80) {
                     $lesson->setRoom($rooms[array_rand($rooms)]);
                }

                $manager->persist($lesson);
            }

        }

        // 8. Create TimeSlots (The Scheduler Grid)
        $startTime = new \DateTime('08:00:00');
        for ($day = 1; $day <= 5; $day++) {
            $currentTime = clone $startTime;
            for ($period = 1; $period <= 8; $period++) {
                $timeSlot = new \App\Entity\TimeSlot();
                $timeSlot->setSchool($school);
                $timeSlot->setDayOfWeek($day);
                $timeSlot->setPeriodNumber($period);
                $timeSlot->setStartTime(clone $currentTime);
                
                // 45 min lesson
                $currentTime->modify('+45 minutes');
                $timeSlot->setEndTime(clone $currentTime);
                
                // 15 min break (simplified, every lesson)
                // In reality, breaks are usually after 2nd, 4th, 6th.
                $currentTime->modify('+10 minutes'); 

                $manager->persist($timeSlot);
            }
        }

        $manager->flush();
    }
}
