<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\BridgePage\BridgeTemplate;

class BridgeTemplateController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth' , ['except'=>array('getList')]);
        $this->middleware('access' , ['except'=>array('getList')]);
        $this->middleware('admin',['except'=>array('getList')]);
        $this->model = new BridgeTemplate();
    }

    public function getList()
    {
        $templates = $this->model->with('type')->whereNull('deleted_at')->get();
        foreach ($templates as $template)
        {
            $template->name = $template->type->name . ' - ' . $template->name;
            $template->preview_url  = 'templates/bptemplate/' . $template->type->folder_slug . '/' . $template->folder_slug . '/snippets.html';
            $template->control_url = 'templates/bcontrol/' . $template->type->folder_slug . '/' . $template->folder_slug . '/control.html';
        }

        return $templates;
    }
}