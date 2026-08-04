<?php
namespace App\Http\Controllers;

class CommunityController extends Controller {
    private function members() {
        return [
            ['id'=>1,'name'=>'أحمد محمد','title'=>'AI Engineer','project'=>'منصة تحليل الأكواد باستخدام نماذج تعلم آلة','bio'=>'ذكاء اصطناعي، تعلم آلي، معالجة اللغات الطبيعية. خبرة 7 سنوات.','skills'=>['Python','TensorFlow','PyTorch'],'likes'=>124],
            ['id'=>2,'name'=>'سارة خالد','title'=>'Full Stack Dev','project'=>'منصة تشخيص هيئة ذكية','bio'=>'مطورة Full Stack بخبرة في بناء تطبيقات SaaS معقدة.','skills'=>['React','Node.js','MongoDB'],'likes'=>98],
            ['id'=>3,'name'=>'أحمد مصطفى','title'=>'DevOps Lead','project'=>'أداة تخصيص الاجتماعات الآلية','bio'=>'خبير في البنية التحتية السحابية و CI/CD.','skills'=>['AWS','Kubernetes','Terraform'],'likes'=>112],
            ['id'=>4,'name'=>'ليلى سعيد','title'=>'ML Engineer','project'=>'نظام تعاون للمطورين العرب','bio'=>'باحثة في الذكاء الاصطناعي التطبيقي.','skills'=>['Python','LangChain','LLM'],'likes'=>215],
            ['id'=>5,'name'=>'محمد علي','title'=>'Security Architect','project'=>'منصة إدارة الحوادث','bio'=>'أمن سيبراني وبنية شبكات.','skills'=>['Cybersecurity','Kafka','ELK'],'likes'=>167],
            ['id'=>6,'name'=>'هبة أحمد','title'=>'Data Scientist','project'=>'منصة تعاون لشركات الأثاث','bio'=>'علوم بيانات و تحليل استهلاك.','skills'=>['Firebase','Flutter','Google Maps'],'likes'=>78],
        ];
    }
    public function index() { return view('community.index', ['members'=>$this->members()]); }
    public function show($id) {
        $member = collect($this->members())->firstWhere('id',(int)$id) ?? $this->members()[0];
        return view('community.show', compact('member'));
    }
}
