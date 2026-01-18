<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
    class Case_closure_recommendation extends My_Model_Factory
    {
    }

    class mysql_case_closure_recommendation extends My_Model
    {
        protected $modelName = "case_closure_recommendation";
        protected $_table = "case_closure_recommendation";
     
      protected $_listFieldName = "investigation_officer_recommendation";
        protected $_fieldsNames=["id","case_id","date_recommended","investigation_officer_recommendation","recommendation_status","approval_remarks","approval_date","approval_status","approvedBy","createdOn","createdBy"];
       
        protected $allowedNulls = [];
        protected $builtInLogs = true;

        public function __construct()
        {
            parent::__construct();
            //case id is compulsury, date_recommended, createdby is compulsury
           $this->validate = [];

        }
        public function load_recommendation_by_id($id)
        {
            $query = [];

            $query["select"] = [
                "case_closure_recommendation.*,
                ISNULL(created.firstName, '') + ' ' + ISNULL(created.lastName, '') AS createdByName,
                ISNULL(approved.firstName, '') + ' ' + ISNULL(approved.lastName, '') AS approvedByName"
            ];

            $query["where"] = ["case_closure_recommendation.id", $id];

            $query["join"] = [
                ["user_profiles created", "created.user_id = case_closure_recommendation.createdBy", "left"],
                ["user_profiles approved", "approved.user_id = case_closure_recommendation.approvedBy", "left"]
            ];

            return  $this->load($query);

        }
        public function load_recommendation_by_case_id($id)
        {
            $query = [];

            $query["select"] = [
                "case_closure_recommendation.*,
                ISNULL(created.firstName, '') + ' ' + ISNULL(created.lastName, '') AS createdByName,
                ISNULL(approved.firstName, '') + ' ' + ISNULL(approved.lastName, '') AS approvedByName"
            ];

            $query["where"] = ["case_closure_recommendation.case_id", $id];

            $query["join"] = [
                ["user_profiles created", "created.user_id = case_closure_recommendation.createdBy", "left"],
                ["user_profiles approved", "approved.user_id = case_closure_recommendation.approvedBy", "left"]
            ];

            return  $this->load($query);

        }



    }

class mysqli_Case_closure_recommendation extends mysql_Case_closure_recommendation
{
}
class sqlsrv_Case_closure_recommendation extends mysql_Case_closure_recommendation
{

}

