<?php

    namespace App\ResponseModel;

    class ResponseStatus{
        const success = "success";
        const failed = "failed";
        const error = "error";
    }

    class ResponseModel{
        public string $status;
        //@template T
        public $data;
        public string $id;
        public string $message;

        private function __construct(string $status, $data, $id,$message){
            $this->status = $status;
            $this->data = $data;
            $this->id = $id;
            $this->message = $message;
        }
        
        public static function Ok($data,string $id,string $message) : ResponseModel{
            return new ResponseModel(ResponseStatus::success,$data,$id,$message);
        }

        public static function Failed($data,string $id,string $message){
            return new ResponseModel(ResponseStatus::failed,$data,$id,$message);
        }
    }