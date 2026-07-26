<?php

namespace App\Services;

use App\Models\Skill;

class SkillService
{
    public function getSkills(array $filters = [])
    {
        $query = Skill::query()->orderBy('display_order', 'asc')->orderBy('name', 'asc');

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function createSkill(array $data): Skill
    {
        return Skill::create($data);
    }

    public function updateSkill(Skill $skill, array $data): Skill
    {
        $skill->update($data);
        return $skill->fresh();
    }

    public function deleteSkill(Skill $skill): bool
    {
        return (bool) $skill->delete();
    }
}
