<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Room;
use App\Entity\ScheduleEntry;
use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SchedulerController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/scheduler', name: 'app_scheduler')]
    public function index(): Response
    {
        $user = $this->getUser();
        $school = $user->getSchool(); // Assuming User has getSchool()

        $lessons = $this->entityManager->getRepository(Lesson::class)->findBy(['school' => $school]);
        $rooms = $this->entityManager->getRepository(Room::class)->findBy(['school' => $school]);
        $timeSlots = $this->entityManager->getRepository(TimeSlot::class)->findBy(['school' => $school], ['dayOfWeek' => 'ASC', 'periodNumber' => 'ASC']);
        $entries = $this->entityManager->getRepository(ScheduleEntry::class)->findBy(['school' => $school]);

        // Organize entries for easy lookup in grid
        // Format: $schedule[timeSlotId][roomId] = ScheduleEntry
        $schedule = [];
        foreach ($entries as $entry) {
            $tsId = $entry->getTimeSlot()->getId();
            $rId = $entry->getRoom()?->getId() ?? 'no_room';
            $schedule[$tsId][$rId] = $entry;
        }

        return $this->render('scheduler/index.html.twig', [
            'lessons' => $lessons,
            'rooms' => $rooms,
            'timeSlots' => $timeSlots,
            'schedule' => $schedule,
        ]);
    }

    #[Route('/scheduler/generate', name: 'app_scheduler_generate', methods: ['POST'])]
    public function generate(\App\Service\AiSchedulingService $aiService): Response
    {
        $user = $this->getUser();
        $school = $user->getSchool();

        try {
            $result = $aiService->generateSchedule($school);
            $this->addFlash('success', 'Schedule generated successfully!');
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
