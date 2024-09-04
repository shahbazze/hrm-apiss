<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TeamService;

class TeamController extends Controller
{
    protected $teamService;

    // Inject TeamService into the controller
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function addTeam(Request $request)
    {
        return $this->teamService->addTeam($request);
    }

    public function deleteTeam(Request $request)
    {
        return $this->teamService->deleteTeam($request);
    }

    public function addTeamMembers(Request $request)
    {
        return $this->teamService->addTeamMembers($request);
    }
  
    public function setTeamLead(Request $request)
    {
        return $this->teamService->setTeamLead($request);
    }

    public function removeMultipleTeamMembers(Request $request)
    {
        return $this->teamService->removeMultipleTeamMembers($request);
    }

}
