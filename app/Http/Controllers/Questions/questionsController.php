<?php
namespace App\Http\Controllers\Questions;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class questionsController extends Controller{
      public function index(){
          $list=DB::table("questions")->paginate(8);
          return view("questions.index",compact("list"));
      }
      public function addQuestions(){
          return view("questions.addQuestions");
      }
    //创建问题
      public function createQuestions(Request $request){
          $data=$request->except("_token");
          $data['user_id']=Auth::user()->id;
          try{
            if(Question::create($data)){
                return json_encode([
                   'status'=>1
                ]);
            }

          }catch(\Exception $e){

          }
      }
    //问题详情
    public function questionsDesc(Request $request){
        $id=$request->get('id');
        $list=DB::table("questions")
            ->join("users","questions.user_id","=","users.id")
            ->where("questions.id",$id)
            ->select("questions.*","users.name")
            ->first();
        return view('questions.questionsDesc',compact("list"));
    }
  //更新问题
   public function editQuestions(Request $request){
        $id=$request->get("id");
       $list=Question::where("id",$id)->first();
       return view("questions.editQuestions",compact("list"));
   }
  public function updateQuestions(Request $request){
      $id=$request->get('id');
      $data=$request->except('_token');
      try{
          if(Question::where('id',$id)->update($data)){
              return json_encode([
                  'status'=>1
              ]);
          }
      }catch(\Exception $e){

      }
  }
}
?>