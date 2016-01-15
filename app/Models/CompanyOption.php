<?php namespace App\Models;


class CompanyOption extends Root
{
    protected $table = 'company_options';

    public static function saveCompanyOption($current_company_id, $key, $value)
    {
        $option = CompanyOption::firstOrCreate(['key' => $key, 'company_id' => $current_company_id]);
        $option->value = $value;
        $option->save();

        return $option;
    }

    public static function deleteCompanyOption($current_company_id, $key)
    {
        $option = CompanyOption::whereKey($key)->whereCompanyId($current_company_id)->delete();
    }

    public static function getCompanyOption($current_company_id, $key)
    {
        $option = CompanyOption::whereCompanyId($current_company_id)->whereKey($key)->first();
        return $option;
    }
}
