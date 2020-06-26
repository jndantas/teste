<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Company{

    /**
     * Get company view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "superadmin") {
            return view('errors/404');   
        }
    	$companies = Database::table("companies")->where("id",">", 1)->get();
    	$companiesData = array();
        $backgroundColors = array("bg-danger","bg-success","bg-warning","bg-purple");
    	foreach ($companies as $company) {
    		$companiesData[] = array(
    											"company" => $company,
    											"owner" => Database::table("users")->where("company",$company->id)->where("role" ,"admin")->first(),
                                                "team" => Database::table("users")->where("company" , $company->id)->count("id", "team")[0]->team,
                                                "files" => Database::table("files")->where("company" , $company->id)->count("id", "files")[0]->files,
                                                "disk" => Database::table("files")->where("company" , $company->id)->sum("size", "size")[0]->size,
    											"color" => $backgroundColors[array_rand($backgroundColors)]
    										);
    	}
    	$data = array(
    			"user" => Auth::user(),
    			"companies" => $companiesData
    		);
    	if ($data["user"]->role != "superadmin") {
    		return view('errors/404');
    	}
        return view('companies', $data);
    }

    /**
     * Delete company account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("companies")->where("id", input("companyid"))->first();
        Database::table("companies")->where("id", input("companyid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Company Deleted!", "Company successfully deleted.","reload()")));
    }

    /**
     * Company update view
     * 
     * @return Json
     */
    public function updateview() {
        $data = array(
                "company" => Database::table("companies")->where("id", input("companyid"))->first()
            );
        return view('extras/updatecompany', $data);
    }

    /**
     * Update company account
     * 
     * @return Json
     */
    public function update() {
        $account = Database::table("companies")->where("id", input("companyid"))->first();
        foreach (input()->post as $field) {
            if ($field->index == "csrf-token" || $field->index == "companyid") {
                continue;
            }
            Database::table("companies")->where("id" , input("companyid"))->update(array($field->index => escape($field->value)));
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Company successfully updated","reload()")));
    }

}
