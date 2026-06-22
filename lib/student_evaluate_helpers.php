<?php

declare(strict_types=1);

function studentEvaluateBuildCategoryList(array $categories, array $questionsByCategory): array
{
    $result = [];
    $totalActiveQuestions = 0;

    foreach ($categories as $category) {
        $categoryId = $category['id'] ?? null;
        $questions = $categoryId !== null ? ($questionsByCategory[$categoryId] ?? []) : [];

        if ($questions === []) {
            continue;
        }

        $category['questions'] = array_values($questions);
        $result[] = $category;
        $totalActiveQuestions += count($questions);
    }

    return [
        'categories' => $result,
        'total_active_questions' => $totalActiveQuestions,
    ];
}

function studentEvaluateMaxRatingValue(array $ratingScales, int $default = 5): int
{
    if ($ratingScales === []) {
        return $default;
    }

    $firstScale = $ratingScales[0]['scale_value'] ?? $default;

    return (int) $firstScale;
}

function studentEvaluateDisplayFacultyName(array $subject): string
{
    $parts = [
        trim((string) ($subject['first_name'] ?? '')),
        trim((string) ($subject['mid_name'] ?? '')),
        trim((string) ($subject['last_name'] ?? '')),
    ];

    $name = trim(implode(' ', array_filter($parts, static fn(string $part): bool => $part !== '')));

    if (($subject['role'] ?? '') === 'admin') {
        $name .= ' (Admin)';
    }

    return $name;
}

