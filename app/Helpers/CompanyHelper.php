<?php namespace App\Helpers;

use App\Models\Company;

class CompanyHelper
{
    public static function getCurrentCompanySites($value='')
    {
        $companies = Company::getUsersCompanies();
        foreach($companies['companies'] as $key=>$company) {
            if (isset($company['selected'])) {
                $selected = $company['id'];
                break;
            }
        }
        if(isset($selected)){
            foreach ($companies['sites'] as $index => $value) {
                if($index==$selected){
                    $selected_site = $value;
                    break;
                }
            }
            if(isset($selected_site)){
                $sites = $companies['sites'][$selected];
                //$sites = Site::whereIn('id',$sites)->get();
                return $sites;
            }
            
        }
        return [];
    }

    public static function getCurrentCompany()
    {
        $companies = Company::getUsersCompanies();
        foreach($companies['companies'] as $key=>$company) {
            if (isset($company['selected'])) {
                return $company;
            }
        }
        return null;
    }
}