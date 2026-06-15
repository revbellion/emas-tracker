<?php

namespace App\Services;

use App\Models\FamilyMember;
use Illuminate\Support\Facades\DB;

class FamilyMemberService
{
    public function getAll(array $filters = [])
    {
        $query = FamilyMember::query();

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        return $query->withCount('transactions')->latest()->paginate(10);
    }

    public function find(int $id): FamilyMember
    {
        return FamilyMember::with('transactions')->findOrFail($id);
    }

    public function create(array $data): FamilyMember
    {
        return DB::transaction(function () use ($data) {
            $member = FamilyMember::create($data);

            app(ActivityLogService::class)->log(
                'CREATE_MEMBER',
                "Anggota keluarga {$member->name} ditambahkan",
                $member
            );

            return $member;
        });
    }

    public function update(FamilyMember $member, array $data): FamilyMember
    {
        return DB::transaction(function () use ($member, $data) {
            $old = $member->toArray();
            $member->update($data);

            app(ActivityLogService::class)->log(
                'UPDATE_MEMBER',
                "Anggota keluarga {$member->name} diperbarui",
                $member,
                $old,
                $member->toArray()
            );

            return $member;
        });
    }

    public function deactivate(FamilyMember $member): FamilyMember
    {
        return DB::transaction(function () use ($member) {
            $member->update(['is_active' => false]);

            app(ActivityLogService::class)->log(
                'DEACTIVATE_MEMBER',
                "Anggota keluarga {$member->name} dinonaktifkan",
                $member
            );

            return $member;
        });
    }

    public function getActiveMembers()
    {
        return FamilyMember::where('is_active', true)->get();
    }
}
