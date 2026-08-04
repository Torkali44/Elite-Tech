<?php
namespace App\Http\Controllers;
class MentorController extends Controller {
    public function index() {
        $mentors = [
            ['name'=>'د. أحمد الشريف','expertise'=>'AI & Machine Learning','tags'=>['Python','ML','15+ سنة']],
            ['name'=>'سارة العلي','expertise'=>'Product Management','tags'=>['SaaS','Startups']],
            ['name'=>'محمد فاروق','expertise'=>'Cloud Architecture','tags'=>['AWS','GCP','K8s']],
            ['name'=>'ليلى الزهراني','expertise'=>'UX Research','tags'=>['Figma','Design Systems']],
            ['name'=>'يوسف العنزي','expertise'=>'Cybersecurity','tags'=>['SOC','Pentesting']],
            ['name'=>'رنا حسن','expertise'=>'Data Engineering','tags'=>['Spark','Kafka']],
        ];
        return view('mentors.index', compact('mentors'));
    }
}
