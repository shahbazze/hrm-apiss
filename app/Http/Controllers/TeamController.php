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

    /**
     * Handle the addition of a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeam(Request $request)
    {
        return $this->teamService->addTeam($request);
    }

    /**
     * Handle the addition of multiple team members.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeamMembers(Request $request)
    {
        return $this->teamService->addTeamMembers($request);
    }

    /**
     * Set a new team lead for the specified team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTeamLead(Request $request)
    {
        return $this->teamService->setTeamLead($request);
    }

    /**
     * Remove a user from a team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTeamMember(Request $request)
    {
        return $this->teamService->removeTeamMember($request);
    }
}
