<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

trait MasterTrait {
    public function checkAllFields(Request $request){
        try {
          $data = (array)$request->all();
          foreach($data as $key => $value) {
            if($data[$key] == ''){
              return response()->json(["status" =>  false, "message" => "Please Enter " . $key ]); 
            }
          }  
        } catch (\Throwable $th) {
          return response()->json(["status" =>  "TH-ERROR-".$th->getCode(), "message" => $th->getMessage()]);  
    
        } catch (\Exception $ex) {
          return response()->json(["status" => "EX-ERROR-".$ex->getCode(), "message" =>$ex->getMessage()]);
        }
    }
    
    public function createTable($model_name, $data){
        return $model_name::create($data);
    }

    public function lastInsertedData($model_name, $data){
        return $model_name::create($data)->id;
    }
    

    public function updateTable($model_name,$cond,$data){
        return $model_name::where($cond)->update($data);
    }

    public function checkTable($model_name,$cond){
        return $model_name::where($cond)->count();
    }
    
    public function singleData($model_name, $cond){
        return $model_name::where($cond)->first();
    }

    public function latestData($model_name, $cond){
        return $model_name::where($cond)->orderBy("created_at","DESC")->first();
    }
    public function selecLatestData($model_name,$fields,$cond){
        return $model_name::where($cond)->select($fields)->orderBy("created_at","DESC")->first();
    }
    public function selectData($model_name,$fields,$cond){
        return $model_name::where($cond)->select($fields)->first();
    }

    public function selectAllData($model_name,$fields,$cond){
        return $model_name::where($cond)->select($fields)->get();
    }

    public function allData($model_name, $cond){
        return $model_name::where($cond)->get();
    }

    public function fetchAll($model_name){
        return $model_name::all();
    }

    public function fetchAllOrderBy($model_name, $filed_name, $order){
        return $model_name::orderBy($filed_name, $order)->get();
    }

    public function find($model_name, $id){
        return $model_name::find($id);
    }

    public function delete($model_name, $id){
        return $model_name::find($id)->delete();
    }

    public function deleteTable($model_name, $cond){
        return $model_name::where($cond)->delete();
    }
    public function countRecord($model_name, $cond){
        return $model_name::where($cond)->count();
    }
    public function countAll($model_name){
        return $model_name::count();
    }

    public function customData($query){
        return \DB::select($query);
    }

    public function sendMail($to, $subject, $body, $attachments = []) {
        
        $mail = new PHPMailer(true);
        //try {
            //Server settings
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host       = env('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('SMTP_USERNAME');
            $mail->Password   = env('SMTP_PASSWORD');
            $mail->SMTPSecure = env('SMTP_ENCRYPTION');
            $mail->Port       = env('SMTP_PORT');
  
            //Recipients
            $mail->setFrom(env('SMTP_USERNAME'), env('APP_NAME'));
            if(is_array($to)) {
              foreach($to as $email_address) {
                $mail->addAddress($email_address);
              }
            }
            else {
              $mail->addAddress($to);
            }
  
            if(count($attachments) > 0) {
              foreach($attachments as $attachment)
              $mail->addAttachment($attachment);
            }
  
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->send();
            if (!$mail->Send())
            {
              echo "Error: $mail->ErrorInfo";
            }
            else
            {
                return true;
            }
            
            
        /* } catch (Exception $e) {
            return false;
        } */
    }
}
