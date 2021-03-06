<?php

namespace App\Http\Controllers;

use App\PIN;
use App\Question;
use App\Response;
use App\ResponseData;
use App\Survey;
use App\Team;
use Illuminate\Http\Request;

class SurveyController extends Controller {


    public function __construct() {
        $this->middleware('auth')->except(['surveyOverview']);
    }

    public function questions($surveyId) {
        $survey = Survey::findOrFail($surveyId);
        if ($survey == null) {
            return response()->json(['error' => 'Not found.'], 404);
        }
        $questions = $survey->questions;
        return view('survey.allQuestions', compact('questions'));
    }

    public function edit($surveyId) {
        $survey = Survey::findOrFail($surveyId);
        if (\Auth::guest() || !\Auth::user()->can('edit_survey', $survey->owner))
            return back()->with(['message' => 'Error:You cannot edit this survey', 'message_type' => 'danger']);
        return view('survey.edit', compact('survey'));
    }

    public function showSurvey($survey) {
        $survey = Survey::findOrFail($survey);
        $team = $survey->owner;
        if ($team == null) {
            return back()->with(['message' => 'Error:That survey has no team associated with it!', 'message_type' => 'danger']);
        }
        if (\Auth::guest() || !\Auth::user()->can('survey_respond', $team))
            return back()->with(['message' => 'Error:You cannot respond to this survey', 'message_type' => 'danger']);
        if ($survey->archived)
            return back()->with(['message' => 'Error:That survey is archived!', 'message_type' => 'danger']);
        return view('survey.view', compact('survey', 'team'));
    }

    public function create() {
        return view('survey.create');
    }

    public function doCreate(Request $request) {
        $this->validate($request, [
            'select_team' => 'required',
            'survey_name' => 'required|max:255',
            'clone_from' => 'exists:surveys,id'], ['select_team.required' => 'A team must own this survey', 'survey_name.required' => 'Please provide a survey name']);
        $survey = new Survey();
        $survey->name = $request->survey_name;
        $survey->team_id = $request->select_team;
        $survey->creator_id = \Auth::user()->id;
        $survey->template = false;
        $survey->save();
        if (isset($request->clone_from))
            if ($request->clone_from != "-1") {
                // Clone questions
                $template = Survey::whereId($request->clone_from)->first();
                foreach ($template->questions as $template_question) {
                    $question = new Question();
                    $question->survey_id = $survey->id;
                    $question->order = $template_question->order;
                    $question->question_type = $template_question->question_type;
                    $question->question_name = $template_question->question_name;
                    $question->extra_data = $template_question->extra_data;
                    $question->save();
                    // Save pin
                    \Log::info($template_question->pin);
                    if ($template_question->pin != null) {
                        $pin = new PIN();
                        $pin->pin_data = $template_question->pin->pin_data;
                        $pin->question_id = $question->id;
                        $pin->save();
                        \Log::info("Saved: " . $pin);
                    }
                }
            }
        return redirect(route('survey.edit', ['id' => $survey->id]));
    }

    public function submitSurvey(Request $request, $survey) {
        $this->validate($request, [
            'team_number' => 'required|numeric',
            'match_number' => 'required|numeric'
        ]);
        $teamNumber = $request->team_number;
        $response = new Response();
        $response->submitter_id = $request->user()->id;
        $response->survey_id = $survey;
        $response->team = $teamNumber;
        $response->initial = $request->initial ? $request->initial : 0;
        $response->match_number = !$response->initial ? $request->match_number : -1;
        $response->save();
        $except = $request->except(['_method', '_token']);
        foreach ($except as $k => $v) {
            if (strpos($k, 'question') !== 0)
                continue;
            $k = str_replace('question-', '', $k);
            $response_data = new ResponseData();
            $response_data->question_id = $k;
            $response_data->response_id = $response->id;
            $response_data->response_data = is_array($v) ? $this->concatArray($v) : $v;
            $response_data->save();
        }
        return redirect(route('survey.view', $survey))->with(['message' => 'Response recorded!', 'message_type' => 'success']);
    }

    private function concatArray(array $array) {
        $toReturn = "";
        foreach ($array as $a) {
            $toReturn .= "$a, ";
        }
        return substr($toReturn, 0, strlen($toReturn) - 2);
    }

    public function delete($survey) {
        $survey = Survey::findOrFail($survey);
        if ($survey == null) {
            // TODO: Throw an error or something
        }
        return view('confirmAction')->with(['action' => "Delete Survey \"$survey->name\"",
            'route' => ['survey.doDelete', $survey->id], 'method' => 'delete', 'extra_desc' => ['Deletion of surveys will delete 
            all questions and allResponses associated with it. This action is PERMANENT and cannot be undone']]);
    }

    public function archive($survey) {
        $survey = Survey::findOrFail($survey);

        return view('confirmAction')->with(['action' => "Archive Survey \"$survey->name\"", 'route' => ['survey.doArchive', $survey->id],
            'method' => 'patch', 'extra_desc' => ['Archiving this survey will hide it from the list and it can no longer be responded to. You can
        unarchive it later from the team settings page']]);
    }

    public function doArchive($survey) {
        $survey = Survey::findOrFail($survey);

        $team = Team::whereId($survey->id)->first();

        $survey->archived = true;
        $survey->save();
        return redirect()->route('teams.show', $team->team_number);
    }

    public function showResponses(Survey $survey) {
        $team = $survey->owner;
        if (\Auth::guest() || !\Auth::user()->can('view_survey', $survey->owner))
            return back()->with(['message' => 'Error;You cannot view the results of this survey', 'message_type' => 'danger']);
        return view('survey.response')->with(compact('survey', 'team'));
    }

    public function doDelete($survey) {
        $survey = Survey::findOrFail($survey);
        foreach ($survey->responses as $response) {
            foreach ($response->data as $data) {
                \Log::info("Deleting ResponseData $data->id");
                $data->delete();
            }
            \Log::info("Deleting response $response->id");
            $response->delete();
        }
        foreach ($survey->questions as $question) {
            \Log::info("Deleting question $question->id");
            $question->delete();
        }
        $survey->delete();
        return redirect(route('teams.show', Team::whereId($survey->team_owner)->first()->team_number))->with(['message' => 'Survey Deleted!', 'message_type' => 'success']);
    }

    public function surveyOverview(Survey $survey, $teamNumber) {
        $responses = array();
        $initial_response = null;
        $questions = $survey->questions;

        foreach ($survey->responses as $response) {
            if ($response->team == $teamNumber) {
                if ($response->initial) {
                    $initial_response = $response;
                } else {
                    $responses[] = $response;
                }
            }
        }

        array_unshift($responses, $initial_response);

        return view('survey.teamOverview', compact('questions', 'initial_response', 'responses'));
    }
}
