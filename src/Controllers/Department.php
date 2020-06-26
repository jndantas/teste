<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Department{

    /**
     * Get departments view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $departments = Database::table("departments")->where("company", Auth::user()->company)->get();
        $departmentsData = array();
        foreach ($departments as $department) {
            $members = Database::table("departmentmembers")->where("department", $department->id)->get("member");
            $departmentMembers = array(0);
            foreach ($members as $member) {
                $departmentMembers[] = $member->member;
            }
            $membersIds = implode(",", $departmentMembers);
            $departmentsData[] = array(
                    "department" => $department,
                    "team" => Database::table("departmentmembers")->where("department" , $department->id)->count("id", "team")[0]->team,
                    "files" => Database::table("files")->where("uploaded_by","IN" , "(".$membersIds.")")->count("id", "files")[0]->files,
                    "disk" => Database::table("files")->where("uploaded_by","IN" , "(".$membersIds.")")->sum("size", "size")[0]->size
                );
        }
        $user = Auth::user();
        $team = Database::table("users")->where("company", Auth::user()->company)->where("role", "!=", "user")->get();
        $departments = $departmentsData;
        return view('departments', compact("user","team","departments"));
    }

    /**
     * Create department
     * 
     * @return Json
     */
    public function create() {
        header('Content-type: application/json');
        $data = array(
        				"company" => Auth::user()->company,
        				"name" => input("name"),
        				"email" => input("email"),
        			);
        Database::table("departments")->insert($data);
        if (!empty(input("members"))) {
        	$departmentId = Database::table("departments")->insertId();
        	foreach (input("members") as $member) {
        		Database::table("departmentmembers")->insert(array("department" => $departmentId, "member" => $member));
        	}
        }
        exit(json_encode(responder("success", "Alright!", "Department successfully added.","reload()")));
    }

    /**
     * Delete Department
     * 
     * @return Json
     */
    public function delete() {
        Database::table("departments")->where("id", input("departmentid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Department Deleted!", "Department successfully deleted.","reload()")));
    }

    /**
     * Department update view
     * 
     * @return Json
     */
    public function updateview() {
    	$members = Database::table("departmentmembers")->where("department", input("departmentid"))->get("member");
    	$membersIds = array();
    	foreach ($members as $member) {
    		$membersIds[] = $member->member;
    	}
        $data = array(
                "department" => Database::table("departments")->where("id", input("departmentid"))->first(),
                "members" => $membersIds,
                "team" => Database::table("users")->where("company", Auth::user()->company)->where("role", "!=", "user")->get()
            );
        return view('extras/updatedepartment', $data);
    }

    /**
     * Update Department
     * 
     * @return Json
     */
    public function update() {
        Database::table("departments")->where("id" , input("departmentid"))->update(array("name" => escape(input("name"))));
        Database::table("departments")->where("id" , input("departmentid"))->update(array("email" => escape(input("email"))));
        Database::table("departmentmembers")->where("department", input("departmentid"))->delete();
        if (!empty(input("members"))) {
        	foreach (input("members") as $member) {
        		Database::table("departmentmembers")->insert(array("department" => input("departmentid"), "member" => $member));
        	}
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Department successfully updated","reload()")));
    }
}
