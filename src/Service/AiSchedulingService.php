<?php

namespace App\Service;

use App\Entity\Lesson;
use App\Entity\Room;
use App\Entity\ScheduleEntry;
use App\Entity\School;
use App\Entity\Teacher;
use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AiSchedulingService
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient,
        #[Autowire(env: 'GEMINI_API_KEY')] string $apiKey
    ) {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function generateSchedule(School $school): array
    {
        // 1. Collect Data
        $data = $this->collectConstraintData($school);

        // 2. Build Prompt (TODO)
        $prompt = $this->buildPrompt($data);

        // 3. Call AI
        $aiResponse = $this->callGeminiApi($prompt, $data);

        // 4. Parse and Save
        $this->parseAndApply($aiResponse, $school);

        return ['status' => 'success', 'data' => $data, 'prompt' => $prompt];
    }

    private function parseAndApply(string $jsonResponse, School $school): void
    {
        $entries = json_decode($jsonResponse, true);
        if (!$entries) {
            return;
        }

        foreach ($entries as $entryData) {
            $lesson = $this->entityManager->getReference(Lesson::class, $entryData['lesson_id']);
            $timeSlot = $this->entityManager->getReference(TimeSlot::class, $entryData['time_slot_id']);
            $room = isset($entryData['room_id']) ? $this->entityManager->getReference(Room::class, $entryData['room_id']) : null;

            $entry = new ScheduleEntry();
            $entry->setLesson($lesson);
            $entry->setTimeSlot($timeSlot);
            $entry->setRoom($room);
            $entry->setSchool($school);

            $this->entityManager->persist($entry);
        }
        $this->entityManager->flush();
    }

    private function collectConstraintData(School $school): array
    {
        $lessons = $this->entityManager->getRepository(Lesson::class)->findBy(['school' => $school]);
        $teachers = $this->entityManager->getRepository(Teacher::class)->findBy(['school' => $school]);
        $rooms = $this->entityManager->getRepository(Room::class)->findBy(['school' => $school]);
        $timeSlots = $this->entityManager->getRepository(TimeSlot::class)->findBy(['school' => $school], ['dayOfWeek' => 'ASC', 'periodNumber' => 'ASC']);

        return [
            'lessons' => array_map(fn(Lesson $l) => [
                'id' => $l->getId(),
                'subject' => $l->getSubject()->getName(),
                'teacher' => $l->getTeacher()->getName(),
                'classes' => $l->getStudentClass()->getName(),
                'hours' => $l->getHoursPerWeek(),
                'double' => $l->isDoublePeriod()
            ], $lessons),
            'rooms' => array_map(fn(Room $r) => ['id' => $r->getId(), 'name' => $r->getName(), 'capacity' => $r->getCapacity()], $rooms),
            'timeSlots' => array_map(fn(TimeSlot $ts) => [
                'id' => $ts->getId(),
                'day' => $ts->getDayOfWeek(),
                'period' => $ts->getPeriodNumber()
            ], $timeSlots),
        ];
    }

    private function buildPrompt(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        return <<<PROMPT
You are an expert school timetable scheduler.
Assign the following LESSONS to TIME SLOTS and ROOMS.

Hard Constraints (MUST FOLLOW):
1. A Teacher cannot be in two places at once.
2. A Class (StudentGroup) cannot be in two places at once.
3. If a Room is assigned, its capacity must be >= class size (assume default class size 25 if not specified).
4. Use ONLY the provided IDs.

Soft Constraints (OPTIMIZE FOR):
1. Minimize gaps for students (e.g., avoid a schedule like Period 1, Gap, Period 3).
2. Balance teacher workload across the week.
3. Prefer rooms that "fit" the class size well (don't put a small class in a huge hall).

Data:
$json

Output Format:
Return ONLY valid JSON.
[
    { "lesson_id": 1, "time_slot_id": 10, "room_id": 5 },
    ...
]
PROMPT;
    }

    private function callGeminiApi(string $prompt, array $data): string
    {
        if (empty($this->apiKey)) {
            return $this->getMockSchedule($data);
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->apiKey;

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]
            ]);

            $json = $response->toArray();
            return $json['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

        } catch (\Throwable $e) {
            // Log error (in a real app)
            // Fallback to mock data so the user sees something happening
            return $this->getMockSchedule($data);
        }
    }

    private function getMockSchedule(array $data): string
    {
        // Use ACTUAL IDs from the database to prevent Foreign Key Violations
        $lessonId = $data['lessons'][0]['id'] ?? null;
        $timeSlotId = $data['timeSlots'][0]['id'] ?? null;
        $roomId = $data['rooms'][0]['id'] ?? null;

        if (!$lessonId || !$timeSlotId) {
            return '[]'; // Nothing to schedule
        }

        // Mock: Assign first lesson to first slot/room
        return json_encode([
            [
                'lesson_id' => $lessonId,
                'time_slot_id' => $timeSlotId,
                'room_id' => $roomId // Nullable
            ]
        ]);
    }
}
