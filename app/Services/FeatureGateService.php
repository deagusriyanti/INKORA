<?php

namespace App\Services;

class FeatureGateService
{
    public static function grade(): string
    {
        $grade = strtolower((string) config('printing.grade', 'basic'));
        return in_array($grade, ['basic', 'standard', 'premium'], true) ? $grade : 'basic';
    }

    public static function gradeLabel(): string
    {
        $labels = config('printing.grade_labels', []);
        return $labels[self::grade()] ?? 'Basic Edition';
    }

    public static function allows(string $feature): bool
    {
        $grade = self::grade();
        $allowedFeatures = config("printing.features.{$grade}", []);
        return in_array($feature, $allowedFeatures, true);
    }

    public static function ensure(string $feature): void
    {
        if (!self::allows($feature)) {
            abort(403, 'Fitur ini tidak tersedia pada paket Inkora ' . self::gradeLabel() . '.');
        }
    }
}