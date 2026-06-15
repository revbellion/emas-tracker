<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Models\FamilyMember;
use App\Services\FamilyMemberService;
use Inertia\Inertia;

class FamilyMemberController extends Controller
{
    public function __construct(private FamilyMemberService $familyMemberService) {}

    public function index()
    {
        return Inertia::render('FamilyMembers/Index', [
            'members' => $this->familyMemberService->getAll(request()->only(['search', 'status'])),
        ]);
    }

    public function store(StoreFamilyMemberRequest $request)
    {
        $this->familyMemberService->create($request->validated());
        return redirect()->route('family-members.index')->with('success', 'Anggota keluarga berhasil ditambahkan');
    }

    public function show(FamilyMember $familyMember)
    {
        return Inertia::render('FamilyMembers/Show', [
            'member' => $familyMember,
            'transactions' => $familyMember->transactions()->with('creator')->latest()->paginate(10),
        ]);
    }

    public function update(UpdateFamilyMemberRequest $request, FamilyMember $familyMember)
    {
        $this->familyMemberService->update($familyMember, $request->validated());
        return redirect()->route('family-members.index')->with('success', 'Anggota keluarga berhasil diperbarui');
    }

    public function deactivate(FamilyMember $familyMember)
    {
        $this->familyMemberService->deactivate($familyMember);
        return redirect()->route('family-members.index')->with('success', 'Anggota keluarga berhasil dinonaktifkan');
    }
}
